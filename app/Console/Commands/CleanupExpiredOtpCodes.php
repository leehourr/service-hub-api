<?php

namespace App\Console\Commands;

use App\Models\OtpCode;
use Illuminate\Console\Command;

class CleanupExpiredOtpCodes extends Command
{
    protected $signature = 'cleanup:expired-otp-codes';

    protected $description = 'Delete expired OTP codes';

    public function handle()
    {
        // Delete expired OTP codes
        OtpCode::where('expires_at', '<', now())->delete();

        info('Expired OTP codes cleaned up successfully.');
    }
}
