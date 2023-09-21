<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class SystemLogsController extends Controller
{
    public function index()
    {
        $d = file_get_contents(storage_path('logs\laravel.log'));
        $js = "<script>
        function check(uri){
          if(confirm(`Yakin Ingin Menghapus Log ?`)){
            window.location.href = uri;
          }
        }
        </script>";
        $del = "<div><button type='button' onclick='check(\"".route('log.destroy')."\")'>Clear Log</button><br/>";
        $data="<div><pre>".$d."</pre></div></div>";
        return $js.=$del .= $data;
    }
    public function destroy()
    {
       return file_put_contents(storage_path('logs/laravel.log'),'')."<br/><a type='button' href='".url('log/system')."'>Back</a>";
    }
}
