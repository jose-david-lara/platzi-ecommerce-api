<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use App\Notifications\NewsLetterNotification;
use Illuminate\Console\Command;

class SendNewsLetterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:newsletter
                            {emails?*} : Correos Electronicos a los cuales enviar directamente
                            {--s|schedule : Si debe ser ejecutado directamente o no}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia un correo electronico';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $emails = $this->argument('emails');
        $schedule = $this->option('schedule');

        $builder = User::query();

        if ($emails) {
            $builder->whereIn('email', $emails);
        }

        $count = $builder->count();


        if ($count) {

            $this->info("Se enviaran {$count} correos");

            if ($this->confirm('Estas de acuerdo?',) || $schedule) {
                $productQuery = Product::query();
                $productQuery->withCount(['qualifications as average_rating' => function($query){
                    $query->select(DB:raw('coalesce(avg(score),0)'));
                }])->orderByDesc('average_rating');

                $products = $productQuery->take(6)->get();

                $this->output->progressStart($count);
                $builder
                    ->whereNotNull('email_verified_at')
                    ->each(function (User $user) {
                        $user->notify(new NewsLetterNotification());
                        $this->output->progressAdvance();
                    });
                $this->output->progressFinish();
                $this->info("Se enviaron {$count} correos");
                return;
            }
        }

        $this->info('No se envio ningun correo');
        // return Command::SUCCESS;
    }
}
