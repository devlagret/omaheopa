@extends('adminlte::page')
@inject('carbon', 'Carbon\Carbon')

@section('title', "MOZAIC Omah'e Opa")

@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('booking.index') }}">Daftar Booking</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail Booking</li>
        </ol>
    </nav>

@stop

@section('content')

    <h3 class="page-title">
        <b>Detail Booking Booking </b> <small>Kelola Booking </small>
    </h3>

    @if (session('msg'))
        <div class="alert alert-{{ session('type') ?? 'info' }}" role="alert">
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
                Detail Booking
            </h5>
            <div class="form-actions float-right">
                <button onclick="location.href='{{ route('booking.index') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>

        <div class="list-booked" id="list-booked">
            <div class="card-body">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <h5 class="col">
                                Atas Nama : {{ $data->sales_order_name }}
                            </h5>
                            <h5 class="col">
                                Subtotal : Rp {{ number_format(empty($data->discount)?$data->sales_order_price:$data->sales_order_price/(100-$data->discount)*100,2)  }}
                            </h5>
                        </div>
                        <div class="row">
                            <h5 class="col">
                                Tanggal Check-In : {{ date('d-m-Y', strtotime($data->checkin_date)) }}
                            </h5>
                            <h5 class="col">
                                Diskon : {{ $data->discount??0 }} %
                            </h5>
                        </div>
                        <div class="row">
                            <h5 class="col">
                                Tanggal Check-Out : Rp {{ date('d-m-Y', strtotime($data->checkout_date)) }}
                            </h5>
                            <h5 class="col">
                                Total :  {{ number_format($data->sales_order_price,2) }}
                            </h5>
                        </div>
                        <div class="row">
                            <h5 class="col">
                                Uang Muka : Rp {{ number_format($data->down_payment,2) }}
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="card border border-dark">
                    <div class="card-header bg-dark clearfix" id="heading-booked-room" data-toggle="collapse"
                        data-target="#daftar-booked-room" aria-expanded="true" aria-controls="daftar-booked-room">
                        <h5 class="mb-0 float-left">
                            Daftar Kamar yang Dibooking
                        </h5>
                    </div>
                    <div id="daftar-booked-room" class="collapse show" aria-labelledby="heading-booked-room"
                        data-parent="#list-booked">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example" style="width:100%"
                                    class="table table-striped table-bordered table-hover table-full-width">
                                    <thead>
                                        <tr>
                                            <th width="2%" style='text-align:center'>No</th>
                                            <th width="15%" style='text-align:center'>Nama Kamar</th>
                                            <th width="15%" style='text-align:center'>Tipe Kamar</th>
                                            <th width="15%" style='text-align:center'>Bangunan</th>
                                            <th width="13%" style='text-align:center'>Jumlah Orang</th>
                                            <th width="15%" style='text-align:center'>Tipe Harga</th>
                                            <th width="15%" style='text-align:center'>Harga Kamar</th>
                                            <th width="20%" style='text-align:center'>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1;$total=0; $days= $carbon::parse($data->checkin_date)->diffInDays($carbon::parse($data->checkout_date));?>
                                        @foreach ($data->rooms as $val)
                                            <tr>
                                                @php
                                                    $rooms = $room->where('room_id', $val->room_id)->first();
                                                    $total += $val->room_price*$days;
                                                @endphp
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $rooms->room_name }}</td>
                                                <td>{{ $rooms->roomType->room_type_name }}</td>
                                                <td>{{ $rooms->building->building_name }}</td>
                                                <td>{{ $val->people }}</td>
                                                <td>
                                                    {{$val->price_type_name_old}}
                                                </td>
                                                <td>
                                                    {{$val->room_price}}
                                                </td>
                                                <td>
                                                    {{number_format($val->room_price*$days,2)}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="6  " class="font-weight-bold text-center fs-4">Subtotal</td>
                                            <td colspan="2" class="font-weight-bold text-center fs-4">
                                            <h5><b>Rp {{number_format($total,2)}}</b> -</h5>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border border-dark">
                    <div class="card-header bg-dark clearfix" id="heading-booked-facility" data-toggle="collapse"
                        data-target="#daftar-booked-facility" aria-expanded="true" aria-controls="daftar-booked-facility">
                        <h5 class="mb-0 float-left">
                            Daftar Fasilitas yang Dipesan
                        </h5>
                        <div class="form-actions float-right">
                        </div>
                    </div>
                    <div id="daftar-booked-facility" class="collapse show" aria-labelledby="heading-booked-facility"
                        data-parent="#list-booked">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example" style="width:100%"
                                    class="table table-striped datatables table-bordered table-hover table-full-width">
                                    <thead>
                                        <tr>
                                            <th width="2%" style='text-align:center'>No</th>
                                                <th width="15%" style='text-align:center'>Nama Fasilitas</th>
                                                <th width="15%" style='text-align:center'>Keterangan</th>
                                                <th width="15%" style='text-align:center'>Harga</th>
                                                <th width="5%" style='text-align:center'>Jumlah</th>
                                                <th width="10%" style='text-align:center'>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; $total2=0; ?>
                                        @foreach ($data->facilities as $vasl)
                                            <tr>
                                                @php
                                                    $fac = $facility->find($vasl->room_facility_id);
                                                    $total2 += $vasl->quantity* $fac->facility_price;
                                                @endphp
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $fac->facility_name }}</td>
                                                <td>{{ $fac->facility_remark }}</td>
                                                <td>{{ $fac->facility_price }}</td>
                                                <td>{{ $vasl->quantity }}</td>
                                                <td>
                                                    {{$vasl->quantity* $data->facility_price }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="font-weight-bold text-center fs-4">Subtotal</td>
                                            <td colspan="2" class="font-weight-bold text-center fs-4">
                                                <h5><b>Rp {{number_format($total2,2)}}</b> -</h5>

                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border border-dark">
                    <div class="card-header bg-dark clearfix" id="heading-booked-menu" data-toggle="collapse"
                        data-target="#daftar-booked-menu" aria-expanded="true" aria-controls="daftar-booked-menu">
                        <h5 class="mb-0 float-left">
                            Daftar Menu yang Dipesan
                        </h5>
                        <div class="form-actions float-right">
                        </div>
                    </div>
                    <div id="daftar-booked-menu" class="collapse show" aria-labelledby="heading-booked-menu"
                        data-parent="#list-booked">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example" style="width:100%"
                                    class="table table-striped datatables table-bordered table-hover table-full-width">
                                    <thead>
                                        <tr>
                                            <th width="3%" style='text-align:center'>No</th>
                                            <th width="20%" style='text-align:center'>Tipe Menu</th>
                                            <th width="20%" style='text-align:center'>Nama Menu</th>
                                            <th width="20%" style='text-align:center'>Harga</th>
                                            <th width="20%" style='text-align:center'>Jumlah</th>
                                            <th width="20%" style='text-align:center'>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; $total3=0; ?>
                                        @foreach ($data->menus as $vals)
                                            <tr>
                                                 @php
                                                    $men = $menu->find($vals->room_menu_id);
                                                    $total3 += $vals->quantity* $men->room_menu_price;
                                                @endphp
                                                <td>{{ $no++ }}</td>
                                                <td>{{$menutype[$men->room_menu_type] }}</td>
                                                <td>{{ $men->room_menu_name }}</td>
                                                <td>{{ $men->room_menu_price }}</td>
                                                <td>{{ $vals->quantity }}</td>
                                                <td>
                                                    {{$vals->quantity* $men->room_menu_price}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="font-weight-bold text-center fs-4">Subtotal</td>
                                            <td colspan="2" class="font-weight-bold text-center fs-4">
                                                <h5><b>Rp {{number_format($total3,2)}}</b> -</h5>

                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions float-right">
                    <button onclick="location.href='{{ route('booking.index') }}'" name="Find" class="btn btn-sm btn-info"
                        title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
                </div>
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
