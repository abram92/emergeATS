<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
		Commands\import_basedata::class,
		Commands\import_aliases::class,
		Commands\import_alias_keywords::class,
		Commands\import_clients::class, 
		Commands\import_clientstaff::class, 
		Commands\import_users::class, 
		Commands\import_jobs::class, 
		Commands\import_jobcontacts::class, 
		Commands\import_joblocations::class, 
		Commands\import_candidates::class, 
		Commands\import_candidates_all::class, 
		Commands\import_candidates_pers::class, 
		Commands\import_candidates_prof::class, 
		Commands\import_longtext::class, 
		Commands\import_longtext_csv_encoded::class, 		
		Commands\import_documents::class, 
		Commands\import_cand_preferredlocations::class,
		Commands\import_application_states::class,
		Commands\import_job_applications::class,
		Commands\import_job_applications_audit::class,
		Commands\import_nextjobno::class,		
		Commands\import_emails::class,
		Commands\import_events::class,
		Commands\import_event_entities::class,
		Commands\map_event_entity_job_applications::class,
//		Commands\import_public_holidays::class,
		Commands\import_static_work_alerts::class,
		Commands\import_static_work_alert_jobs::class,
		Commands\import_static_work_alert_candidates::class,
		Commands\import_static_work_emails::class,
		Commands\import_static_work_email_recipients::class,
		Commands\import_static_work_email_jobs::class,
		Commands\import_static_work_email_candidates::class,
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
