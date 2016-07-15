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
        //->everyThirtyMinutes();

        // @todo: add reminder to update vendor stuff
    }

    private function wsdlUpdateJob() {
        $fullMap = Custom\getWsdlMapAsArray();

        foreach ($fullMap as $WSDL) {
            Custom\checkAndUpdateWSDLFileWithCurl($WSDL);
        }

        Mail::send("Emails.wsdl_check_info",
          ["datetimeOfCheck" => date("Y-m-d H:i:s")],
          function($msg) {
              $msg->to("mhavelant+lumen2@brainsum.com")
                ->subject("test");
          });
    }
}
