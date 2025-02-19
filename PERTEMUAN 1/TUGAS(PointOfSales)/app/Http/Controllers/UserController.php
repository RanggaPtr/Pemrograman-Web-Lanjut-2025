<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // public function user($id, $name){
    //     return view('users');
    // }

    public function user($id, $name) {
        return view('users', [
            'id' => $id,
            'name' => $name,
        ]);
    }
}
