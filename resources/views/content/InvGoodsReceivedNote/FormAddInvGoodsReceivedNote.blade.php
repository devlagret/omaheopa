@inject('InvGoodsReceivedNote', 'App\Http\Controllers\InvGoodsReceivedNoteController')

@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")
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

	function quantityReceivedChange(k, value){
		if (value == ""){
			alert("Value cannot be empty");
		} else if (isNaN(value)){
			alert("Value must be a number");
		} else if(parseFloat(value)<0){
			alert("Value must be more than 0");
		} else {
			// document.getElementById("quantity_received_"+k).value = value;
		}

		var quantity_received_total;

		quantity_received_total = 0;

		var total_no 				= document.getElementById("total_no").value;

		for (var key = 1; key <= total_no; key++) {

			var quantity_received = parseFloat(document.getElementById("quantity_received_"+key).value);

			if (quantity_received == ""){
				quantity_received = 0;
			} else if (isNaN(quantity_received)){
				quantity_received = 0;
			}

			quantity_received_total = parseFloat(quantity_received_total) + parseFloat(quantity_received);
		}

		document.getElementById("quantity_received_total").value = quantity_received_total;
	}
    
	$(document).ready(function(){
        var quantity_received_total = 0;
        var total_no 				= document.getElementById("total_no").value;

        for (var key = 1; key <= total_no; key++) {

            var quantity_received = parseFloat(document.getElementById("quantity_received_"+key).value);

            if (quantity_received == ""){
                quantity_received = 0;
            } else if (isNaN(quantity_received)){
                quantity_received = 0;
            }

            quantity_received_total = parseFloat(quantity_received_total) + parseFloat(quantity_received);
        }
        
		document.getElementById("quantity_received_total").value = quantity_received_total;
	});

    $(document).ready(function(){
        var purchase_invoice_item_id = {!! json_encode($null_add_purchaseInvoiceitem) !!};
        
        if(purchase_invoice_item_id == null){
            $("#purchase_invoice_item_id").select2("val", "0");
        }
    });

    $(document).ready(function(){
        var item_unit_id = {!! json_encode($null_add_unit_purchaseInvoiceitem) !!};
        
        if(item_unit_id == null){
            $("#item_unit_id").select2("val", "0");
        }
    });

    function clearResult(){
        document.getElementById("result").value = ''
    }

</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('goods-received-note') }}">Daftar Penerimaan Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Penerimaan Barang</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Penerimaan Barang
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
@php
    $purchase_invoice_id = Request::segment(3);
@endphp
    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Tambah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('goods-received-note') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{route('process-add-goods-received-note')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">No PO</a>
                        <input class="form-control input-bb" type="text" name="purchase_invoice_no" id="purchase_invoice_no" value="{{$purchaseInvoice['purchase_invoice_no']}}" readonly/>
                        <input class="form-control input-bb" type="hidden" name="purchase_invoice_id" id="purchase_invoice_id" value="{{$purchaseInvoice['purchase_invoice_id']}}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal PO</a>
                        <input class="form-control input-bb" type="text" name="purchase_invoice_date" id="purchase_invoice_date" value="{{$purchaseInvoice['purchase_invoice_date']}}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Pemasok</a>
                        <input class="form-control input-bb" type="text" name="supplier_id_view" id="supplier_id_view" value="{{$InvGoodsReceivedNote->getCoreSupplierName($purchaseInvoice['supplier_id'])}}" readonly/>
                        <input class="form-control input-bb" type="hidden" name="supplier_id" id="supplier_id" value="{{$purchaseInvoice['supplier_id']}}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Gudang</a>
                        <input class="form-control input-bb" type="text" name="warehouse_id_view" id="warehou ?? ''se_id_view" value="{{$InvGoodsReceivedNote->getInvWarehouseName($purchaseInvoice['warehouse_id'])}}" readonly/>
                        <input class="form-control input-bb" type="hidden" name="warehouse_id" id="warehouse_id" value="{{$purchaseInvoice['warehouse_id']}}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <section class="control-label">Tanggal Penerimaan
                        <span class="required text-danger">
                            *
                        </span>
                    </section>
                    <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="goods_received_note_date" id="goods_received_note_date" onChange="function_elements_add(this.name, this.value);" value="" style="width: 15rem;"/>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Merchant
                            <span class="required text-danger">
                            </span></a>
                        <input class="form-control input-bb" type="text" name="merchant_id_view" id="merchant_id_view" value="{{ $InvGoodsReceivedNote->getMerchantName($merchant_id) ?? '' }}" readonly />
                    </div>
                </div>
                <div class="col-md-6">
                    <b>File Gambar Kwitansi</b><br/>
                    <input type="file" name="receipt_image" id="receipt_image" value="" accept="image/*"/>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-12 ">
                    <a class="text-dark">Keterangan</a>
                    <div class="">
                        <textarea rows="3" type="text" class="form-control input-bb" name="goods_received_note_remark" onChange="function_elements_add(this.name, this.value);" id="goods_received_note_remark" ></textarea>
                    </div>
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
            <div class="float-right">
                {{-- <a href='#addbatch' data-toggle='modal' name="Find" class="btn btn-success btn-sm" title="Add Data">Tambah</a> --}}
            </div>
        </div>
    
        <div class="card-body table-responsive">
            <div class="form-body form">
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover" >
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center'>No.</th>
                                <th style='text-align:center'>Merchant</th>
                                <th style='text-align:center'>Kategori Barang</th>
                                <th style='text-align:center'>Nama Barang</th>
                                <th style='text-align:center'>Satuan</th>
                                <th style='text-align:center'>Qty Order</th>
                                <th style='text-align:center'>Qty Diterima</th>
                            </tr>
                        </thead>
                        <tbody >
                            <?php
                                if(count($merge_data)<1){
                                    echo "<tr><th colspan='7' style='text-align  : center !important;'>Data Kosong</th></tr>";
                                } else {
                                    $no =1;
                                    // $noo =0;
                                    $total_price = 0;
                                    $outstanding_total = 0;
                                    $purchase_invoice_item_id = -1;
                                    foreach ($merge_data AS $key => $val){
                                        if( $purchase_invoice_item_id !=  $val['purchase_invoice_item_id']){
                                        echo"
                                            <tr>
                                                <td style='text-align  : center'>".$no.".</td>
                                                <td style='text-align  : left !important;'>".$InvGoodsReceivedNote->getMerchantName($val['merchant_id'])."</td>
                                                <td style='text-align  : left !important;'>".$InvGoodsReceivedNote->getItemCategoryName($val['item_category_id'])."</td>
                                                <td style='text-align  : left !important;'>".$InvGoodsReceivedNote->getItemName($val['item_id'])."</td>
                                                <td style='text-align  : left !important;'>".$InvGoodsReceivedNote->getInvItemUnitName($val['item_unit_id'])."</td>
                                                <td style='text-align  : right !important;'>".$val['quantity']."</td>
                                                <td style='text-align  : right !important;'>
                                                    <input class='form-control' style='text-align:right;' type='number' name='quantity_received_".$no."' id='quantity_received_".$no."' value='".$val['quantity']."' onchange='quantityReceivedChange(".$no.",this.value);' autocomplete='off'/>


                                                    <input class='form-control' style='text-align:right;'type='hidden' name='merchant_id_".$no."' id='merchant_id_".$no."' value='".$val['merchant_id']."'/>

                                                    <input class='form-control' style='text-align:right;'type='hidden' name='item_category_id_".$no."' id='item_category_id_".$no."' value='".$val['item_category_id']."'/>

                                                    <input class='form-control' style='text-align:right;'type='hidden' name='item_id_".$no."' id='item_id_".$no."' value='".$val['item_id']."'/>

                                                    <input class='form-control' style='text-align:right;'type='hidden' name='item_unit_id_".$no."' id='item_unit_id_".$no."' value='".$val['item_unit_id']."'/>

                                                    <input class='form-control' style='text-align:right;'type='hidden' name='item_unit_cost_".$no."' id='item_unit_cost_".$no."' value='".$val['item_unit_cost']."'/>

                                                    <input class='form-control' style='text-align:right;'type='hidden' name='purchase_invoice_id_".$no."' id='purchase_invoice_id_".$no."' value='".$val['purchase_invoice_id']."'/>

                                                    <input class='form-control' style='text-align:right;'type='hidden' name='purchase_invoice_item_id_".$no."' id='purchase_invoice_item_id_".$no."' value='".$val['purchase_invoice_item_id']."'/>
                                                </td>";
                                        $no++;
                                        // $total_price+=$val['total_price'];
                                        // $outstanding_total+=$val['quantity_outstanding'];
                                        }else{
                                            echo"
                                            <tr>
                                                <td style='text-align  : center'>".$no.".</td>
                                                <td style='text-align  : left !important;'></td>
                                                <td style='text-align  : left !important;'></td>
                                                <td style='text-align  : left !important;'></td>
                                                <td style='text-align  : right !important;'></td>
                                                <td style='text-align  : right !important;'></td>
                                                <td style='text-align  : right !important;'>
                                                    <input class='form-control' style='text-align:right;' type='number' name='quantity_received_".$no."' id='quantity_received_".$no."' value='".$val['quantity_outstanding']."' onchange='quantityReceivedChange(".$no.",this.value);' autocomplete='off'/>

                                                    <input class='form-control' style='text-align:right;'type='hidden' name='item_category_id_".$no."' id='item_category_id_".$no."' value='".$val['item_category_id']."'/>

                                                    <input class='form-control' style='text-align:right;'type='hidden' name='item_id_".$no."' id='item_id_".$no."' value='".$val['item_type_id']."'/>

                                                    <input class='form-control' style='text-align:right;'type='hidden' name='item_unit_id_".$no."' id='item_unit_id_".$no."' value='".$val['item_unit_id']."'/>

                                                    <input class='form-control' style='text-align:right;'type='hidden' name='item_unit_cost_".$no."' id='item_unit_cost_".$no."' value='".$val['item_unit_cost']."'/>

                                                    <input class='form-control' style='text-align:right;'type='hidden' name='purchase_invoice_id_".$no."' id='purchase_invoice_id_".$no."' value='".$val['purchase_invoice_id']."'/>

                                                    <input class='form-control' style='text-align:right;'type='hidden' name='purchase_invoice_item_id_".$no."' id='purchase_invoice_item_id_".$no."' value='".$val['purchase_invoice_item_id']."'/>
                                                </td>";
                                        $no++;
                                            }
                                            $purchase_invoice_item_id = $val['purchase_invoice_item_id'];

                                    }
                                    
                                        $total_no = $no - 1;
                                        echo"
                                        <tbody></tbody>
                                        <th style='text-align  : center' colspan='5'>Total</th>
                                        <th style='text-align  : right'>
                                            <input class='form-control' style='text-align:right;'type='text' name='quantity_received_total' id='quantity_received_total' value='' readonly/>
                                            <input class='form-control' style='text-align:right;'type='hidden' name='total_no' id='total_no' value='".$total_no."' readonly/>
                                        </th>
                                        <th style='text-align  : center' colspan='2'></th>
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
                <a href="{{route('delete-new-purchase-order-item', $purchase_invoice_id)}}"type="reset" name="Reset" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Batal</a>
                <button type="submit" name="Save" class="btn btn-primary btn-sm" title="Save"><i class="fa fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>
</form>
<br>
<br>


<form action="{{route('add-new-purchase-order-item', $purchase_invoice_id)}}" method="POST">
    @csrf
    
    <div class="modal fade bs-modal-md" id="addbatch" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header"  style='text-align:left !important'>
                    <h4>Form Tambah Daftar Barang</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <a class="text-dark">Tipe Barang<a class='red'> </a></a>
                            {!! Form::select('purchase_invoice_item_id',  $add_type_purchaseInvoiceitem, '', ['class' => 'selection-search-clear select-form', 'id' => 'purchase_invoice_item_id']) !!}
                            <input type="hidden" name="purchase_invoice_id" id="purchase_invoice_id" value="{{ $purchase_invoice_id}}">
                        </div>
                        {{-- <div class="col-md-12 mb-3">
                            <a class="text-dark">Satuan<a class='red'> </a></a>
                            {!! Form::select('item_unit_id',  $add_unit_purchaseorderitem, '', ['class' => 'selection-search-clear select-form', 'id' => 'item_unit_id']) !!}
                        </div> --}}
                        <div class="col-md-12">
                            <a class="text-dark">Quantity<a class='red'> </a></a>
                            <input type="number" class="form-control input-bb" name="quantity" id="quantity" value="" autocomplete="off">
                        </div>
                    </div>
                    <br>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-danger btn-sm" >Batal</button> --}}
                        <button type="submit" class="btn btn-primary btn-sm" style="margin-right: -3%">Tambah</button>
                    </div>
                </div>
            </div>
        </div>
</form>




<br>
<br>



@stop

@section('footer')
    
@stop

@section('css')
    
@stop