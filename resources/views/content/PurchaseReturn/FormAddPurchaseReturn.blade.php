@inject('PurchaseReturn', 'App\Http\Controllers\PurchaseReturnController')
@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")
@section('js')
<script>
    function function_elements_add(name, value){
        console.log("name " + name);
        console.log("value " + value);
		$.ajax({
				type: "POST",
				url : "{{route('add-elements-purchase-return')}}",
				data : {
                    'name'      : name, 
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
			}
		});
	}

	$(document).ready(function(){
        $("#purchase_return_quantity").change(function(){
            var quantity = $("#purchase_return_quantity").val();
            var cost = $("#purchase_return_cost").val();
            var subtotal = quantity * cost;

            $("#purchase_return_subtotal").val(subtotal);
            $("#purchase_return_subtotal_view").val(toRp(subtotal));

        });
        $("#purchase_return_cost").change(function(){
            var quantity = $("#purchase_return_quantity").val();
            var cost       = $("#purchase_return_cost").val();
            var subtotal = quantity * cost;

            $("#purchase_return_subtotal").val(subtotal);
            $("#purchase_return_subtotal_view").val(toRp(subtotal));

        });
    });

    function processAddArrayPurchaseReturn(){
        var item_category_id		    = document.getElementById("item_category_id").value;
        var item_id		                = document.getElementById("item_id").value;
        var item_unit_id		        = document.getElementById("item_unit_id").value;
        var purchase_return_cost		= document.getElementById("purchase_return_cost").value;
        var purchase_return_quantity    = document.getElementById("purchase_return_quantity").value;
        var purchase_return_subtotal    = document.getElementById("purchase_return_subtotal").value;

        $.ajax({
            type: "POST",
            url : "{{route('add-array-purchase-return')}}",
            data: {
                'item_category_id'          : item_category_id,
                'item_id'    		        : item_id, 
                'item_unit_id'              : item_unit_id,
                'purchase_return_cost'      : purchase_return_cost,
                'purchase_return_quantity'  : purchase_return_quantity,
                'purchase_return_subtotal'  : purchase_return_subtotal,
                '_token'                    : '{{csrf_token()}}'
            },
            success: function(msg){
                location.reload();
            }
        });
    }

    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('add-reset-purchase-return')}}",
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
        <li class="breadcrumb-item"><a href="{{ url('purchase-return') }}">Daftar Retur Pembelian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Retur Pembelian</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Retur Pembelian
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
            <button onclick="location.href='{{ url('purchase-return') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php 
            // if (empty($coresection)){
            //     $coresection['section_name'] = '';
            // }
        ?>

    <form method="post" action="{{ route('process-add-purchase-return') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Pemasok<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $datases['purchase_return_supplier'] ?? '' }}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Gudang<a class='red'> *</a></a>
                        {!! Form::select('warehouse_id',  $warehouses, $datases['warehouse_id']?? '' , ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_id', 'name' => 'warehouse_id', 'onchange' => 'function_elements_add(this.name, this.value)']) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Retur Pembelian<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_date" id="purchase_return_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $datases['purchase_return_date']?? ''}}"/>
                    </div>
                </div>
                <div class="col-md-6">

                </div>
                <div class="col-md-9 mt-3">
                    <div class="form-group">
                        <a class="text-dark">Keterangan<a class='red'> *</a></a>
                        <textarea class="form-control input-bb" name="purchase_return_remark" id="purchase_return_remark" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{ $datases['purchase_return_remark'] ?? '' }}</textarea>
                    </div>
                </div>

                <h6 class="col-md-8 mt-4 mb-3"><b>Data Retur Pembelian Barang</b></h6>

                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                        {!! Form::select('item_category_id',  $categorys, 0, ['class' => 'selection-search-clear select-form', 'id' => 'item_category_id', 'name' => 'item_category_id']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                        {!! Form::select('item_id',  $items, 0, ['class' => 'selection-search-clear selec   t-form', 'id' => 'item_id', 'name' => 'item_id']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kode Satuan<a class='red'> *</a></a>
                        {!! Form::select('item_unit_id',  $units, 0, ['class' => 'selection-search-clear select-form', 'id' => 'item_unit_id', 'name' => 'item_unit_id']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Biaya Barang Satuan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_cost" id="purchase_return_cost" type="text" autocomplete="off" value=""/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Jumlah<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_quantity" id="purchase_return_quantity" type="text" autocomplete="off" value=""/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Subtotal<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_subtotal_view" id="purchase_return_subtotal_view" type="text" autocomplete="off" value="" disabled/>
                        <input class="form-control input-bb" name="purchase_return_subtotal" id="purchase_return_subtotal" type="text" autocomplete="off" value="" hidden/>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <a type="submit" name="Save" class="btn btn-primary" title="Save" onclick="processAddArrayPurchaseReturn()"> Tambah</a>
            </div>
        </div>
    </div>
    </div>


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
                                <th style='text-align:center'>Barang</th>
                                <th style='text-align:center'>Jumlah</th>
                                <th style='text-align:center'>Biaya Satuan</th>
                                <th style='text-align:center'>Subtotal</th>
                                <th style='text-align:center'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(!is_array($arraydatases)){
                                echo "<tr><th colspan='7' style='text-align  : center !important;'>Data Kosong</th></tr>";
                            } else {
                                $total_quantity = 0;
                                $subtotal = 0;
                                foreach ($arraydatases AS $key => $val){
                                    echo"
                                    <tr>
                                                <td style='text-align  : left !important;'>".$PurchaseReturn->getItemName($val['item_id'])."</td>
                                                <td style='text-align  : right !important;'>".$val['purchase_return_quantity']."</td>
                                                <td style='text-align  : right !important;'>".number_format($val['purchase_return_cost'],2,',','.')."</td>
                                                <td style='text-align  : right !important;'>".number_format($val['purchase_return_subtotal'],2,',','.')."</td>";
                                                ?>
                                                
                                                <td style='text-align  : center'>
                                                    <a href="{{route('delete-array-purchase-return', ['record_id' => $key])}}" name='Reset' class='btn btn-danger btn-sm'></i> Hapus</a>
                                                </td>
                                                
                                                <?php
                                                echo"
                                            </tr>
                                        ";
                                        $subtotal += $val['purchase_return_subtotal'];
                                        $total_quantity += $val['purchase_return_quantity'];

                                }
                                echo"
                                <th style='text-align  : center' colspan='1'>Total</th>
                                <th style='text-align  : right'>".$total_quantity."</th>
                                <th style='text-align  : center'></th>
                                <th style='text-align  : right'>".number_format($subtotal,2,',','.')."</th>
                                <th style='text-align  : center'></th>
                                <div>
                                    <input class='form-control input-bb' type='hidden' name='total_quantity' id='total_quantity' value='".$total_quantity."'/>
                                    <input class='form-control input-bb' type='hidden' name='subtotal' id='subtotal' value='".$subtotal."'/>
                                </div>
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
                <button type="reset" name="Reset" class="btn btn-danger" onClick="reset_add();"><i class="fa fa-times"></i> Reset Data</button>
                <button type="submit" name="Save" class="btn btn-primary" title="Save"><i class="fa fa-check"></i> Simpan</button>
            </div>
        </div>
</form>
</div>



@stop

@section('footer')
    
@stop

@section('css')
    
@stop