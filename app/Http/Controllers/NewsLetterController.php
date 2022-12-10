<?php

namespace App\Http\Controllers;

use App\Console\Commands\SendNewsVerificationReminderCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
class NewsLetterController extends Controller
{
    //
    /**
     * @param array $middleware
     */
    public function send()
    {
        Artisan::call(SendNewsVerificationReminderCommand::class);
        return response()->json([
            'data' => 'Todo OK'
        ]);
    }
}
