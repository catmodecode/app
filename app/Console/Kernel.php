<?php

namespace App\Console;

use App\Console\Commands\CreateUserCommand;
use App\Console\Commands\GenerateExceptionCommand;
use App\Console\Commands\GenerateTokensCommand;
use App\Console\Commands\KeyGenerateCommand;
use App\Console\Commands\Test;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CreateUserCommand::class,
        GenerateTokensCommand::class,
        KeyGenerateCommand::class,
        GenerateExceptionCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
