@inject('SalesInvoiceReservation','App\Http\Controllers\SalesReservationController' )
@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")
@section('js')
<script>
  
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('sales-reservation') }}">Daftar Reservasi</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Reservasi</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Detail Reservasi
</h3>
<br/>

    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Tambah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('sales-reservation') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">No. Invoice Reservasi<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="" id="" type="text" autocomplete="off" value="{{ $salesinvoicereservation['sales_invoice_reservation_no'] }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Invoice Reservasi<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="" id="" type="text" autocomplete="off" value="{{ date('d-m-Y', strtotime($salesinvoicereservation['sales_invoice_reservation_date'])) }}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Pelanggan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="" id="" type="text" autocomplete="off" value="{{ $salesinvoicereservation['customer_name'] }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">No. HP<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="" id="" type="text" autocomplete="off" value="{{ $salesinvoicereservation['customer_phone'] }}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Alamat<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="" id="" type="text" autocomplete="off" value="{{ $salesinvoicereservation['customer_address'] }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Jatuh Tempo<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="" id="" type="text" autocomplete="off" value="{{ $salesinvoicereservation['sales_invoice_reservation_due_date'] }}" readonly/>
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
                                <th style='text-align:center'>Item</th>
                                <th style='text-align:center'>Quantity</th>
                                <th style='text-align:center'>Harga</th>
                                <th style='text-align:center'>Total</th>
                                <th style='text-align:center'>Diskon</th>
                                <th style='text-align:center'>Subtotal Setelah Diskon</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $no = 1;
                            @endphp
                                @foreach ($salesinvoicereservationitem as $salesinvoicereservationitem )

                                @php
                                    $subtotal =  $salesinvoicereservationitem['quantity']  * $SalesInvoiceReservation->getReservationPrice($salesinvoicereservationitem['reservation_id']);
                                    $subtotalafdiscount =  $salesinvoicereservationitem['quantity']  * $salesinvoicereservationitem['item_unit_price'] - $salesinvoicereservationitem['discount_percentage'] ;
                                @endphp
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $SalesInvoiceReservation->getReservationName($salesinvoicereservationitem['reservation_id']) }}</td>
                                        <td style="text-align: right">{{ $salesinvoicereservationitem['quantity'] }}</td>
                                        <td>{{ $SalesInvoiceReservation->getReservationPrice($salesinvoicereservationitem['reservation_id']) }}</td>
                                        <td style="text-align: right">{{ number_format($subtotal,2,'.',',') }}</td>
                                        <td style="text-align: right">{{ $salesinvoicereservationitem['discount_percentage'] }}</td>
                                        <td style="text-align: right">{{ number_format($subtotalafdiscount,2,'.',',') }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6">Total Barang</td>
                                    <td style="text-align: right ">{{ $salesinvoicereservation['subtotal_item'] }}</td>
                                </tr>
                                <tr>
                                    <td colspan="6">Subtotal</td>
                                    <td style="text-align: right ">{{ number_format($salesinvoicereservation['subtotal_amount'],2,'.',',') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5">Diskon</td>
                                    <td style="text-align: right ">{{ $salesinvoicereservation['discount_percentage_total'] }}</td>
                                    <td style="text-align: right ">{{ number_format($salesinvoicereservation['discount_amount_total'],2,'.',',') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="6">Total</td>
                                    <td style="text-align: right ">{{ number_format($salesinvoicereservation['total_amount'],2,'.',',') }}</td>
                                </tr>
                                <tr class="bg-secondary">
                                    <td colspan="6">DP</td>
                                    <td style="text-align: right ">{{ number_format($salesinvoicereservation['paid_amount'],2,'.',',') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="6">SISA</td>
                                    <td style="text-align: right ">{{ number_format($salesinvoicereservation['owing_amount'],2,'.',',') }}</td>
                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>


<div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Detail Pembayaran
        </h5>
    </div>
        <div class="card-body">
            <div class="form-body form">
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center'>No</th>
                                <th style='text-align:center'>Tanggal bayar</th>
                                <th style='text-align:center'>Jumlah bayar</th>
                                <th style='text-align:center'>Sisa Hutang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($paymentreservation == null)
                                    <tr><th colspan='4' style='text-align  : center !important;'>Data Kosong</th></tr>
                            @else
                            @php
                            $no = 1;
                            @endphp
                                @foreach ($paymentreservation AS $key => $val)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $val['payment_date'] }}</td>
                                        <td style="text-align: right">{{ $val['payment_amount'] }}</td>
                                        <td style="text-align: right">{{ $val['rounding_amount'] }}</td>
                                    </tr>
                                @endforeach
                            @endif
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