<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use DB;

/**
 * App\Models\PermissionRole
 *
 * @mixin \Eloquent
 * @property int $permission_id
 * @property int $role_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PermissionRole wherePermissionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PermissionRole whereRoleId($value)
 */
class PermissionRole extends Model
{

    use Auditable;

    protected $table = 'permission_role';

    public static function deletePermissionsByRol($role,$options){

        $result = DB::table('permission_role')
            ->where('role_id', $role)
            ->whereNotIn('permission_id', $options);
        $result->delete();
        return true;
    }

    public static function deletePermissionsRole($permission){

        $result = DB::table('permission_role')
            ->where('permission_id', $permission);
        $result ->delete();
        return true;
    }
}
