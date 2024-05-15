<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;

    //Apa2 boleh juga define di controller INI utk custom so dekat sini aku custom dia jadi base controller
    // aku guna use extend illuminate routing tu

    // extends \Illuminate\Routing\Controller
    // boleh gunakan ini kalau nak pakai middleware dan gate/policy di controller

    // kalau nak guna salah satu boleh implement extend HasMiddleware di controller
}