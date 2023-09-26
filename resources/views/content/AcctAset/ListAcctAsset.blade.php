@inject('LA', 'App\Http\Controllers\AcctAssetController')
@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
function check(name,uri){
  if(confirm(`Yakin Ingin Menghapus Asset dengan nama '`+name+`' ?`)){
    window.location.href = uri;
  }
}
</script>
@stop
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Jenis Aset</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Jenis Aset</b> <small>Kelola Jenis Aset </small>
</h3>
<br/>

@if(session('msg'))
<div class="alert alert-{{session('type')?session('type'):''}}" role="alert">
    {{session('msg')}}
</div>
@endif
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar Jenis Aset
    </h5>
    <div class="form-actions float-right">
        <button onclick="location.href='{{ route('aset.add') }}'" name="add" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Jenis Aset </button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                      <th width="5%">
                        Nomor
                      </th>
                      <th width="25%">
                        Tipe Asset
                      </th>
                      <th width="25%">
                        Kode
                      </th>
                      <th width="25%">
                        Nama
                      </th>
                      <th width="25%">
                        Tanggal Pembelian
                      </th>
                      <th width="25%">
                        Nilai pembelian
                      </th>
                      <th width="25%">
                        Nilai Penyusutan
                      </th>
                      <th width="25%">
                        Detail Lokasi
                      </th>
                      <th width="25%">
                        Deskripsi
                      </th>
                      <th width="25%">
                        Aksi
                      </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($aset as $row)
                    <tr>
                        <td style='text-align:center'>{{ $no++ }}</td>
                        <td>{{ $LA->getAssetTypeName($row['asset_type_id'])}}</td>
                        <td>{{ $row->asset_code }}</td>
                        <td>{{ $row->asset_name }}</td>
                        <td>{{ $row->asset_purchase_date }}</td>
                        <td>{{number_format($row['asset_purchase_value'], 2) }}</td>
                        <td>{{number_format($row['asset_depreciation_value'], 2) }}</td>
                        <td>{{ $row->asset_location_detail }}</td>
                        <td>{{ $row->asset_description }}</td>

                        <td class="text-center">
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ route('aset.detail',$row->asset_id) }}">Detail</a>
                            <a type="button" href="{{ route('aset.depreciation',$row->asset_id) }}" class="btn btn-outline-info btn-sm" >Penyesuaian</a>
                        </td>
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

@section('js')

@stop