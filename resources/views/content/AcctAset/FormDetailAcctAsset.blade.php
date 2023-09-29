@inject('Asset', 'App\Http\Controllers\AcctAssetController')
@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
    <script>
        function function_elements_add(name, value) {

            $.ajax({
                type: "POST",
                url: "{{ route('aset-type.elements-add') }}",
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
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ url('aset') }}">Daftar Asset</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail Aset</li>
        </ol>
    </nav>
    <div class="float-right">
        <button onclick="location.href='{{ url('aset') }}'" name="Find" class="btn btn-sm btn-info"
            title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
    </div>

@stop

@section('content')

    <h3 class="page-title">
        Form Detail Aset
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
    <div class="row">
        <div class="col-md-12"> 
            <div class="portlet">
                <div class="actions">
                </div>
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption text-dark">
                            Detail Aset
                        </div>
                    </div>
                    <!-- <?php
                    print_r('$acctasset');
                    print_r($acctasset);
                    ?> -->
                    <div class="portlet-body ">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input">
                                        <label class="text-dark">Tipe</label>
                                        <input type="text" class="form-control" name="asset_type_name"
                                            id="asset_type_name" placeholder="123"
                                            value="{{ $Asset->getAssetTypeName($acctasset['asset_type_id']) }} " readonly />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input">
                                        <label class="text-dark">Tipe Asset</label>
                                        <input type="text" class="form-control" id="asset_type_name"
                                            name="asset_type_name" onChange="function_elements_add(this.name, this.value);"
                                            value="{{ $Asset->getAssetTypeName($acctasset['asset_type_id']) }}" readonly />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input">
                                        <label class="text-dark">Kode Asset</label>
                                        <input type="text" class="form-control" name="asset_code" id="asset_code"
                                            placeholder="123" value="{{ $acctasset['asset_code'] }} " readonly />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input">
                                        <label class="text-dark">Nama Asset</label>
                                        <input type="text" class="form-control" name="asset_name" id="asset_name"
                                            placeholder="Nama" value="{{ $acctasset['asset_name'] }}" readonly />
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-md-line-input">
                                        <label class="text-dark">Tanggal Pembelian</label>
                                        <input type="text" class="form-control" name="asset_purchase_date"
                                            id="asset_purchase_date" placeholder="Name"
                                            value="{{ $acctasset['asset_purchase_date'] }} " readonly />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-md-line-input">
                                        <label class="text-dark">Satuan</label>
                                        <input type="text" class="form-control" name="item_unit_code" id="item_unit_code"
                                            value="{{ number_format((int)$acctasset['item_unit_code'],2) }}" readonly />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input">
                                        <label class="text-dark">Harga Pembelian</label>
                                        <input type="text" class="form-control" name="asset_purchase_value"
                                            id="asset_purchase_value"
                                            value="{{ number_format($acctasset['asset_purchase_value'], 2) }}" readonly />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input">
                                        <label class="text-dark">Metode Penyusutan</label>
                                        <input type="text" class="form-control" name="asset_purchase_value"
                                            id="asset_purchase_value" value="{{ $acctasset['asset_depreciation_type'] }}"
                                            readonly />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input">
                                        <label class="text-dark">Nilai Perolehan</label>
                                        <input type="text" class="form-control" name="asset_book_value"
                                            id="asset_book_value"
                                            value="{{ number_format($acctasset['asset_book_value'], 2) }}" readonly />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input">
                                        <label class="text-dark">Taksiran Usia</label>
                                        <input type="text" class="form-control" name="asset_estimate_lifespan"
                                            id="asset_estimate_lifespan" placeholder="123"
                                            value="{{ $acctasset['asset_estimated_lifespan'] }}" readonly />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input">
                                        <label class="text-dark">Nilai Residu</label>
                                        <input type="text" class="form-control" name="asset_salvage_value"
                                            id="asset_salvage_value" value="{{ $acctasset['asset_salvage_value'] }}"
                                            readonly />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group form-md-line-input">
                                        <label class="text-dark">Detail Lokasi</label>
                                        <input type="textarea" class="form-control" name="asset_location_detail"
                                            id="asset_location_detail" value="{{ $acctasset['asset_location_detail'] }}"
                                            readonly />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group form-md-line-input">
                                        <label class="text-dark">Deskripsi</label>
                                        <input type="textarea" class="form-control" name="asset_description"
                                            id="asset_description" value="{{ $acctasset['asset_description'] }}"
                                            readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
