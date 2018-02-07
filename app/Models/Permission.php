<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustPermission;
use OwenIt\Auditing\Auditable;
use DB;

/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereDisplayName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 */
class Permission extends EntrustPermission
{
    use Auditable;

    protected $fillable = [
        'id', 'name','display_name', 'description'
    ];

    public static function Permissions($start, $len, $search, $column, $dir){

        $permissions = DB::table('permissions')->select('id','name','display_name','description')->distinct();

        if($start!== null && $len !== null){
            $permissions->skip($start)->limit($len);
        }

        if($search !== null){
            $permissions->where('display_name','like', '%'.$search.'%');
        }

        if($column!== null && $dir !== null){
            $permissions->orderBy('display_name', $dir);
        }else{
            $permissions->orderBy('id', 'asc');
        }

        return $permissions->get();
    }

    public static function getPermissions(){
        return DB::table('permissions')
            ->select(
                'permissions.id as id_permiso',
                'permissions.name',
                'permissions.display_name',
                'permissions.description')
            ->get();
    }

    public static function getPermissionsByRol($id_rol){
        return DB::table('permissions')
            ->join('permission_role','permissions.id','=','permission_role.permission_id')
            ->join('roles','permission_role.role_id','=','roles.id')
            ->select('permissions.id as id_permiso','permissions.name','permissions.display_name','roles.id as role_id','permissions.description as description')
            ->where('roles.id','=',$id_rol)
            ->get();
    }





    public static function getCountPermissions(){

        return Permission::all()->count();
    }

}
