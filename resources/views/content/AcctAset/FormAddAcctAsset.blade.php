@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
    <script>
        function function_elements_add(name, value) {

            $.ajax({
                type: "POST",
                url: "{{ route('aset.elements-add') }}",
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
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('aset.index') }}">Daftar Jenis Aset</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Jenis Aset</li>
        </ol>
    </nav>

@stop

@section('content')

    <h3 class="page-title">
        Form Tambah Jenis Aset
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
                Form Tambah Jenis Aset
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ route('aset.index') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>

    

        <form method="post" action="{{ route('aset.process-add') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade show active" id="aset">
                     <div class="row form-group">
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Tipe Aset<a class='red'> *</a></a>
                                {!! Form::select('asset_type_id', $acctassettype, [
                                'class' => 'selection-search-clear select-form',
                                'id' => 'asset_type_id',
                                'name' => 'asset_type_id',
                                'onchange' => 'function_elements_add(this.name, this.value)',
                            ]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Kode Aset<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="asset_code" id="asset_code" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)"/>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Nama Aset</a>
                                <input class="form-control input-bb" name="asset_name" id="asset_name" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <a class="text-dark">Tanggal Pembelian</a>
                                <input class="form-control input-bb" name="asset_purchase_date" id="asset_purchase_date" type="date" autocomplete="off" onchange="function_elements_add(this.name, this.value)"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <a class="text-dark">Satuan</a>
                                <input class="form-control input-bb" name="item_unit_code" id="item_unit_code" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Harga Pembelian</a>
                                <input class="form-control input-bb" name="asset_purchase_value" id="asset_purchase_value" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Metode Penyusutan<a class='red'> *</a></a>
                                {!! Form::select('asset_depreciation_type', $depreciation_method, [
                                'class' => 'selection-search-clear select-form',
                                'id' => 'asset_depreciation_type',
                                'name' => 'asset_depreciation_type',
                                'onchange' => 'function_elements_add(this.name, this.value)',
                            ]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Nilai Perolehan</a>
                                <input class="form-control input-bb" name="asset_book_value" id="asset_book_value" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Taksiran Usia</a>
                                <input class="form-control input-bb" name="asset_estimated_lifespan" id="asset_estimated_lifespan" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Nilai Residu</a>
                                <input class="form-control input-bb" name="asset_salvage_value" id="asset_salvage_value" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Detail Lokasi</a>
                                <input class="form-control input-bb" name="asset_location_detail" id="asset_location_detail" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <a class="text-dark">Deskripsi</a>
                                <input class="form-control input-bb" name="asset_description" id="asset_description" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" />
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" class="btn btn-danger"><i
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
