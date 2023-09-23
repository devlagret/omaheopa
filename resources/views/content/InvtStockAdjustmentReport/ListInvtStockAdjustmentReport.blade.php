@inject('ISARC','App\Http\Controllers\InvtStockAdjustmentReportController' )

@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Laporan Stock Barang</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Laporan Stock Barang</b>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('stock-adjustment-report-filter') }}" enctype="multipart/form-data">
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
                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Kategori Barang
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            {!! Form::select('item_category_id',  $category, $category_id, ['class' => 'selection-search-clear select-form', 'id' => 'category_id', 'name' => 'category_id'] ?? '') !!}
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Gudang
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            {!! Form::select('warehouse_id',  $warehouse, $warehouse_id, ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_id', 'name' => 'warehouse_id'] ?? '') !!}
                        </div>
                    </div>
                    {{-- <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Gudang</section>
                            {!! Form::select('warehouse_id',  $warehouse, 0, ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_id', 'name' => 'warehouse_id']) !!}
                            
                        </div>
                    </div> --}}
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a href="{{ route('stock-adjustment-report-reset') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
                    <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
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
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style='text-align:center; width: 5%'>No</th>
                        <th style='text-align:center'>Nama Gudang</th>
                        <th style='text-align:center'>Kategori Barang</th>
                        <th style='text-align:center'>Nama Barang</th>
                        <th style='text-align:center'>Satuan Barang</th>
                        <th style='text-align:center'>Stok Sistem</th>
                    </tr>
                </thead>
                <tbody>
                   <?php $no =1; ?>
                   @foreach ($data as $row )
                       <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $ISARC->getWarehouseName($row['warehouse_id'] ?? '') }}</td>
                        <td>{{ $ISARC->getItemCategoryName($row['item_category_id'] ?? '') }}</td>
                        <td>{{ $ISARC->getItemName($row['item_id'] ?? '') }}</td>
                        <td>{{ $ISARC->getItemUnitName($row['item_unit_id'] ?? '') }}</td>
                        <td>{{ $ISARC->getStock($row['item_id'] ?? '',$row['item_category_id'] ?? '',$row['item_unit_id'] ?? '',$row['warehouse_id'] ?? '') }}</td>
                       </tr>
                   @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <a class="btn btn-danger" href="/stock-adjustment-report/print"> Preview</a>
            <a class="btn btn-primary" href="/stock-adjustment-report/export"><i class="fa fa-download"></i> Export Data</a>
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