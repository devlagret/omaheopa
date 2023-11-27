@inject('SalesInvoice', 'App\Http\Controllers\SalesInvoiceController')

@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
    <script>
        function reset_add() {
            $.ajax({
                type: "GET",
                url: "{{ route('filter-reset-sales-reservation') }}",
                success: function(msg) {
                    location.reload();
                }

            });
        }
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item active" aria-current="page">Daftar Reservasi</li>
        </ol>
    </nav>

@stop
@section('content')

    <h3 class="page-title">
        <b>Daftar Reservasi</b> <small>Kelola Reservasi </small>
    </h3>
    <br />
    @if (session('msg'))
        <div class="alert alert-info" role="alert">
            {{ session('msg') }}
        </div>
    @endif
    <div id="accordion">
        <form method="post" action="{{ route('filter-sales-reservation') }}" enctype="multipart/form-data">
            @csrf
            <div class="card border border-dark">
                <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne"
                    aria-expanded="true" aria-controls="collapseOne">
                    <h5 class="mb-0">
                        Filter
                    </h5>
                </div>

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <div class = "row">
                            <div class = "col-md-6">
                                <div class="form-group form-md-line-input">
                                    <section class="control-label">Tanggal Mulai
                                        <span class="required text-danger">
                                            *
                                        </span>
                                    </section>
                                    <input type ="date"
                                        class="form-control form-control-inline input-medium date-picker input-date"
                                        data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date"
                                        value="{{ $start_date ?? '' }}" style="width: 15rem;" />
                                </div>
                            </div>

                            <div class = "col-md-6">
                                <div class="form-group form-md-line-input">
                                    <section class="control-label">Tanggal Akhir
                                        <span class="required text-danger">
                                            *
                                        </span>
                                    </section>
                                    <input type ="date"
                                        class="form-control form-control-inline input-medium date-picker input-date"
                                        data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date"
                                        value="{{ $end_date ?? '' }}" style="width: 15rem;" />
                                </div>
                            </div>

                            {{-- <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Status Pembayaran
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <select  class="form-control "  type="text" name="" id=""  value="" >
                                <option value=""></option>
                            </select>
                        </div>
                    </div> --}}

                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <div class="form-actions float-right">
                            <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i
                                    class="fa fa-times"></i> Batal</button>
                            <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i
                                    class="fa fa-search"></i> Cari</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <br />

    <div class="card border border-dark">
        <div class="card-header bg-dark clearfix">
            <h5 class="mb-0 float-left">
                Daftar
            </h5>
            <div class="form-actions float-right">
                <button onclick="location.href='{{ url('/sales-reservation/add') }}'" name="Find"
                    class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Reservasi</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="example" style="width:100%"
                    class="table table-striped table-bordered table-hover table-full-width">
                    <thead>
                        <tr>
                            <th width="2%" style='text-align:center'>No</th>
                            <th width="15%" style='text-align:center'>Tanggal Reservasi</th>
                            <th width="15%" style='text-align:center'>Jatuh Tempo</th>
                            <th width="15%" style='text-align:center'>Nomor Reservasi</th>
                            <th width="15%" style='text-align:center'>Marketing</th>

                            <th width="15%" style='text-align:center'>Pelanggan</th>
                            <th width="15%" style='text-align:center'>DP</th>
                            <th width="15%" style='text-align:center'>Sisa</th>
                            <th width="15%" style='text-align:center'>Subtotal</th>
                            <th width="10%" style='text-align:center'>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        @foreach ($data as $row)
                            <tr>
                                <td style='text-align:center'>{{ $no++ }}</td>
                                <td>{{ $row['sales_invoice_reservation_date'] ?? '' }}</td>
                                <td>{{ $row['sales_invoice_reservation_due_date'] ?? '' }}</td>
                                <td>{{ $row['sales_invoice_reservation_no'] ?? '' }}</td>
                                <td>{{ $row['sales_name'] ?? '' }}</td>
                                <td>{{ $row['customer_name'] ?? '' }}</td>
                                <td style="text-align: right">{{ $row['paid_amount'] ?? '' }}</td>
                                <td style="text-align: right">{{ $row['owing_amount'] ?? '' }}</td>
                                <td style="text-align: right">{{ $row['total_amount'] ?? '' }}</td>
                                <td class="text-center">
                                    <?php if($row['sales_invoice_reservation_status'] == 0){ ?>
                                    <?php if($row['owing_amount'] > 0){ ?>
                                    <a type="button" class="btn btn-outline-primary btn-sm"
                                        href="{{ url('/reservation-payment/select-customer/' . $row['sales_invoice_reservation_id'] ?? '') }}">Bayar</a>
                                    <?php } ?>
                                    <a type="button" class="btn btn-outline-warning btn-sm"
                                        href="{{ url('/sales-reservation/detail/' . $row['sales_invoice_reservation_id'] ?? '') }}">Detail</a>
                                    <a href="{{ route('delete-sales-reservation', ['sales_invoice_reservation_id' => $row['sales_invoice_reservation_id']]) }}"
                                        name='Reset' class='btn btn-outline-danger btn-sm'
                                        onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini ?')"></i>
                                        Hapus</a>
                                    <?php if($row['owing_amount'] > 0){ ?>
                                    <a href="{{ route('reject-sales-reservation', ['sales_invoice_reservation_id' => $row['sales_invoice_reservation_id']]) }}"
                                        name='Reset' class='btn btn-outline-secondary btn-sm'
                                        onclick="return confirm('Apakah Anda Yakin Ingin membatalkan Reservasi ?')"></i>
                                        Batal</a>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if($row['sales_invoice_reservation_status'] == 2){ ?>
                                        <a type="button" class="btn btn-danger btn-sm">Dibatalkan</a>
                                    <?php } ?>
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
