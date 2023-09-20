@inject('PurchaseOrder', 'App\Http\Controllers\PurchaseOrderController')
@extends('adminlte::page')

@section('title', 'PBF | Koperasi Menjangan Enam')
<link rel="shortcut icon" href="{{ asset('resources/assets/logo_pbf.ico') }}" />
@section('js')
<script>
	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1.')
			.split('').reverse().join('');
		return rupiah + ',' + cents.slice(0, 2);
	}

	$(document).ready(function(){
        $("#item_category_id").select2("val", "0");
        $("#item_unit_id").select2("val", "0");
        $("#item_category_id").change(function(){
			var item_category_id 	= $("#item_category_id").val();
                $.ajax({
                    type: "POST",
                    url : "{{route('warehouse-transfer-type')}}",
                    dataType: "html",
                    data: {
                        'item_category_id'			    : item_category_id,
                        '_token'                        : '{{csrf_token()}}',
                    },
                    success: function(return_data){ 
					    $('#item_type_id').html(return_data);
                        // console.log(return_data);
                    },
                    error: function(data)
                    {
                        console.log(data);

                    }
                });

		});    
        $("#price").change(function(){
			var price 	    = $("#price").val();
			var quantity 	= $("#quantity").val();
            var total_price = price*quantity;
            $("#total_price").val(total_price);
            $("#total_price_view").val(toRp(total_price));

		});   
        $("#quantity").change(function(){
			var price 	    = $("#price").val();
			var quantity 	= $("#quantity").val();
            var total_price = price*quantity;
            $("#total_price").val(total_price);
            $("#total_price_view").val(toRp(total_price));

		}); 
	});
    
    
    function processAddArrayPurchaseOrderItem(){
        var item_category_id	= document.getElementById("item_category_id").value;
        var item_type_id		= document.getElementById("item_type_id").value;
        var item_unit_id	    = document.getElementById("item_unit_id").value;
        var quantity			= document.getElementById("quantity").value;
        var price			    = document.getElementById("price").value;
        var total_price			= document.getElementById("total_price").value;

        $.ajax({
            type: "POST",
            url : "{{route('purchase-order-add-array')}}",
            data: {
                'item_category_id'	: item_category_id,
                'item_type_id' 		: item_type_id, 
                'item_unit_id' 		: item_unit_id,
                'quantity' 			: quantity,
                'price' 			: price,
                'total_price' 		: total_price,
                '_token'            : '{{csrf_token()}}'
            },
            success: function(msg){
                location.reload();
            }
        });
    }

</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('purchase-order') }}">Daftar Purchase Order</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Purchase Order</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Edit Purchase Order
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif
@if(count($errors) > 0)
<div class="alert alert-danger" role="alert">
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</div>
@endif
    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Edit
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('purchase-order') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{route('process-add-purchase-order')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <section class="control-label">Tanggal PO
                        <span class="required text-danger">
                            *
                        </span>
                    </section>
                    <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="purchase_order_date" id="purchase_order_date" onChange="function_elements_add(this.name, this.value);" value="{{$purchaseorder['purchase_order_date']}}" style="width: 15rem;"/>
                </div>
                <div class="col-md-6">
                    <section class="control-label">Tanggal Pengiriman
                        <span class="required text-danger">
                            *
                        </span>
                    </section>
                    <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="purchase_order_shipment_date" id="purchase_order_shipment_date" onChange="function_elements_add(this.name, this.value);" value="{{$purchaseorder['purchase_order_shipment_date']}}" style="width: 15rem;"/>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <a class="text-dark">Gudang<a class='red'> *</a></a>
                    {!! Form::select('warehouse_id',  $warehouse, $purchaseorder['warehouse_id'], ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_id']) !!}
                </div>
                <div class="col-md-6">
                    <a class="text-dark">Pemasok<a class='red'> *</a></a>
                    {!! Form::select('supplier_id',  $supplier, $purchaseorder['supplier_id'], ['class' => 'selection-search-clear select-form', 'id' => 'supplier_id']) !!}
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <a class="text-dark">Tipe PO<a class='red'> *</a></a>
                    {!! Form::select('purchase_order_type_id',  $purchaseordertype, $purchaseorder['purchase_order_type_id'], ['class' => 'selection-search-clear select-form', 'id' => 'purchase_order_type_id', 'onchange' => 'elements_add(this.name , this.value);']) !!}
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-12 ">
                    <a class="text-dark">Keterangan</a>
                    <div class="">
                        <textarea rows="3" type="text" class="form-control input-bb" name="purchase_order_remark" onChange="function_elements_add(this.name, this.value);" id="purchase_order_remark" >{{$purchaseorder['purchase_order_remark']}}</textarea>
                    </div>
                </div>
            </div>
            
            <br/>
            <h4 class="text-dark">Daftar Barang</h4>
            <hr/>
            <div class="row form-group">
                <div class="col-md-6">
                    <a class="text-dark">Kategori Barang<a class='red'> *</a></a>
                    {!! Form::select('item_category_id',  $itemcategory, 0, ['class' => 'selection-search-clear select-form', 'id' => 'item_category_id']) !!}
                </div>
                <div class="col-md-6">
                    <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                    {!! Form::select('item_type_id',  $itemtype, 0, ['class' => 'selection-search-clear select-form', 'id' => 'item_type_id']) !!}
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <a class="text-dark">Satuan<a class='red'> *</a></a>
                    {!! Form::select('item_unit_id',  $itemunit, 0, ['class' => 'selection-search-clear select-form', 'id' => 'item_unit_id']) !!}
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Quantity</a>
                        <input class="form-control input-bb" type="text" name="quantity" id="quantity" value=""/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Harga Satuan</a>
                        <input class="form-control input-bb" type="text" name="price" id="price" value=""/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Total Harga</a>
                        <input class="form-control input-bb" type="text" name="total_price_view" id="total_price_view" value="" readonly/>
                        <input class="form-control input-bb" type="hidden" name="total_price" id="total_price" value=""/>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <a type="submit" name="Save" class="btn btn-primary" title="Save" onclick="processAddArrayPurchaseOrderItem()">Tambah</a>
            </div>
        </div>
    </div>
    </div>

    <br/>
    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Daftar
            </h5>
        </div>
    
        <div class="card-body">
            <div class="form-body form">
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center'>No.</th>
                                <th style='text-align:center'>Kategori Barang</th>
                                <th style='text-align:center'>Tipe Barang</th>
                                <th style='text-align:center'>Satuan</th>
                                <th style='text-align:center'>Harga Satuan</th>
                                <th style='text-align:center'>Quantity</th>
                                <th style='text-align:center'>Total Harga</th>
                                <th style='text-align:center'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // print_r($purchaseorderitem);
                                if(count($purchaseorderitem)<1){
                                    echo "<tr><th colspan='7' style='text-align  : center !important;'>Data Kosong</th></tr>";
                                } else {
                                    $no =1;
                                    $total_price = 0;
                                    $total_item = 0;
                                    foreach ($purchaseorderitem AS $key => $val){
                                        echo"
                                            <tr>
                                                <td style='text-align  : center'>".$no."</td>
                                                <td style='text-align  : left !important;'>".$PurchaseOrder->getItemCategoryName($val['item_category_id'])."</td>
                                                <td style='text-align  : left !important;'>".$PurchaseOrder->getItemTypeName($val['item_type_id'])."</td>
                                                <td style='text-align  : left !important;'>".$PurchaseOrder->getItemUnitName($val['item_unit_id'])."</td>
                                                <td style='text-align  : right !important;'>".number_format($val['price'],2,',','.')."</td>
                                                <td style='text-align  : right !important;'>".$val['quantity']."</td>
                                                <td style='text-align  : right !important;'>".number_format($val['total_price'],2,',','.')."</td>";
                                                ?>
                                                
                                                <td style='text-align  : center'>
                                                    <a href="{{route('purchase-order-delete-array', ['record_id' => $key])}}" name='Reset' class='btn btn-danger btn-sm' onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'></i> Hapus</a>
                                                <?php
                                                    if($val['purchase_order_item_id']){
                                                        echo"
                                                        <input class='form-control input-bb' type='text' name='total_price_all' id='total_price_all' value='".$val['purchase_order_item_id']."'/>";
                                                    }"
                                                </td>";
                                                echo"
                                            </tr>
                                        ";
                                        $no++;
                                        $total_price+=$val['total_price'];
                                        $total_item+=$val['quantity'];
                                    }
                                        echo"
                                        <th style='text-align  : center' colspan='5'>Total</th>
                                        <th style='text-align  : right'>".$total_item."</th>
                                        <th style='text-align  : right'>".number_format($total_price,2,',','.')."</th>
                                        <th>
                                            <input class='form-control input-bb' type='hidden' name='total_price_all' id='total_price_all' value='".$total_price."'/>
                                            <input class='form-control input-bb' type='hidden' name='total_item_all' id='total_item_all' value='".$total_item."'/>
                                        </th>
                                        ";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
                <button type="submit" name="Save" class="btn btn-primary" title="Save"><i class="fa fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>
</form>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop