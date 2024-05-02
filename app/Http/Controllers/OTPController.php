<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;
use App\Models\OTP;
use App\Models\Account;

class OTPController extends Controller
{
    public function sendOTP(Request $request, $account_id)
    {
        OTP::where('account_id', $account_id)->delete();
        // Generate OTP
        $otpCode = mt_rand(100000, 999999); // Generate a random OTP code

        // Send OTP via email
        // Retrieve the user's email based on the provided account_id
        $user = Account::findOrFail($account_id); // Assuming Account model is used
        // Mail::to($user->email)->send(new OTPMail($otpCode));

        // Store OTP in database
        OTP::create([
            'code' => $otpCode,
            'account_id' => $account_id,
        ]);

        return response()->json(['message' => 'OTP sent successfully'], 200);
    }

}
