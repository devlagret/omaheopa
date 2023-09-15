<?php
namespace App\Helpers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AppHelper
{
    public static function quote()
    {
        $quotes = collect(json_decode(Storage::get('public/quotes.min.json')))->random();
        return $quotes->quote.' - '.$quotes->by;
    }
    public static function status():Collection {
        return collect(['id'=>[1=>'Sudah Dibayar',2=>'Sudah Check-In',3=>'Sudah Check-Out',],'type'=>[1=>'success',2=>'primary',3=>'info',]]);
    }
    public static function menuType():Collection {
        return collect([1 => 'Breakfast', 2 => 'Lunch', 3 => 'Dinner']);
    }
}