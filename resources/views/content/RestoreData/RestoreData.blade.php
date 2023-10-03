
@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
function check(name,uri){
  if(confirm(`Yakin Ingin Menghapus Divisi dengan nama '`+name+`' ?`)){
    window.location.href = uri;
  }
}
</script>
@stop
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Restore Data</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Restore Data</b> <small>Kelola Data yang Dihapus </small>
</h3>
<br/>

@if(session('msg'))
<div class="alert alert-{{session('type')?session('type'):''}}" role="alert">
    {{session('msg')}}
</div>
@endif
@if ($table->count()==0)
<div class="row justify-content-center text-center align-middle" style="height: 50vh !important;">
  <div class="col-6 align-self-center">
    Tidak Ada data yang dihapus
  </div>
</div>
@endif
<div class="row">
    @foreach ($table as $key => $val)
    <div class="col-md-3 col-sm-6 col-12">
            <a class="text-body"href="{{route('restore.table',$key)}}">
            <div class="info-box text-black">
                <span class="info-box-icon bg-info"><i class="fa fa-light fa-table"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number">Tabel '{{$key}}'</span>
                    <span class="info-box-text">Data Dihapus : <b> {{$val}} </b></span>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

@stop

@section('footer')

@stop

@section('css')

@stop

@section('js')

@stop