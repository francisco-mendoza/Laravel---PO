<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Whossun\Toastr\Facades\Toastr;

use App\Models\User;
use App\Models\Role;
use App\Models\Area;


class UsersController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('permission:ver_users.index',['only' => ['index', 'show']]);
        $this->middleware('permission:crear_usuarios',['only' => ['create', 'store']]);
        $this->middleware('permission:editar_usuarios',['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar_usuarios',['only' => ['destroy']]);
    }


    public function index(Request $request)
	{
	    return view('users.index', []);
	}

	public function create(Request $request)
	{
		$roles = Role::getRoleOptions();
		$areas = Area::getAreasOption();
	    return view('users.add', [ 'roles' => $roles , 'areas' => $areas]);
	}

	public function edit(Request $request, $id)
	{
		$user = User::findUserJoined($id);
		$roles = Role::getRoleOptions();
		$areas = Area::getAreasOption();
	    return view('users.add', [  'model' => $user, 'roles' => $roles, 'areas' => $areas  ]);
	}

	public function show(Request $request, $id)
	{
		$user = User::findUserJoined($id);

	    return view('users.show', [  'model' => $user  ]);
	}

	public function grid(Request $request)
	{
		$len = $request->length;
		$start = $request->start;
		$search = null;
		$orderby = null;
		$dir = null;
		
		$count = User::getCountUsers();

		if($request->search['value']) {
			$search = $request->search['value'];
		}

		if($request->order[0]){
			$orderby = $request->order[0]['column'];
			$dir = $request->order[0]['dir'];
		}

		$results = User::Users($start,$len, $search, $orderby, $dir);

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

    public function validateFormUser(Request $request){

        $rules = [
            'username' => 'required|max:60',
            'firstname' => 'required|max:128',
            'lastname' => 'required|max:128',
            'email' => 'required|max:60|email',
            'id_area' => 'required',
            'id_role' => 'required',
        ];

        $niceNames = [
            'username' => 'Nombre de Usuario',
            'firstname' => 'Nombre',
            'lastname' => 'Apellido',
            'email' => 'Correo',
            'id_area' => 'Area Asignada',
            'id_role' => 'Rol Asignado',
        ];

        $this->validate($request, $rules ,[],$niceNames);

    }


	public function update(Request $request) {

        //Validar formulario
        $this->validateFormUser($request);

		$user = null;
		if($request->id_user > 0) {
			$user = User::findUser($request->id_user);
			$mensaje = "Se ha modificado correctamente.";
			$titulo_mensaje = "Usuario modificado!";
		}
		else {
			$user = new User;
			$mensaje = "Se ha creado correctamente.";
			$titulo_mensaje = "Usuario creado!";
		}    
		
			$user->username = $request->username;
			$user->firstname = $request->firstname;
			$user->lastname = $request->lastname;
			$user->email = $request->email;
			$user->id_area = $request->id_area;



        try {

            $user->save();

            $user->detachRoles();

            //No debería ocurrir porque siempre obligamos a seleccionar un rol en pantalla
            if($request->id_role == null || $request->id_role == ""){

                $user->attachRole( Role::getDefaultRole()->id_role);
            }else{
                $user->attachRole($request->id_role);
            }

            Toastr::success($mensaje, $titulo_mensaje, [
                "positionClass" => "toast-top-right",
                "progressBar" => true,
                "closeButton" => true,
            ]);

        }catch(\Exception $e){

            Toastr::error("Ocurrió una excepción al crear el usuario" , "Ocurrió un error", [
                "positionClass" => "toast-top-right",
                "progressBar" => true,
                "closeButton" => true,
            ]);
        }

	    return redirect('/users');

	}

	public function store(Request $request)
	{
		return $this->update($request);
	}

	public function destroy(Request $request, $id) {
		
		$user = User::findUser($id);

		try {
			$user->delete();
		} catch (\Exception $e) {
			return false;
		}
		return "OK";
	    
	}

	
}