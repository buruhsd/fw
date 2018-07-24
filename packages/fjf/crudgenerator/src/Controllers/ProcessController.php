<?php

namespace Fjf\Crudgenerator\Controllers;

use App\Http\Controllers\Controller;
use Artisan;
use File;
use Illuminate\Http\Request;
use Response;
use View;


class ProcessController extends Controller
{
    /** @var string  */
    protected $routeName = '';

    /** @var string  */
    protected $controller = '';

    /**
     * Display generator.
     *
     * @return Response
     */
    public function getGenerator()
    {
        $data['script_master'] = $this->footer_script(__FUNCTION__);
        return view('laravel-admin::generator', $data);
    }

    /**
    * @param $request form #choose
    * @return
    */
    public function run(Request $request){
        $choose = '';
        if ($request->has('choose')) {
            $choose = $request->choose;
        }
        switch ($choose) {
            case 'all':
                return $this->postGenerator($request);
                break;
            case 'controller':
                return $this->postController($request);
                break;
            case 'model':
                return $this->postModel($request);
                break;
            case 'migration':
                return $this->postMigration($request);
                break;
            case 'view':
                return $this->postView($request);
                break;
            default:
                return $this->postGenerator($request);
                break;
        }
    }

    /**
     * Process generator.
     *
     * @return Response
     */
    public function postGenerator($request)
    {
        $commandArg = [];
        $commandArg['name'] = $request->crud_name;

        if ($request->has('fields')) {
            $fieldsArray = [];
            $validationsArray = [];
            $x = 0;
            foreach ($request->fields as $field) {
                if ($request->fields_required[$x] == 1) {
                    $validationsArray[] = $field;
                }

                $fieldsArray[] = $field . '#' . $request->fields_type[$x];

                $x++;
            }

            $commandArg['--fields'] = implode(";", $fieldsArray);
        }

        if (!empty($validationsArray)) {
            $commandArg['--validations'] = implode("#required;", $validationsArray) . "#required";
        }

        if ($request->has('route')) {
            $commandArg['--route'] = $request->route;
        }

        if ($request->has('view_path')) {
            $commandArg['--view-path'] = $request->view_path;
        }

        if ($request->has('controller_namespace')) {
            $commandArg['--controller-namespace'] = $request->controller_namespace;
        }

        if ($request->has('model_namespace')) {
            $commandArg['--model-namespace'] = $request->model_namespace;
        }

        if ($request->has('route_group')) {
            $commandArg['--route-group'] = $request->route_group;
        }

        if ($request->has('relationships')) {
            $commandArg['--relationships'] = $request->relationships;
        }

        if ($request->has('form_helper')) {
            $commandArg['--form-helper'] = $request->form_helper;
        }

        if ($request->has('soft_deletes')) {
            $commandArg['--soft-deletes'] = $request->soft_deletes;
        }

        try {
            Artisan::call('crud:generate', $commandArg);

            $menus = json_decode(File::get(base_path('resources/laravel-admin/menus.json')));

            $name = $commandArg['name'];
            $routeName = ($commandArg['--route-group']) ? $commandArg['--route-group'] . '/' . snake_case($name, '-') : snake_case($name, '-');

            $menus->menus = array_map(function ($menu) use ($name, $routeName) {
                if ($menu->section == 'Modules') {
                    array_push($menu->items, (object) [
                        'title' => $name,
                        'url' => '/' . $routeName,
                    ]);
                }

                return $menu;
            }, $menus->menus);

            File::put(base_path('resources/laravel-admin/menus.json'), json_encode($menus));

            Artisan::call('migrate');
        } catch (\Exception $e) {
            return Response::make($e->getMessage(), 500);
        }

        $status = 200;
        $message = 'Your CRUD has been generated. See on the menu.';
        return redirect('admin/generator')->with(['flash_status', $status, 'flash_message', $message]);
    }

    /**
    * Process Generate Controller
    * @param
    * @return
    */
    public function postController($request){
        $name = $request->crud_name;
        $modelName = str_singular($name);
        $crud_arr['--crud-name'] = $name;
        $crud_arr['--model-name'] = $modelName; 

        if ($request->has('controller_namespace')) {
            $controllerNamespace = $request->controller_namespace;
            $crud_arr['name'] = $controllerNamespace . $name . 'Controller';
        }

        if ($request->has('model_namespace')) {
            $modelNamespace = $request->model_namespace;
            $crud_arr['--model-namespace'] = $modelNamespace; 
        }

        if ($request->has('view_path')) {
            $viewPath = $request->view_path;
            $crud_arr['--view-path'] = $viewPath; 
        }

        if ($request->has('route_group')) {
            $routeGroup = $request->route_group;
            $crud_arr['--route-group'] = $routeGroup; 
            $this->routeName = ($routeGroup) ? $routeGroup . '/' . snake_case($name, '-') : snake_case($name, '-');
        }

        if ($request->has('fields')) {
            $fieldsArray = [];
            $validationsArray = [];
            $x = 0;
            foreach ($request->fields as $field) {
                if ($request->fields_required[$x] == 1) {
                    $validationsArray[] = $field;
                }
                $fieldsArray[] = $field . '#' . $request->fields_type[$x];
                $x++;
            }
            $fields = implode(";", $fieldsArray);
            $crud_arr['--fields'] = $fields; 
        }

        if (!empty($validationsArray)) {
            $validations = implode("#required;", $validationsArray) . "#required";
            $crud_arr['--validations'] = $validations;
        }

        if ($request->has('perpage')) {
            $perpage = $request->perpage;
            $crud_arr['--pagination'] = $perpage; 
        }

        $route = 'no';
        if ($request->has('route')) {
            $route = $request->route;
        }

        try {
            Artisan::call('crud:controller', $crud_arr);

            // Updating the Http/routes.php file
            $routeFile = app_path('Http/routes.php');
            if (\App::VERSION() >= '5.3') {
                $routeFile = base_path('routes/web.php');
            }

            $status = 200;
            $message = 'Your Controller has been generated. See on the menu.';
            
            if (file_exists($routeFile) && $route == 'yes') {
                $this->controller = ($controllerNamespace != '') ? $controllerNamespace . '\\' . $name . 'Controller' : $name . 'Controller';

                $isAdded = File::append($routeFile, "\n" . implode("\n", $this->addRoutes()));

                if ($isAdded) {
                    $message .= 'Crud/Resource route added to ' . $routeFile;
                } else {
                    $message .= 'Unable to add the route to ' . $routeFile;
                }
            }
            // Updating Menus
            $menus = json_decode(File::get(base_path('resources/laravel-admin/menus.json')));

            $routeName = ($crud_arr['--route-group']) ? $crud_arr['--route-group'] . '/' . snake_case($name, '-') : snake_case($name, '-');
            $menus->menus = array_map(function ($menu) use ($name, $routeName) {
                if ($menu->section == 'Modules') {
                    array_push($menu->items, (object) [
                        'title' => $name,
                        'url' => '/' . $routeName,
                    ]);
                }
                return $menu;
            }, $menus->menus);
        } catch (\Exception $e) {
            return Response::make($e->getMessage(), 500);
        }

        File::put(base_path('resources/laravel-admin/menus.json'), json_encode($menus));

        return redirect('admin/generator')->with(['flash_status', $status, 'flash_message', $message]);
    }

    /**
    * Process Generate Model
    * @param
    * @return
    */
    public function postModel($request){
        $name = $request->crud_name;
        $modelName = str_singular($name);
        $tableName = str_plural(snake_case($name));
        $crud_arr['--table'] = $tableName;

        $crud_arr['name'] = $modelName; 
        if ($request->has('model_namespace')) {
            $modelNamespace = $request->model_namespace;
            $crud_arr['name'] = $modelNamespace . $modelName; 
        }

        if ($request->has('fields')) {
            $fieldsArray = [];
            $x = 0;
            foreach ($request->fields as $field) {
                $fieldsArray[] = $field . '#' . $request->fields_type[$x];
                $x++;
            }
            $fields = implode(";", $fieldsArray);
        }
        $fieldsArray = explode(';', $fields);
        $fillableArray = [];
        $migrationFields = '';

        foreach ($fieldsArray as $item) {
            $spareParts = explode('#', trim($item));
            $fillableArray[] = $spareParts[0];
            $modifier = !empty($spareParts[2]) ? $spareParts[2] : 'nullable';

            // Process migration fields
            $migrationFields .= $spareParts[0] . '#' . $spareParts[1];
            $migrationFields .= '#' . $modifier;
            $migrationFields .= ';';
        }

        $commaSeparetedString = implode("', '", $fillableArray);
        $fillable = "['" . $commaSeparetedString . "']";
        $crud_arr['--fillable'] = $fillable;
        
        if ($request->has('relationships')) {
            $relationships = $request->relationships;
            $crud_arr['--relationships'] = $relationships;
        }
        
        if ($request->has('soft_deletes')) {
            $softDeletes = $request->soft_deletes;
            $crud_arr['--soft-deletes'] = $softDeletes;
        }

        if ($request->has('pk')) {
            $pk = $request->pk;
            $crud_arr['--pk'] = $pk; 
        }
        
        try {
        Artisan::call('crud:model', $crud_arr);
        } catch (\Exception $e) {
            return Response::make($e->getMessage(), 500);
        }

        $status = 200;
        $message = 'Your Model has been generated.';
        return redirect('admin/generator')->with(['flash_status', $status, 'flash_message', $message]);
    }

    /**
    * Process Generate Migration
    * @param
    * @return
    */
    public function postMigration($request){
        $name = $request->crud_name;
        $migrationName = str_plural(snake_case($name));
        $crud_arr['name'] = $migrationName;

        if ($request->has('fields')) {
            $fieldsArray = [];
            $x = 0;
            foreach ($request->fields as $field) {
                $fieldsArray[] = $field . '#' . $request->fields_type[$x];
                $x++;
            }
        }

        $migrationFields = '';
        foreach ($fieldsArray as $item) {
            $spareParts = explode('#', trim($item));
            $modifier = !empty($spareParts[2]) ? $spareParts[2] : 'nullable';

            // Process migration fields
            $migrationFields .= $spareParts[0] . '#' . $spareParts[1];
            $migrationFields .= '#' . $modifier;
            $migrationFields .= ';';
        }
        $crud_arr['--schema'] = $migrationFields;

        if ($request->has('pk')) {
            $pk = $request->pk;
            $crud_arr['--pk'] = $pk; 
        }

        if ($request->has('indexes')) {
            $indexes = $request->indexes;
            $crud_arr['--indexes'] = $indexes; 
        }

        if ($request->has('foreignKeys')) {
            $foreignKeys = $request->foreignKeys;
            $crud_arr['--foreign-keys'] = $foreignKeys; 
        }

        if ($request->has('soft_deletes')) {
            $softDeletes = $request->soft_deletes;
            $crud_arr['--soft-deletes'] = $softDeletes;
        }

        try {
        Artisan::call('crud:migration', $crud_arr);
        } catch (\Exception $e) {
            return Response::make($e->getMessage(), 500);
        }

        $status = 200;
        $message = 'Your Migration has been generated.';
        return redirect('admin/generator')->with(['flash_status', $status, 'flash_message', $message]);
    }

    /**
    * Process Generate View
    * @param
    * @return
    */
    public function postView($request){
        $name = $request->crud_name;
        $crud_arr['name'] = $name;

        if ($request->has('fields')) {
            $fieldsArray = [];
            $validationsArray = [];
            $x = 0;
            foreach ($request->fields as $field) {
                if ($request->fields_required[$x] == 1) {
                    $validationsArray[] = $field;
                }
                $fieldsArray[] = $field . '#' . $request->fields_type[$x];
                $x++;
            }
            $fields = implode(";", $fieldsArray);
        }
        $crud_arr['--fields'] = $fields;

        if (!empty($validationsArray)) {
            $validations = implode("#required;", $validationsArray) . "#required";
            $crud_arr['--validations'] = $validations;
        }

        if ($request->has('view_path')) {
            $viewPath = $request->view_path;
            $crud_arr['--view-path'] = $viewPath;
        }

        if ($request->has('route_group')) {
            $routeGroup = $request->route_group;
            $crud_arr['--route-group'] = $routeGroup;
        }

        if ($request->has('localize')) {
            $localize = $request->localize;
            $crud_arr['--localize'] = $localize;
        }

        if ($request->has('pk')) {
            $pk = $request->pk;
            $crud_arr['--pk'] = $pk; 
        }

        if ($request->has('form_helper')) {
            $formHelper = $request->form_helper;
            $crud_arr['--form-helper'] = $formHelper;
        }

        try {
        Artisan::call('crud:view', $crud_arr);
        } catch (\Exception $e) {
            return Response::make($e->getMessage(), 500);
        }

        $status = 200;
        $message = 'Your View has been generated.';
        return redirect('admin/generator')->with(['flash_status', $status, 'flash_message', $message]);
    }

    /**
     * Add routes.
     *
     * @return  array
     */
    protected function addRoutes()
    {
        return ["Route::resource('" . $this->routeName . "', '" . $this->controller . "');"];
    }

    /**
    * @param method $method
    * @return add main footer script / in spesific method
    */
    public function footer_script($method=''){
        ob_start();
        switch ($method) {
            case 'getGenerator':
                ?>
                <script type="text/javascript">
                    function arr(type=''){
                        switch(type) {
                            case 'controller':
                                $('.a').slideUp();
                                $('.c').show('slow');
                            break;
                            case 'model':
                                $('.a').slideUp();
                                $('.mo').show('slow');
                            break;
                            case 'view':
                                $('.a').slideUp();
                                $('.v').show('slow');
                            break;
                            case 'migration':
                                $('.a').slideUp();
                                $('.mi').show('slow');
                            break;
                            default:
                                $('.a').slideUp();
                                $('.a').show('slow');
                        }
                    }
                    $( document ).ready(function() {
                        arr();
                        $('#choose').on('change', function(){
                            arr($(this).find(':selected').val());
                        });
                        $(document).on('click', '.btn-add', function(e) {
                            e.preventDefault();

                            var tableFields = $('.table-fields'),
                                currentEntry = $(this).parents('.entry:first'),
                                newEntry = $(currentEntry.clone()).appendTo(tableFields);

                            newEntry.find('input').val('');
                            tableFields.find('.entry:not(:last) .btn-add')
                                .removeClass('btn-add').addClass('btn-remove')
                                .removeClass('btn-success').addClass('btn-danger')
                                .html('<span class="fa fa-minus"></span>');
                        }).on('click', '.btn-remove', function(e) {
                            $(this).parents('.entry:first').remove();

                            e.preventDefault();
                            return false;
                        });

                    });
                </script>
                <?php
                break;
        }
        $script = ob_get_contents();
        ob_end_clean();
        return $script;
    }
}
