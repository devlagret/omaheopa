
@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
function check(name,uri){
  if(confirm(`Yakin Ingin Menghapus Harga kamar '`+name+`' ?`)){
    window.location.href = uri;
  }
}
</script>
@stop
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Harga Kamar</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Harga Kamar</b> <small>Kelola Harga Kamar </small>
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
        Daftar Harga Kamar
    </h5>
    <div class="form-actions float-right">
        <button onclick="location.href='{{ route('room-price.add') }}'" name="add" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Harga Kamar </button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No</th>
                        <th width="10%" style='text-align:center'>Nama Kamar</th>
                        <th width="10%" style='text-align:center'>Tipe Kamar</th>
                        <th width="20%" style='text-align:center'>Tipe Harga</th>
                        <th width="20%" style='text-align:center'>Harga</th>
                        <th width="10%" style='text-align:center'>Dari Tanggal</th>
                        <th width="10%" style='text-align:center'>Sampai Tanggal</th>
                        <th width="10%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($roomprice as $row)
                    <tr>
                        <td style='text-align:center'>{{ $no++ }}</td>
                        <td>{{ $row->room->room_name }}</td>
                        <td>{{ $row->room->roomType->room_type_name }}</td>
                        <td>{{ $row->type->price_type_name }}</td>
                        <td>{{ number_format($row->room_price_price,2,',','.') }}</td>
                        <td>{{ $row->room_price_start_date }}</td>
                        <td>{{ $row->room_price_end_date }}</td>
                        <td class="text-center">
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ route('room-price.edit',$row->room_price_id) }}">Edit</a>
                            <button type="button" onclick="$('this').attr('disabled');check('{{$row->room->room_name}}','{{ route('room-price.delete',$row->room_price_id) }}')" class="btn btn-outline-danger btn-sm" >Hapus</button>
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