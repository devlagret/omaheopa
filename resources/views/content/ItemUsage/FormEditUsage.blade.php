@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
    <script>
        function function_elements_add(name, value) {

            $.ajax({
                type: "POST",
                url: "{{ route('room.elements-add') }}",
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

        function reset() {
            console.log('foo');
            $('#room_name').val('');
        }
        function changeCategory(id, el) {
            loadingWidget();
            if($('#'+id).val()!=''){
                $('#merchant_id').val($('#'+id).val());
            }
            var merchant_id = $("#" + id).val();
            $('#merchant_id').val(merchant_id);
            console.log(id);
            $.ajax({
                type: "POST",
                url: "{{ route('get-item-category') }}",
                dataType: "html",
                data: {
                    'merchant_id': merchant_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    function_elements_add(id, merchant_id);
                    $('#' + el).html(return_data);
                    changeItem($('#' + el).val());
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function changeItem(category) {
            loadingWidget();
            var id = $("#merchant_id").val();
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
                    $('#item_id').val(1);
                    $('#item_id').html(return_data);
                    console.log('ci c')
                    changeSatuan();
                    function_elements_add('item_category_id', category);
                }
            });
        }
        function changeSatuan() {
            var item_id = $("#item_id").val();
            loadingWidget();
            $.ajax({
                type: "POST",
                url: "{{ route('get-item-unit') }}",
                dataType: "html",
                data: {
                    'item_id': item_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#item_unit_id').val(1);
                    $('#item_unit_id').html(return_data);
                    function_elements_add('item_id', item_id);
                },
                complete: function() {
                    loadingWidget(0);
                    setTimeout(function() {
                        loadingWidget(0);
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
        $(document).ready(function() {
            if($('#merchant_id_view').val()!=''){
                $('#merchant_id').val($('#merchant_id_view').val());
            }
            changeCategory('merchant_id_view', 'item_category_id');
        });
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hi.index') }}">Daftar Penggunaan Barang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Penggunaan Barang</li>
        </ol>
    </nav>

@stop

@section('content')

    <h3 class="page-title">
        Form Tambah Penggunaan Barang
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
                Form Tambah Penggunaan Barang
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ route('hi.index') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>

        <?php
        if (empty($sessiondata)) {
            $sessiondata['room_name'] = '';
            $sessiondata['room_type_id'] = '';
            $sessiondata['building_id'] = '';
            $sessiondata['room_facility'] = '';
        }
        ?>

        <form method="post" action="{{ route('hi.process-edit') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade show active" id="room">
                        <div class="row form-group mt-5">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Wahana / Merchant<a class='red'> *</a></a>
                                    {!! Form::select('merchant_id', $merchant, $sessiondata['merchant_id'] ?? $data->merchant_id, [
                                        'class' => 'selection-search-clear select-form',
                                        'name' => 'merchant_id_view',
                                        'id' => 'merchant_id_view',
                                        'onchange' => 'changeCategory(this.id,`item_category_id`)',
                                        'autofocus' => 'autofocus',
                                        $merchant->count()==1?"disabled":''
                                    ]) !!}
                                    <input type="hidden" name="merchant_id" id="merchant_id" />
                                    <input type="hidden" name="invt_item_usage_id" id="invt_item_usage_id" value="{{$data->invt_item_usage_id}}"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Kategori Barang / Paket<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form" value="{{$data->item->item_category_id}}"
                                        placeholder="Masukan Kategori Barang" required name="item_category_id" id="item_category_id"
                                        onchange="changeItem(this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form" required value="{{$data->item_id}}" placeholder="Masukan Nama Barang"
                                        name="item_id" id="item_id" onchange="changeSatuan()">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Satuan Barang<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form"
                                        placeholder="Masukan Satuan Barang" value="{{$data->item_unit_id}}" required name="item_unit_id" id="item_unit_id">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Jumlah<a class='red'> *</a></a>
                                    <input class="form-control input-bb required" required type="number" min="1" value="1"
                                        placeholder="Masukan Jumlah Barang" value="{{$data->quantity}}" name="quantity" id="quantity">
                                </input>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Keterangan</a>
                                    <input class="form-control input-bb" type="text"
                                        placeholder="Masukan Keterangan Penggunaan" value="{{$data->usage_remark}}" autocomplete="off"  name="usage_remark" id="usage_remark">
                                </input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="button" class="btn btn-danger" onclick="$('#room_name').val('');"><i
                            class="fa fa-times"></i> Batal</button>
                    <button type="submit" class="btn btn-primary" title="Save"><i class="fa fa-check"></i>
                        Simpan</button>
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
