@inject('PurchaseReturn', 'App\Http\Controllers\PurchaseReturnController')

@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")
@section('js')
<script>
 function deleteItem(id,name){
    $.ajax({
				type: "GET",
				url : "{{url('/item/check-delete-item/')}}"+'/'+id,
				success: function(msg){
                    console.log(msg);
                    if(msg!=0){
                        if(confirm('Barang "'+name+'" dipakai di paket. Anda yakin ingin tetap menghapus?')){
                         window.location.href = "{{ url('/item/delete-item/') }}"+'/'+id;
                        }
                        return 0;
                    }
                    if(confirm(`Yakin Ingin Menghapus Item dengan nama '`+name+`' ?`)){
                      window.location.href = "{{ url('/item/delete-item/') }}"+'/'+id;
                    }
                    return 0;
			}

		});
 }

function check(name,uri){
 if(confirm(`Yakin Ingin Menghapus Paket dengan nama '`+name+`' ?`)){
   window.location.href = uri;
 }
}
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Penerimaan Barang</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Penerimaan Barang</b> <small>Mengelola Penerimaan Barang</small>
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif 
<div class="card border border-dark">
    <div class="card-header bg-dark clearfix">
        <h5 class="mb-0 float-left">
            Daftar
        </h5>
        <div class="form-actions float-right">
            <button onclick="location.href='{{ url('purchase-return') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No.</th>
                        <th width="10%" style='text-align:center'>No. Penerimaan</th>
                        <th width="10%" style='text-align:center'>Tanggal Penerimaan</th>
                        <th width="18%" style='text-align:center'>Nama Supplier</th>
                        <th width="10%" style='text-align:center'>No.  Pembelian</th>
                        <th width="12%" style='text-align:center'>Tanggal Pembelian</th>
                        <th width="8%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; 
                    ?>
                    @foreach($GoodsReceivedNote as $item)
                    <tr>
                        <td style='text-align:center'>{{$no}}.</td>
                        <td>{{$item['goods_received_note_no']}}</td>
                        <td>{{date('d/m/Y', strtotime($item['goods_received_note_date']))}}</td>
                        <td>{{$PurchaseReturn->getCoreSupplierName($item['supplier_id'])}}</td>
                        <td>{{ $PurchaseReturn->getPurchaseinvoiceNo($item['purchase_invoice_id'])}}</td>
                        <td>{{$PurchaseReturn->getPurchaseinvoiceDate($item['purchase_invoice_id'])}}</td>
                        <td><a href="{{ url('/purchase-return/add/'.$item['goods_received_note_id']) }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i></a></td>
                    </tr>
                    <?php 
                    $no++; 
                    ?>
                    @endforeach
                </tbody>
            </table>
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