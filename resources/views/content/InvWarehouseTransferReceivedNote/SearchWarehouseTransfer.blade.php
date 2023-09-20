@inject('InvWarehouseTransferReceivedNote', 'App\Http\Controllers\InvWarehouseTransferReceivedNoteController')

@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")
<link rel="shortcut icon" href="{{ asset('resources/assets/logo_pbf.ico') }}" />

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Transfer Gudang</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Transfer Gudang</b> <small>Mengelola Transfer Gudang</small>
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
            <button onclick="location.href='{{ url('warehouse-transfer-received-note') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No.</th>
                        <th width="10%" style='text-align:center'>No. Transfer Gudang</th>
                        <th width="10%" style='text-align:center'>Tanggal</th>
                        <th width="18%" style='text-align:center'>Gudang Asal</th>
                        <th width="10%" style='text-align:center'>Gudang Tujuan</th>
                        <th width="12%" style='text-align:center'>Tipe Transfer</th>
                        <th width="8%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; 
                    // print_r($warehousetransfer);
                    ?>
                    @foreach($warehousetransfer as $item)
                        <tr>
                            <td style='text-align:center'>{{$no}}.</td>
                            <td>{{$item['warehouse_transfer_no']}}</td>
                            <td>{{date('d/m/Y', strtotime($item['warehouse_transfer_date']))}}</td>
                            <td>{{$InvWarehouseTransferReceivedNote->getInvWarehouseName($item['warehouse_from_id'])}}</td>
                            <td>{{$InvWarehouseTransferReceivedNote->getInvWarehouseName($item['warehouse_to_id'])}}</td>
                            <td>{{$InvWarehouseTransferReceivedNote->getInvWarehouseTransferTypeName($item['warehouse_transfer_type_id'])}}</td>
                            <td class="" style='text-align:center'>
                                <a type="button" class="btn btn-outline-success btn-sm" href="{{ url('/warehouse-transfer-received-note/add/'.$item['warehouse_transfer_id']) }}"><i class="fa fa-plus"></i> Tambah</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop

@section('js')
    
@stop