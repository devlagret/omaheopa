@extends('adminlte::page')
<?php
if (empty($items)) {
    $items['item_code'] = '';
    $items['item_name'] = '';
    $items['item_barcode'] = '';
    $items['item_remark'] = '';
    $items['item_quantity'] = '';
    $items['item_price'] = '';
    $items['item_cost'] = '';
    $items['package_item_id'] = 1;
    $items['kemasan'] = 1;
    $items['max_kemasan'] = 4;
}
if (empty($paket)) {
    $paket = [];
}
?>
@section('title', "MOZAIC Omah'e Opa")
@section('js')
    <script>
        function function_elements_add(name, value) {
            $.ajax({
                type: "POST",
                url: "{{ route('booking.elements-add') }}",
                data: {
                    'name': name,
                    'value': value,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(msg) {},
                error: function(data) {
                    console.log(data);
                }
            });
        }
        var index = {{ $sessiondata['tab-index'] ?? 1 }};

        function next() {
            index++;
            function_elements_add('tab-index', index)
            $('#navigator-booking li:nth-child(' + index + ') a').tab('show');
        }

        function preft() {
            index--;
            function_elements_add('tab-index', index)
            $('#navigator-booking li:nth-child(' + index + ') a').tab('show');
        }

        function disableNav() {
            loadingWidget();
            $(".booking-nav").each(function() {
                $(this).prop('disabled', true);
            });
        }

        function enableNav() {
            loadingWidget(0);
            $(".booking-nav").each(function() {
                $(this).prop('disabled', false);
            });
        }

        function changeType() {
            loading();
            var building_id = $("#building_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('booking.get-room-type') }}",
                dataType: "html",
                data: {
                    'building_id': building_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    function_elements_add('building_id', building_id);
                    $('#room_type_id').html(return_data);
                    changeRoom($('#room_type_id').val());
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function changeRoom(room_type_id) {
            loading();
            var building_id = $("#building_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('booking.get-room') }}",
                dataType: "html",
                data: {
                    'room_type_id': room_type_id,
                    'building_id': building_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    function_elements_add('room_type_id', room_type_id);
                    $('#room_id').html(return_data);
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 20);
                },
                complete: function() {
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                }
            });
        }

        function addRoom() {
            var room_id = $("#room_id").val();
            if ($('.room-' + room_id).length) {
                // $('#item_package_' + room_id).val(function(i, oldval) {
                //     var newval = ++oldval;
                //     changeHowManyPerson(room_id, newval);
                //     return newval;
                // });
                return 0;
            }
            loading();
            $.ajax({
                type: "POST",
                url: "{{ route('booking.add-room') }}",
                dataType: "html",
                data: {
                    'no': $('.booked-room').length,
                    'room_id': room_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    if ($('.booked-room').length == 0) {
                        $('#room-table').html(return_data);
                    } else {
                        $('#room-table').append(return_data);
                    }
                    $(".select-form").each(function() {
                        $(this).select2();
                    });
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 20);
                },
                complete: function() {
                    subtotal()
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                }
            });
        }

        function clearBooked() {
            $.ajax({
                type: "get",
                url: "{{ route('booking.clear-booked') }}",
                dataType: "html",
                success: function(return_data) {
                    $('.booked-room').each(function(index) {
                        $(this).remove();
                    });
                    $('#room-table').html(
                        '<td valign="top" colspan="7" class="dataTables_empty">No data available in table</td>'
                    );
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function changeHowManyPerson(id, qty) {
            loadingWidget();
            disableNav();
            $.ajax({
                type: "POST",
                url: "{{ route('booking.add-person') }}",
                dataType: "html",
                data: {
                    'id': id,
                    'qty': qty,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    loadingWidget(0);
                    setTimeout(function() {
                        enableNav();
                        loadingWidget(0);
                    }, 100);
                    enableNav();
                },
                complete: function() {
                    loadingWidget(0);
                    setTimeout(function() {
                        loadingWidget(0);
                        enableNav();
                    }, 200);
                    enableNav();
                },
                error: function(data) {
                    console.log(data);
                    loadingWidget(0);
                    setTimeout(function() {
                        loadingWidget(0);
                        enableNav();
                    }, 200);
                }
            });
        }

        function changePrice(name, room_price_id = null) {
            disableNav();
            var room_id = name.charAt(14);
            $.ajax({
                type: "post",
                url: "{{ route('booking.get-room-price') }}",
                dataType: "html",
                data: {
                    'room_price_id': room_price_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $("#room_price_" + room_id).val(return_data);
                    $("#room_price_view_" + room_id).val(toRp(return_data));
                },
                complete: function() {
                    subtotal()
                    enableNav();
                    setTimeout(function() {
                        enableNav();
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
        function subtotal(){
            let total = 0;
            $(".room_price_price").each(function() {
                total += Number($(this).val());
            });
            $("#sbs-room-view").html(toRp(total));
            $("#sbs-room").val(total);
            return total;
        }
        function changeMenu(){
            loading();
            var room_menu_type = $("#room_menu_type").val();
            $.ajax({
                type: "post",
                url: "{{ route('booking.get-room-menu') }}",
                dataType: "html",
                data: {
                    'room_menu_type': room_menu_type,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#room_menu_name').html(return_data);
                    loading(0);
                    return 0;
                },
                complete: function() {
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
        function addMenuItem()
        {
            loading();
            var room_menu_id = $("#room_menu_id").val();
            $.ajax({
                type: "post",
                url: "{{ route('booking.add-menu-item') }}",
                dataType: "html",
                data: {
                    'room_menu_id': room_menu_id,
                    'no': $('.menu-item').length,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#menu-item-table').html(return_data);
                    if ($('.menu-item').length == 0) {
                        $('#menu-item-table').html(return_data);
                    } else {
                        $('#menu-item-table').append(return_data);
                    }
                    loading(0);
                    return 0;
                },
                complete: function() {
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
        function deleteBooked(room_id){
            loading();
            $.ajax({
                type: "get",
                url: "{{ route('booking.delete-booked-room') }}"+room_id,
                dataType: "html",
               
                success: function(return_data) {
                    $('#menu-item-table').html(return_data);
                    if ($('.menu-item').length == 0) {
                        $('#menu-item-table').html(return_data);
                    } else {
                        $('#menu-item-table').append(return_data);
                    }
                    loading(0);
                    return 0;
                },
                complete: function() {
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
        $(document).ready(function() {
            subtotal();changeMenu();
            $('#navigator-booking li:nth-child(' + index + ') a').tab('show');
            changeType();
            $("input").each(function() {
                $(this).change(function() {
                    function_elements_add(this.name, this.value)
                });
            });
            $(".prev-btn").each(function() {
                $(this).click(function() {
                    preft()
                })
            });
            $(".next-btn").each(function() {
                $(this).click(function() {
                    next()
                })
            });
            $(".room_price_price").each(function() {
                var price = $(this);
                $("#room_price_view_" + $(this).data('id')).val(toRp(price.val()));
                // console.log();
                $(".room_price_price_view").each(function() {
                    $(this).change(function() {
                        price.val($(this).val());
                        $(this).val(toRp($(this).val()))
                    })
                });

            });
        });
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ url('item') }}">Daftar Booking</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Booking</li>
        </ol>
    </nav>

@stop
@section('content')
    <h3 class="page-title">
        Form Tambah Booking
    </h3>
    <br />
    @if (session('msg'))
        <div class="alert alert-info" role="alert">
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
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Form Tambah
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ route('booking.index') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>

        <form method="post" id="form-barang" action="{{ route('booking.process-add') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <ul class="nav nav-tabs" id="navigator-booking" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active booking-nav" onclick="function_elements_add('tab-index',1)"
                            href="#tanggal" role="tab" data-toggle="tab">Tanggal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link booking-nav" onclick="function_elements_add('tab-index',2)" href="#room"
                            role="tab" data-toggle="tab">Kamar</a>
                    </li>
                    <li class="nav-item booking-nav">
                        <a class="nav-link" onclick="function_elements_add('tab-index',3)" href="#facility" role="tab"
                            data-toggle="tab">Fasilitas</a>
                    </li>
                    <li class="nav-item booking-nav">
                        <a class="nav-link" onclick="function_elements_add('tab-index',4)" href="#menus" role="tab"
                            data-toggle="tab">Menu</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade show active" id="tanggal">
                        <div class="row form-group mt-5">
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <section class="control-label">Tanggal Check-In
                                        <span class="required text-danger">
                                            *
                                        </span>
                                    </section>
                                    <input type="date"
                                        class="form-control form-control-inline input-medium date-picker input-date"
                                        data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date"
                                        value="{{ $sessiondata['start_date'] ?? date('Y-m-d') }}" style="width: 15rem;" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <section class="control-label">Tanggal Check-Out
                                        <span class="required text-danger">
                                            *
                                        </span>
                                    </section>
                                    <input type="date"
                                        class="form-control form-control-inline input-medium date-picker input-date"
                                        data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date"
                                        value="{{ $sessiondata['end_date'] ?? date('Y-m-d') }}" style="width: 15rem;" />
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <div class="form-actions float-right">
                                <button type="button" class="btn next-btn btn-primary">
                                    Berikutnya <i class="fa fa-solid fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="room">
                        <div class="row form-group">
                            <div class="col">
                                <div class="form-group">
                                    <a class="text-dark">Atas Nama<a class='red'> *</a></a>
                                    <input class="form-control required input-bb" required form="form-barang" name="a.n"
                                        id="a.n" type="text" autocomplete="off"
                                        onchange="function_elements_add(this.name, this.value)"
                                        value="{{ $items['a.n'] ?? '' }}" />
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Bagunan<a class='red'> *</a></a>
                                    {!! Form::select('building_id', $building, $sessiondata['building_id'] ?? '', [
                                        'class' => 'selection-search-clear required select-form',
                                        'name' => 'building_id',
                                        'id' => 'building_id',
                                        'onchange' => 'changeType()',
                                        'form' => 'form-barang',
                                        'autofocus' => 'autofocus',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <a class="text-dark">Tipe Kamar<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form" required
                                        form="form-barang" placeholder="Pilih Tipe" name="room_type_id"
                                        id="room_type_id" onchange="changeRoom(this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Kamar<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form" required
                                        form="form-barang" placeholder="Pilih Nama" name="room_id" id="room_id"
                                        onchange="function_elements_add(this.name, this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto justify-content-center">
                                <button class="btn btn-sm btn-primary mt-4" type="button" onclick="addRoom()"><i
                                        class="fa fa-plus" id="add-package-item"></i> Tambah Kamar</button>
                            </div>
                        </div>
                        <div class="card border border-dark">
                            <div class="card-header bg-dark clearfix">
                                <h5 class="mb-0 float-left">
                                    Daftar Kamar yang Dipesan
                                </h5>
                                <div class="form-actions float-right">
                                </div>
                            </div>
                            <div class="card-body-">
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
                                                <th colspan="2" width="30%" style='text-align:center'>Harga Kamar
                                                </th>
                                                <th width="10%" style='text-align:center'>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="room-table">
                                            @isset($room)
                                                @php $no = 1; @endphp
                                                @foreach ($room as $val)
                                                    <tr class="booked-room room-{{ $val->room_id }}">
                                                        <td>{{ $no++ }}
                                                            <input type='hidden' id="room_id_{{ $val->room_id }}" </td>
                                                        <td>{{ $val->room_name }}</td>
                                                        <td>{{ $val->roomType->room_type_name }}</td>
                                                        <td>{{ $val->building->building_name }}</td>
                                                        <td>
                                                            <div class='row'>
                                                                <div class="col-5">
                                                                    <input
                                                                        oninput='changeHowManyPerson({{ $val->room_id }}, this.value)'
                                                                        type='number' name='room_qty_{{ $val->room_id }}'
                                                                        id='room_qty_{{ $val->room_id }}'
                                                                        style='text-align: center; height: 30px; font-weight: bold; font-size: 15px'
                                                                        class='form-control input-bb' min='1'
                                                                        value='{{ $booked[$val->room_id] ?? 1 }}' autocomplete='off'>
                                                                </div>
                                                                <div class='col-auto'>Orang</div>
                                                            </div>
                                                        </td>
                                                        <td width="15%">
                                                            {!! Form::select(
                                                                'room_price_id',
                                                                $val->price->pluck('type.price_type_name', 'room_price_id'),
                                                                $sessiondata['room_price_id_'.$val->room_id] ?? '',
                                                                [
                                                                    'class' => 'selection-search-clear required select-form',
                                                                    'name' => 'room_price_id_' . $val->room_id,
                                                                    'id' => 'room_price_id_' . $val->room_id,
                                                                    'onchange' => 'changePrice(this.name,this.value)',
                                                                    'required',
                                                                ],
                                                            ) !!}
                                                        </td>
                                                        <td width="10%">
                                                            <input type="text"
                                                                class="form-control input-bb readonly room_price_price_view"
                                                                name="room_price_view_{{ $val->room_id }}"
                                                                id="room_price_view_{{ $val->room_id }}" readonly />
                                                            <input type="hidden" class="room_price_price"
                                                                name="room_price_{{ $val->room_id }}"
                                                                id="room_price_{{ $val->room_id }}"
                                                                data-id="{{ $val->room_id }}"
                                                                value="{{ $sessiondata['room_price'] ?? $val->price->first()->room_price_price }}" />
                                                        </td>
                                                        <td class='text-center'><button type='button'
                                                                class='btn btn-outline-danger btn-sm'
                                                                onclick='deleteBooked({{ $val->room_id }})'>Hapus</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endisset
                                            @empty($menuItm)
                                            <tr><td align="center" valign="top" colspan="7" class="dataTables_empty">No data available in table</td></tr>
                                            @endempty
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="font-weight-bold text-center fs-4">Subtotal</td>
                                                <td colspan="2" class="font-weight-bold text-center fs-4">
                                                    <h5 id="subtotal"> Rp. <div class="sbs-room-view d-inline" id="sbs-room-view"></div> - </h5>
                                                    <input type="hidden" name="subtotal_room" id="sbs-room"/>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <div class="form-actions float-left">
                                <button type="button" class="btn prev-btn btn-primary booking-nav"> <i
                                        class="fa fa-solid fa-arrow-left"></i>
                                    Kembali</button>
                            </div>
                            <div class="form-actions float-right">
                                <button type="button" class="btn next-btn btn-primary booking-nav">
                                    Berikutnya <i class="fa fa-solid fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="facility">
                        <div class="row form-group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Fasilitas<a class='red'> *</a></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <div class="form-actions float-left">
                                <button type="button" class="btn prev-btn btn-primary"> <i
                                        class="fa fa-solid fa-arrow-left"></i>
                                    Kembali</button>
                            </div>
                            <div class="form-actions float-right">
                                <button type="button" class="btn next-btn btn-primary">
                                    Berikutnya <i class="fa fa-solid fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="menus">
                        <div class="row form-group">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <a class="text-dark">Tipe<a class='red'> *</a></a>
                                    {!! Form::select(
                                        'room_menu_type',
                                        $menutype,
                                        $sessiondata['room_menu_type'] ?? '',
                                        [
                                            'class' => 'selection-search-clear required select-form',
                                            'name' => 'room_menu_type',
                                            'id' => 'room_menu_type',
                                            'onchange' => 'changeMenu()',
                                            'required',
                                        ],
                                    ) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Name<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form"
                                        placeholder="Masukan Kategori Barang" name="room_menu_id"
                                        id="room_menu_id" onchange="function_elements_add(this.name, this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-primary mt-4" type="button" onclick="addMenuItem()"><i
                                        class="fa fa-plus" id="add-package-item"></i>Tambah</button>
                            </div>
                        </div>
                        <div class="card border border-dark">
                            <div class="card-header bg-dark clearfix">
                                <h5 class="mb-0 float-left">
                                    Daftar
                                </h5>
                                <div class="form-actions float-right">
                                </div>
                            </div>
                            <div class="menu-item-table">
                                <div class="table-responsive">
                                    <table id="example" style="width:100%"
                                        class="table table-striped table-bordered table-hover table-full-width">
                                        <thead>
                                            <tr>
                                                <th width="2%" style='text-align:center'>No</th>
                                                <th width="20%" style='text-align:center'>Tipe Menu</th>
                                                <th width="20%" style='text-align:center'>Nama Menu</th>
                                                <th width="20%" style='text-align:center'>Jumlah</th>
                                                <th width="20%" style='text-align:center'>Harga</th>
                                                <th width="20%" style='text-align:center'>Subtotal</th>
                                                <th width="10%" style='text-align:center'>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="package-table">
                                            @isset($menuItm)
                                                @php $no = 1; @endphp
                                                @foreach ($menuItm as $val)
                                                @endforeach
                                            @endisset
                                            @empty($menuItm)
                                            <tr><td align="center" valign="top" colspan="7" class="dataTables_empty">No data available in table</td></tr>
                                            @endempty
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="font-weight-bold text-center fs-4">Subtotal</td>
                                                <td colspan="2" class="font-weight-bold text-center fs-4">
                                                    <h5 id="subtotal"> Rp. <div class="sbs-menu-view d-inline" id="sbs-menu-view"></div> - </h5>
                                                    <input type="hidden" name="subtotal_menu" id="sbs-menu"/>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <div class="form-actions float-left">
                                <button type="button" class="btn prev-btn btn-primary"> <i
                                        class="fa fa-solid fa-arrow-left"></i>
                                    Kembali</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>

@stop

@section('footer')

@stop

@section('css')

@stop
