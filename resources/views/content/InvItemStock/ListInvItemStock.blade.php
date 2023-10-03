@inject('InvItemStock', 'App\Http\Controllers\InvItemStockController')
@inject('Grading', 'App\Http\Controllers\GradingController')
@inject('ISARC','App\Http\Controllers\InvtStockAdjustmentReportController' )

@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")
<link rel="shortcut icon" href="{{ asset('resources/assets/logo_pbf.ico') }}" />
@section('js')
<script>
	$(document).ready(function(){
        var item_category_id    = {!! json_encode($item_category_id) !!};
        var item_id             = {!! json_encode($item_id) !!};
        var grade_id            = {!! json_encode($grade_id) !!};
        var warehouse_id        = {!! json_encode($warehouse_id) !!};
        
        if(item_category_id == null){
            $("#item_category_id").select2("val", "0");
        }
        if(item_id == null){
            $("#item_id").select2("val", "0");
        }
        if(grade_id == null){
            $("#grade_id").select2("val", "0");
        }
        if(warehouse_id == null){
            $("#warehouse_id").select2("val", "0");
        }

        
        $("#item_category_id").change(function(){
			var item_category_id 	= $("#item_category_id").val();
                $.ajax({
                    type: "POST",
                    url : "{{route('item-stock-type')}}",
                    dataType: "html",
                    data: {
                        'item_category_id'	  : item_category_id,
                        '_token'              : '{{csrf_token()}}',
                    },
                    success: function(return_data){ 
                        console.log(return_data);
					$('#item_id').html(return_data);
                    },
                    error: function(data)
                    {
                        console.log(data);

                    }
                });

		});
        
        $("#item_id").change(function(){
            var item_id 	= $("#item_id").val();
                $.ajax({
                    type: "POST",
                    url : "{{route('item-stock-grade')}}",
                    dataType: "html",
                    data: {
                        'item_id'	  : item_id,
                        '_token'              : '{{csrf_token()}}',
                    },
                    success: function(return_data){ 
                        $('#grade_id').html(return_data);
                    },
                    error: function(data)
                    {
                        console.log(data);

                    }
                });

        });
    });
</script>
@stop

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Stock Barang</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Stock Barang</b> <small>Mengelola Stock Barang </small>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{route('filter-item-stock')}}" enctype="multipart/form-data">
    @csrf
        <div class="card border border-dark">
        <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                Filter
            </h5>
        </div>
    
        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class = "row form-group">
                    {{-- <div class = "col-md-3">
                        <a class="text-dark">Wahama / Merchant</a>
                        <br/>
                        {!! Form::select('merchant_id',  $merchant, $merchant_id, ['class' => 'selection-search-clear select-form', 'id' => 'merchant_id', 'name'=>'merchant_id']) !!}
                    </div> --}}
                    <div class = "col-md-4">
                        <a class="text-dark">Kategori</a>
                        <br/>
                        {!! Form::select('item_category_id',  $invitemcategory, $item_category_id, ['class' => 'selection-search-clear select-form', 'id' => 'item_category_id']) !!}
                    </div>
                    <div class = "col-md-4">
                        <a class="text-dark">Nama Barang</a>
                        <br/>
                        {!! Form::select('item_id',  $invitem, $item_id, ['class' => 'selection-search-clear select-form', 'id' => 'item_id']) !!}
                    </div>
                    <div class = "col-md-4">
                        <a class="text-dark">Gudang</a>
                        <br/>
                        {!! Form::select('warehouse_id',  $invwarehouse, $warehouse_id, ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_id']) !!}
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger btn-sm" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
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
            <button onclick="location.href='{{ url('item-stock/export') }}'" name="Find" class="btn btn-sm btn-info" title="Export Excel"><i class="fa fa-print"></i> Export</button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No</th>
                        <th width="13%" style='text-align:center'>Wahana / Merchant</th>
                        <th width="13%" style='text-align:center'>Kategori Barang</th>
                        <th width="15%" style='text-align:center'>Nama Barang</th>
                        {{-- <th width="10%" style='text-align:center'>Grade</th> --}}
                        {{-- <th width="10%" style='text-align:center'>Unit</th> --}}
                        <th width="10%" style='text-align:center'>Gudang</th>
                        <th width="10%" style='text-align:center'>Tanggal Datang</th>
                        <th width="10%" style='text-align:center'>Qty</th>
                        {{-- <th width="8%" style='text-align:center'>Aksi</th> --}}
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; $l = 1;?>
                    @foreach($invitemstock as $stock)
                    @php $id @endphp
                    <tr>
                        <td style='text-align:center'>{{$no}}</td>
                        <td>{{$stock->item->merchant->merchant_name}}</td>
                        <td>{{$stock->category->item_category_name}}</td>
                        <td>{{$stock->item->item_name}}</td>
                        {{-- <?php if($stock['item_id']==0) {?>
                            <td>No Grade</td>
                        <?php } else {?>
                            <td>{{$InvItemStock->getCoreGradeName($stock['item_id'])}}</td>
                        <?php } ?> --}}
                        <td>{{$stock->warehouse->warehouse_name}}</td>
                        <td>{{date('d/m/Y', strtotime($stock['created_at']))}}</td>
                        <td>{{number_format($stock->last_balance)." ".$stock->unit->item_unit_name}}</td>
                        {{-- <td class="">
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/inv-item-stock/edit/'.$stock['item_stock_id']) }}">Edit</a>
                            <a type="button" class="btn btn-outline-danger btn-sm" href="{{ url('/inv-item-stock/delete-inv-item-stock/'.$stock['item_stock_id']) }}">Hapus</a>
                        </td> --}}
                    </tr>
                    <?php $no++; $l++?>
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

@section('js')
    
@stop  