<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Storage;

class AppHelper
{
    public static function quote()
    {
        $quotes = collect(json_decode(Storage::get('public/quotes.min.json')))->random();
        return $quotes->quote.' - '.$quotes->by;
    }
}