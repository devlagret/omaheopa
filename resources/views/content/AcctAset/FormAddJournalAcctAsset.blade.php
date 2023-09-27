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
        Jurnal Penyusutan Aset
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
                        <a class="text-dark">Kode Asset<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier"
                            type="text" autocomplete="off" value="{{ $Asset->getAssetTypeKode($acctasset['asset_type_id']) }}" readonly />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Asset<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier"
                            type="text" autocomplete="off" value="{{ $Asset->getAssetTypeName($acctasset['asset_type_id']) }}" readonly />
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Periode Penyusutan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier"
                            type="text" autocomplete="off" value="{{ AppHelper::month($acctassetItem['asset_depreciation_end_month']
                            ) ." " . $acctassetItem['asset_depreciation_item_year'] }}" readonly />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Biaya Penyusutan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_return_supplier" id="purchase_return_supplier"
                            type="text" autocomplete="off" value="{{ $acctassetItem['asset_depreciation_item_amount'] }}" readonly />
                    </div>
                </div>
            </div>
            <h5 class="mb-0 float-left">
                Posting Jurnal
            </h5>
            <form method="post" action="{{ route('aset.process-add-journal-depreciation') }}" enctype="multipart/form-data">
                @csrf
            <br>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Penyusutan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="depreciation_date" id="depreciation_date"
                            type="date" autocomplete="off" value="{{ date('Y-m-d');  }}" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Deskripsi Jurnal<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="depreciation_description" id="depreciation_description"
                            type="text" autocomplete="off" value="Penyusutan {{ $Asset->getAssetTypeKode($acctasset['asset_type_id']) ." ". $Asset->getAssetTypeName($acctasset['asset_type_id']) . " ". AppHelper::month($acctassetItem['asset_depreciation_end_month']
                            ) ." " . $acctassetItem['asset_depreciation_item_year']}}" />
                    </div>
                </div>
            </div>

            <!-- <div class="row"> -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover table-full-width">
                    <thead>
                        <th width="50%" style="text-align:center">Nama Perkiraan</th>
                        <th width="25%" style="text-align:center">Debet</th>
                        <th width="25%" style="text-align:center">Kredit</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {!! Form::select('account_id_debit',  $acctaccount, null, ['class' => 'selection-search-clear select-form', 'id' => 'account_id_debit', 'onchange' => 'elements_add(this.name , this.value);']) !!}
                            </td>
                            <td>
                                <input type="hidden" name="journal_voucher_debit_amount" id="journal_voucher_debit_amount" value="{{ $acctassetItem['asset_depreciation_item_amount']; }} " class="form-control">

                                <input type="text" style="text-align:right" name="journal_voucher_debit_amount_view" id="journal_voucher_debit_amount_view" value="{{ number_format($acctassetItem['asset_depreciation_item_amount'], 2); }} " class="form-control">
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>
                                {!! Form::select('account_id_credit',  $acctaccount, null, ['class' => 'selection-search-clear select-form', 'id' => 'account_id_credit', 'onchange' => 'elements_add(this.name , this.value);']) !!}
                            </td>
                            <td></td>
                            <td>
                                <input type="hidden" name="journal_voucher_credit_amount" id="journal_voucher_credit_amount" value="{{ $acctassetItem['asset_depreciation_item_amount']; }} " class="form-control">

                                <input type="text" style="text-align:right" name="journal_voucher_credit_amount_view" id="journal_voucher_credit_amount_view" value="{{ number_format($acctassetItem['asset_depreciation_item_amount'], 2); }} " class="form-control">
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger btn-sm" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
                    <button type="submit" name="Save" class="btn btn-primary btn-sm" title="Save"><i class="fa fa-check"></i> Simpan</button>
                </div>
            </div>
        </div>
        </form>
    </div>
    </div>



@stop

@section('footer')

@stop

@section('css')

@stop
