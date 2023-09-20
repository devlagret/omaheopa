@inject('InvWarehouseTransferReceivedNote', 'App\Http\Controllers\InvWarehouseTransferReceivedNoteController')
@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")
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
        <li class="breadcrumb-item"><a href="{{ url('warehouse-transfer-received-note') }}">Daftar Penerimaan Transfer Gudang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Penerimaan Transfer Gudang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Penerimaan Transfer Gudang
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
            <button onclick="location.href='{{ url('warehouse-transfer-received-note/search-warehouse-transfer') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{route('process-add-warehouse-transfer-received-note')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Gudang Asal</a>
                        <input class="form-control input-bb" type="text" name="warehouse_transfer_from_name" id="warehouse_transfer_from_name" value="{{$InvWarehouseTransferReceivedNote->getInvWarehouseName($warehousetransfer['warehouse_from_id'])}}" readonly/>
                        <input class="form-control input-bb" type="hidden" name="warehouse_transfer_from" id="warehouse_transfer_from" value="{{$warehousetransfer['warehouse_from_id']}}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Gudang Tujuan</a>
                        <input class="form-control input-bb" type="text" name="warehouse_transfer_to_name" id="warehouse_transfer_to_name" value="{{$InvWarehouseTransferReceivedNote->getInvWarehouseName($warehousetransfer['warehouse_to_id'])}}" readonly/>
                        <input class="form-control input-bb" type="hidden" name="warehouse_transfer_to" id="warehouse_transfer_to" value="{{$warehousetransfer['warehouse_to_id']}}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tipe Transfer</a>
                        <input class="form-control input-bb" type="text" name="warehouse_transfer_type_name" id="warehouse_transfer_type_name" value="{{$InvWarehouseTransferReceivedNote->getInvWarehouseTransferTypeName($warehousetransfer['warehouse_transfer_type_id'])}}" readonly/>
                        <input class="form-control input-bb" type="hidden" name="warehouse_transfer_type_id" id="warehouse_transfer_type_id" value="{{$warehousetransfer['warehouse_transfer_type_id']}}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <section class="control-label">Tanggal Penerimaan
                        <span class="required text-danger">
                            *
                        </span>
                    </section>
                    <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="warehouse_transfer_received_note_date" id="warehouse_transfer_received_note_date" onChange="function_elements_add(this.name, this.value);" value="" style="width: 15rem;"/>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-12 ">
                    <a class="text-dark">Keterangan</a>
                    <div class="">
                        <textarea rows="3" type="text" class="form-control input-bb" name="warehouse_transfer_received_note_remark" onChange="function_elements_add(this.name, this.value);" id="warehouse_transfer_received_note_remark" ></textarea>
                        <input class="form-control input-bb" type="hidden" name="warehouse_transfer_id" id="warehouse_transfer_id" value="{{$warehousetransfer['warehouse_transfer_id']}}"/>
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
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center' width="5%">No.</th>
                                <th style='text-align:center' width="20%">Barang</th>
                                <th style='text-align:center' width="10%">Satuan</th>
                                <th style='text-align:center' width="25%">Keterangan Barang</th>
                                <th style='text-align:center' width="10%">Quantity Dikirim</th>
                                <th style='text-align:center' width="10%">Quantity Diterima</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(count($warehousetransferitem)<1){
                                    echo "<tr><th colspan='7' style='text-align  : center !important;'>Data Kosong</th></tr>";
                                } else {
                                    $no =1;
                                    $total_price = 0;
                                    $outstanding_total = 0;
                                    foreach ($warehousetransferitem AS $key => $val){
                                        echo"
                                            <tr>
                                                <td style='text-align  : center'>".$no."</td>";
                                            if($val['item_id'] != 0){
                                                echo"
                                                <td style='text-align  : left'>".$InvWarehouseTransferReceivedNote->getItemName($val['item_id'])."</td>";
                                            }else{
                                                echo"
                                                <td style='text-align  : left'></td>";
                                            }
                                                echo"
                                                <td style='text-align  : left'>".$InvWarehouseTransferReceivedNote->getInvItemUnitName($val['item_unit_id'])."</td>
                                                <td style='text-align  : left'>".$val['warehouse_transfer_item_remark']."</td>
                                                <td style='text-align  : right'>".$val['quantity']."</td>
                                                <td style='text-align  : center'>
                                                    <input class='form-control' style='text-align:right;'type='text' name='quantity_received_".$no."' id='quantity_received_".$no."' value='".$val['quantity']."'/>  
                                                    <input class='form-control' style='text-align:right;'type='hidden' name='item_id_".$no."' id='item_id_".$no."' value='".$val['item_id']."'/>     
                                                    <input class='form-control' style='text-align:right;'type='hidden' name='item_category_id_".$no."' id='item_category_id_".$no."' value='".$val['item_category_id']."'/>   
                                                    <input class='form-control' style='text-align:right;'type='hidden' name='item_unit_id_".$no."' id='item_unit_id_".$no."' value='".$val['item_unit_id']."'/>   
                                                    <input class='form-control' style='text-align:right;'type='hidden' name='item_stock_id_".$no."' id='item_stock_id_".$no."' value='".$val['item_stock_id']."'/>   
                                                    <input class='form-control' style='text-align:right;'type='hidden' name='warehouse_transfer_item_id_".$no."' id='warehouse_transfer_item_id_".$no."' value='".$val['warehouse_transfer_item_id']."'/>   
                                                </td>
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
                <button type="reset" name="Reset" class="btn btn-danger" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
                <button type="submit" name="Save" class="btn btn-primary" title="Save"><i class="fa fa-check"></i> Simpan</button>
                <input class="form-control input-bb" type="hidden" name="total_no" id="total_no" value="{{$no-1}}" readonly/>
            </div>
        </div>
    </div>
</form>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop