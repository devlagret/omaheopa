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
                url: "{{ route('add-item-elements') }}",
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

        function reset_add() {
            $.ajax({
                type: "GET",
                url: "{{ route('add-reset-item') }}",
                success: function(msg) {
                    location.reload();
                }

            });
        }

        function changeCategory(id, el, from_paket = 0, from = 0) {
            loading();
            var merchant_id = $("#" + id).val();
            console.log(id);
            $.ajax({
                type: "POST",
                url: "{{ route('get-item-category') }}",
                dataType: "html",
                data: {
                    'merchant_id': merchant_id,
                    'from_paket': from_paket,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    if (from) {
                        clearIsiPaket();
                    }
                    if (from_paket) {
                        function_elements_add('package_merchant_id', merchant_id);
                        $('#' + el).html(return_data);
                        changeItem($('#' + el).val());
                        return 0;
                    } else {
                        loading(0);
                        setTimeout(function() {
                            loading(0);
                        }, 2000);
                        $('#' + el).html(return_data);
                        function_elements_add('merchant_id', merchant_id);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function changeItem(category) {
            loading();
            var id = $("#package_merchant_id").val();
            var no = $('.pkg-itm').length;
            $.ajax({
                type: "POST",
                url: "{{ route('get-merchant-item') }}",
                dataType: "html",
                data: {
                    'no': no,
                    'merchant_id': id,
                    'item_category_id': category,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#package_item_id').val(1);
                    $('#package_item_id').html(return_data);
                    changeSatuan();
                    function_elements_add('package_merchant_id', id);
                    function_elements_add('package_item_category', category);
                }
            });
        }
        //* salah nama (sebaiknya dianti ke 'checkKemasan', jangan lupa ubah kode yg lain)
        function checkCategory() {
            const max = {{ $items['max_kemasan'] ?? 4 }};
            var no = $('.input-kemasan').length;
            while (no > max) {
                removeKemasan('input-kemasan-' + no)
            }
            if (no >= max) {
                $('#add-kmsn').addClass('disabled');
            } else {
                $('#add-kmsn').removeClass('disabled');
            }
        }

        function addKemasan() {
            const max = {{ $items['max_kemasan'] ?? 4 }};
            var no = $('.input-kemasan').length;
            var noa = $('.input-kemasan').length + 1;
            if (no != max) {
                $.ajax({
                    type: "get",
                    url: "{{ route('add-kemasan') }}",
                    dataType: "html",
                    success: function(return_data) {
                        location.reload();
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        }

        function removeKemasan(el) {
            $.ajax({
                type: "get",
                url: "{{ route('remove-kemasan') }}",
                dataType: "html",
                success: function(return_data) {
                    $('#' + el).remove();
                    checkCategory()
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function addCategory() {
            location.href = '{{ route('add-item-category') }}' + '/' + $('#merchant_id').val();
        }

        function addPackageItem(qty = 1) {
            loading();
            var package_item_id = $('#package_item_id').val();
            var package_item_unit = $('#package_item_unit').val();
            var package_item_id = $("#package_item_id").val();
            if ($('#item_package_' + package_item_id + '_' + package_item_unit + '_quantity').length) {
                $('#item_package_' + package_item_id + '_' + package_item_unit + '_quantity').val(function(i, oldval) {
                    var newval = ++oldval;
                    function_change_quantity(package_item_id, package_item_unit, newval);
                    return ++newval;
                });
                return 0;
            }
            $.ajax({
                type: "post",
                url: "{{ route('package.process-add-item') }}",
                dataType: "html",
                data: {
                    'item_id': package_item_id,
                    'item_unit': package_item_unit,
                    'qty': qty,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    if ($('.pkg-itm').length == 0) {
                        $('#package-table').html(return_data);
                    } else {
                        $('#package-table').append(return_data);
                    }
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 500);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function checkIsiPaket() {
            var length = $('.pkg-itm').length;
            if (length == null || length == 0 || length == '') {
                alert('Harap Tambah Barang dalam Paket');
                return 0;
            }
            $('#form-paket').submit();
        }

        function clearIsiPaket() {
            $.ajax({
                type: "get",
                url: "{{ route('package.clear-item') }}",
                dataType: "html",
                success: function(return_data) {
                    $('.pkg-itm').each(function(index) {
                        $(this).remove();
                    });
                    $('#package-table').html(
                        '<td valign="top" colspan="7" class="dataTables_empty">No data available in table</td>'
                    );
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function deleteIsiPaket(item_id) {
            $.ajax({
                type: "get",
                url: "{{ url('package/delete-item/') }}" + '/' + item_id,
                dataType: "html",
                success: function(return_data) {
                    $('#col-package-item-' + item_id).remove();
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function function_change_quantity(item_packge_id, unit_id, value) {
            if (value != '') {
                $.ajax({
                    url: "{{ url('package/item/change-qty') }}" + '/' + item_packge_id + '/' + unit_id + '/' +
                        value,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {

                    }
                });
            }
        }

        function changeSatuan() {
            var package_item_id = $("#package_item_id").val();
            loading();
            $.ajax({
                type: "POST",
                url: "{{ route('get-item-unit') }}",
                dataType: "html",
                data: {
                    'item_id': package_item_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#package_item_unit').val(1);
                    $('#package_item_unit').html(return_data);
                    function_elements_add('package_item_id', package_item_id);
                },
                complete: function() {
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 2000);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function formatRp() {
            var harga = $('#package_price_view').val();
            function_elements_add('package_price_view', harga);
            $('#package_price_view').val(toRp(harga));
            $('#package_price').val(harga);
        }
        $(document).ready(function() {
                    changeCategory('merchant_id', 'item_category_id');
                    changeCategory('package_merchant_id', 'package_item_category', 1);
                    checkCategory();
                    if ($('#package_price_view').val() != '') {
                        formatRp();
                    }
                    $(document).ready(function() {});
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
                <button onclick="location.href='{{ url('item') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>

        <form method="post" id="form-barang" action="{{ route('process-add-item') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div id="stepper2" class="bs-stepper">
                    <div class="bs-stepper-header" role="tablist">
                        <div class="step" data-target="#test-nl-1">
                            <button type="button" class="step-trigger" role="tab" id="stepper2trigger1"
                                aria-controls="test-nl-1">
                                <span class="bs-stepper-circle">
                                    <span class="fas fa-user" aria-hidden="true"></span>
                                </span>
                                <span class="bs-stepper-label">Name</span>
                            </button>
                        </div>
                        <div class="bs-stepper-line"></div>
                        <div class="step" data-target="#test-nl-2">
                            <button type="button" class="step-trigger" role="tab" id="stepper2trigger2"
                                aria-controls="test-nl-2">
                                <span class="bs-stepper-circle">
                                    <span class="fas fa-map-marked" aria-hidden="true"></span>
                                </span>
                                <span class="bs-stepper-label">Address</span>
                            </button>
                        </div>
                        <div class="bs-stepper-line"></div>
                        <div class="step" data-target="#test-nl-3">
                            <button type="button" class="step-trigger" role="tab" id="stepper2trigger3"
                                aria-controls="test-nl-3">
                                <span class="bs-stepper-circle">
                                    <span class="fas fa-save" aria-hidden="true"></span>
                                </span>
                                <span class="bs-stepper-label">Submit</span>
                            </button>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link ? 'active' : '' }}" href="#barang" role="tab" data-toggle="tab">Data
                            Barang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ? 'active' : '' }}" href="#form-kemasan" role="tab"
                            data-toggle="tab">Kemasan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link : '' }}" href="#form-pkt" role="tab" data-toggle="tab">Paket</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade ? 'show active' : '' }}" id="barang">
                        <div class="row form-group mt-5">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Wahana / Merchant<a class='red'> *</a></a>
                                    {!! Form::select('merchant_id', $room, $items['merchant_id'] ?? '', [
                                        'class' => 'selection-search-clear required select-form',
                                        'name' => 'merchant_id',
                                        'id' => 'merchant_id',
                                        'onchange' => 'changeCategory(this.id,`item_category_id`)',
                                        'form' => 'form-barang',
                                        'autofocus' => 'autofocus',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Kategori Barang / Paket<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form" required form="form-barang"
                                        placeholder="Masukan Kategori" name="item_category_id" id="item_category_id"
                                        onchange="function_elements_add(this.name, this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Kode Barang / Paket<a class='red'> *</a></a>
                                    <input class="form-control input-bb" form="form-barang" name="item_code"
                                        id="item_code" type="text" autocomplete="off"
                                        onchange="function_elements_add(this.name, this.value)"
                                        value="{{ $items['item_code'] ?? '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Barang / Paket<a class='red'> *</a></a>
                                    <input class="form-control required input-bb" required form="form-barang"
                                        name="item_name" id="item_name" type="text" autocomplete="off"
                                        onchange="function_elements_add(this.name, this.value)"
                                        value="{{ $items['item_name'] }}" />
                                </div>
                            </div>
                            <div class="col-md-8 mt-3">
                                <div class="form-group">
                                    <a class="text-dark">Keterangan</a>
                                    <textarea class="form-control input-bb" form="form-barang" name="item_remark" id="item_remark" type="text"
                                        autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{ $items['item_remark'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade ? 'show active' : '' }}" id="form-kemasan">
                    </div>
                    <div role="tabpanel" class="tab-pane fade show active' : '' }}" id="form-pkt">
                        <div class="row form-group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Kategori<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form"
                                        placeholder="Masukan Kategori Barang" name="package_item_category"
                                        id="package_item_category" onchange="changeItem(this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                                    {{-- {!! Form::select('item_id', $invtitm, $items['item_id'] ?? '', [
                                        'class' => 'selection-search-clear required select-form',
                                        'name' => 'package_item_id',
                                        'id' => 'package_item_id',
                                    ]) !!} --}}
                                    <select class="selection-search-clear required select-form"
                                        placeholder="Masukan Nama Barang" name="package_item_id" id="package_item_id"
                                        onchange="changeSatuan()">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Satuan<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form"
                                        placeholder="Masukan Kategori Barang" name="package_item_unit"
                                        id="package_item_unit" onchange="function_elements_add(this.name, this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto justify-content-center">
                                <button class="btn btn-sm btn-primary mt-4" type="button" onclick="addPackageItem()"><i
                                        class="fa fa-plus" id="add-package-item"></i>Tambah
                                    Barang</button>
                            </div>
                        </div>
                        <div class="card border border-dark">
                            <div class="card-header bg-dark clearfix">
                                <h5 class="mb-0 float-left">
                                    Daftar Isi Paket
                                </h5>
                                <div class="form-actions float-right">
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example" style="width:100%"
                                        class="table table-striped table-bordered table-hover table-full-width">
                                        <thead>
                                            <tr>
                                                <th width="2%" style='text-align:center'>No</th>
                                                <th width="20%" style='text-align:center'>Nama Kategori Barang</th>
                                                <th width="20%" style='text-align:center'>Kode Barang</th>
                                                <th width="20%" style='text-align:center'>Wahana / Merchant</th>
                                                <th width="20%" style='text-align:center'>Nama Barang</th>
                                                <th width="20%" style='text-align:center'>Jumlah</th>
                                                <th width="10%" style='text-align:center'>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="package-table">
                                        
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" form="form-barang" name="Reset" class="btn btn-danger"
                    onclick="reset_add();"><i class="fa fa-times"></i> Batal</button>
                <button type="submit" form="form-barang" name="Save" class="btn btn-primary" title="Save"><i
                        class="fa fa-check"></i>
                    Simpan</button>
            </div>
        </div>
    </div>
@stop

@section('footer')

@stop

@section('css')

@stop
