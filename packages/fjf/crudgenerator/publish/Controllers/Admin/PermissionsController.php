<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Permission;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{

    private $perPage = 15;
    private $mainTable = 'permissions';

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');

        if (!empty($keyword)) {
            $data['permissions'] = Permission::where('name', 'LIKE', "%$keyword%")->orWhere('label', 'LIKE', "%$keyword%")
                ->paginate($this->perPage);
        } else {
            $data['permissions'] = Permission::paginate($this->perPage);
        }
        $data['footer_script'] = $this->footer_script(__FUNCTION__);

        return view('admin.permissions.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        $data['footer_script'] = $this->footer_script(__FUNCTION__);
        return view('admin.permissions.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function store(Request $request)
    {
        $status = 200;
        $message = 'Permission added!';
        $this->validate($request, ['name' => 'required']);

        $res = Permission::create($request->all());
        if(!$res){
            $status = 500;
            $message = 'Permission Not added!';
        }

        return redirect('admin/permissions')
            ->with(['flash_status' => $status,'flash_message' => $message]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function show($id)
    {
        $data['permission'] = Permission::findOrFail($id);
        $data['footer_script'] = $this->footer_script(__FUNCTION__);

        return view('admin.permissions.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function edit($id)
    {
        $data['permission'] = Permission::findOrFail($id);
        $data['footer_script'] = $this->footer_script(__FUNCTION__);

        return view('admin.permissions.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return void
     */
    public function update(Request $request, $id)
    {
        $status = 200;
        $message = 'Permission updated!';
        $this->validate($request, ['name' => 'required']);

        $permission = Permission::findOrFail($id);
        $res = $permission->update($request->all());
        if(!$res){
            $status = 500;
            $message = 'Permission Not updated!';
        }

        return redirect('admin/permissions')
            ->with(['flash_status' => $status,'flash_message' => $message]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy($id)
    {
        $status = 200;
        $message = 'Permission deleted!';
        $res = Permission::destroy($id);
        if(!$res){
            $status = 500;
            $message = 'Permission Not deleted!';
        }

        return redirect('admin/permissions')
            ->with(['flash_status' => $status,'flash_message' => $message]);
    }

    /**
    * @param method $method
    * @return add main footer script / in spesific method
    */
    public function footer_script($method=''){
        ob_start();
        ?>
            <script type="text/javascript"></script>
        <?php
        switch ($method) {
            case 'index':
                ?>
                    <script type="text/javascript"></script>
                <?php
                break;
            case 'create':
                ?>
                    <script type="text/javascript"></script>
                <?php
                break;
            case 'show':
                ?>
                    <script type="text/javascript"></script>
                <?php
                break;
            case 'edit':
                ?>
                    <script type="text/javascript"></script>
                <?php
                break;
        }
        $script = ob_get_contents();
        ob_end_clean();
        return $script;
    }
}
