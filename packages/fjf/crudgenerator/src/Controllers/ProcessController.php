<?php

namespace Fjf\Crudgenerator\Controllers;

use App\Http\Controllers\Controller;
use Artisan;
use File;
use Illuminate\Http\Request;
use Response;
use View;
use Illuminate\Console\Command;


class ProcessController extends Controller
{
    /**
     * Display generator.
     *
     * @return Response
     */
    public function getGenerator()
    {
        return view('laravel-admin::generator');
    }

    /**
     * Process generator.
     *
     * @return Response
     */
    public function postGenerator(Request $request)
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

        return redirect('admin/generator')->with('flash_message', 'Your CRUD has been generated. See on the menu.');
    }

    /**
    * Process Generate Controller
    * @param
    * @return
    */
    public function postController(Request $request){
        $name = $request->crud_name;
        $modelName = str_singular($name);

        if ($request->has('controller_namespace')) {
            $$controllerNamespace = $request->controller_namespace;
        }

        if ($request->has('model_namespace')) {
            $modelNamespace = $request->model_namespace;
        }

        if ($request->has('view_path')) {
            $viewPath = $request->view_path;
        }

        if ($request->has('route_group')) {
            $routeGroup = $request->route_group;
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
        }

        if (!empty($validationsArray)) {
            $validations = implode("#required;", $validationsArray) . "#required";
        }

        $perPage = intval(Command::option('pagination'));

        try {
        Artisan::call('crud:controller', ['name' => $controllerNamespace . $name . 'Controller', '--crud-name' => $name, '--model-name' => $modelName, '--model-namespace' => $modelNamespace, '--view-path' => $viewPath, '--route-group' => $routeGroup, '--pagination' => $perPage, '--fields' => $fields, '--validations' => $validations]);
        } catch (\Exception $e) {
            return Response::make($e->getMessage(), 500);
        }

        return redirect('admin/generator')->with('flash_message', 'Your Controller has been generated. See on the menu.');
    }

    /**
    * Process Generate Model
    * @param
    * @return
    */
    public function postModel(Request $request){
        $name = $request->crud_name;
        $modelName = str_singular($name);
        $tableName = str_plural(snake_case($name));

        if ($request->has('model_namespace')) {
            $modelNamespace = $request->model_namespace;
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
        
        if ($request->has('relationships')) {
            $relationships = $request->relationships;
        }
        
        if ($request->has('soft_deletes')) {
            $softDeletes = $request->soft_deletes;
        }

        $primaryKey = Command::option('pk');
        
        try {
        Artisan::call('crud:model', ['name' => $modelNamespace . $modelName, '--fillable' => $fillable, '--table' => $tableName, '--pk' => $primaryKey, '--relationships' => $relationships, '--soft-deletes' => $softDeletes]);
        } catch (\Exception $e) {
            return Response::make($e->getMessage(), 500);
        }

        return redirect('admin/generator')->with('flash_message', 'Your Model has been generated.');
    }

    /**
    * Process Generate Migration
    * @param
    * @return
    */
    public function postMigration(Request $request){
        $name = $request->crud_name;
        $migrationName = str_plural(snake_case($name));

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

        $primaryKey = Command::option('pk');
        $indexes = Command::option('indexes');
        $foreignKeys = Command::option('foreign-keys');

        if ($request->has('soft_deletes')) {
            $softDeletes = $request->soft_deletes;
        }

        try {
        Artisan::call('crud:migration', ['name' => $migrationName, '--schema' => $migrationFields, '--pk' => $primaryKey, '--indexes' => $indexes, '--foreign-keys' => $foreignKeys, '--soft-deletes' => $softDeletes]);
        } catch (\Exception $e) {
            return Response::make($e->getMessage(), 500);
        }

        return redirect('admin/generator')->with('flash_message', 'Your Migration has been generated.');
    }

    /**
    * Process Generate View
    * @param
    * @return
    */
    public function postView(Request $request){
        $name = $request->crud_name;

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

        if (!empty($validationsArray)) {
            $validations = implode("#required;", $validationsArray) . "#required";
        }

        if ($request->has('view_path')) {
            $viewPath = $request->view_path;
        }

        if ($request->has('route_group')) {
            $routeGroup = $request->route_group;
        }

        $localize = $this->option('localize');
        $primaryKey = Command::option('pk');
        if ($request->has('form_helper')) {
            $commandArg['--form-helper'] = $request->form_helper;
        }

        try {
        Artisan::call('crud:view', ['name' => $name, '--fields' => $fields, '--validations' => $validations, '--view-path' => $viewPath, '--route-group' => $routeGroup, '--localize' => $localize, '--pk' => $primaryKey, '--form-helper' => $formHelper]);
        } catch (\Exception $e) {
            return Response::make($e->getMessage(), 500);
        }

        return redirect('admin/generator')->with('flash_message', 'Your View has been generated.');
    }
}
