<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Participant;
use Illuminate\Http\Request;
use DB;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }
    public function sendChat(Request $request, $user_id)
    {
        DB::beginTransaction();
        try {
            $data = $request->validate(
                [
                    'message_text' => 'required',
                    'sender_id' => 'required',
                    'chat_id' => 'nullable'
                ],
            );
            $existingChat = Chat::where('id', $data['chat_id'] ?? null)->first();

            if (!$existingChat) {
                // If no existing chat, create a new one
                $newChat = Chat::create();
                Participant::create([
                    'user_id' => $user_id,
                    'chat_id' => $newChat->id,
                ]);

                $chatId = $newChat->id;
            } else {
                $chatId = $existingChat->id;
            }

            $message = Message::create([
                'message_text' => $data['message_text'],
                'sender_id' => $data['sender_id'],
                'chat_id' => $chatId,
            ]);
            DB::commit();
            return response()->json(['message' => 'Chat sent successfully', 'chat' => $existingChat], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }

    public function viewChat(Request $request, $chat_id)
    {
        try {

            $chat = Chat::with(['messages.sender:id,name'])
                ->where('id', $chat_id)
                ->first();

            if (!$chat) {
                return response()->json(['message' => 'No message'], 404);
            }

            $messages = $chat->messages->map(function ($message) {
                $messageArray = $message->toArray();
                $messageArray['sender_name'] = $message->sender->name;
                unset($messageArray['sender']);
                return $messageArray;
            });

            return response()->json(['messages' => $messages], 200);
        } catch (\Throwable $e) {
            \Log::error("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }

    public function getChatList($user_id)
    {
        try {
            // Find chat IDs where the user is the sender in any message
            $chatIdsFromMessages = Chat::whereHas('messages', function ($query) use ($user_id) {
                $query->where('sender_id', $user_id);
            })
                ->pluck('id');

            // Find chat IDs where the user is a participant
            $chatIdsFromParticipants = Chat::whereHas('participants', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
                ->pluck('id');

            // Combine the chat IDs from messages and participants
            $allChatIds = $chatIdsFromMessages->merge($chatIdsFromParticipants)->unique();

            // Retrieve chats with participants and messages
            $chats = Chat::with(['participants.user'])
                ->whereIn('id', $allChatIds)
                ->get();

            $formattedChats = $chats->map(function ($chat) use ($user_id) {
                // Extract participant names
                $participantNames = $chat->participants->map(function ($participant) {
                    return $participant->user->name;
                });

                $firstParticipant = $chat->participants->first();

                return [
                    'chat_id' => $chat->id,
                    'name' => $participantNames->first(), // Take the first participant's name
                    'account_type' => $firstParticipant ? $firstParticipant->user->account_type : null,
                ];
            });

            return response()->json(['chats' => $formattedChats], 200);
        } catch (\Throwable $e) {
            \Log::error("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }
}
