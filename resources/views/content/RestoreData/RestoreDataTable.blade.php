
@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
function checkh(name,uri){
  if(confirm(`Yakin Ingin Menghapus Data nomor '`+name+`' Secara Permanen ?`)){
    window.location.href = uri;
  }
}
</script>
@stop
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
      <li class="breadcrumb-item"><a href="{{ route('restore.index') }}">Restore Data</a></li>
      <li class="breadcrumb-item active" aria-current="page">Restore Data Tabel '{{$table}}'</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Restore Data '{{$table}}'</b> <small>Kelola Tabel '{{$table}}'</small>
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
       Restore Data Tabel '{{$table}}'
    </h5>
    <div class="form-actions float-right">
        <button onclick="location.href='{{ route('restore.index') }}'" name="add" class="btn btn-sm btn-info" title="Kembali"><i class="fa fa-arrow-left"></i> Kembali </button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="auto" style='text-align:center'>No</th>
                        <th width="10" style='text-align:center'>Aksi</th>
                        @foreach($header as $val)
                        <th width="auto" style='text-align:center'>{{$val}}</th>   
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($data as $row)
                    <tr>
                        <td style='text-align:center'>{{ $no++ }}</td>
                        <td class="text-center">
                            <a type="button" class="btn btn-success m-1 btn-sm" href="{{ route('restore.data',['table'=>$table,'col'=>$pk,'id'=>$row->$pk]) }}">Restore</a>
                            {{-- <button type="button" class="btn btn-danger m-1 btn-sm" onclick="checkh({{$no++}},'{{route('restore.force-delete',['table'=>$table,'col'=>$pk,'id'=>$row->$pk])}}')">Delete</button> --}}
                        </td>
                        @foreach($header as $key)
                        <td>{{ $row->$key }}</td>
                        @endforeach
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