<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Custom;

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
        })->everyFiveMinutes();

        // @todo: add reminder to update vendor stuff
    }

    private function wsdlUpdateJob() {
        $fullMap = Custom\getWsdlMapAsArray();

        foreach ($fullMap as $WSDL) {
            Custom\checkAndUpdateWSDLFileWithCurl($WSDL);
        }
    }
}
