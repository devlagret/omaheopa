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
        <li class="breadcrumb-item active" aria-current="page">Daftar Penerimaan Barang</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Penerimaan Barang</b> <small>Mengelola Penerimaan Barang</small>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{route('filter-goods-received-note')}}" enctype="multipart/form-data">
    @csrf
        <div class="card border border-dark">
        <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                Filter
            </h5>
        </div>
    
        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class = "row">
                    <div class = "col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Mulai
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" onChange="function_elements_add(this.name, this.value);" value="{{$start_date}}" style="width: 15rem;"/>
                        </div>
                    </div>

                    <div class = "col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Akhir
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" onChange="function_elements_add(this.name, this.value);" value="{{$end_date}}" style="width: 15rem;"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a href="{{route('filter-reset-goods-received-note')}}" type="reset" name="Reset" class="btn btn-danger btn-sm" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</a>
                    <button type="submit" name="Find" class="btn btn-primary btn-sm" title="Search Data"><i class="fa fa-search"></i> Cari</button>
                </div>
            </div>
        </div>
        </div>
    </form>
</div>
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
            <button onclick="location.href='{{ url('goods-received-note/search-purchase-order') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Penerimaan Barang Baru</button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No</th>
                        <th width="15%" style='text-align:center'>No Penerimaan</th>
                        <th width="13%" style='text-align:center'>Tanggal Penerimaan</th>
                        <th width="15%" style='text-align:center'>Nama Gudang</th>
                        <th width="15%" style='text-align:center'>Nama Supplier</th>
                        <th width="15%" style='text-align:center'>No PO</th>
                        <th width="15%" style='text-align:center'>Tanggal PO</th>
                        {{-- <th width="15%" style='text-align:center'>No Faktur</th> --}}
                        <th width="10%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($goodsreceivednote as $item)
                    <tr>
                        <td style='text-align:center'>{{$no}}</td>
                        <td>{{$item['goods_received_note_no']}}</td>
                        <td>{{date('d/m/Y', strtotime($item['goods_received_note_date']))}}</td>
                        <td>{{$InvGoodsReceivedNote->getInvWarehouseName($item['warehouse_id'])}}</td>
                        <td>{{$InvGoodsReceivedNote->getCoreSupplierName($item['supplier_id'])}}</td>
                        <td>{{$InvGoodsReceivedNote->getPurchaseinvoiceNo($item['purchase_invoice_id'])}}</td>
                        <td>{{date('d/m/Y', strtotime($InvGoodsReceivedNote->getPurchaseInvoiceDate($item['purchase_invoice_id'])))}}</td>
                        {{-- <td>{{$item['faktur_no']}}</td> --}}
                        <td class="" style='text-align:center'>
                            <a type="button" class="btn btn-outline-success btn-sm" href="{{ url('/goods-received-note/detail/'.$item['goods_received_note_id']) }}">Detail</a>
                            <a type="button" class="btn btn-outline-danger btn-sm" href="{{ url('/goods-received-note/delete-goods-received-note/'.$item['goods_received_note_id']) }}">Hapus</a>
                        </td>
                    </tr>
                    <?php $no++; ?>
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