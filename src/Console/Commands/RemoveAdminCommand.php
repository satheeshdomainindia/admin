<?php

namespace laravelzone\admin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use laravelzone\admin\traits\RemoveAdmin;

class RemoveAdminCommand extends Command
{
    protected $appname='admin';
    protected $stub_path;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Admin Panel ';

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
        $this->unPublishGuard()
             ->rollbackControllers()
             ->rollbackRoutes()
             ->unRegisterRoutes()
             ->rollbackViews()
             ->removeFactory()
             ->removeMigration()
             ->removeModel();
    }

  

    protected function rollbackControllers()
    {
        try {
            $guard = $this->parseName()['{{singularClass}}'];
            $path = app_path("/Http/Controllers/{$guard}");
            array_map('unlink', glob("{$path}/*.php"));
            rmdir($path);
        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
        $this->error("Step 2.  Controllers for {$guard} is rollbacked from App\Http\Controller\ {$guard} \n");

        return $this;
    }

    protected function rollbackRoutes()
    {
        try {
            unlink(base_path("routes/{$this->appname}.php"));
            $this->error("Step 3. Routes for {$this->appname} is rollbacked from routes directory \n");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        return $this;
    }



    protected function rollbackViews()
    {
        $guard = $this->parseName()['{{singularClass}}'];
        $views_path = resource_path("views/{$guard}");
        $dirs = ['/passwords/', '/admin/','/roles/', '/layouts/', '/'];
        foreach ($dirs as $dir) {
            array_map('unlink', glob("{$views_path}{$dir}*.php"));
            rmdir($views_path.$dir);
        }
        $this->error("Step 5. Views are removed from resources\\views\\{$guard}  directory \n");

        return $this;
    }

    protected function removeModel()
    {
        $model = app_path($this->parseName()['{{singularClass}}'].'.php');
        unlink($model);
        unlink( app_path('Role.php'));
        $this->error("Step 8. Model for {$this->appname} is removed from App\\{$this->appname}.php \n");

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

    /**
     * Get project namespace
     * Default: App.
     * @return string
     */
    protected function getNamespace()
    {
        $namespace = Container::getInstance()->getNamespace();

        return rtrim($namespace, '\\');
    }
}
