@inject('SalesInvoice','App\Http\Controllers\SalesInvoiceController' )
@extends('adminlte::page')

@section('title', 'MOZAIC Point of Sales')
@section('js')
<script>
  
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('sales-invoice') }}">Daftar Penjualan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Penjualan</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Detail Penjualan
</h3>
<br/>

    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Tambah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('sales-invoice') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php 
            // if (empty($coresection)){
            //     $coresection['section_name'] = '';
            // }
        ?>
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">No. Invoice Penjualan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $salesinvoice['sales_invoice_no'] }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Invoice Penjualan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ date('d-m-Y', strtotime($salesinvoice['sales_invoice_date'])) }}" readonly/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>


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
                                <th style='text-align:center'>No</th>
                                <th style='text-align:center'>Kategori Barang</th>
                                <th style='text-align:center'>Nama Barang</th>
                                <th style='text-align:center'>Quantity</th>
                                <th style='text-align:center'>Satuan</th>
                                <th style='text-align:center'>Harga</th>
                                <th style='text-align:center'>Subtotal</th>
                                <th style='text-align:center'>Diskon</th>
                                <th style='text-align:center'>Subtotal Setelah Diskon</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $no = 1;
                            @endphp
                                @foreach ($salesinvoiceitem as $salesinvoiceitem )
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $SalesInvoice->getCategoryName($salesinvoiceitem['item_category_id']) }}</td>
                                        <td>{{ $SalesInvoice->getItemName($salesinvoiceitem['item_id']) }}</td>
                                        <td style="text-align: right">{{ $salesinvoiceitem['quantity'] }}</td>
                                        <td>{{ $SalesInvoice->getItemUnitName($salesinvoiceitem['item_unit_id']) }}</td>
                                        <td style="text-align: right">{{ number_format($salesinvoiceitem['item_unit_price'],2,'.',',') }}</td>
                                        <td style="text-align: right">{{ number_format($salesinvoiceitem['subtotal_amount'],2,'.',',') }}</td>
                                        <td style="text-align: right">{{ $salesinvoiceitem['discount_percentage'] }}</td>
                                        <td style="text-align: right">{{ number_format($salesinvoiceitem['subtotal_amount_after_discount'],2,'.',',') }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="8">Total Barang</td>
                                    <td style="text-align: right ">{{ $salesinvoice['subtotal_item'] }}</td>
                                </tr>
                                <tr>
                                    <td colspan="8">Subtotal</td>
                                    <td style="text-align: right ">{{ number_format($salesinvoice['subtotal_amount'],2,'.',',') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="7">Diskon</td>
                                    <td style="text-align: right ">{{ $salesinvoice['discount_percentage_total'] }}</td>
                                    <td style="text-align: right ">{{ number_format($salesinvoice['discount_amount_total'],2,'.',',') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="8">Total</td>
                                    <td style="text-align: right ">{{ number_format($salesinvoice['total_amount'],2,'.',',') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="8">Bayar</td>
                                    <td style="text-align: right ">{{ number_format($salesinvoice['paid_amount'],2,'.',',') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="8">Kembalian</td>
                                    <td style="text-align: right ">{{ number_format($salesinvoice['change_amount'],2,'.',',') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="8">Tanggal Pembayaran</td>
                                    <td style="text-align: right " >{{ date('d-m-Y', strtotime($salesinvoice['sales_invoice_date'])) }}</td>
                                </tr>
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