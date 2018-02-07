<?php

namespace App\Http\Controllers;

use App\Http\ViewComposers\MenuOptions;
use App\Models\MenuOption;
use App\Models\MenuOptionsRole;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Whossun\Toastr\Facades\Toastr;

use App\Models\Role;
use App\Models\User;
use App\Models\PermissionRole;

use DB;
use App\Models\Permission;



class RolesController extends Controller
{

    public function __construct()
    {

        $this->middleware('permission:ver_roles.index',['only' => ['index', 'show']]);
        $this->middleware('permission:crear_roles',['only' => ['create', 'store']]);
        $this->middleware('permission:editar_roles',['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar_roles',['only' => ['destroy']]);
    }


    public function index(Request $request)
	{
	    return view('roles.index', []);
	}

	public function create(Request $request)
	{
        $permissions = Permission::getPermissions();

	    return view('roles.add', [  'options' => $permissions  ]);
	}

	public function edit(Request $request, $id)
	{
		$role = Role::findRole($id);
		$permissions = Permission::getPermissions();
		$optionsRole = Permission::getPermissionsByRol($role->id);

	    return view('roles.add', [ 'model' => $role , 'options' => $permissions, 'optionsRole' => $optionsRole ]);
	}

	public function show(Request $request, $id)
	{
		$role = Role::findRole($id);
        $permissions = Permission::getPermissions();
        $optionsRole = Permission::getPermissionsByRol($role->id);

	    return view('roles.show', [  'model' => $role , 'options' => $permissions, 'optionsRole' => $optionsRole ]);
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

		$count = Role::getCountRoles();

		$results = Role::Roles($start,$len, $search, $orderby, $dir);
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
		$role = null;

		$tipo_accion = '';

		if($request->id_role > 0) {
			$role = Role::findRole($request->id_role);
			$mensaje = "Se ha modificado correctamente.";
			$titulo_mensaje = "Rol modificado!";}
		else { 
			$role = new Role;
			$mensaje = "Se ha creado correctamente.";
			$titulo_mensaje = "Rol creado!";
            $tipo_accion = 'new';
		}

		//Estoy activando el rol principal
		if(($role->is_default !== $request->is_default) && ($request->is_default == "on")){
			Role::cleanDefaultRole();
			if($request->id_role > 0) { $role = Role::findRole($request->id_role);}
		}

		//Solo si es un nuevo rol se puede agregar este campo
		if($tipo_accion == 'new'){
            $role->name = $request->name;
        }
        $role->display_name = $request->display_name;
		$role->is_default = $request->is_default == "on" ? 1 : 0;

		try {
			$role->save();
			//Registrar las opciones del menu
			$options[] = explode(',',$request->get('permissions_role')[0]);

			if ($options[0] !== null) {
				$keysOptions = array_values($options[0]); //Obtener los ids de los permisos del menú seleccionadas

				//Obtener los permisos actuales
                $roleOptions = Permission::getPermissionsByRol($role->id);
				$arrayOptions = $roleOptions->toArray();
				$roleOptionsIds = array_map(function ($obj) {
					return $obj->id_permiso;
				}, $arrayOptions);

				//Eliminar la asignación de los permisos que no se seleccionaron
                PermissionRole::deletePermissionsByRol($role->id, $keysOptions);

				$newOptions = array_diff($keysOptions, $roleOptionsIds);
				foreach ($newOptions as $id_permision) {
				    $permision = Permission::find($id_permision);
                    $role->attachPermission($permision);

				}
			}

			Toastr::success($mensaje, $titulo_mensaje, [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);

		}catch(\Exception $e){
			Toastr::error("Ocurrió una excepción al crear el rol" , "Ocurrió un error", [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);
		}

	    return redirect('/roles');

	}

	public function store(Request $request)
	{
		return $this->update($request);
	}

	public function destroy(Request $request, $id) {
		$role = Role::find($id);
		try {
            // Force Delete
            $role->users()->sync([]); // Delete relationship data
            $role->perms()->sync([]); // Delete relationship data

            $role->forceDelete();

		} catch (\Exception $e) {
			return false;
		}
		return "OK";
	}

}