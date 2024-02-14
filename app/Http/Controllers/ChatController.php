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
            $data = $request->validate([
                'message_text' => 'required',
                'sender_id' => 'required',
            ]);

            // Check if there is an existing chat between sender_id and user_id
            $existingChat = Chat::whereHas('participants', function ($query) use ($user_id, $data) {
                $query->where('user_id', $user_id)
                    ->orWhere('user_id', $data['sender_id']);
            }, '=', 2)->first();

            if (!$existingChat) {
                // If no existing chat, create a new one
                $newChat = Chat::create();
                Participant::create([
                    'user_id' => $user_id,
                    'chat_id' => $newChat->id,
                ]);
                Participant::create([
                    'user_id' => $data['sender_id'],
                    'chat_id' => $newChat->id,
                ]);

                $existingChat = $newChat;
            }

            // Create the message
            $message = Message::create([
                'message_text' => $data['message_text'],
                'sender_id' => $data['sender_id'],
                'chat_id' => $existingChat->id,
            ]);

            DB::commit();

            return response()->json(['message' => 'Chat sent successfully', 'chat' => $message], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }



    public function viewChat(Request $request, $sender_id, $user_id)
    {
        try {
            // Find the chat based on participants
            $chat = Chat::whereHas('participants', function ($query) use ($sender_id, $user_id) {
                $query->where('user_id', $sender_id);
            })->whereHas('participants', function ($query) use ($sender_id, $user_id) {
                $query->where('user_id', $user_id);
            })->with(['messages.sender:id,name'])
                ->first();

            if (!$chat) {
                return response()->json(['message' => 'No chat found'], 404);
            }

            $messages = $chat->messages()
                ->where(function ($query) use ($sender_id, $user_id) {
                    $query->where('sender_id', $sender_id)
                        ->orWhere('sender_id', $user_id);
                })
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) use ($user_id) {
                    $messageArray = $message->toArray();
                    $messageArray['sender_name'] = $message->sender->name;
                    $messageArray['isYou'] = ($message->sender_id == $user_id);
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
            $chatIdsFromMessages = Chat::whereHas('messages', function ($query) use ($user_id) {
                $query->where('sender_id', $user_id);
            })
                ->pluck('id');

            // Find chat IDs where the user is a participant
            $chatIdsFromParticipants = Chat::whereHas('participants', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
                ->pluck('id');

            $allChatIds = $chatIdsFromMessages->merge($chatIdsFromParticipants)->unique();

            // Retrieve chats with participants, messages, and last message
            $chats = Chat::with([
                'participants.user',
            ])
                ->whereIn('id', $allChatIds)
                ->get();

            $formattedChats = $chats->map(function ($chat) use ($user_id) {
                // Exclude the current user from participants
                $participants = $chat->participants->reject(function ($participant) use ($user_id) {
                    return $participant->user->id == $user_id;
                });

                // Extract participant names
                $participantNames = $participants->map(function ($participant) {
                    return $participant->user->name;
                });

                $lastMessage = $chat->messages()
                    ->orderBy('created_at', 'desc')
                    ->first();

                return [
                    'chat_id' => $chat->id,
                    'user_id' => $participants->first()->user->id,
                    'name' => $participantNames->first(), // Take the first participant's name
                    'account_type' => $participants->first()->user->account_type,
                    'last_text' => $lastMessage ? $lastMessage->message_text : null,
                ];
            });

            return response()->json(['chats' => $formattedChats], 200);
        } catch (\Throwable $e) {
            \Log::error("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }
    public function deleteChat($chat_id)
    {
        DB::beginTransaction();
        try {
            Chat::where('id', $chat_id)->delete();
            DB::commit();

            return response()->json(['message' => "chat removed"], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }

}
