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
    protected $signature = 'fjf-crud:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installing Fjf Admin Crud.';

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

        $userFile = app_path('User.php');
        $repUser = 'use Notifiable, HasRoles;';
        
        $this->info("Updating Model User usable");
        $this->replaceUser($userFile, $repUser);

        $kernelFile = app_path('Http\Kernel.php');
        $repKernel ='protected $routeMiddleware = ['.PHP_EOL."\t\t".'\'roles\' => \App\Http\Middleware\CheckRole::class,';
        
        $this->info("Updating Kernel");
        $this->replaceKernel($kernelFile, $repKernel);

        $providerFile = app_path('Http\\Providers\\AppServiceProvider.php');
        $repProvider ='public function boot()'.PHP_EOL."\t".'{'.PHP_EOL."\t\t".'Schema::defaultStringLength(191);';
        
        $this->info("Updating AppProvider");
        $this->replaceProvider($providerFile, $repProvider);

        $this->info("Overriding the AuthServiceProvider");
        $contents = File::get(__DIR__ . '/../publish/Providers/AuthServiceProvider.php');
        File::put(app_path('Providers/AuthServiceProvider.php'), $contents);

        $this->info("Successfully installed Fjf Admin Crud!!!");
    }

    /**
     * Replace the replace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $replace
     *
     * @return $this
     */
    protected function replaceUser(&$stub, $replace)
    {
        $fhandle = fopen($stub,"r");
        $content = fread($fhandle,filesize($stub));
        $content = str_replace("use Notifiable;", $replace, $content);

        $fhandle = fopen($stub,"w");
        fwrite($fhandle,$content);
        fclose($fhandle);
    }

    /**
     * Replace the replace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $replace
     *
     * @return $this
     */
    protected function replaceKernel(&$stub, $replace)
    {
        $fhandle = fopen($stub,"r");
        $content = fread($fhandle,filesize($stub));
        $content = str_replace('protected $routeMiddleware = [', $replace, $content);

        $fhandle = fopen($stub,"w");
        fwrite($fhandle,$content);
        fclose($fhandle);
    }

    /**
     * Replace the replace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $replace
     *
     * @return $this
     */
    protected function replaceProvider(&$stub, $replace)
    {
        $fhandle = fopen($stub,"r");
        $content = fread($fhandle,filesize($stub));
        $content = str_replace('public function boot()'.PHP_EOL."\t".'{', $replace, $content);

        $fhandle = fopen($stub,"w");
        fwrite($fhandle,$content);
        fclose($fhandle);
    }

}
