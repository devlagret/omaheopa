@inject('InvWarehouseTransfer', 'App\Http\Controllers\InvWarehouseTransferController')
@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
	$(document).ready(function(){
        $("#item_category_id").select2("val", "0");
        $("#item_unit_id").select2("val", "0");
        
        var elements = {!! json_encode($warehousetransferelements) !!};

        if(!elements || elements==''){
            elements = [];
        }

        if(!elements['warehouse_to_id']){
            $("#warehouse_to_id").select2("val", "0");
        }

        if(!elements['warehouse_from_id']){
            $("#warehouse_from_id").select2("val", "0");
        }

        if(!elements['warehouse_transfer_type_id']){
            $("#warehouse_transfer_type_id").select2("val", "0");
        }

        if(!elements['expedition_id']){
            $("#expedition_id").select2("val", "0");
        }

        $("#item_category_id").change(function(){
			var item_category_id 	= $("#item_category_id").val();

            $.ajax({
                type: "POST",
                url : "{{route('warehouse-transfer-item')}}",
                dataType: "html",
                data: {
                    'item_category_id'			    : item_category_id,
                    '_token'                        : '{{csrf_token()}}',
                },
                 success: function(return_data){ 
                    // console.log(item_category_id);
                    $('#item_id').html(return_data);
                    // console.log(return_data);
                },
                error: function(data)
                {
                    console.log(data);

                }
            });
		});
    });


    $("#item_id").change(function(){
			var item_id 	= $("#item_id").val();
                $.ajax({
                    type: "POST",
                    url : "{{route('select-data-unit')}}",
                    dataType: "html",
                    data: {
                        'item_id'	: item_id,
                        '_token'        : '{{csrf_token()}}',
                    },
                    success: function(return_data){ 
					$('#item_unit_id').html(return_data);
                        console.log(return_data);       
                    },
                    error: function(data)
                    {
                        console.log(data);

                    }
                });
		});

     


        $("#item_unit_id").change(function(){
            var item_category_id 	= $("#item_category_id").val();
            var item_unit_id 	= $("#item_unit_id").val();
			var item_id 	= $("#item_id").val();
                $.ajax({
                    type: "POST",
                    url : "{{route('select-data-item')}}",
                    dataType: "html",
                    data: {
                        'item_category_id'	: item_category_id,
                        'item_unit_id'	    : item_unit_id,
                        'item_id'	        : item_id,
                        '_token'            : '{{csrf_token()}}',
                    },
                    success: function(return_data){ 
					$('#stock_quantity').val(return_data);
                        console.log(return_data);     
                       
                    },
                    error: function(data)
                    {
                        console.log(data);

                    }
                });
		});



        $("#item_unit_id").change(function(){
            var item_category_id 	= $("#item_category_id").val();
            var item_unit_id 	= $("#item_unit_id").val();
			var item_id 	= $("#item_id").val();
                $.ajax({
                    type: "POST",
                    url : "{{route('select-data-stock')}}",
                    dataType: "html",
                    data: {
                        'item_category_id'	: item_category_id,
                        'item_unit_id'	    : item_unit_id,
                        'item_id'	        : item_id,
                        '_token'            : '{{csrf_token()}}',
                    },
                    success: function(return_data){ 
					$('#item_stock_id').val(return_data);
                        console.log(return_data);  
                   
                    },
                    error: function(data)
                    {
                        console.log(data);

                    }
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
        var item_id			                    = document.getElementById("item_id").value;
        var item_unit_id		                = document.getElementById("item_unit_id").value;
        var quantity			                = document.getElementById("quantity").value;
        var warehouse_transfer_item_remark		= document.getElementById("warehouse_transfer_item_remark").value;
        var stock_quantity			            = document.getElementById("stock_quantity").value;
        var item_stock_id			            = document.getElementById("item_stock_id").value;

        // if(item_unit_id == default_item_unit_id){
        //     if(stock_quantity<quantity){
        //         alert("Jumlah barang yang dipindah melebihi jumlah barang persediaan!");
        //         return 0;
        //     }
        // }

        $.ajax({
            type: "POST",
            url : "{{route('warehouse_transfer-add-array')}}",
            data: {
                'item_category_id'	                : item_category_id,
                'item_id' 			                : item_id,
                'item_unit_id' 		                : item_unit_id,
                'quantity' 			                : quantity,
                'item_stock_id'                     : item_stock_id,
                'warehouse_transfer_item_remark' 	: warehouse_transfer_item_remark,
                '_token'                            : '{{csrf_token()}}'
            },
            success: function(msg){
                location.reload();
            }
        });

    }



    function addWarehouseType(){
        var warehouse_transfer_type_name 	    = $("#warehouse_transfer_type_name").val();
        var warehouse_transfer_type_remark 	    = $("#warehouse_transfer_type_remark").val();
        $.ajax({
            type: "POST",
            url : "{{route('add-transfer-type-warehouse-transfer')}}",
            dataType: "html",
            data: {
                'warehouse_transfer_type_name'	    : warehouse_transfer_type_name,
                'warehouse_transfer_type_remark'	: warehouse_transfer_type_remark,
                '_token'                            : '{{csrf_token()}}',
            },
            success: function(return_data){ 
                $('#warehouse_transfer_type_id').html(return_data);
                $('#cancel_btn_warehouse_transfer').click();
            },
            error: function(data)
            {
                console.log(data);
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
        <li class="breadcrumb-item active" aria-current="page">Tambah Transfer Gudang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Transfer Gudang
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
            Form Tambah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('warehouse-transfer') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{route('process-add-warehouse-transfer')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="form-group form-md-line-input col-md-4">
                    <section class="control-label">Tanggal
                        <span class="required text-danger">
                            *
                        </span>
                    </section>
                    <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="warehouse_transfer_date" id="warehouse_transfer_date" value="{{$warehousetransferelements == null ?  date('Y-m-d')   :  $warehousetransferelements['warehouse_transfer_date']}}"  onChange="elements_add(this.name, this.value);"/>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-5">
                    <a class="text-dark">Gudang Asal<a class='red'> *</a></a>
                    {!! Form::select('warehouse_from_id',  $invwarehouse, $warehousetransferelements == null ? '' : $warehousetransferelements['warehouse_from_id'], ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_from_id', 'onchange' => 'elements_add(this.name , this.value);']) !!}
                </div>
                <div class="col-md-1" style="margin-top: 0.3%">
                    <a class="text-dark"></a>
                    {{-- <a href='#addwarehousefrom' data-toggle='modal' name="Find" class="btn btn-success add-btn btn-sm" title="Add Data">Tambah</a> --}}
                </div>
                <div class="col-md-5">
                    <a class="text-dark">Gudang Tujuan<a class='red'> *</a></a>
                    {!! Form::select('warehouse_to_id',  $invwarehouse, $warehousetransferelements == null ? '' : $warehousetransferelements['warehouse_to_id'], ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_to_id', 'onchange' => 'elements_add(this.name , this.value);']) !!}
                </div>
                <div class="col-md-1" style="margin-top: 0.3%">
                    <a class="text-dark"></a>
                    {{-- <a href='#addwarehouseto' data-toggle='modal' name="Find" class="btn btn-success add-btn btn-sm" title="Add Data">Tambah</a> --}}
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-5">
                    <a class="text-dark">Tipe Transfer Gudang</a>
                    {!! Form::select('warehouse_transfer_type_id',  $warehousetransfertype, $warehousetransferelements == null ? '' :  $warehousetransferelements['warehouse_transfer_type_id'], ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_transfer_type_id', 'onchange' => 'elements_add(this.name , this.value);']) !!}
                </div>
                <div class="col-md-1" style="margin-top: 0.3%">
                    <a class="text-dark"></a>
                    {{-- <a href='#addwarehousetransfertype' data-toggle='modal' name="Find" class="btn btn-success add-btn btn-sm" title="Add Data">Tambah</a> --}}
                </div>
                <div class="col-md-5">
                    <a class="text-dark">Ekspedisi</a>
                    {!! Form::select('expedition_id',  $expedition, $warehousetransferelements == null ? '' :  $warehousetransferelements['expedition_id'], ['class' => 'selection-search-clear select-form', 'id' => 'expedition_id', 'onchange' => 'elements_add(this.name , this.value);']) !!}
                </div>
                <div class="col-md-1" style="margin-top: 0.3%">
                    <a class="text-dark"></a>
                    {{-- <a href='#addexpedition' data-toggle='modal' name="Find" class="btn btn-success add-btn btn-sm" title="Add Data">Tambah</a> --}}
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-12 ">
                    <a class="text-dark">Keterangan</a>
                    <div class="">
                        <textarea rows="3" type="text" class="form-control input-bb" name="warehouse_transfer_remark" id="warehouse_transfer_remark" onChange="elements_add(this.name, this.value);" >{{$warehousetransferelements == null ? '' :  $warehousetransferelements['warehouse_transfer_remark']}}</textarea>
                    </div>
                </div>
            </div>
            
            <br/>
            <h4 class="text-dark">Daftar Barang</h4>
            <hr/>
            <div class="row form-group">
                <div class="col-md-6">
                    <a class="text-dark">Kategori Barang</a>
                    {!! Form::select('item_category_id',  $invitemcategory, 0, ['class' => 'selection-search-clear select-form', 'id' => 'item_category_id']) !!}
                </div>
                <div class="col-md-6">
                    <a class="text-dark">Barang</a>
                    {!! Form::select('item_id',  $invitem, 0, ['class' => 'selection-search-clear select-form', 'id' => 'item_id']) !!}
                </div>
            </div>
            <div class="row form-group">
             
            </div>
            <div class="row form-group">
                {{-- <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Satuan Default</a>
                        <input class="form-control input-bb" type="text" name="default_item_unit_id" id="default_item_unit_id" value="" readonly/>
                    </div>
                </div> --}}
                <div class="col-md-6">
                    <a class="text-dark">Satuan Barang</a>
                    {!! Form::select('item_unit_id',  $invitemunit, 0, ['class' => 'selection-search-clear select-form', 'id' => 'item_unit_id']) !!}
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Stock Quantity</a>
                        <input class="form-control input-bb" type="text" name="stock_quantity" id="stock_quantity" value="" readonly/>
                    </div>
                </div>
                <div class="col-md-6" hidden>
                    <div class="form-group">
                        <a class="text-dark">Stock </a>
                        <input class="form-control input-bb" type="text" name="item_stock_id" id="item_stock_id" value="" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Quantity</a>
                        <input class="form-control input-bb" type="text" name="quantity" id="quantity" value=""/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-12 ">
                    <a class="text-dark">Keterangan Barang</a>
                    <div class="">
                        <textarea rows="3" type="text" class="form-control input-bb" name="warehouse_transfer_item_remark" id="warehouse_transfer_item_remark" ></textarea>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <a name="Save" class="btn btn-primary btn-sm" title="Save" onclick='processAddArrayWarehouseTransferItem()'>Tambah</a>
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
                                <th width='10%' style='text-align:center'>Quantity</th>
                                <th width='10%' style='text-align:center'>Satuan</th>
                                <th width='30%' style='text-align:center'>Keterangan Barang</th>
                                <th width='5%' style='text-align:center'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(!is_array($warehousetransferitem)){
                                    echo "<tr><th colspan='8' style='text-align  : center !important;'>Data Kosong</th></tr>";
                                } else {
                                    $no =1;
                                    foreach ($warehousetransferitem AS $key => $val){
                                        echo"
                                            <tr>
                                                <td style='text-align  : center'>".$no."</td>";
                                            if($val['item_id']==0){
                                                echo"
                                                <td style='text-align  : left !important;'></td>";
                                            }else{
                                                echo"
                                                <td style='text-align  : left !important;'>".$InvWarehouseTransfer->getItemName($val['item_id'])."</td>";
                                            }
                                                echo"
                                                <td style='text-align  : right !important;'>".$val['quantity']."</td>
                                                <td style='text-align  : left !important;'>".$InvWarehouseTransfer->getItemUnitName($val['item_unit_id'])."</td>
                                                <td style='text-align  : left !important;'>".$val['warehouse_transfer_item_remark']."</td>
                                                ";?>
                                                <td style='text-align  : center'>
                                                    <a href="{{route('warehouse-transfer-delete-array', ['record_id' => $key])}}" name='Reset' class='btn btn-danger btn-sm' onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'></i> Hapus</a>
                                                </td>
                                                <?php
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
                <button type="reset" name="Reset" class="btn btn-danger btn-sm" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
                <button type="submit" name="Save" class="btn btn-primary btn-sm" title="Save"><i class="fa fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>
</form>
<br>

<div class="modal fade bs-modal-lg" id="addwarehousefrom" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"  style='text-align:left !important'>
                <h4>Form Tambah Gudang</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Kode Gudang</a>
                            <input class="form-control input-bb" type="text" name="warehouse_code_from" id="warehouse_code_from" value=""/>
                        </div>
                    </div>	
                    <div class="col-md-6">
                        <div class="form-group">	
                            <a class="text-dark">Nama Gudang</a>
                            <input class="form-control input-bb" type="text" name="warehouse_name_from" id="warehouse_name_from" value=""/>
                        </div>
                    </div>	
                </div>
                <div class="row">
                    <div class="col-md-12 ">
                        <a class="text-dark">Alamat</a>
                        <div class="">
                            <textarea rows="3" type="text" class="form-control input-bb" name="warehouse_address_from" onChange="elements_add(this.name, this.value);" id="warehouse_address_from" ></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Lokasi<a class='red'> *</a></a>
                            {!! Form::select('warehouse_location_id_from',  $location, 0, ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_location_id_from']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">		
                        <div class="form-group">		
                            <a class="text-dark">No Telp</a>
                            <input class="form-control input-bb" type="text" name="warehouse_phone_from" id="warehouse_phone_from" value=""/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 ">
                        <a class="text-dark">Keterangan</a>
                        <div class="">
                            <textarea rows="3" type="text" class="form-control input-bb" name="warehouse_remark_from" onChange="elements_add(this.name, this.value);" id="warehouse_remark_from" ></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id='cancel_btn_warehouse_from'>Batal</button>
                <a class="btn btn-primary" onClick="addWarehouseFrom()">Simpan</a>
            </div>
        </div>
    </div>
</div>



<div class="modal fade bs-modal-lg" id="addwarehouseto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"  style='text-align:left !important'>
                <h4>Form Tambah Gudang</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Kode Gudang</a>
                            <input class="form-control input-bb" type="text" name="warehouse_code_to" id="warehouse_code_to" value=""/>
                        </div>
                    </div>	
                    <div class="col-md-6">
                        <div class="form-group">	
                            <a class="text-dark">Nama Gudang</a>
                            <input class="form-control input-bb" type="text" name="warehouse_name_to" id="warehouse_name_to" value=""/>
                        </div>
                    </div>	
                </div>
                <div class="row">
                    <div class="col-md-12 ">
                        <a class="text-dark">Alamat</a>
                        <div class="">
                            <textarea rows="3" type="text" class="form-control input-bb" name="warehouse_address_to" onChange="elements_add(this.name, this.value);" id="warehouse_address_to" ></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Lokasi<a class='red'> *</a></a>
                            {!! Form::select('warehouse_location_id_to',  $location, 0, ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_location_id_to']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">		
                        <div class="form-group">		
                            <a class="text-dark">No Telp</a>
                            <input class="form-control input-bb" type="text" name="warehouse_phone_to" id="warehouse_phone_to" value=""/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 ">
                        <a class="text-dark">Keterangan</a>
                        <div class="">
                            <textarea rows="3" type="text" class="form-control input-bb" name="warehouse_remark_to" onChange="elements_add(this.name, this.value);" id="warehouse_remark_to" ></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id='cancel_btn_warehouse_to'>Batal</button>
                <a class="btn btn-primary" onClick="addWarehouseTo()">Simpan</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bs-modal-lg" id="addwarehousetransfertype" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"  style='text-align:left !important'>
                <h4>Form Tambah Tipe Transfer Gudang</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">	
                            <a class="text-dark">Nama Tipe Transfer Gudang</a>
                            <input class="form-control input-bb" type="text" name="warehouse_transfer_type_name" id="warehouse_transfer_type_name" value=""/>
                        </div>
                    </div>	
                </div>
                <div class="row">
                    <div class="col-md-12 ">
                        <a class="text-dark">Keterangan</a>
                        <div class="">
                            <textarea rows="3" type="text" class="form-control input-bb" name="warehouse_transfer_type_remark" onChange="elements_add(this.name, this.value);" id="warehouse_transfer_type_remark" ></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id='cancel_btn_warehouse_transfer'>Batal</button>
                <a class="btn btn-primary" onClick="addWarehouseType()">Simpan</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-modal-lg" id="addexpedition" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"  style='text-align:left !important'>
                <h4>Form Tambah Ekspedisi</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Kode Ekspedisi</a>
                            <input class="form-control input-bb" type="text" name="expedition_code" id="expedition_code" value=""/>
                        </div>
                    </div>
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Nama Ekspedisi</a>
                            <input class="form-control input-bb" type="text" name="expedition_name" id="expedition_name" value=""/>
                        </div>
                    </div>	
                </div>
                <div class="row">
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Rute</a>
                            <input class="form-control input-bb" type="text" name="expedition_route" id="expedition_route" value=""/>
                        </div>
                    </div>	
                </div>
                <div class="row">
                    <div class="col-md-12">	
                        <div class="form-group">	
                            <a class="text-dark">Alamat</a>
                            <input class="form-control input-bb" type="text" name="expedition_address" id="expedition_address" value=""/>
                        </div>
                    </div>	
                </div>
                <div class="row">
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Nama Kota<a class='red'> *</a></a>
                            {!! Form::select('expedition_city',  $city, 0, ['class' => 'selection-search-clear select-form', 'id' => 'expedition_city']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">		
                        <div class="form-group">		
                            <a class="text-dark">Nomor Telepon</a>
                            <input class="form-control input-bb" type="text" name="expedition_home_phone" id="expedition_home_phone" value=""/>
                        </div>
                    </div>	
                </div>
                <div class="row">
                    <div class="col-md-6">		
                        <div class="form-group">		
                            <a class="text-dark">Nomor handphone 1</a>
                            <input class="form-control input-bb" type="text" name="expedition_mobile_phone1" id="expedition_mobile_phone1" value=""/>
                        </div>
                    </div>	
                    <div class="col-md-6">		
                        <div class="form-group">		
                            <a class="text-dark">Nomor handphone 2</a>
                            <input class="form-control input-bb" type="text" name="expedition_mobile_phone2" id="expedition_mobile_phone2" value=""/>
                        </div>
                    </div>	
                </div>
                <div class="row">
                    <div class="col-md-6">		
                        <div class="form-group">		
                            <a class="text-dark">Nomor Fax</a>
                            <input class="form-control input-bb" type="text" name="expedition_fax_number" id="expedition_fax_number" value=""/>
                        </div>
                    </div>	
                    <div class="col-md-6">		
                        <div class="form-group">		
                            <a class="text-dark">Email</a>
                            <input class="form-control input-bb" type="text" name="expedition_email" id="expedition_email" value=""/>
                        </div>
                    </div>	
                </div>
                <div class="row">
                    <div class="col-md-6">		
                        <div class="form-group">		
                            <a class="text-dark">Person in Charge</a>
                            <input class="form-control input-bb" type="text" name="expedition_person_in_charge" id="expedition_person_in_charge" value=""/>
                        </div>
                    </div>	
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Status<a class='red'> *</a></a>
                            {!! Form::select('expedition_status',  $status, 0, ['class' => 'selection-search-clear select-form', 'id' => 'expedition_status']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">		
                        <div class="form-group">		
                            <a class="text-dark">Keterangan</a>
                            <input class="form-control input-bb" type="text" name="expedition_remark" id="expedition_remark" value=""/>
                        </div>
                    </div>	
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id='cancel_btn_expedition'>Batal</button>
                <a class="btn btn-primary" onClick="addExpedition()">Simpan</a>
            </div>
        </div>
    </div>
</div>
<br>
<br>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop