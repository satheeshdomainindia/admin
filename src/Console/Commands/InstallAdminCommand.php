<?php

namespace laravelzone\admin\Console\Commands;

use Illuminate\Console\Command;
use laravelzone\admin\traits\MakeAdmin;

class InstallAdminCommand extends Command
{
    protected $appname='admin';
    protected $stub_path;

    Use MakeAdmin;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Admin Panel';

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
        $this->addGuard()
        ->publishControllers()
        ->publishRoutes()
        ->registerRoutes()
        ->loadViews()
        ->publishFactory()
        ->publishMigration()
        ->publishModel(); 
        
        if (!is_dir(resource_path('/views/auth'))) {
            $this->call('make:auth');
        }
                
       if ($this->CheckDBCon()) {
        $this->call('migrate', ['--force' => true]);        
        $this->call('admin:seed', ['--role' => 'super']);
       }
       else {
        $this->error("Unable to Connect database !  \n ");
        $this->info("you should connect Mananullay \n Run command (php artisan migrate && php artisan admin:seed) \n");
       }


    }

    public function CheckDBCon()
    {
        try {
            \DB::connection()->getPdo();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }



    protected function publishControllers()
    {
        $this->call('vendor:publish', ['--tag' =>'admin:controllers']);       
        $this->info("Step 2. New Controllers for Admin is added to App\Http\Controller\Admin \n");
        return $this;
    }

    protected function publishRoutes()
    {
        $this->call('vendor:publish', ['--tag' =>'admin:routes']);
        $this->info("Step 3. Routes for Admin is added to routes/admin.php file \n");
        return $this;
    }


    protected function loadViews()
    {
        $this->call('vendor:publish', ['--tag' =>'admin:views']);
        $this->info("Step 5. Views are added to resources\\views\\admin directory \n");
        return $this;
    }



    protected function publishMigration()
    {
        $this->call('vendor:publish', ['--tag' =>array('admin:migrations','admin:assets','admin:factories','admin:config')]);
        $this->info("Step 7. Migration for admin table schema is added to database\migrations \n");
        return $this;
    }


    protected function publishModel()
    {  
        $this->call('vendor:publish', ['--tag' =>'admin:models']);
        $this->info("Step 8. Model for Admin is added to App\\Admin.php \n");

        return $this;
    }

      /**
     * Parse guard name
     * Get the guard name in different cases.
     * @param string $name
     * @return array
     */
    protected function parseName($name = null)
    {
        if (! $name) {
            $name = $this->appname;
        }

        return $parsed = [
            '{{pluralCamel}}'   => str_plural(camel_case($name)),
            '{{pluralSlug}}'    => str_plural(str_slug($name)),
            '{{pluralSnake}}'   => str_plural(snake_case($name)),
            '{{pluralClass}}'   => str_plural(studly_case($name)),
            '{{singularCamel}}' => str_singular(camel_case($name)),
            '{{singularSlug}}'  => str_singular(str_slug($name)),
            '{{singularSnake}}' => str_singular(snake_case($name)),
            '{{singularClass}}' => str_singular(studly_case($name)),
            '{{namespace}}'     => $this->getNamespace(),
        ];
    }

}
