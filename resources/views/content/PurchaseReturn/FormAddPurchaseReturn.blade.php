@inject('PurchaseReturn', 'App\Http\Controllers\PurchaseReturnController')

@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")
@section('js')
<script>
    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('filter-reset-purchase-return')}}",
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
        <li class="breadcrumb-item"><a href="{{ url('purchase-return') }}">Daftar Retur Pembelian
        </a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Retur Pembelian
        </li>
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
            <button onclick="location.href='{{ url('purchase-return/search-goods-received-note') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{route('process-add-purchase-return')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">No. Penerimaan<a class='red'> *</a></a>
                        <div class="">
                            <input type ="text" class="form-control input-bb"  name="goods_received_note_no" id="goods_received_note_no"  style="width: 15rem;" value="{{ $GoodsReceivedNote->goods_received_note_no }}" readonly/>
                        </div>
                        <div hidden class="">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Penerimaan</a>
                        <input type ="text" class="form-control input-bb"  name="goods_received_note_date" id="goods_received_note_date"  style="width: 15rem;" value="{{ $GoodsReceivedNote->goods_received_note_date }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">No. Pembelian<a class='red'> *</a></a>
                        <div class="">
                            <input type ="text" class="form-control input-bb"  name="purchase_invoice_no" id="purchase_invoice_no"  style="width: 15rem;" value="{{  $PurchaseReturn->getPurchaseinvoiceNo($GoodsReceivedNote->purchase_invoice_id) }}" readonly/>
                        </div>
                        <div hidden class="">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Pembelian</a>
                        <input type ="text" class="form-control input-bb"  name="purchase_invoice_date" id="purchase_invoice_date"  style="width: 15rem;" value="{{ $PurchaseReturn->getPurchaseinvoiceDate($GoodsReceivedNote->purchase_invoice_id) }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Supplier</a>
                        <input type ="text" class="form-control input-bb"  name="supplier_id" id="supplier_id"  style="width: 15rem;" value="{{  $PurchaseReturn->getCoreSupplierName($GoodsReceivedNote->supplier_id)  }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Retur</a>
                        <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="purchase_return_date" id="purchase_return_date" onChange="function_elements_add(this.name, this.value);" value="" style="width: 15rem;"/>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <a class="text-dark">Keterangan<a class='red'> *</a></a>
                        <div class="">
                            <textarea rows="3" type="text" class="form-control input-bb" name="goods_received_note_remark" id="goods_received_note_remark" onChange="function_elements_add(this.name, this.value);" value="{{ $GoodsReceivedNote->goods_received_note_remark }}" readonly></textarea>
                        </div>
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
        </div>
    
        <div class="card-body">
            <div class="form-body form">
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover" >
                        <thead class="thead-light" >
                            <tr>
                                <th width="2%" style='text-align:center'>No.</th>
                                <th width="10%" style='text-align:center'>Merchant</th>
                                <th width="2%" style='text-align:center'>Kategori</th>
                                <th width="10%" style='text-align:center'>Barang</th>
                                <th width="3%" style='text-align:center'>Satuan</th>
                                <th width="3%" style='text-align:center'>Qty Kirim</th>
                                <th width="3%" style='text-align:center'>Qty Diterima</th>
                                <th width="3%" style='text-align:center'>Qty Retur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                    if(count($GoodsReceivedNoteItem)==0){
                                        echo "<tr><th colspan='9' style='text-align  : center !important;'>Data Kosong</th></tr>";
                                    } else {
                                        $no =1;
                                        foreach ($GoodsReceivedNoteItem AS $key => $val){
                                            echo"
                                                <tr>
                                                    <td style='text-align  : center'>".$no."</td>
                                                    <td style='text-align  : left !important;'>".$PurchaseReturn->getMerchantName($val['merchant_id'])."</td>
                                                    <td style='text-align  : left !important;'>".$PurchaseReturn->getInvItemCategoryName($val['item_category_id'])."</td>
                                                    <td style='text-align  : left !important;'>".$PurchaseReturn->getItemName($val['item_id'])."</td>
                                                    <td style='text-align  : left !important;'>".$PurchaseReturn->getInvItemCategoryName($val['item_unit_id'])."</td>
                                                    <td style='text-align  : right !important;'>".$PurchaseReturn->getQtyPurchaseInvoiceItem($val['purchase_invoice_item_id'])."</td>
                                                    <td style='text-align  : right !important;'>".$val['quantity']."</td>
                                                    <td style='text-align  : right !important;'> <input class='form-control' style='text-align:right;'type='number' name='quantity_return_".$no."' id='quantity_return_".$no."'/>
                                                    
                                                        <input class='form-control' style='text-align:right;'type='hidden' name='merchant_id_".$no."' id='merchant_id_".$no."' value='".$val['merchant_id']."'/>
                                                        <input class='form-control' style='text-align:right;'type='hidden' name='item_category_id_".$no."' id='item_category_id_".$no."' value='".$val['item_category_id']."'/>
                                                        <input class='form-control' style='text-align:right;'type='hidden' name='item_id_".$no."' id='item_id_".$no."' value='".$val['item_id']."'/>   
                                                        <input class='form-control' style='text-align:right;'type='hidden' name='item_unit_id_".$no."' id='item_unit_id_".$no."' value='".$val['item_unit_id']."'/>

                                                    </td>
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
                <a name='Reset'  class='btn btn-danger btn-sm' onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'><i class="fa fa-times"></i> Reset</a>
                <button type="submit" name="Save"  class="btn btn-primary btn-sm" title="Save"><i class="fa fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>
    </form>
</div>
<br/>
<br/>
<br/>
@stop

@section('footer')
    
@stop

@section('css')
    
@stop

@section('js')
    
@stop   