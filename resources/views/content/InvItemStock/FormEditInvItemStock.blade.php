@extends('adminlte::page')

@section('title', 'PBF | Koperasi Menjangan Enam')
<link rel="shortcut icon" href="{{ asset('resources/assets/logo_pbf.ico') }}" />    

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('inv-item-stock') }}">Daftar Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Barang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Edit Barang
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif
@if(count($errors) > 0)
<div class="alert alert-danger" role="alert">
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</div>
@endif
    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Edit
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('inv-item-unit') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{route('process-edit-inv-item-unit')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kode Barang Satuan<a class='red'> *</a></a>
                        <input class="form-control input-bb" type="text" name="item_unit_code" id="item_unit_code" value="{{$invitemunit['item_unit_code']}}"/>
                        <input class="form-control input-bb" type="hidden" name="item_unit_id" id="item_unit_id" value="{{$invitemunit['item_unit_id']}}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Barang Satuan<a class='red'> *</a></a>
                        <input class="form-control input-bb" type="text" name="item_unit_name" id="item_unit_name" value="{{$invitemunit['item_unit_name']}}"/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Default Quantity<a class='red'> *</a></a>
                        <input class="form-control input-bb" type="text" name="item_unit_default_quantity" id="item_unit_default_quantity" value="{{$invitemunit['item_unit_default_quantity']}}"/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-12">
                    <div class="form-group">
                        <a class="text-dark">Keterangan</a>
                        <input class="form-control input-bb" type="text" name="item_unit_remark" id="item_unit_remark" value="{{$invitemunit['item_unit_remark']}}"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
                <button type="submit" name="Save" class="btn btn-primary" title="Save"><i class="fa fa-check"></i> Simpan</button>
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