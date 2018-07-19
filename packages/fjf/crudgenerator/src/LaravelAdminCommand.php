<?php

namespace Fjf\Crudgenerator;

use File;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class LaravelAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-admin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installing Laravel Admin Crud.';

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
        try {
            $this->call('migrate');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->error($e->getMessage());
            exit();
        }

        if (\App::VERSION() >= '5.2') {
            $this->info("Generating the authentication scaffolding");
            $this->call('make:auth');
        }

        $this->info("Publishing the assets");
        $this->call('vendor:publish', ['--provider' => 'Appzcoder\CrudGenerator\CrudGeneratorServiceProvider', '--force' => true]);
        $this->call('vendor:publish', ['--provider' => 'Fjf\Crudgenerator\LaravelAdminServiceProvider', '--force' => true]);

        $this->info("Dumping the composer autoload");
        (new Process('composer dump-autoload'))->run();

        $this->info("Migrating the database tables into your application");
        $this->call('migrate');

        $this->info("Adding the routes");

        $routeFile = app_path('Http/routes.php');
        if (\App::VERSION() >= '5.3') {
            $routeFile = base_path('routes/web.php');
        }

        $routes =
            <<<EOD
Route::get('admin', 'Admin\\AdminController@index');
Route::resource('admin/roles', 'Admin\\RolesController');
Route::resource('admin/permissions', 'Admin\\PermissionsController');
Route::resource('admin/users', 'Admin\\UsersController');
Route::get('admin/generator', ['uses' => '\Fjf\Crudgenerator\Controllers\ProcessController@getGenerator']);
Route::post('admin/generator', ['uses' => '\Fjf\Crudgenerator\Controllers\ProcessController@run']);
EOD;

        File::append($routeFile, "\n" . $routes);

        $userFile = app_path('Http/User.php');
        if (\App::VERSION() >= '5.3') {
            $routeFile = base_path('routes/web.php');
        }
        $repUser = 'use Notifiable, HasRoles';
        
        $this->info("Updating Model User usable");
        $this->replaseUser($userFile, $name);

        $this->info("Overriding the AuthServiceProvider");
        $contents = File::get(__DIR__ . '/../publish/Providers/AuthServiceProvider.php');
        File::put(app_path('Providers/AuthServiceProvider.php'), $contents);

        $this->info("Successfully installed Laravel Admin!");
    }

    /**
     * Replace the crudName for the given stub.
     *
     * @param  string  $stub
     * @param  string  $crudName
     *
     * @return $this
     */
    protected function replaseUser(&$stub, $crudName)
    {
        $stub = str_replace('use Notifiable', $crudName, $stub);

        return $this;
    }

}
