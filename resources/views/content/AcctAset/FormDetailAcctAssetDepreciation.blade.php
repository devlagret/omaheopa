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

@stop

@section('content')

    <h3 class="page-title">
        Form Penyusutan Aset
    </h3>
    <br />

    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Detail Formulir
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ url('aset') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Type Asset<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier"
                            type="text" autocomplete="off"
                            value="{{ $Asset->getAssetTypeName($acctasset['asset_type_id']) }}" readonly />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Metode Penyusutan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier"
                            type="text" autocomplete="off"
                            value="{{ $Asset->DepreciationMethod($acctasset['asset_type_id']) }}" readonly />
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier"
                            type="text" autocomplete="off" value="{{ $acctasset['asset_name'] }}" readonly />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Penyusutan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier"
                            type="text" autocomplete="off" value="{{ $acctasset['asset_depreciation_date'] }}"
                            readonly />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">Satuan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier"
                            type="text" autocomplete="off" value="{{ $acctasset['item_unit_code'] }}" readonly />
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Metode Penyusutan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier"
                            type="text" autocomplete="off"
                            value="{{ $Asset->DepreciationMethod($acctasset['asset_type_id']) }}" readonly />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Taksiran Usia<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier"
                            type="text" autocomplete="off" value="{{ $acctasset['asset_depreciation_duration'] }}"
                            readonly />
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nilai Perolehan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier"
                            type="text" autocomplete="off" value="{{ $acctasset['asset_depreciation_book_value'] }}"
                            readonly />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nilai Residu<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier"
                            id="purchase_return_supplier" type="text" autocomplete="off"
                            value="{{ $acctasset['asset_depreciation_salvage_value'] }}" readonly />
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Mulai Bulan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier"
                            id="purchase_return_supplier" type="text" autocomplete="off"
                            value="{{ $acctasset['asset_depreciation_start_month'] }}" readonly />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Awal tahun<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier"
                            id="purchase_return_supplier" type="text" autocomplete="off"
                            value="{{ $acctasset['asset_depreciation_start_year'] }}" readonly />
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Bulan Akhir<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier"
                            id="purchase_return_supplier" type="text" autocomplete="off"
                            value="{{ $acctasset['asset_depreciation_end_month'] }}" readonly />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Akhir Tahun<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier"
                            id="purchase_return_supplier" type="text" autocomplete="off"
                            value="{{ $acctasset['asset_depreciation_end_year'] }}" readonly />
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>


    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Daftar
            </h5>
        </div>
        <div class="card-body">
            <div class="form-body form">
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center'>Nomor. </th>
                                <th style='text-align:center'>Tahun Ke</th>
                                <th style='text-align:center'>Bulan Penyusutan</th>
                                <th style='text-align:center'>Tahun Penyusutan</th>
                                <th style='text-align:center'>Biaya Penyusutan</th>
                                <th style='text-align:center'>Akm. Penyusutan</th>
                                <th style='text-align:center'>Nilai Buku</th>
                                <th style='text-align:center'>Jurnal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                                $a=0;
                            @endphp
                            @foreach ($acctassetItem as $row)
                                @php
                                    if ($row['asset_depreciation_item_month'] < 10) {
                                        $monthdepreciation = $a . $row['asset_depreciation_item_month'];
                                    } else {
                                        $monthdepreciation = $row['asset_depreciation_item_month'];
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $row['asset_depreciation_item_year_to'] }}</td>
                                    <td>{{ $monthdepreciation }}</td>
                                    <td style="text-align: right">{{ $row['asset_depreciation_item_year'] }}</td>
                                    <td>{{ $row['asset_depreciation_item_amount'] }}</td>
                                    <td style="text-align: right">{{ $row['asset_depreciation_item_accumulation_amount'] }}</td>
                                    <td style="text-align: right">{{ $row['asset_depreciation_item_book_value'] }}</td>

                                    <?php if($row['asset_depreciation_item_journal_status'] == 0){ ?>
                                        <td align='center'>
                                            <a href="{{ route('aset.depreciation',$row->asset_depreciation_item_id.'/'.$row->asset_id) }}" class='btn default btn-sm btn-success'><i class="fa fa-pen"></i> Posting Jurnal</a>
                                            </td>
                                    <?php }else{ ?>
                                        <td align='center'>
                                            <a href="" class='btn default btn-sm btn-warning'><i class='fas fa-book'></i> Detail Jurnal</a>
                                            </td>
                                    <?php } ?>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



@stop

@section('footer')

@stop

@section('css')

@stop
