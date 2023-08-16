@inject('PurchaseInvoice','App\Http\Controllers\PurchaseInvoiceController')
@extends('adminlte::page')

@section('title', 'MOZAIC Point of Sales')
@section('js')
<script>
    function function_elements_add(name, value){
        console.log("name " + name);
        console.log("value " + value);
		$.ajax({
				type: "POST",
				url : "{{route('add-elements-purchase-invoice')}}",
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
        $("#quantity").change(function(){
            var quantity = $("#quantity").val();
            var cost = $("#item_unit_cost").val();
            var subtotal = quantity * cost;

            $("#subtotal_amount").val(subtotal);
            $("#subtotal_amount_view").val(toRp(subtotal));

        });
        $("#item_unit_cost").change(function(){
            var quantity = $("#quantity").val();
            var cost       = $("#item_unit_cost").val();
            var subtotal = quantity * cost;

            $("#subtotal_amount").val(subtotal);
            $("#subtotal_amount_view").val(toRp(subtotal));

        });
        $("#discount_percentage_total").change(function(){
            var discount_percentage_total   = $("#discount_percentage_total").val();
            var subtotal_amount_total            = $("#subtotal_amount_total").val();
            var discount_amount_total       = (discount_percentage_total * subtotal_amount_total) / 100;
            var total_amount                = subtotal_amount_total - discount_amount_total;

            $("#discount_amount_total").val(discount_amount_total);
            $("#discount_amount_total_view").val(toRp(discount_amount_total));
            $("#total_amount").val(total_amount);
            $("#total_amount_view").val(toRp(total_amount));
        });
        $("#paid_amount").change(function(){
            var paid_amount = $("#paid_amount").val();
            var total_amount = $("#total_amount").val();
            var owing_amount = paid_amount - total_amount;

            $("#owing_amount").val(owing_amount);
            $("#owing_amount_view").val(toRp(owing_amount));
        });
    });

    function processAddArrayPurchaseInvoice(){
        var item_category_id    = document.getElementById("item_category_id").value;
        var item_id		        = document.getElementById("item_id").value;
        var item_unit_id		= document.getElementById("item_unit_id").value;
        var item_unit_cost		= document.getElementById("item_unit_cost").value;
        var quantity            = document.getElementById("quantity").value;
        var subtotal_amount     = document.getElementById("subtotal_amount").value;
        var item_expired_date   = document.getElementById("item_expired_date").value;

        $.ajax({
            type: "POST",
            url : "{{route('add-array-purchase-invoice')}}",
            data: {
                'item_category_id'  : item_category_id,
                'item_id'    	    : item_id, 
                'item_unit_id'      : item_unit_id,
                'item_unit_cost'    : item_unit_cost,
                'quantity'          : quantity,
                'subtotal_amount'   : subtotal_amount,
                'item_expired_date' : item_expired_date,
                '_token'            : '{{csrf_token()}}'
            },
            success: function(msg){
                location.reload();
            }
        });
    }

    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('add-reset-purchase-invoice')}}",
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
        <li class="breadcrumb-item"><a href="{{ url('purchase-invoice') }}">Daftar Pembelian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Pembelian</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Pembelian
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
            <button onclick="location.href='{{ url('purchase-invoice') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php 
            // if (empty($coresection)){
            //     $coresection['section_name'] = '';
            // }
        ?>

    <form method="post" action="{{ route('process-add-purchase-invoice') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Pemasok<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_invoice_supplier" id="purchase_invoice_supplier" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $datases['purchase_invoice_supplier'] }}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Gudang<a class='red'> *</a></a>
                        {!! Form::select('warehouse_id', $warehouses, $datases['warehouse_id'], ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_id', 'name' => 'warehouse_id', 'onchange' => 'function_elements_add(this.name, this.value)']) !!}
                        
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Invoice Pembelian<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_invoice_date" id="purchase_invoice_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $datases['purchase_invoice_date'] }}"/>
                    </div>
                </div>
                <div class="col-md-6">

                </div>
                <div class="col-md-9 mt-3">
                    <div class="form-group">
                        <a class="text-dark">Keterangan<a class='red'> *</a></a>
                        <textarea class="form-control input-bb" name="purchase_invoice_remark" id="purchase_invoice_remark" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{ $datases['purchase_invoice_remark'] }}</textarea>
                    </div>
                </div>

                <h6 class="col-md-8 mt-4 mb-3"><b>Data Invoice Pembelian Barang</b></h6>

                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                        {!! Form::select('item_category_id', $categorys, 0, ['class' => 'selection-search-clear select-form', 'id' => 'item_category_id']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                        {!! Form::select('item_id', $items, 0, ['class' => 'selection-search-clear select-form', 'id' => 'item_id']) !!}
                        
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kode Satuan<a class='red'> *</a></a>
                        {!! Form::select('item_unit_id', $units, 0, ['class' => 'selection-search-clear select-form', 'id' => 'item_unit_id']) !!}
                       
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Biaya Barang Satuan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="item_unit_cost" id="item_unit_cost" type="text" autocomplete="off" value=""/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Jumlah<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="quantity" id="quantity" type="text" autocomplete="off" value=""/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Subtotal<a class='red'> *</a></a>
                        <input style="text-align: right" class="form-control input-bb" name="subtotal_amount_view" id="subtotal_amount_view" type="text" autocomplete="off" value="" disabled/>
                        <input class="form-control input-bb" name="subtotal_amount" id="subtotal_amount" type="text" autocomplete="off" value="" hidden/>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Kadaluarsa<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="item_expired_date" id="item_expired_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ date('Y-m-d') }}"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <a type="submit" name="Save" class="btn btn-primary" title="Save" onclick="processAddArrayPurchaseInvoice()"> Tambah</a>
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
                                <th style='text-align:center'>Harga Satuan</th>
                                <th style='text-align:center'>Subtotal</th>
                                <th style='text-align:center'>Kadaluarsa</th>
                                <th style='text-align:center'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $quantity = 0;
                        $subtotal_amount = 0;
                            if(!is_array($arraydatases)){
                                echo "<tr><th colspan='6' style='text-align  : center !important;'>Data Kosong</th></tr>";
                            } else {
                                foreach ($arraydatases AS $key => $val){
                                    echo"
                                    <tr>
                                                <td style='text-align  : left !important;'>".$PurchaseInvoice->getItemName($val['item_id'])."</td>
                                                <td style='text-align  : right !important;'>".$val['quantity']."</td>
                                                <td style='text-align  : right !important;'>".number_format($val['item_unit_cost'],2,',','.')."</td>
                                                <td style='text-align  : right !important;'>".number_format($val['subtotal_amount'],2,',','.')."</td>
                                                <td style='text-align  : right !important;'>".date('d-m-Y', strtotime($val['item_expired_date']))."</td>";
                                                ?>
                                                
                                                <td style='text-align  : center'>
                                                    <a href="{{route('delete-array-purchase-invoice', ['record_id' => $key])}}" name='Reset' class='btn btn-danger btn-sm' onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini ?')"></i> Hapus</a>
                                                </td>
                                                
                                                <?php
                                                echo"
                                            </tr>
                                        ";

                                    $quantity += $val['quantity'];
                                    $subtotal_amount += $val['subtotal_amount'];
                                    
                                }
                            }
                        ?>
                            <tr>
                                <td colspan="2">Sub Total</td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="subtotal_item" id="subtotal_item" value="{{ $quantity }}" readonly/>
								</td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="subtotal_amount_total_view" id="subtotal_amount_total_view" value="{{ number_format($subtotal_amount,2,',','.') }}" readonly/>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="subtotal_amount_total" id="subtotal_amount_total" value="{{ $subtotal_amount }}" hidden/>
								</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2">Diskon (%)</td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="discount_percentage_total" id="discount_percentage_total" value="" autocomplete="off"/>
								</td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="discount_amount_total_view" id="discount_amount_total_view" value="" readonly/>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="discount_amount_total" id="discount_amount_total" value="" hidden/>
								</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3">Jumlah Total</td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="total_amount_view" id="total_amount_view" value="{{ number_format($subtotal_amount,2,',','.') }}" readonly/>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="total_amount" id="total_amount" value="{{ $subtotal_amount }}" hidden/>
								</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3">Di Bayar</td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="paid_amount" id="paid_amount" value="" autocomplete="off"/>
								</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3">Sisa</td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="owing_amount_view" id="owing_amount_view" value="" readonly/>
                                    <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="owing_amount" id="owing_amount" value="" hidden/>
								</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i class="fa fa-times"></i> Reset Data</button>
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