@inject('InvWarehouseTransfer', 'App\Http\Controllers\InvWarehouseTransferController')
@extends('adminlte::page')

@section('title', 'PBF | Koperasi Menjangan Enam')
<link rel="shortcut icon" href="{{ asset('resources/assets/logo_pbf.ico') }}" />
@section('js')
<script>
	$(document).ready(function(){
        $("#item_category_id").select2("val", "0");
        $("#item_unit_id").select2("val", "0");

        $("#item_category_id").change(function(){
			var item_category_id 	= $("#item_category_id").val();

            $.ajax({
                type: "POST",
                url : "{{route('warehouse-transfer-change-type')}}",
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

        $("#item_type_id").change(function(){
			var item_category_id 	= $("#item_category_id").val();
			var item_type_id 	    = $("#item_type_id").val();

            $.ajax({
                type: "POST",
                url : "{{route('warehouse-transfer-item')}}",
                dataType: "html",
                data: {
                    'item_category_id'	: item_category_id,
                    'item_type_id'	    : item_type_id,
                    '_token'            : '{{csrf_token()}}',
                },
                success: function(return_data){ 
                    $('#item_id').html(return_data);
                },
                error: function(data)
                {
                    console.log(data);

                }
            });
		});

        $("#item_id").change(function(){
            var warehouse_from_id 	= $("#warehouse_from_id").val();
            var item_category_id 	= $("#item_category_id").val();
            var item_type_id 	    = $("#item_type_id").val();
            var item_id 	        = $("#item_id").val();

            $.ajax({
                type: "POST",
                url : "{{route('warehouse-transfer-batch-number')}}",
                dataType: "html",
                data: {
                    'warehouse_from_id'	: warehouse_from_id,
                    'item_category_id'	: item_category_id,
                    'item_type_id'	    : item_type_id,
                    'item_id'	        : item_id,
                    '_token'            : '{{csrf_token()}}',
                },
                success: function(return_data){ 
                    return_data = JSON.parse(return_data);
                    return_data = JSON.parse(return_data);

                    $('#item_batch_number').html(return_data['data']);
                    $('#default_item_unit_id').val(return_data['itemunit']);
                },
                error: function(data)
                {
                    console.log(data);

                }
            });
        });

        $("#item_batch_number").change(function(){
            var item_stock_id 	= $("#item_batch_number").val();

            $.ajax({
                type: "POST",
                url : "{{route('warehouse-transfer-batch-number-detail')}}",
                dataType: "html",
                data: {
                    'item_stock_id'	    : item_stock_id,
                    '_token'            : '{{csrf_token()}}',
                },
                success: function(return_data){ 
                    // return_data = return_data.slice(1);
                    return_data = JSON.parse(return_data);
                    $('#stock_quantity').val(return_data['item_total']);
                },
                error: function(data)
                {
                    console.log(data);
                }
            });
        });
    
	});
    
    function elements_add(name, value){
        $.ajax({
            type: "POST",
            url : "{{route('elements-add-warehouse-transfer')}}",
            dataType: "html",
            data: {
                'name'      : name,
                'value'	    : value,
                '_token'    : '{{csrf_token()}}',
            },
            success: function(return_data){ 
                console.log(return_data);
            },
            error: function(data)
            {
                console.log(data);

            }
        });
    }
    
    function processAddArrayWarehouseTransferItem(){
        var item_category_id	                = document.getElementById("item_category_id").value;
        var item_type_id		                = document.getElementById("item_type_id").value;
        var item_id			                    = document.getElementById("item_id").value;
        var item_unit_id		                = document.getElementById("item_unit_id").value;
        var quantity			                = document.getElementById("quantity").value;
        var item_batch_number			        = document.getElementById("item_batch_number").value;
        var warehouse_transfer_item_remark		= document.getElementById("warehouse_transfer_item_remark").value;

        $.ajax({
            type: "POST",
            url : "{{route('warehouse_transfer-add-array')}}",
            data: {
                'item_category_id'	                : item_category_id,
                'item_type_id' 		                : item_type_id, 
                'item_id' 			                : item_id,
                'item_unit_id' 		                : item_unit_id,
                'quantity' 			                : quantity,
                'item_batch_number' 			    : item_batch_number,
                'warehouse_transfer_item_remark' 	: warehouse_transfer_item_remark,
                '_token'                            : '{{csrf_token()}}'
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
        <li class="breadcrumb-item"><a href="{{ url('warehouse-transfer') }}">Daftar Transfer Gudang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Hapus Transfer Gudang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Hapus Transfer Gudang
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif
    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Hapus
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('warehouse-transfer') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{route('process-void-warehouse-transfer')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="form-group form-md-line-input col-md-4">
                    <a class="text-dark">Tanggal<a class='red'> *</a></a>
                    <input class="form-control input-bb" type="text" name="warehouse_transfer_date" id="warehouse_transfer_date" value="{{$warehousetransfer['warehouse_transfer_date']}}" readonly/>
                    <input class="form-control input-bb" type="hidden" name="warehouse_transfer_id" id="warehouse_transfer_id" value="{{$warehousetransfer['warehouse_transfer_id']}}" readonly/>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <a class="text-dark">Gudang Asal<a class='red'> *</a></a>
                    <input class="form-control input-bb" type="text" name="warehouse_from_id" id="warehouse_from_id" value="{{$InvWarehouseTransfer->getInvWarehouseName($warehousetransfer['warehouse_transfer_from'])}}" readonly/>
                </div>
                <div class="col-md-6">
                    <a class="text-dark">Gudang Tujuan<a class='red'> *</a></a>
                    <input class="form-control input-bb" type="text" name="warehouse_to_id" id="warehouse_to_id" value="{{$InvWarehouseTransfer->getInvWarehouseName($warehousetransfer['warehouse_transfer_to'])}}" readonly/>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <a class="text-dark">Tipe Transfer Gudang</a>
                    <input class="form-control input-bb" type="text" name="warehouse_transfer_type_id" id="warehouse_transfer_type_id" value="{{$InvWarehouseTransfer->getInvWarehouseTransferTypeName($warehousetransfer['warehouse_transfer_type_id'])}}" readonly/>
                </div>
                <div class="col-md-6">
                    <a class="text-dark">Ekspedisi</a>
                    <input class="form-control input-bb" type="text" name="expedition_id" id="expedition_id" value="{{$InvWarehouseTransfer->getCoreExpeditionName($warehousetransfer['expedition_id'])}}" readonly/>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-12 ">
                    <a class="text-dark">Keterangan</a>
                    <div class="">
                        <textarea rows="3" type="text" class="form-control input-bb" name="warehouse_transfer_remark" id="warehouse_transfer_remark" onChange="elements_add(this.name, this.value);" readonly>{{$warehousetransfer['warehouse_transfer_remark']}}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <a name="Save" class="btn btn-primary" title="Save" onclick='processAddArrayWarehouseTransferItem()'>Hapus</a>
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
                                <th width='5%' style='text-align:center'>No.</th>
                                <th width='20%' style='text-align:center'>Barang</th>
                                <th width='20%' style='text-align:center'>Batch Number</th>
                                <th width='10%' style='text-align:center'>Quantity</th>
                                <th width='10%' style='text-align:center'>Satuan</th>
                                <th width='30%' style='text-align:center'>Keterangan Barang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(count($warehousetransferitem)==0){
                                    echo "<tr><th colspan='8' style='text-align  : center !important;'>Data Kosong</th></tr>";
                                } else {
                                    $no =1;
                                    foreach ($warehousetransferitem AS $key => $val){
                                        echo"
                                            <tr>
                                                <td style='text-align  : center'>".$no."</td>";
                                            if($val['item_id']==0){
                                                echo"
                                                <td style='text-align  : left !important;'>".$InvWarehouseTransfer->getItemNameItemId0($val['item_id'], $val['item_type_id'], $val['item_category_id'])."</td>";
                                            }else{
                                                echo"
                                                <td style='text-align  : left !important;'>".$InvWarehouseTransfer->getItemName($val['item_id'])."</td>";
                                            }
                                                echo"
                                                <td style='text-align  : left !important;'>".$InvWarehouseTransfer->getItemBatchNumberName($val['item_stock_id'])."</td>
                                                <td style='text-align  : right !important;'>".$val['quantity']."</td>
                                                <td style='text-align  : left !important;'>".$InvWarehouseTransfer->getItemUnitName($val['item_unit_id'])."</td>
                                                <td style='text-align  : left !important;'>".$val['warehouse_transfer_item_remark']."</td>
                                                ";
                                                echo"
                                            </tr>
                                        ";
                                        $no++;
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="submit" name="Save" class="btn btn-danger" title="Save">Hapus</button>
            </div>
        </div>
    </div>
</form>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop