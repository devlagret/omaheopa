@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")

@section('js')
<script>
    function check(name,uri){
      if(confirm(`Yakin Ingin Menghapus Booking Atas Nama '`+name+`' ?`)){
        window.location.href = uri;
      }
    }
    </script>
@stop

@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Booking </li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Booking </b> <small>Kelola Booking  </small>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('booking.filter') }}" enctype="multipart/form-data">
    @csrf
        <div class="card border border-dark">
        <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                Filter
            </h5>
        </div>

        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class="row ">
                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Mulai
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="{{ $start_date ?? date('Y-m-d')}}" style="width: 15rem;"/>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Akhir
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="{{ $end_date ?? date('Y-m-d')}}" style="width: 15rem;"/>
                        </div>
                    </div>
                    {{--<div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Gudang
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <select class="form-control"  type="text" name="end_date" id="end_date" onChange="function_elements_add(this.name, this.value);" value="">
                                <option value=""></option>
                            </select>
                        </div>
                    </div> --}}
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a href="{{ route('list-reset-stock-adjustment') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
                    <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
                </div>
            </div>
        </div>
        </div>
    </form>
</div>
<br/>
@if (session('msg'))
<div class="alert alert-{{session('type')??'info'}}" role="alert">
    {{ session('msg') }}
</div>
@endif
@if (count($errors) > 0)
<div class="alert alert-danger" role="alert">
    @foreach ($errors->all() as $error)
        {{ $error }}
    @endforeach
</div>
@endif
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar
    </h5>
    <div class="form-actions float-right">
        <button onclick="location.href='{{ route('booking.add') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Booking</button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table style="width:100%" class="table datatables table-striped table-bordered table-hover table-full-width display nowarp">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 3%">No </th>
                        <th style="text-align: center; width: 9%">Tgl Check-In</th>
                        <th style="text-align: center; width: 10%">Tgl Check-Out</th>
                        <th style="text-align: center; width: 10%">Atas Nama</th>
                        <th style="text-align: center; width: 10%">No Hp</th>
                        <th style="text-align: center; width: 10%">Kamar Dipesan</th>
                        <th style="text-align: center; width: 9%">Uang Muka</th>
                        <th style="text-align: center; width: 9%">Subtotal</th>
                        <th style="text-align: center; width: 9%">Perpanjangan</th>
                        <th style="text-align: center; width: 9%">Total</th>
                        <th style="text-align: center; width: 15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                  @foreach ($booking as $row)
                      <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ $row->checkin_date }}</td>
                        <td class="text-center">{{ $row->checkout_date }}</td>
                        <td>{{ $row->sales_order_name }}</td>
                        <td class="text-center">{{ $row->phone_number }}</td>
                        <td class="text-center">{{ $row->rooms->count() }}</td>
                        <td>{{ number_format($row->down_payment) }}</td>
                        <td>{{ number_format($row->sales_order_price) }}</td>
                        <td>{{ is_null($row->invoice)?'-':number_format($row->invoice->extend_price-$row->sales_order_price) }}</td>
                        <td>{{ number_format($row->invoice->extend_price??$row->sales_order_price) }}</td>
                        <td style="text-align: center">
                            @if (!$row->sales_order_status)
                          <a type="button" class="btn btn-outline-primary my-1 btn-sm" href="{{ route('booking.rescedule',$row->sales_order_id) }}">Rescedule</a>
                          <a type="button" class="btn btn-outline-warning my-1 btn-sm" href="{{ route('booking.edit',$row->sales_order_id) }}">Edit</a>
                          <a type="button" class="btn btn-outline-danger my-1 btn-sm" onclick="check('{{ $row->sales_order_name}}', '{{route('booking.delete',$row->sales_order_id)}}')">Hapus</a>
                          @else
                          @if($row->sales_order_type==0)
                          <div class="px-1 rounded-pill mx-auto bg-info mb-2" style="font-size:0.9rem;"> Sudah Bayar Uang Muka </div>
                          @else
                          <div class="text-center px-auto w-75 rounded-pill mx-auto bg-info" style="font-size:0.9rem;">Langsung Check-In</div>
                          @endif
                          @endif
                            <a type="button" class="btn btn-outline-dark my-1 btn-sm" href="{{ route('booking.detail',$row->sales_order_id) }}">Detail</a>
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