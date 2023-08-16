@inject('SIBYRC','App\Http\Controllers\SalesInvoicebyYearReportController' )

@extends('adminlte::page')

@section('title', 'MOZAIC Point of Sales')

@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Laporan Penjualan Tahunan</li>
    </ol>
</nav>

@stop

@section('content')
<h3 class="page-title">
    <b>Laporan Penjualan Tahunan</b>
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
  </div>
  <form action="{{ route('filter-sales-invoice-by-year-report') }}" method="post">
    @csrf
    <div class="col-md-3 mt-4 ml-2">
        {!! Form::select(0, $yearlist, $year, ['class' => 'selection-search-clear select-form', 'id' => 'year_period', 'name' => 'year_period']) !!}
    <button type="submit" class="btn btn-success mt-3 btn-sm">Cari</button>
    </div>
  </form>
    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style='text-align:center; width: 5%'>No</th>
                        <th style='text-align:center; width: 30%'>Nama Kategori</th>
                        <th style='text-align:center; width: 30%'>Nama Barang</th>
                        <th style='text-align:center; width: 15%'>Jumlah Penjualan</th>
                        <th style='text-align:center; width: 15%'>Total</th>
                    </tr>
                </thead>
                <tbody>
                  <?php $no=1; ?>
                    @foreach ($data as $val)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $SIBYRC->getCategoryName($val['item_category_id']) }}</td>
                            <td>{{ $SIBYRC->getItemName($val['item_id']) }}</td>
                            <td style="text-align: right">{{ $val['quantity'] }}</td>
                            <td style="text-align: right">{{ number_format($val['subtotal_amount_after_discount'],2,'.',',') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <a class="btn btn-danger" href="/sales-invoice-by-year-report/print"> Preview</a>
            <a class="btn btn-primary" href="/sales-invoice-by-year-report/export"><i class="fa fa-download"></i> Export Data</a>
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