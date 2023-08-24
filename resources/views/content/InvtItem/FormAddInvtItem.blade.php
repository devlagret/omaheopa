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
                success: function(msg) {}
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

        function changeCategory() {
            var merchant_id = $("#merchant_id").val();

            $.ajax({
                type: "POST",
                url: "{{ route('get-item-category') }}",
                dataType: "html",
                data: {
                    'merchant_id': merchant_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#item_category_id').val('');
                    $('#item_category_id').html(return_data);
                    function_elements_add('merchant_id', merchant_id);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
        function changeItem() {
            var package_merchant_id = $("#package_merchant_id").val();

            $.ajax({
                type: "POST",
                url: "{{ route('get-merchant-item') }}",
                data: {
                    'merchant_id': package_merchant_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#package_item_id').html(return_data);
                    $('#package_item_id').val('');
                    function_elements_add('package_merchant_id', merchant_id);
                }
            });
        }

        function checkCategory() {
            const max = {{ $items['max_kemasan'] }};
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
            const max = {{ $items['max_kemasan'] }};
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
        function addPackageItem(){
            var package_item_id = $('#package_item_id').val();
                $.ajax({
                    type: "get",
                    url: "{{ route('package.process-add-item') }}",
                    dataType: "html",
                    data: {
                    'item_id': package_item_id,
                    '_token': '{{ csrf_token() }}',
                    },
                    success: function(return_data) {
                      console.log(return_data);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
        }
        $(document).ready(function() {
            changeCategory();
            checkCategory();
            addPackageItem();
            changeItem();
            $('#add-package-item').on("click", function() {
                addPackageItem();
             } );
        });
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ url('item') }}">Daftar Barang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Barang</li>
        </ol>
    </nav>

@stop

@section('content')

    <h3 class="page-title">
        Form Tambah Barang
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
                <li>{{ $error }}</li>
            @endforeach
        </div>
    @endif
    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Form Tambah
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ url('item') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>

        <form method="post" action="{{ route('process-add-item') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $items['kemasan'] == 1 ? 'active' : '' }}" href="#barang" role="tab" data-toggle="tab">Data Barang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $items['kemasan'] > 1 ? 'active' : '' }}" href="#form-kemasan" role="tab" data-toggle="tab">Kemasan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $items['kemasan'] > 10 ? 'active' : '' }}" href="#form-paket" role="tab" data-toggle="tab">Paket</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade {{ $items['kemasan'] == 1 ? 'show active' : '' }}" id="barang">
                        @if ($canAddCategory)
                            {{-- <button type="button" onclick="addCategory()" id="add-k" data-toggle="tooltip" data-placement="top" name="Add" class="btn float-right mt-4 btn-sm btn-info" title="Tambah Kategori"><i class="fa fa-plus"></i> Kategori</button> --}}
                        @endif
                        <div class="row form-group mt-5">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Wahana / Merchant<a class='red'> *</a></a>
                                    {!! Form::select('merchant_id', $merchant, $items['merchant_id'] ?? '', ['class' => 'selection-search-clear required select-form','name' => 'merchant_id','id' => 'merchant_id','onchange' => 'changeCategory()']) !!}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form" placeholder="Masukan Kategori" name="item_category_id" id="item_category_id" onchange="function_elements_add(this.name, this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                                    <input class="form-control required input-bb" name="item_name" id="item_name" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_name'] }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Kode Barang<a class='red'> *</a></a>
                                    <input class="form-control required input-bb" name="item_code" id="item_code" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_code'] ?? '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Barcode Barang<a class='red'> *</a></a>
                                    <input class="form-control input-bb" name="item_barcode" id="item_barcode" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_barcode'] }}" />
                                </div>
                            </div>
                            <div class="col-md-8 mt-3">
                                <div class="form-group">
                                    <a class="text-dark">Keterangan</a>
                                    <textarea class="form-control input-bb" name="item_remark" id="item_remark" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{ $items['item_remark'] }}</textarea> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade {{ $items['kemasan'] > 1 ? 'show active' : '' }}" id="form-kemasan">
                        <button type="button" onclick="addKemasan()" id="add-kmsn" data-toggle="tooltip"
                            data-placement="top" name="Add" class="btn mt-4 btn-sm btn-info"
                            title="Tambah Kategori"><i class="fa fa-plus"></i> Tambah Kemasan</button>
                        <div class="div-kemasan" id="div-kemasan">
                            @for ($x = 1; $x <= $items['kemasan']; $x++)
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
                                                {!! Form::select('item_unit_id', $itemunits, $items['item_unit_id' . $x] ?? 0, [
                                                    'class' => 'selection-search-clear required select-form form-control',
                                                    'name' => 'item_unit_id' . $x,
                                                    'id' => 'item_unit_id_' . ($x - 1),
                                                    'onchange' => 'function_elements_add(this.name, this.value)',
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <a class="text-dark">Kuantitas Standar {{ $x }}<a
                                                        class='red'> *</a></a>
                                                <input class="form-control input-bb required" required
                                                    name="item_default_quantity{{ $x }}"
                                                    id="item_default_quantity_{{ $x - 1 }}" type="text"
                                                    autocomplete="off"
                                                    onchange="function_elements_add(this.name, this.value)"
                                                    value="{{ $items['item_default_quantity' . $x] ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <a class="text-dark">Harga Jual {{ $x }}<a class='red'>
                                                        *</a></a>
                                                <input class="form-control input-bb required" required
                                                    name="item_unit_price{{ $x }}"
                                                    id="item_unit_price_{{ $x - 1 }}" type="text"
                                                    autocomplete="off"
                                                    onchange="function_elements_add(this.name, this.value)"
                                                    value="{{ $items['item_unit_price' . $x] ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <a class="text-dark">Harga Beli {{ $x }}<a class='red'>
                                                        *</a></a>
                                                <input class="form-control input-bb required" required
                                                    name="item_unit_cost{{ $x }}"
                                                    id="item_unit_cost_{{ $x - 1 }}" type="text"
                                                    autocomplete="off"
                                                    onchange="function_elements_add(this.name, this.value)"
                                                    value="{{ $items['item_unit_cost' . $x] ?? '' }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade {{ $items['kemasan'] == 10 ? 'show active' : '' }}" id="form-paket">
                        <div class="row form-group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Wahana / Merchant<a class='red'> *</a></a>
                                    {!! Form::select('package_merchant_id', $merchant, $items['package_merchant_id'] ?? '', [
                                        'class' => 'selection-search-clear required select-form',
                                        'name' => 'package_merchant_id',
                                        'id' => 'package_merchant_id',
                                        'onchange' => 'changeItem()',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Paket<a class='red'> *</a></a>
                                    <input class="form-control required input-bb" name="package_name" id="package_name"
                                        type="text" autocomplete="off"
                                        onchange="function_elements_add(this.name, this.value)"
                                        value="{{ $items['package_name'] ?? '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Kode Paket<a class='red'> *</a></a>
                                    <input class="form-control required input-bb" name="package_item_code"
                                        id="package_item_code" type="text" autocomplete="off"
                                        onchange="function_elements_add(this.name, this.value)"
                                        value="{{ $items['package_item_code'] ?? '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Barcode Paket</a>
                                    <input class="form-control input-bb" name="item_barcode" id="item_barcode"
                                        type="text" autocomplete="off"
                                        onchange="function_elements_add(this.name, this.value)"
                                        value="{{ $items['item_barcode'] }}" />
                                </div>
                            </div>
                            <div class="col-md-8 mt-3">
                                <div class="form-group">
                                    <a class="text-dark">Keterangan</a>
                                    <textarea class="form-control input-bb" name="item_remark" id="item_remark" type="text" autocomplete="off"
                                        onchange="function_elements_add(this.name, this.value)">{{ $items['item_remark'] }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                                    {{-- {!! Form::select('item_id', $invtitm, $items['item_id'] ?? '', [
                                        'class' => 'selection-search-clear required select-form',
                                        'name' => 'package_item_id',
                                        'id' => 'package_item_id',
                                    ]) !!} --}}
                                    <select class="selection-search-clear required select-form" placeholder="Masukan Nama Barang" name="package_item_id" id="package_item_id" onchange="function_elements_add(this.name, this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto justify-content-center">
                                <button class="btn btn-sm btn-primary mt-4" type="button"><i class="fa fa-plus" id="add-package-item"></i>Tambah Barang</button>
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
                                    <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
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
                                        <tbody>
                                            <?php $no = 1; ?>
                                            {{-- @foreach ($data as $row)
                                                        <tr>
                                                            <td style='text-align:center'>{{ $no++ }}</td>
                                                            <td>{{ $row['item_category_name'] }}</td>
                                                            <td>{{ $row['item_code'] }}</td>
                                                            <td>{{ $row->merchant->merchant_name }}</td>
                                                            <td>{{ $row['item_name'] }}</td>
                                                            <td class="text-center">
                                                                <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/item/edit-item/'.$row['item_id']) }}">Edit</a>
                                                                <a type="button" class="btn btn-outline-danger btn-sm" href="{{ url('/item/delete-item/'.$row['item_id']) }}">Hapus</a>
                                                            </td>
                                                        </tr>
                                                        @endforeach --}}
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
                    <button type="submit" name="Save" class="btn btn-primary" title="Save"><i
                            class="fa fa-check"></i>
                        Simpan</button>
                </div>
            </div>
        </form>
    </div>
@stop

@section('footer')

@stop

@section('css')

@stop
