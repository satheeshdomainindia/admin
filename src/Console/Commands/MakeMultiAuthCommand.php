<?php

namespace laravelzone\admin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use laravelzone\admin\traits\MakeAdmin;

class MakeMultiAuthCommand extends Command
{
    protected $name;
    protected $stub_path;

    use MakeAdmin;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:make 
                                {name=student : Give a name for guard}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic multilogin and registration system';

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
        if ($this->checkGuard()) {
            $this->error("Guard '{$this->name}' already exist");

            return;
        }
        $this->addGuard()
             ->publishControllers()
             ->publishRoutes()
             ->registerRoutes()
             ->loadViews()
             ->publishFactory()
             ->publishMigration()
             ->publishModel()
             ->publishMiddleware()
             ->registerMiddleware()
             ->publishNotification();
    }

}
