<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendNewsVerificationReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia un correo electronico a los usuarios que no han verificado su cuenta despues de haberse registraod hace una semana';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::query()
            ->whereDate('created_at', '=', Carbon::now()->subDays(7)->format('Y-m-d'))
            ->whereNull('email_verified_at')
            ->each(function (User $user){
                $user->sendEmailVerificationNotification();
            });
    }
}
