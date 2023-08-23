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
    $items['kemasan'] = 1;
    $items['merchant_id'] = '';
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

        function changeCategory(name = null, value = null) {
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

        function checkCategory() {
            const max = {{ $items['max_kemasan'] }};
            var no = $('.input-kemasan').length;
            while(no>max){
                removeKemasan('input-kemasan-'+no)
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
        $(document).ready(function() {
            changeCategory();
            checkCategory();
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
                <button onclick="location.href='{{ url('item') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>
        <form method="post" action="{{ route('process-edit-item') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#barang" role="tab" data-toggle="tab">Data Barang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kemasan" role="tab" data-toggle="tab">Kemasan</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in show active" id="barang">
                        <div class="row form-group mt-5">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Wahana / Merchant<a class='red'> *</a></a>
                                    {!! Form::select(
                                        'merchant_id',
                                        $merchant,
                                        $items['merchant_id'] ? $items['merchant_id'] : $data['merchant_id'],
                                        [
                                            'class' => 'selection-search-clear required select-form',
                                            'name' => 'merchant_id',
                                            'id' => 'merchant_id',
                                            'onchange' => 'changeCategory()',
                                        ],
                                    ) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                                    <select class=" required selection-search-clear select-form"
                                        placeholder="Masukan Kategori" name="item_category_id" id="item_category_id">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Kode Barang<a class='red'> *</a></a>
                                    <input class="form-control input-bb" name="item_code" id="item_code" type="text"
                                        autocomplete="off"
                                        value="{{ $items['item_code'] ? $items['item_code'] : $data['item_code'] }}" />
                                    <input class="form-control input-bb" name="item_id" id="item_id" type="text"
                                        autocomplete="off" value="{{ $data['item_id'] }}" hidden />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                                    <input class="form-control input-bb" name="item_name" id="item_name" type="text"
                                        autocomplete="off" value="{{ $data['item_name'] }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Barcode Barang<a class='red'> *</a></a>
                                    <input class="form-control input-bb" name="item_barcode" id="item_barcode"
                                        type="text" autocomplete="off" value="{{ $data['item_barcode'] }}" />
                                </div>
                            </div>
                            <div class="col-md-8 mt-3">
                                <div class="form-group">
                                    <a class="text-dark">Keterangan<a class='red'> *</a></a>
                                    <textarea class="form-control input-bb" name="item_remark" id="item_remark" type="text" autocomplete="off">{{ $data['item_remark'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="kemasan">
                        <button type="button" onclick="addKemasan()" id="add-kmsn" data-toggle="tooltip"
                            data-placement="top" name="Add" class="btn mt-4 btn-sm btn-info"
                            title="Tambah Kategori"><i class="fa fa-plus"></i> Tambah Kemasan</button>
                        <div class="div-kemasan" id="div-kemasan">
                            @php
                                dump($package);
                            @endphp
                            <input type="hidden" name="base_kemasan" value="{{$base_kemasan}}" />
                            @for ($x = 1; $x <= $items['kemasan'] + ($base_kemasan-1); $x++)
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
                                                    isset($items['kemasan[' . ($x - 1) . '][item_unit_id]'])
                                                        ? $items['kemasan[' . ($x - 1) . '][item_unit_id]']
                                                        : ($x<=$base_kemasan?$package[$x - 1]->item_unit_id:''),
                                                    [
                                                        'class' => 'selection-search-clear required select-form form-control',
                                                        'name' => 'kemasan[' . ($x - 1) . '][item_unit_id]',
                                                        'id' => 'item_unit_id_' . ($x - 1),
                                                        'onchange' => 'function_elements_add(this.name, this.value)',
                                                    ],
                                                ) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <a class="text-dark">Kuantitas Standar {{ $x }}<a
                                                        class='red'> *</a></a>
                                                <input type="hidden" name="kemasan[{{($x - 1)}}][item_packge_id]" value="{{$x<=$base_kemasan?$package[$x - 1]->item_packge_id:''}}" />
                                                <input class="form-control input-bb required" required
                                                    name="kemasan[{{ $x - 1 }}][item_default_quantity]"
                                                    id="item_default_quantity_{{ $x - 1 }}" type="text"
                                                    autocomplete="off"
                                                    onchange="function_elements_add(this.name, this.value)"
                                                    value="{{ isset($items['kemasan[' . ($x - 1) . '][item_default_quantity]']) ? $items['kemasan[' . ($x - 1) . '][item_default_quantity]'] : ($x<=$base_kemasan?$package[$x - 1]->item_default_quantity:'') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <a class="text-dark">Harga Jual {{ $x }}<a class='red'>
                                                        *</a></a>
                                                <input class="form-control input-bb"
                                                    name="kemasan[{{ $x - 1 }}][item_unit_price]"
                                                    id="item_unit_price_{{ $x - 1 }}" type="text"
                                                    autocomplete="off"
                                                    onchange="function_elements_add(this.name, this.value)"
                                                    value="{{ isset($items['kemasan[' . ($x - 1) . '][item_unit_price]']) ? $items['kemasan[' . ($x - 1) . '][item_unit_price]'] : ($x<=$base_kemasan?$package[$x - 1]->item_unit_price:'') }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <a class="text-dark">Harga Beli {{ $x }}<a class='red'>
                                                        *</a></a>
                                                <input class="form-control input-bb"
                                                    name="kemasan[{{ $x - 1 }}][item_unit_cost]"
                                                    id="item_unit_cost_{{ $x - 1 }}" type="text" autocomplete="off"
                                                    onchange="function_elements_add(this.name, this.value)"
                                                    value="{{ isset($items['kemasan[' . ($x - 1) . '][item_unit_cost]']) ? $items['kemasan[' . ($x - 1) . '][item_unit_cost]'] : ($x<=$base_kemasan?$package[$x - 1]->item_unit_cost:'') }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onclick="window.location.reload();"><i
                            class="fa fa-times"></i> Batal</button>
                    <button type="submit" name="Save" class="btn btn-primary" title="Save"><i
                            class="fa fa-check"></i> Simpan</button>
                </div>
            </div>
    </div>
    </div>
    </form>

@stop

@section('footer')

@stop

@section('css')

@stop
