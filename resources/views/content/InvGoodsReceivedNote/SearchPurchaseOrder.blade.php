@inject('InvGoodsReceivedNote', 'App\Http\Controllers\InvGoodsReceivedNoteController')

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
        <li class="breadcrumb-item active" aria-current="page">Daftar Purchase Invoice</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Purchase Invoice</b> <small>Mengelola Purchase Invoice</small>
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
            <button onclick="location.href='{{ url('goods-received-note') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No.</th>
                        <th width="10%" style='text-align:center'>No. Pembelian</th>
                        <th width="10%" style='text-align:center'>Tanggal Pembelian</th>
                        <th width="18%" style='text-align:center'>Nama Supplier</th>
                        <th width="10%" style='text-align:center'>Kategori Barang</th>
                        {{-- <th width="12%" style='text-align:center'>Nama Barang</th> --}}
                        <th width="10%" style='text-align:center'>Satuan</th>
                        <th width="10%" style='text-align:center'>Qty</th>
                        <th width="8%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; 
                    $purchase_order_id = -1;
                    ?>
                    @foreach($purchaseorder as $item)
                    <?php 
                        if($purchase_order_id != $item['purchase_invoice_id']){
                        $purchase_order_id = $item['purchase_invoice_id'];
                    ?>
                    <tr>
                        <td style='text-align:center'>{{$no}}.</td>
                        <td>{{$item['purchase_invoice_no']}}</td>
                        <td>{{date('d/m/Y', strtotime($item['purchase_invoice_date']))}}</td>
                        <td>{{$InvGoodsReceivedNote->getCoreSupplierName($item['supplier_id'])}}</td>
                        <td>{{$InvGoodsReceivedNote->getInvItemCategoryName($item['item_category_id'])}}</td>
                        {{-- <td>{{$InvGoodsReceivedNote->getInvItemTypeName($item['item_type_id'])}}</td> --}}
                        <td>{{$InvGoodsReceivedNote->getInvItemUnitName($item['item_unit_id'])}}</td>
                        <td>{{$item['quantity']}}</td>
                        <td class="" style='text-align:center'>
                            <a type="button" class="btn btn-outline-success btn-sm" href="{{ url('/goods-received-note/add/'.$item['purchase_invoice_id']) }}"><i class="fa fa-plus"></i> Tambah</a>
                        </td>
                    </tr>
                    <?php 
                        $no++; 
                        } else {
                    ?>
                    <tr>
                        <td style='text-align:center'>{{$no}}.</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$InvGoodsReceivedNote->getInvItemCategoryName($item['item_category_id'])}}</td>
                        {{-- <td>{{$InvGoodsReceivedNote->getInvItemTypeName($item['item_type_id'])}}</td> --}}
                        <td>{{$InvGoodsReceivedNote->getInvItemUnitName($item['item_unit_id'])}}</td>
                        <td>{{$item['quantity']}}</td>
                        <td class="" style='text-align:center'>
                        </td>
                    </tr>
                    <?php 
                    $no++; 
                    }
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