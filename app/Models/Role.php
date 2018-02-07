<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;
use Auth;
use League\Flysystem\Exception;
use Session;
use OwenIt\Auditing\Auditable;
use Redirect;
use Config;

use Zizaco\Entrust\EntrustRole;

/**
 * App\Models\Role
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $description
 * @property bool $is_default
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereIdRole($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereIsDefault($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @property string $name
 * @property string $display_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $perms
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereDisplayName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereUpdatedAt($value)
 */
class Role extends EntrustRole
{

    use Auditable;

    const Finanzas = 1;
    const Gerente = 2;
    const General = 3;



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name','display_name', 'is_default'
    ];


    public static function Roles($start, $len, $search, $column, $dir){

        $roles = DB::table('roles')->select('id','display_name')->distinct();

        if($start!== null && $len !== null){
            $roles->skip($start)->limit($len);
        }

        if($search !== null){
            $roles->where('display_name','like', '%'.$search.'%');
        }

        if($column!== null && $dir !== null){
            $roles->orderBy('display_name', $dir);
        }else{
            $roles->orderBy('id', 'asc');
        }

        return $roles->get();
    }

    public static function getCountRoles(){

        return Role::all()->count();
    }

    public static function findRole($id){

        return Role::where('id',$id)->first();

    }

    public static function getDefaultRole(){
        return Role::where('is_default',true)->first();
    }

    public static function cleanDefaultRole(){

        return DB::table('roles')->update(['is_default'=> 0]);
    }

    public static function getRoleOptions(){

        return Role::orderBy('is_default', 'desc')->pluck('display_name','id');
    }

    public static function deleteRoleFromUsers($role){

        return DB::table('role_user')->where('role_id',$role)->delete();
    }

    /**
     * Many-to-Many relations with the user model.
     * Metodo reparado de entrust
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(){
        return $this->belongsToMany(Config::get('auth.providers.users.model'), Config::get('entrust.role_user_table'),Config::get('entrust.role_foreign_key'),Config::get('entrust.user_foreign_key'));
        // return $this->belongsToMany(Config::get('auth.model'), Config::get('entrust.role_user_table'));
    }

//    public static function RolGerente(){
//        return Auth::getUser()->id_role == self::Gerente ? true:false;
//    }
//    public static function RolFinanzas(){
//        return Auth::getUser()->id_role == self::Finanzas ? true:false;
//    }
//    public static function RolGeneral(){
//        return Auth::getUser()->id_role == self::General ? true:false;
//    }

//    public static function hasAccess($roles = []){
//        /** @var Role $rolUser */
//        $rolUser = Auth::getUser()->id_role;
//        try{
//            if(!in_array($rolUser,$roles)){
//                Session::flash('error_message', "No tienes permisos para realizar esta acción");
//                Redirect::to('/')->send();
//                throw new \Exception('No tienes permisos para realizar esta acción');
//            }
//        }catch(Exception $e){
//            echo $e->getMessage();
//        }
//
//    }

    public static function findBy($columnName,$value){
        return Role::where($columnName,'=',$value)->first();
    }

}