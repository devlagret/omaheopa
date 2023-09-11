@extends('adminlte::page')
<?php
if (empty($items)) {
    $item['merchant_id'] = '';
    $items['kemasan'] = 1;
    $items['max_kemasan'] = 4;
}
if (empty($invtpaket)) {
    $invtpaket = '';
}
if (empty($pktitem)) {
    $pktitem = collect();
}
?>
@section('title', "MOZAIC Omah'e Opa")
@section('js')
    <script src="{{ asset('resources/js/paketHelper.js') }}"></script>
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

        function function_change_quantity(item_packge_id, unit_id, value) {
            if (value != '') {
                $("#simpan-brg").prop('disabled', true);
                $.ajax({
                    url: "{{ url('package/item/change-qty') }}" + '/' + item_packge_id + '/' + unit_id + '/' +
                        value,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {

                    },
                    complete: function() {
                        $("#simpan-brg").prop('disabled', false);
                        setTimeout(function() {
                            $("#simpan-brg").prop('disabled', false);
                        }, 20);
                    }
                });
            }
        }

        function changeCategory(id, el, from_paket = 0) {
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
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
        $(document).ready(function() {
            changeCategory('merchant_id', 'item_category_id');
            changeCategory('package_merchant_id', 'package_item_category', 1);
            checkKemasan();
            if ($('#package_price_view').val() != '') {
                formatRp();
            }
        });
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ url('item') }}">Daftar Barang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ubah Barang</li>
        </ol>
    </nav>

@stop

@section('content')

    <h3 class="page-title">
        Form Ubah Barang
    </h3>
    <br />

    @if (session('msg'))
        <div class="alert alert-info" role="alert">
            {{ session('msg') }}
        </div>
    @endif

    @if (!empty($msg))
        <div class="alert alert-warning" role="alert">
            <i class="fa fa-exclamation"></i> &nbsp; {{ $msg }}
        </div>
    @endif
    @if (count($errors) > 0)
        <div class="alert alert-danger" role="alert">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
    @endif
    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Form Ubah
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ url('item') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>
        <form method="post" id="form-barang" action="{{ route('process-edit-item') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $counts->count() == 0 ? 'active' : '' }}" href="#barang" role="tab"
                            data-toggle="tab">Data Barang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $counts->count() == 0 ? '' : '' }}" href="#kemasan" role="tab"
                            data-toggle="tab">Kemasan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $counts->count() >= 1 ? 'active' : '' }}" href="#form-pkt" role="tab"
                            data-toggle="tab">Paket</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in {{ $counts->count() == 0 ? 'show active' : '' }}"
                        id="barang">
                        <div class="row form-group mt-5">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Wahana / Merchant<a class='red'> *</a></a>
                                    {!! Form::select(
                                        'merchant_id',
                                        $merchant,
                                        isset($items['merchant_id']) ? $items['merchant_id'] : $data['merchant_id'],
                                        [
                                            'class' => 'selection-search-clear required select-form',
                                            'name' => 'merchant_id',
                                            'id' => 'merchant_id',
                                            'onchange' => 'changeCategory(`' . route('get-item-category') . '`,`' . csrf_token() . '`)',
                                            'form' => 'form-barang',
                                            'required',
                                            !empty($pkg) ? 'disabled' : '',
                                        ],
                                    ) !!}
                                    @if (!empty($pkg))
                                        <input type="hidden" name="merchant_id" value="{{ $data['merchant_id'] }}" />
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                                    <select class=" required selection-search-clear select-form" required form="form-barang"
                                        placeholder="Masukan Kategori" name="item_category_id" id="item_category_id"
                                        onchange="function_elements_add(this.name, this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Kode Barang<a class='red'> *</a></a>
                                    <input class="form-control input-bb" name="item_code" id="item_code" type="text"
                                        form="form-barang" autocomplete="off"
                                        onchange="function_elements_add(this.name, this.value)"
                                        value="{{ isset($items['item_code']) ? ($items['item_code'] != '' ? $items['item_code'] : $data['item_code']) : $data['item_code'] }}" />
                                    <input class="form-control input-bb" name="item_id" id="item_id" type="text"
                                        form="form-barang" autocomplete="off" value="{{ $data['item_id'] }}" hidden />
                                    <input type="hidden" form="form-barang" name="used_in_package"
                                        value="{{ !empty($pkg) ? 1 : 0 }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                                    <input class="form-control input-bb" name="item_name" id="item_name" form="form-barang"
                                        type="text" onchange="function_elements_add(this.name, this.value)"
                                        autocomplete="off"
                                        value="{{ isset($items['item_name']) ? ($items['item_name'] != '' ? $items['item_name'] : $data['item_name']) : $data['item_name'] }}" />
                                </div>
                            </div>
                            <div class="col-md-8 mt-3">
                                <div class="form-group">
                                    <a class="text-dark">Keterangan<a class='red'> *</a></a>
                                    <textarea class="form-control input-bb" name="item_remark" id="item_remark" form="form-barang" type="text"
                                        autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{ isset($items['item_remark']) ? ($items['item_remark'] != '' ? $items['item_remark'] : $data['item_remark']) : $data['item_remark'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="kemasan">
                        <button type="button" onclick="addKemasan('{{ route('add-kemasan') }}')" id="add-kmsn"
                            data-toggle="tooltip" data-placement="top" name="Add" class="btn mt-4 btn-sm btn-info"
                            title="Tambah Kategori"><i class="fa fa-plus"></i> Tambah Kemasan</button>
                        <div class="div-kemasan" id="div-kemasan">
                            <input type="hidden" name="base_kemasan" value="{{ $base_kemasan }}" />
                            @for ($x = 1; $x <= $items['kemasan'] + ($base_kemasan - 1); $x++)
                                <div class="input-kemasan" id="input-kemasan-{{ $x }}">
                                    @if ($x != 1)
                                        <a class="float-right text-body" data-toggle="tooltip" data-placement="top"
                                            title="Hapus Kemasan"
                                            onclick="removeKemasan('input-kemasan-{{ $x }}')"><i
                                                class="fa fa-times"></i></a>
                                    @endif
                                    <h5 class="mt-3"><b>Kemasan {{ $x }}</b></h5>
                                    <div class="row form-group mt-2">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <a class="text-dark">Satuan Barang {{ $x }}<a class='red'>
                                                        *</a></a>
                                                {!! Form::select(
                                                    'item_unit_id',
                                                    $itemunits,
                                                    isset($items['item_unit_id' . $x])
                                                        ? $items['item_unit_id' . $x]
                                                        : ($x <= $base_kemasan
                                                            ? $data['item_unit_id' . $x]
                                                            : ''),
                                                    [
                                                        'class' => 'selection-search-clear required select-form form-control',
                                                        'name' => 'item_unit_id' . $x,
                                                        'id' => 'item_unit_id_' . ($x - 1),
                                                        'onchange' => 'function_elements_add(this.name, this.value)',
                                                        'form' => 'form-barang',
                                                    ],
                                                ) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <a class="text-dark">Kuantitas Standar {{ $x }}<a
                                                        class='red'> *</a></a>
                                                <input class="form-control input-bb required" required form="form-barang"
                                                    name="item_default_quantity{{ $x }}"
                                                    id="item_default_quantity_{{ $x - 1 }}" type="text"
                                                    autocomplete="off"
                                                    onchange="function_elements_add(this.name, this.value)"
                                                    value="{{ isset($items['item_default_quantity' . $x]) ? $items['item_default_quantity' . $x] : ($x <= $base_kemasan ? $data['item_default_quantity' . $x] : '') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <a class="text-dark">Harga Jual {{ $x }}<a class='red'>
                                                        *</a></a>
                                                <input class="form-control input-bb required" required form="form-barang"
                                                    name="item_unit_price{{ $x }}"
                                                    id="item_unit_price_{{ $x - 1 }}" type="text"
                                                    autocomplete="off"
                                                    onchange="function_elements_add(this.name, this.value)"
                                                    value="{{ isset($items['item_unit_price' . $x]) ? $items['item_unit_price' . $x] : ($x <= $base_kemasan ? $data['item_unit_price' . $x] : '') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <a class="text-dark">Harga Beli {{ $x }}<a class='red'>
                                                        *</a></a>
                                                <input class="form-control input-bb required" required form="form-barang"
                                                    name="item_unit_cost{{ $x }}"
                                                    id="item_unit_cost_{{ $x - 1 }}" type="text"
                                                    autocomplete="off"
                                                    onchange="function_elements_add(this.name, this.value)"
                                                    value="{{ isset($items['item_unit_cost' . $x]) ? $items['item_unit_cost' . $x] : ($x <= $base_kemasan ? $data['item_unit_cost' . $x] : '') }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade {{ $counts->count() >= 1 ? 'show active' : '' }}"
                        id="form-pkt">
                        <div class="row form-group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input name="item_package_id" form="form-paket" type="hidden"
                                        value="{{ $invtpaket->item_package_id ?? '' }}" />
                                    <a class="text-dark">Wahana / Merchant<a class='red'> *</a></a>
                                    {!! Form::select(
                                        'package_merchant_id',
                                        $merchant,
                                        isset($items['package_merchant_id']) ? $items['package_merchant_id'] : $invtpaket->merchant_id ?? '',
                                        [
                                            'class' => 'selection-search-clear required select-form',
                                            'name' => 'package_merchant_id',
                                            'id' => 'package_merchant_id',
                                            'onchange' => 'changeCategory(this.id,`package_item_category`,1)',
                                            'form' => 'form-paket',
                                        ],
                                    ) !!}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Kategori Barang / Paket<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form" required
                                        form="form-paket" placeholder="Masukan Kategori" name="package_item_category"
                                        id="package_item_category" onchange="function_elements_add(this.name, this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form"
                                        placeholder="Masukan Nama Barang" name="package_item_id" id="package_item_id"
                                        onchange="changeSatuan('{{ route('get-item-unit') }}','{{ csrf_token() }}')">
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto justify-content-center">
                                <button class="btn btn-sm btn-primary mt-4" type="button"
                                    onclick="addPackageItem('{{ route('package.process-add-item') }}','{{ csrf_token() }}','{{ url('package/item/change-qty') }}')"><i
                                        class="fa fa-plus" id="add-package-item"></i>Tambah Barang</button>
                            </div>
                            <div class="col-md-4 ml-5">
                                <div class="form-group">
                                    <a class="text-dark">Satuan<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form"
                                        placeholder="Masukan Kategori Barang" name="package_item_unit"
                                        id="package_item_unit" onchange="function_elements_add(this.name, this.value)">
                                    </select>
                                </div>
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
                                            @for ($no = 1; $no <= $pktitem->count(); $no++)
                                                @php $row = $paket->where('item_id',array_keys($pktitem[$no-1])[0])->first(); @endphp
                                                <tr class='pkg-itm' id="col-package-item-{{ $row->item_id }}">
                                                    <td style='text-align:center'>{{ $no }}</td>
                                                    <td>{{ $row->category->item_category_name }}</td>
                                                    <td>{{ $row['item_code'] }}</td>
                                                    <td>{{ $row->merchant->merchant_name }}</td>
                                                    <td>{{ $row['item_name'] }}</td>
                                                    <td>
                                                        <div class="row">
                                                            <input
                                                                oninput="function_change_quantity({{ $row->item_id }},{{ $pktitem[$no - 1][$row->item_id][1] }}, this.value)"
                                                                type="number"
                                                                name="item_package_{{ $row->item_id }}_{{ $pktitem[$no - 1][$row->item_id][1] }}_quantity"
                                                                id="item_package_{{ $row->item_id }}_{{ $pktitem[$no - 1][$row->item_id][1] }}_quantity"
                                                                style="width: 100%; text-align: center; height: 30px; font-weight: bold; font-size: 15px"
                                                                class="form-control col input-bb" min='1'
                                                                value="{{ $pktitem[$no - 1][$row->item_id][0] }}"
                                                                autocomplete="off">
                                                            <div class="col-auto">
                                                                {{ $unit->where('item_unit_id', $pktitem[$no - 1][$row->item_id][1])->pluck('item_unit_name')[0] }}
                                                                </col>
                                                            </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                            onclick="deleteIsiPaket('{{ $row->item_id }}','{{ $pktitem[$no - 1][$row->item_id][1] }}','{{ url('package/delete-item/') }}')">Hapus</button>
                                                    </td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i
                            class="fa fa-times"></i> Batal</button>
                    <button type="submit" id="simpan-brg" name="Save" class="btn btn-primary" title="Save"><i
                            class="fa fa-check"></i>
                        Simpan</button>
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
