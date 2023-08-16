@extends('adminlte::page')

@section('title', 'MOZAIC Point of Sales')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Laporan Laba/Rugi </li>
    </ol>
  </nav>

@stop

@section('content')
<h3 class="page-title">
    <b>Laporan Perhitungan Laba/Rugi</b>
</h3>
<br/>
<div id="accordion">
    <form action="{{ route('filter-profit-loss-report') }}" method="post">
        @csrf
        <div class="card border border-dark">
            <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <h5 class="mb-0">
                    Filter
                </h5>
            </div>
            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    <div class="row">
                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input">
                                <section class="control-label">Tanggal Awal
                                    <span class="required text-danger">
                                        *
                                    </span>
                                </section>
                                <input style="width: 50%" class="form-control input-bb" name="start_date" id="start_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $start_date }}"/>
                            </div>
                        </div>

                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input">
                                <section class="control-label">Tanggal Akhir
                                    <span class="required text-danger">
                                        *
                                    </span>
                                </section>
                                <input style="width: 50%" class="form-control input-bb" name="end_date" id="end_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $end_date }}"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <div class="form-actions float-right">
                        <a href="{{ route('reset-filter-profit-loss-report') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
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
        <div class="table-responsive pt-5">
            <table id="" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <td colspan='2' style='text-align:center;'>
                            <div style='font-weight:bold'>Laporan Perhitungan Laba/Rugi
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2' style='text-align:center;'>
                            <div>
                                Period {{ date('d-m-Y', strtotime($start_date)) }} s.d. {{ date('d-m-Y', strtotime($end_date)) }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2'></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th colspan="2">Pendapatan</th>
                    </tr>
                    <tr>
                        <td style="width: 80%">&nbsp&nbsp&nbsp&nbsp&nbsp Penjualan Produk</td>
                        <td style="width: 20%; text-align: right">{{ number_format($total_sales_amount,2,'.',',') }}</td>
                    </tr>
                    <tr>
                        <th style="width: 80%">Total Pendapatan</th>
                        <th style="width: 20%; text-align: right">{{ number_format($total_sales_amount,2,'.',',') }}</th>
                    </tr>
                    <tr>
                        <td colspan='2'></td>
                    </tr>
                    <tr>
                        <th colspan="2">Pengeluaran</th>
                    </tr>
                    <tr>
                        <td style="width: 80%">&nbsp&nbsp&nbsp&nbsp&nbsp Pembelian Produk</td>
                        <td style="width: 20%; text-align: right">{{ number_format($total_purchase_amount,2,'.',',') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 80%">&nbsp&nbsp&nbsp&nbsp&nbsp Pengeluaran Lainya</td>
                        <td style="width: 20%; text-align: right">{{ number_format($total_expenditure_amount,2,'.',',') }}</td>
                    </tr>
                    <tr>
                        <?php $subtotal_expenditure = $total_purchase_amount + $total_expenditure_amount ?>
                        <th style="width: 80%">Total Pengeluaran</th>
                        <th style="width: 20%; text-align: right">{{ number_format($subtotal_expenditure,2,'.',',') }}</th>
                    </tr>
                    <tr>
                        <td colspan='2'></td>
                    </tr>
                    <tr>
                        <?php $subtotal_difference = $total_sales_amount - $subtotal_expenditure ?>
                        <th style="width: 80%">Selisih</th>
                        <th style="width: 20%; text-align: right">{{ number_format($subtotal_difference,2,'.',',') }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="text-muted mt-3">
            <div class="form-actions float-right">
                <a class="btn btn-danger" href="/profit-loss-report/print"> Preview</a>
                <a class="btn btn-primary" href="/profit-loss-report/export"><i class="fa fa-download"></i> Export Data</a>
            </div>
        </div>
        {{-- <div id="tahun" class="tab-pane fade">
            <form action="{{ route('filter-profit-loss-report') }}" method="post">
                @csrf
                <div class="row mt-5">
                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Awal
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input style="width: 50%" class="form-control input-bb" name="start_date" id="start_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $start_date }}"/>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Akhir
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input style="width: 50%" class="form-control input-bb" name="end_date" id="end_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $end_date }}"/>
                        </div>
                    </div>
                </div>
                <div class="text-muted mt-3 pr-5">
                    <div class="form-actions float-right">
                        <a href="{{ route('reset-filter-profit-loss-report') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
                        <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive pt-5">
                <table id="" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                    <thead>
                        <tr>
                            <td colspan='2' style='text-align:center;'>
                                <div style='font-weight:bold'>Laporan Perhitungan Laba/Rugi
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan='2' style='text-align:center;'>
                                <div>
                                    Period {{ date('d-m-Y', strtotime($start_date)) }} s.d. {{ date('d-m-Y', strtotime($end_date)) }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan='2'></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th colspan="2">Pendapatan</th>
                        </tr>
                        <tr>
                            <td style="width: 80%">&nbsp&nbsp&nbsp&nbsp&nbsp Penjualan Produk</td>
                            <td style="width: 20%; text-align: right">{{ number_format($total_sales_amount,2,'.',',') }}</td>
                        </tr>
                        <tr>
                            <th style="width: 80%">Total Pendapatan</th>
                            <th style="width: 20%; text-align: right">{{ number_format($total_sales_amount,2,'.',',') }}</th>
                        </tr>
                        <tr>
                            <td colspan='2'></td>
                        </tr>
                        <tr>
                            <th colspan="2">Pengeluaran</th>
                        </tr>
                        <tr>
                            <td style="width: 80%">&nbsp&nbsp&nbsp&nbsp&nbsp Pembelian Produk</td>
                            <td style="width: 20%; text-align: right">{{ number_format($total_purchase_amount,2,'.',',') }}</td>
                        </tr>
                        <tr>
                            <td style="width: 80%">&nbsp&nbsp&nbsp&nbsp&nbsp Pengeluaran Lainya</td>
                            <td style="width: 20%; text-align: right">{{ number_format($total_expenditure_amount,2,'.',',') }}</td>
                        </tr>
                        <tr>
                            <?php $subtotal_expenditure = $total_purchase_amount + $total_expenditure_amount ?>
                            <th style="width: 80%">Total Pengeluaran</th>
                            <th style="width: 20%; text-align: right">{{ number_format($subtotal_expenditure,2,'.',',') }}</th>
                        </tr>
                        <tr>
                            <td colspan='2'></td>
                        </tr>
                        <tr>
                            <?php $subtotal_difference = $total_sales_amount - $subtotal_expenditure ?>
                            <th style="width: 80%">Selisih</th>
                            <th style="width: 20%; text-align: right">{{ number_format($subtotal_difference,2,'.',',') }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-muted mt-3">
                <div class="form-actions float-right">
                    <a class="btn btn-danger" href="/profit-loss-report/print"> Preview</a>
                    <a class="btn btn-primary" href="/profit-loss-report/export"><i class="fa fa-download"></i> Export Data</a>
                </div>
            </div>
        </div> --}}
  </div>
</div>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop

@section('js')
    
@stop   