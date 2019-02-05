<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;
use Storage;
use Illuminate\Support\Facades\Schema;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    
    '\App\Console\Commands\feeNotification',
    '\App\Console\Commands\attendanceNotification',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
       if (Schema::hasTable('cronschedule')){
        $cronschedule = DB::table('cronschedule')->first();
        if(!empty($cronschedule)){
        $schedule->command('feeNotification:notification')
       // ->everyMinute();
         ->monthlyOn($cronschedule->date, $cronschedule->time)->timezone('Asia/Karachi');
         }
         }
        //$test = $schedule->exec('touch /tmp/mytest____')->everyMinute();
           if(Storage::disk('local')->exists('/public/cronsettings.txt')){
              $contant = Storage::get('/public/cronsettings.txt');
              $data = explode('<br>',$contant );

                //echo "<pre>";print_r($data);
                $attendance_time = $data[0]; 
             $schedule->command('attendanceNotification:attendacenotification')->everyFiveMinutes()/*->dailyAt($attendance_time)->timezone('Asia/Karachi')*/;
            }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
