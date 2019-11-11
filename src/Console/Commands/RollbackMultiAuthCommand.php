<?php

namespace laravelzone\admin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use laravelzone\admin\traits\RemoveAdmin;

class RollbackMultiAuthCommand extends Command
{
    protected $name;
    protected $stub_path;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:rollback 
                                {name=student : Give a name for guard}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback everything for Scaffoldings for any guard you have created';

    use RemoveAdmin;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->stub_path = __DIR__.'/../../../stubs';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->name = $this->argument('name');
        $this->rollback();
    }

    protected function rollback()
    {
        if (! $this->checkGuard()) {
            $this->error("Guard {$this->name} does't exist");

            return;
        }
        $this->unPublishGuard()
             ->rollbackControllers()
             ->rollbackRoutes()
             ->unRegisterRoutes()
             ->rollbackViews()
             ->removeFactory()
             ->removeMigration()
             ->removeModel()
             ->removeMiddleware()
             ->unRegisterMiddleware()
             ->removeNotification();
    }

   
}
