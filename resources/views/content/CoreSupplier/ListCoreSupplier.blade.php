
@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
function check(name,uri){
  if(confirm(`Yakin Ingin Menghapus Supplier dengan nama '`+name+`' ?`)){
    window.location.href = uri;
  }
}
</script>
@stop
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Supplier</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Supplier</b> <small>Kelola Supplier </small>
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
        Daftar Supplier
    </h5>
    <div class="form-actions float-right">
        <button onclick="location.href='{{ route('supplier.add') }}'" name="add" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Supplier </button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                      <th width="2%" style='text-align:center'>No</th>
                      <th width="20%" style='text-align:center'>Nama Supplier</th>
                      <th width="20%" style='text-align:center'>Telp Supplier</th>
                      <th width="20%" style='text-align:center'>Alamat</th>
                      <th width="10%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($supplier as $row)
                    <tr>
                        <td style='text-align:center'>{{ $no++ }}</td>
                        <td>{{ $row->supplier_name }}</td>
                        <td>{{ $row->supplier_mobile_phone1 }}</td>
                        <td>{{ $row->supplier_address }}</td>
                        <td class="text-center">
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ route('supplier.edit',$row->supplier_id) }}">Edit</a>
                            <button type="button" onclick="$('this').attr('disabled');check('{{$row->supplier_name}}','{{ route('supplier.delete',$row->supplier_id) }}')" class="btn btn-outline-danger btn-sm" >Hapus</button>
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