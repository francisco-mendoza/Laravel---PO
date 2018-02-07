<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use DB;
use Auth;
use OwenIt\Auditing\Auditable;

use Zizaco\Entrust\Traits\EntrustUserTrait;

/**
 * App\Models\User
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @mixin \Eloquent
 * @property int $id_user
 * @property string $username
 * @property string $password
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $remember_token
 * @property int $id_area
 * @property string $social_provider_id
 * @property string $social_provider
 * @property string $url_avatar
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereIdUser($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereFirstname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereLastname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereIdArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereSocialProviderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereSocialProvider($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereUrlAvatar($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @property string $rut
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereRut($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 */
class User extends Authenticatable
{
    use Notifiable;
    use Auditable;
    use EntrustUserTrait; // add this trait to your user model


    const USERNAME = 1 ;
    const MAIL = 2 ;
    const DESCRIPTION = 4 ;
    const AREAS_NAME = 5 ;

    protected $primaryKey = 'id_user';
    protected $fillable = [
        'username',
        'firstname',
        'lastname',
        'email',
        'password',
        'social_provider',
        'social_provider_id',
        'url_avatar',
        'id_user',
        'id_area'
    ];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function Users($start, $len, $search, $column, $dir){



        $users =  DB::table('users')->leftJoin('areas', 'users.id_area', '=', 'areas.id_area')
            ->leftJoin('role_user', 'role_user.user_id', '=','users.id_user')
            ->leftJoin('roles', 'role_user.role_id', '=','roles.id')
            ->select('users.id_user' , 'users.username', 'users.email', 'users.url_avatar', 'roles.display_name', 'areas.short_name');

        if($start!== null && $len !== null){
            $users->skip($start)->limit($len);
        }

        if($search !== null){
            $users->where('users.username','like', '%'.$search.'%')
                ->orWhere('users.email','like','%'.$search.'%')
                ->orWhere('roles.display_name','like','%'.$search.'%')
                ->orWhere('areas.short_name','like','%'.$search.'%');
        }

        if($column!== null && $dir !== null){
            switch($column){
                case self::USERNAME:
                    $users->orderBy('users.username', $dir);
                    break;
                case self::MAIL:
                    $users->orderBy('users.email', $dir);
                    break;
                case self::DESCRIPTION:
                    $users->orderBy('roles.display_name', $dir);
                    break;
                case self::AREAS_NAME:
                    $users->orderBy('areas.short_name', $dir);
                    break;
                default:
                    $users->orderBy('users.username', 'asc');
                    break;
            }
        }else{
            $users->orderBy('users.username', 'asc');
        }

        return $users->get();
    }

    public static function getCountUsers(){

        return User::all()->count();
    }

    public static function findUserJoined($id){

        return DB::table('users')->leftJoin('areas', 'users.id_area', '=', 'areas.id_area')
            ->leftJoin('role_user', 'role_user.user_id', '=','users.id_user')
            ->leftJoin('roles', 'role_user.role_id', '=','roles.id')
            ->where('users.id_user','=',$id)
            ->select('users.*','areas.long_name', 'roles.display_name', 'role_user.role_id as id_role')
            ->first();

    }

    public  static function findUser($id){

        return User::where('id_user',$id)->first();

    }

    public static function getUsers(){

        $users = User::select(DB::raw('CONCAT(firstname, " ", lastname) AS full_name'),'id_user')->get();

        return $users;
    }

    public static function findBy($columnName,$value){
        return User::where($columnName,'=',$value)->first();
    }

    public static function needFilteringByArea(){
        return Auth::user()->hasRole(config('constants.finanzasName')) ? false : true;
    }

    public static function needFilteringByUser(){
        return Auth::user()->hasRole(config('constants.generalName')) ? true : false;
    }

    
}