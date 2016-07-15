<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Custom;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function() {
            $this->wsdlUpdateJob();
        })
        ->everyMinute();

        $schedule->call(function() {
            $this->reminderForAppUpdate();
        })->monthly();
    }

    private function wsdlUpdateJob() {
        $fullMap = Custom\getWsdlMapAsArray();

        foreach ($fullMap as $WSDL) {
            Custom\checkAndUpdateWSDLFileWithCurl($WSDL);
        }
    }

    private function reminderForAppUpdate() {
        Mail::send("Emails.update_reminder",
          function($msg) {
              $msg->to("mhavelant+lumen2@brainsum.com")
                ->subject("Reminder - Check for updates!");
          });
    }
}
