@extends('adminlte::page')
@inject('carbon', 'Carbon\Carbon')

@section('title', "MOZAIC Omah'e Opa")

@section('js')
<script>
    function check(id){
        subtotal();
        loadingWidget();
        var checkout_date = $('#checkout_date').val();
        var checkout_date_old = $('#checkout_date_old').val();
        var checkin_date = $('#checkin_date').val();
        $.ajax({
                type: "POST",
                url: "{{ route('cc.check-extend') }}",
                data: {
                    'sales_order_id': id,
                    'checkin_date' : checkin_date,
                    'checkout_date' : checkout_date,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    console.log(return_data);
                    if(return_data!=0){
                        if(confirm('Kamar Sudah di Booking, Yakin Ingin Memperpanjang ?')){
                            $('#form-extend').submit();
                        }
                        return 0;
                    }
                    $('#form-extend').submit();
                   loadingWidget(0);
                   setTimeout(function() {
                    loadingWidget(0);
                   }, 200);
                },
                complete: function() {
                    loadingWidget(0);
                    setTimeout(function() {
                        loadingWidget(0);
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                    loadingWidget(0);
                    setTimeout(function() {
                        loadingWidget(0);
                    }, 200);
                }
            });
    }
    function changeDate(name = null, value = null) {
            var start_date = moment($("#checkin_date").val());
            var end_date = moment($("#checkout_date").val());
            var days = end_date.diff(start_date, 'days');
            if (days <= 0) {
                // alert("Tanggal Check-Out Tidak Boleh Sebelum Tanggal Check-In");
                $("#checkout_date").val(start_date.format('Y-MM-DD'));
                end_date = moment($("#checkout_date").val());
                days = 1;
            }
            $("#days_booked").val(days);
            subtotal();
        }

        function subtotal() {
            let sbstotal = 0;
            let total = 0;
            var days = $("#days_booked").val();
            $(".room_price_price").each(function() {
                sbstotal = Number($(this).val()) * days;
                $("#booked-room-sbs-" + $(this).data('id')).html('Rp ' + toRp(sbstotal));
                total += sbstotal;
            });
            $("#sbs-room-view").html('Rp '+toRp(total));
            $("#subtotal_all_room").val(toRp(total));
            $("#sbs-room").val(total);
            count_total();
            return total;
        }

        function count_total() {
            var subtotal_room = $("#sbs-room").val();
            var subtotal_facility = $("#sbs-facility").val();
            var subtotal_menu = $("#sbs-menu").val();
            var discount_percentage = $("#discount_percentage_total").val();
            if (discount_percentage > 100) {
                discount_percentage = 100;
                $("#discount_percentage_total").val(discount_percentage);
            }
            if (discount_percentage < 0) {
                discount_percentage = 0;
                $("#discount_percentage_total").val(discount_percentage);
            }
            sbsAll = Number(subtotal_room) + Number(subtotal_facility) + Number(subtotal_menu);
            $("#subtotal_all_view").val(toRp(sbsAll));
            $("#subtotal_all").val(sbsAll);
            diskon = (sbsAll * discount_percentage) / 100;
            $("#discount_amount_view").val(toRp(diskon));
            $("#discount_amount").val(diskon);
            if (discount_percentage == '') {
                $("#discount_amount_view").val('');
                $("#discount_amount").val('');
            }
            $("#total_amount_view").val(toRp(sbsAll - diskon));
            $("#total_amount").val(sbsAll - diskon);
            $('#change_amount_view').attr('min', sbsAll - diskon);
            if ($("#payed_amount").val() != '') {
                $("#change_amount_view").val(toRp($("#payed_amount").val() - $('#total_amount').val()));
                $("#change_amount").val($("#payed_amount").val() - $('#total_amount').val());
                $("#payed_amount_view").val(toRp($("#payed_amount").val()));
            }
        }
        $(document).ready(function() {
           changeDate();
            $("#discount_amount_view").change(function() {
                $("#discount_amount").val(this.value);
                $("#discount_amount_view").val(toRp(this.value));
                var discount_percentage = (parseInt($(this).val()) / parseInt($("#total_amount").val())) *
                    100;
                $("#discount_percentage_total").val(discount_percentage);
                count_total();
            });
            if ($("#down_payment").val() != '') {
                $("#down_payment_view").val(toRp($("#down_payment").val()));
            }
            $('#subtotal_all_view').change(function (e) {
                $('#total_amount_view').val(toRp(this.value));
                $('#total_amount').val(this.value);
                $('#subtotal_all').val(this.value);
                $(this).val(toRp(this.value));
            });
            $('#subtotal_all_menu').val(toRp($('#sbs-menu').val()));
            $('#subtotal_all_facility').val(toRp($('#sbs-facility').val()));
        });
</script>
@stop

@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cc.index') }}">Check-In dan Check-Out</a></li>
            <li class="breadcrumb-item active" aria-current="page">Perpanjangan</li>
        </ol>
    </nav>

@stop

@section('content')

    <h3 class="page-title">
        <b>Perpanjangan </b> <small>Kelola Check-In dan Check-Out </small>
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
                <button onclick="location.href='{{ route('cc.index') }}'" name="Find" class="btn btn-sm btn-info"
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
                                        Subtotal : Rp {{ number_format(is_null($data->invoice->extend_price)?(empty($data->discount)?($data->sales_order_price):($data->sales_order_price)/(100-$data->discount)*100):(empty($data->invoice->extend_discount)?($data->invoice->extend_price):($data->invoice->extend_price)/(100-$data->invoice->extend_discount)*100),2)  }}
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
                                        Total :  Rp {{ number_format($data->invoice->extend_price??$data->sales_order_price, 2) }}
                                    </h5>
                                </div>
                                <div class="row">
                                    <h5 class="col">
                                        Alamat : {{ $data->address }}
                                    </h5>
                                    <h5 class="col">
                                        Uang Muka : Rp {{ number_format($data->down_payment,2) }}
                                    </h5>
                                </div>
                                <div class="row">
                                    <h5 class="col">
                                        No Hp : {{ $data->phone_number }}
                                    </h5>
                                    @isset($data->invoice->extend_discount)
                                    <h5 class="col">
                                        Diskon Perpanjangan : {{ $data->invoice->extend_discount }} %
                                    </h5>
                                    @endisset
                                </div>
                                @isset($data->invoice->extend_price)
                                <div class="row">
                                    <h5 class="col">
                                        Diperpanjang Sebanyak : {{ $data->extend->groupBy('checkout_date_new')->get()->count() }} Kali
                                    </h5>
                                </div>
                                @endisset
                                <form action="{{ route('cc.process-extend') }}" id="form-extend" method="post">
                                    <div class="card border border-dark mt-4" id="card-total-all">
                                        <div class="card-header bg-dark clearfix">
                                            <h5 class="mb-0 float-left">
                                                Perpanjangan
                                            </h5>
                                            {{-- <div class="form-actions float-right">
                                                    <button onclick="location.href='{{ route('booking.index') }}'" name="Find" class="btn btn-sm btn-info"
                                                        title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
                                                </div> --}}
                                        </div>
                                        <div class="card-body">
                                            <div class="row justify-content-end">
                                                @csrf
                                                <input type="hidden" name="sales_order_id" id="sales_order_id"
                                                    value="{{ $data->sales_order_id }}">
                                                <input type="date" name="checkin_date" id="checkin_date"
                                                    value="{{ $data->checkin_date }}" hidden />
                                                <div class="col">
                                                <div class="col form-group form-md-line-input" id="date-out">
                                                    <section class="control-label">Tanggal Check-Out
                                                        <span class="required text-danger">
                                                            *
                                                        </span>
                                                    </section>
                                                    <input type="date"
                                                        class="form-control form-control-inline input-medium date-picker input-date"
                                                        data-date-format="dd-mm-yyyy"  name="checkout_date"
                                                        id="checkout_date" min="{{$data->checkout_date}}"
                                                        value="{{ $sessiondata['checkout_date'] ??$data->checkout_date }}"
                                                        onchange="changeDate(this.name,this.value)" style="width: 15rem;" />
                                                    <input type="text" name="checkout_date_old" id="checkout_date_old"
                                                        value="{{$data->checkout_date }}" hidden />
                                                    <input type="text" name="days_booked" id="days_booked" hidden />
                                                </div>
                                                </div>
                                            </div>
                                            <div class="row d-none">
                                                <div class="form-group">
                                                    <a class="text-dark">Malam<a class='red'> *</a></a>
                                                    <input class="form-control required input-bb" required form="form-barang"
                                                        name="night" id="night" type="number" min="0"
                                                        autocomplete="off
                                                        value="{{ $sessiondata['night'] ?? '' }}" />
                                                </div>
                                            </div>
                                            {{-- <div class="row"> --}}
                                                <div class="row mb-3">
                                                    <div class="col-3">
                                                        <a class="text-dark col-form-label">Sub Total</a>
                                                    </div>
                                                    <div class="col-auto">
                                                        :
                                                    </div>
                                                    <div class="col-8">
                                                        <input class="form-control input-bb" type="text" id="subtotal_all_view"
                                                            name="subtotal_all_view" autocomplete="off"  />
                                                        <input class="form-control input-bb" type="hidden" id="subtotal_all"
                                                            name="subtotal_all" autocomplete="off"  />
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-3">
                                                        <a class="text-dark col-form-label">Diskon (%)</a>
                                                    </div>
                                                    <div class="col-auto">
                                                        :
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="number" min="0" max="100"
                                                            class="form-control input-bb" value="{{$data->invoice->extend_discount??0}}"
                                                            id="discount_percentage_total" name="discount_percentage_total"
                                                            autocomplete="off" onchange="count_total()" />
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-3">
                                                        <a class="text-dark col-form-label">Jumlah Diskon</a>
                                                    </div>
                                                    <div class="col-auto">
                                                        :
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control input-bb"
                                                            id="discount_amount_view" name="discount_amount_view"
                                                            autocomplete="off" />
                                                        <input type="hidden" class="form-control input-bb" id="discount_amount"
                                                            name="discount_amount" autocomplete="off" />
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-3">
                                                        <a class="text-dark col-form-label">Total</a><a class='red'> *</a></a>
                                                    </div>
                                                    <div class="col-auto">
                                                        :
                                                    </div>
                                                    <div class="col-8">
                                                        <input class="form-control input-bb" id="total_amount_view"
                                                            name="total_amount_view" autocomplete="off" readonly />
                                                        <input class="form-control input-bb" id="total_amount" type="hidden"
                                                            name="total_amount" autocomplete="off" />
                                                    </div>
                                                </div>
                                            {{-- </div> --}}
                                            <br>
                                            <div class="row form-actions float-right">
                                                <button type="reset" name="Reset" class="btn btn-danger" autocomplete="off"
                                                    id="form-reset"><i class="fa fa-times"></i> Batal</button>
                                                <button type="button" name="Save" id="save-btn" class="btn btn-success button-prevent"
                                                    onclick="$(this).prop('disabled',true);check('{{ $data->sales_order_id }}');"
                                                    title="Save"><i class="fa fa-check"></i> Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

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
                                            <tr id="booked-room" data-id="{{ $val->sales_order_room_id }}">
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
                                                    {{number_format($val->room_price,2)}}
                                                    <input class="room_price_price" type="hidden"
                                                        name="room_price_price[]"
                                                        id="room_price_price_{{ $val->sales_order_room_id }}"
                                                        data-id="{{ $val->sales_order_room_id }}"
                                                        value="{{ $val->room_price }}" />
                                                </td>
                                                <td id="booked-room-sbs-{{ $val->sales_order_room_id }}">
                                                    {{number_format($val->room_price*$days,2)}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="6  " class="font-weight-bold text-center fs-4">Subtotal</td>
                                            <td colspan="2" class="font-weight-bold text-center fs-4">
                                            <h5><b id="sbs-room-view">Rp {{number_format($total,2)}}</b> -</h5>
                                            <input type="hidden" name="sbs-room" id="sbs-room"
                                            value="{{ $total }}" />
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
                                                <td>{{ number_format($fac->facility_price,2) }}</td>
                                                <td>{{ $vasl->quantity }}</td>
                                                <td>
                                                    {{number_format($vasl->quantity * $fac->facility_price,2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="font-weight-bold text-center fs-4">Subtotal</td>
                                            <td colspan="2" class="font-weight-bold text-center fs-4">
                                                <h5><b>Rp {{number_format($total2,2)}}</b> -</h5>
                                                <input type="hidden" name="sbs-facility" id="sbs-facility"
                                                value="{{ $total2 }}" />
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
                                <table  style="width:100%"
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
                                                <td>{{number_format($men->room_menu_price,2) }}</td>
                                                <td>{{ $vals->quantity }}</td>
                                                <td>
                                                    {{number_format($vals->quantity* $men->room_menu_price,2)}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="font-weight-bold text-center fs-4">Subtotal</td>
                                            <td colspan="2" class="font-weight-bold text-center fs-4">
                                                <h5><b>Rp {{number_format($total3,2)}}</b> -</h5>
                                                <input type="hidden" name="sbs-menu" id="sbs-menu"
                                                value="{{ $total3 }}" />
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