@inject('InvWarehouseTransferReceivedNote', 'App\Http\Controllers\InvWarehouseTransferReceivedNoteController')
@extends('adminlte::page')

@section('title', 'PBF | Koperasi Menjangan Enam')
<link rel="shortcut icon" href="{{ asset('resources/assets/logo_pbf.ico') }}" />
@section('js')
<script>
	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1.')
			.split('').reverse().join('');
		return rupiah + ',' + cents.slice(0, 2);
	}
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('goods-received-note') }}">Daftar Penerimaan Transfer Gudang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Hapus Penerimaan Transfer Gudang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Hapus Penerimaan Transfer Gudang
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
            <button onclick="location.href='{{ url('warehouse-transfer-received-note') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="get" action="{{route('process-delete-warehouse-transfer-received-note', ['id' => $goods_received_note_id])}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">No Penerimaan</a>
                        <input class="form-control input-bb" type="text" name="warehouse_transfer_received_note_no" id="warehouse_transfer_received_note_no" value="{{$invwarehousetransferreceivednote['warehouse_transfer_received_note_no']}}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Penerimaan</a>
                        <input class="form-control input-bb" type="text" name="goods_received_note_date" id="goods_received_note_date" value="{{$invwarehousetransferreceivednote['warehouse_transfer_received_note_date']}}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Gudang Asal</a>
                        <input class="form-control input-bb" type="text" name="warehouse_transfer_from" id="warehouse_transfer_from" value="{{$InvWarehouseTransferReceivedNote->getInvWarehouseName($invwarehousetransferreceivednote['warehouse_transfer_from'])}}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Gudang Tujuan</a>
                        <input class="form-control input-bb" type="text" name="warehouse_transfer_to" id="warehouse_transfer_to" value="{{$InvWarehouseTransferReceivedNote->getInvWarehouseName($invwarehousetransferreceivednote['warehouse_transfer_to'])}}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tipe Transfer</a>
                        <input class="form-control input-bb" type="text" name="warehouse_transfer_type_id" id="warehouse_transfer_type_id" value="{{$PurchaseOrder->getInvWarehouseTransferTypeName($invwarehousetransferreceivednote['warehouse_transfer_type_id'])}}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-12 ">
                    <a class="text-dark">Keterangan</a>
                    <div class="">
                        <textarea rows="3" type="text" class="form-control input-bb" name="warehouse_transfer_received_note_remark" onChange="function_elements_add(this.name, this.value);" id="warehouse_transfer_received_note_remark" readonly>{{$invwarehousetransferreceivednote['warehouse_transfer_received_note_remark']}}</textarea>
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
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center'>No.</th>
                                <th style='text-align:center'>Barang</th>
                                <th style='text-align:center'>Batch Number</th>
                                <th style='text-align:center'>Satuan</th>
                                <th style='text-align:center'>Keterangan Barang</th>
                                <th style='text-align:center'>Quantity Dikirim</th>
                                <th style='text-align:center'>Quantity Diterima</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(count($invwarehousetransferreceivednoteitem)<1){
                                    echo "<tr><th colspan='7' style='text-align  : center !important;'>Data Kosong</th></tr>";
                                } else {
                                    $no =1;
                                    foreach ($invwarehousetransferreceivednoteitem AS $key => $val){
                                        echo"
                                            <tr>
                                                <td style='text-align  : center'>".$no."</td>
                                                <td style='text-align  : left !important;'>".$InvWarehouseTransferReceivedNote->getItemName($val['item_id'])."</td>
                                                <td style='text-align  : left !important;'>".$InvWarehouseTransferReceivedNote->getItemStockBatchNumber($val['item_stock_id'])."</td>
                                                <td style='text-align  : left !important;'>".$InvWarehouseTransferReceivedNote->getItemUnitName($val['item_unit_id'])."</td>
                                                <td style='text-align  : right !important;'>".$val['warehouse_transfer_received_note_item_remark']."</td>
                                                <td style='text-align  : right !important;'>".$val['warehouse_transfer_item.quantity']."</td>
                                                <td style='text-align  : right !important;'>".$val['warehouse_transfer_received_note_item.quantity']."</td>";
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
                <button type="submit" name="Save" class="btn btn-danger" title="Save"><i class="fa fa-trash"></i> Hapus</button>
            </div>
        </div>

    </div>
</form>
<br/>
<br/>
<br/>
<br/>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop