<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redis;

class MainController extends Controller
{
    public function index()
    {
        //$data   =   Redis::set('newsname:Kay',1111);
        $ret    =   Redis::get('newsname:Kay');
        var_dump($ret);
        echo "welcome to main!";
    }
}
