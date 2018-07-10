<?php 

namespace Aljawad\CrudGenerator;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CrudGeneratorController extends Controller
{

    public function index()
    {
       return view('crudgenerator::index');
        // return view('crudgenerator.index');
    }

    public function store(Request $request)
    {
    		$exitCode = \Artisan::call('make:model', ['name'=> $request->table, '--all'=>'default']);
    		var_dump($exitCode); die();
            $response = file_put_contents(database_path('migrations/'.$request->table.'.php'), $request->table);
          
        
    }

}
