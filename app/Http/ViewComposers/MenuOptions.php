<?php
/**
 * Created by PhpStorm.
 * User: francisco mendoza
 * Date: 13-01-17
 * Time: 11:24
 */

namespace App\Http\ViewComposers;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\MenuOption;

class MenuOptions
{
    public function compose(View $view)
    {

        $menu_opciones = MenuOption::findMenuOptions();

        $view->with('menu_opciones',$menu_opciones);
    }
}