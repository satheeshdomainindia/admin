<?php

namespace laravelzone\admin\Console\Commands;

use App\Role;
use App\Admin;
use Illuminate\Console\Command;

class SeedCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:seed {--r|role=}';

    protected $adminEmail;
    protected $adminName;
    protected $adminPassword;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed one super admin for multi auth package
                            {--role= : Give any role name to create new role}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $rolename = $this->option('role');
        $role = Role::whereName($rolename)->first();
        if (! $rolename) {
            $this->error("please provide role as --role='roleName'");

            return;
        }
        $admin = $this->createSuperAdmin($role, $rolename);

        $this->info("You have created an admin name '{$admin->name}' with role of '{$admin->roles->first()->name}' ");
        $this->info("Now log-in with {$admin->email} email and password as {$admin->adminPassword}");
    }

    protected function createSuperAdmin($role, $rolename)
    {
        if (empty($this->adminName)) {
            $this->adminName = $this->ask('What is the admin Name?', 'admin');
        }
       
        if (empty($this->adminEmail)) {
            $this->adminEmail = $this->ask('What is the admin email?', 'admin@app.com');
        }
        if (empty($this->adminPassword)) {
            $this->adminPassword = $this->secret('What is the admin password?');
        }
        $admin = factory(Admin::class)
            ->create([
            'name' =>ucfirst($this->adminName),
            'email' => $this->adminEmail,
            'password' => bcrypt($this->adminPassword),
             ]);
        if (! $role) {
            $role = factory(Role::class)->create(['name' => $rolename]);
        }
        $admin->roles()->attach($role);

        return $admin;
    }
}
