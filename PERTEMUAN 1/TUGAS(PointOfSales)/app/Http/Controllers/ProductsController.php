<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductsController extends Controller
{

    public function beautyHealth(){
        return view('beautyHealth');
    }

    public function foodBeverage(){
        return view('foodBeverage');
    }
    public function babyKid(){
        return view('babyKid');
    }
    public function homeCare(){
        return view('homeCare');
    }


    
}
