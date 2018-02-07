<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\PermissionRole;
use Toastr;

class PermissionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver_permissions.index',['only' => ['index', 'show']]);
        $this->middleware('permission:crear_permisos',['only' => ['create', 'store']]);
        $this->middleware('permission:editar_permisos',['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar_permisos',['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('permissions.index', []);
    }

    public function create(Request $request)
    {

        return view('permissions.add');
    }

    public function edit(Request $request, $id)
    {
        $permission = Permission::find($id);
        return view('permissions.add', [ 'model' => $permission ]);
    }

    public function show(Request $request, $id)
    {
        $permission = Permission::find($id);

        return view('permissions.show', ['model' => $permission]);
    }

    public function update(Request $request) {

        $rules = [
            'display_name' => 'required|max:60',
            'name' => 'required | max:60'
        ];
        $niceNames = [
            'display_name' => 'Nombre a mostrar',
            'name' => 'Nombre'
        ];


        $this->validate($request, $rules ,[],$niceNames);
        $permission = null;

        $tipo_accion = '';

        if($request->id_permission > 0) {
            $permission = Permission::find($request->id_permission);
            $mensaje = "Se ha modificado correctamente.";
            $titulo_mensaje = "Permiso modificado!";}
        else {
            $permission = new Permission;
            $mensaje = "Se ha creado correctamente.";
            $titulo_mensaje = "Permiso creado!";
            $tipo_accion = 'new';
        }


        //Solo si es un nuevo permiso se puede agregar este campo
        if($tipo_accion == 'new'){
            $permission->name = $request->name;
        }
        $permission->display_name = $request->display_name;
        $permission->description = $request->description;

        try {
            $permission->save();

            Toastr::success($mensaje, $titulo_mensaje, [
                "positionClass" => "toast-top-right",
                "progressBar" => true,
                "closeButton" => true,
            ]);

        }catch(\Exception $e){
            Toastr::error("Ocurrió una excepción al crear el permiso" , "Ocurrió un error", [
                "positionClass" => "toast-top-right",
                "progressBar" => true,
                "closeButton" => true,
            ]);
        }



        return redirect('/permissions');

    }

    public function store(Request $request)
    {
        return $this->update($request);
    }


    public function grid(Request $request)
    {

        $len = $request->length;
        $start = $request->start;
        $search = $orderby = $dir = null;

        if($request->search['value']) {
            $search = $request->search['value'];
        }

        if($request->order[0]){
            $orderby = $request->order[0]['column'];
            $dir = $request->order[0]['dir'];
        }

        $count = Permission::getCountPermissions();

        $results = Permission::Permissions($start,$len, $search, $orderby, $dir);
        $ret = [];
        foreach ($results as $row) {
            $r = [];
            foreach ($row as $value) {
                $r[] = $value;
            }
            $ret[] = $r;
        }

        $ret['data'] = $ret;
        $ret['recordsTotal'] = $count;
        $ret['iTotalDisplayRecords'] = $count;

        $ret['recordsFiltered'] = count($ret);
        $ret['draw'] = $request->draw;

        return json_encode($ret);

    }

    public function destroy(Request $request, $id) {

        $permission = Permission::find($id);

        try {
            //Borra los permisos asignados en roles
            PermissionRole::deletePermissionsRole($id);
            
            $permission->delete();

        } catch (\Exception $e) {
            return false;
        }

        return "OK";

    }


}
