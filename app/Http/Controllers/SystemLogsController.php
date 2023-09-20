<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class SystemLogsController extends Controller
{
    public function index()
    {
        $d = file_get_contents(storage_path('logs\laravel.log'));
        $del = "<div><a type='button' href='".route('log.destroy')."'>Clear Log</a><br/>";
        $data="<div><pre>".$d."</pre></div></div>";
        return $del .= $data;
    }
    public function destroy()
    {
       return file_put_contents(storage_path('logs/laravel.log'),'')."<br/><a type='button' href='".url('log/system')."'>Back</a>";
    }
}
