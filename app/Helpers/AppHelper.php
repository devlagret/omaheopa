<?php
namespace App\Helpers;
use App\Models\PreferenceTransactionModule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AppHelper
{
    protected static $data;
    /**
     * Get random quote from storage
     *
     * @return string format: {quote}-{by}
     */
    public static function quote()
    {
        $quotes = collect(json_decode(Storage::get('public/quotes.min.json')))->random();
        return $quotes->quote.' - '.$quotes->by;
    }
    /**
     * Get sales order status
     *
     * @return Collection
     */
    public static function status():Collection {
        return collect(['id'=>[1=>'Sudah Dibayar',2=>'Sudah Check-In',3=>'Sudah Check-Out',],'type'=>[1=>'success',2=>'primary',3=>'info',]]);
    }
    /**
     * Get menu type
     *
     * @return Collection
     */
    public static function menuType():Collection {
        return collect([1 => 'Breakfast', 2 => 'Lunch', 3 => 'Dinner']);
    }
    /**
     * Get order (booking) type
     *
     * @return Collection
     */
    public static function orderType():Collection {
        return collect([0 => 'Dengan Uang Muka', 3 => 'Tanpa Uang Muka', 4 => 'Full Book']);
    }
    /**
     * Get Transaction Module
     *
     * @param [string] $transaction_module_code
     * @return AppHelper
     */
    public static function getTransactionModule(string $transaction_module_code)
    {
        self::$data = PreferenceTransactionModule::where('transaction_module_code',$transaction_module_code)->first();
        return new self;
    }
    public function name(){
        return self::$data->transaction_module_name;
    }
    public function id(){
        return self::$data->transaction_module_id;
    }

}