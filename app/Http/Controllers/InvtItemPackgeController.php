<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use Illuminate\Http\Request;

class InvtItemPackgeController extends Controller
{
    public function processAddItem(Request $request) {
       $item = InvtItem::find($request->item_id);
        error_log(strval($item));
        return $request->item_id;
    }
}
