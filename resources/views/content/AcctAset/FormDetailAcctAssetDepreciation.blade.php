
@inject('Asset', 'App\Http\Controllers\AcctAssetController')
@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
    <script>
        function function_elements_add(name, value) {

            $.ajax({
                type: "POST",
                url: "{{ route('aset-type.elements-add') }}",
                data: {
                    'name': name,
                    'value': value,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(msg) {
                    console.log(msg);
                }
            });
        }
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ url('aset') }}">Daftar Asset</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail Aset</li>
        </ol>
    </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Penyusutan Aset
</h3>
<br/>

    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Detail Formulir
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('aset') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Type Asset<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $acctasset['asset_type_id'] }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Metode Penyusutan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $acctasset['asset_type_id'] }}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $acctasset['asset_name'] }}" readonly/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Penyusutan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $acctasset['asset_depreciation_date'] }}" readonly/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">Satuan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $acctasset['item_unit_code'] }}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Metode Penyusutan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $acctasset['asset_depreciation_type'] }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Taksiran Usia<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $acctasset['asset_depreciation_duration'] }}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nilai Perolehan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $acctasset['asset_depreciation_book_value'] }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nilai Residu<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $acctasset['asset_type_id'] }}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Mulai Bulan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $acctasset['asset_type_id'] }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Awal tahun<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $acctasset['asset_type_id'] }}" readonly/>
                    </div>
                </div>
            </div>
             <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Bulan Akhir<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $acctasset['asset_type_id'] }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Akhir Tahun<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier" type="text" autocomplete="off" value="{{ $acctasset['asset_depreciation_end_year'] }}" readonly/>
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
                                <th style='text-align:center'>Nomor. </th>
                                <th style='text-align:center'>Tahun Ke</th>
                                <th style='text-align:center'>Bulan Penyusutan</th>
                                <th style='text-align:center'>Tahun Penyusutan</th>
                                <th style='text-align:center'>Biaya Penyusutan</th>
                                <th style='text-align:center'>Akm. Penyusutan</th>
                                <th style='text-align:center'>Nilai Buku</th>
                                <th style='text-align:center'>Jurnal</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @php
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
                                </tr> --}}
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
