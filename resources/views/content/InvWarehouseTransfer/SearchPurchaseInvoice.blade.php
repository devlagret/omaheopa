@inject('InvWarehouseTransfer', 'App\Http\Controllers\InvWarehouseTransferController')

@extends('adminlte::page')

@section('title', 'PBF | Koperasi Menjangan Enam')
<link rel="shortcut icon" href="{{ asset('resources/assets/logo_pbf.ico') }}" />

@section('js')
<script>
</script>
@stop

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item"><a href="{{ url('warehouse-transfer') }}">Daftar Transfer Gudang</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Invoice Pembelian</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Invoice Pembelian</b>
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
    <div class="float-right">
        <button onclick="location.href='{{ url('warehouse-transfer') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
    </div>
  </div>

  <form method="post" action="{{route('add-warehouse-transfer')}}" enctype="multipart/form-data">
    @csrf
    <div class="card-body">
        <div class="table-responsive">
            <table style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No</th>
                        <th width="13%" style='text-align:center'>Pemasok</th>
                        <th width="15%" style='text-align:center'>No. Invoice Pembelian</th>
                        <th width="10%" style='text-align:center'>Tanggal PO</th>
                        <th width="12%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($purchaseinvoice as $item)
                    <tr>
                        <td style='text-align:center'>{{$no}}</td>
                        <td>{{$InvWarehouseTransfer->getSupplierName($item['supplier_id'])}}</td>
                        <td>{{$item['purchase_invoice_no']}}</td>
                        <td>{{$InvWarehouseTransfer->getPurchaseOrderDate($item['purchase_order_id'])}}</td>
                        <td style='text-align:center'>
                            <a type="button" class="btn btn-outline-primary btn-sm" href="{{ url('/warehouse-transfer/add/'.$item['purchase_invoice_id']) }}"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                    <?php $no++; ?>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
        
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <button type="submit" name="Save" class="btn btn-primary" title="Save">Tambah</button>
        </div>
    </div>
  </div>
</div>
</form>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop