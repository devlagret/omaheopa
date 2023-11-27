@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Setting Tiket</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Edit  Tiket</b> <small>Kelola Tiket </small>
</h3>
<br/>

@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif 
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Ubah Tiket  
    </h5>

  </div>

  <form method="post" action="{{ url('setting-tiket/process-edit') }}" enctype="multipart/form-data">
    @csrf
    <div class="card-body">
        <div class="row form-group">
          {{-- <div class="col-md-6">
            <div class="form-group">
              {{-- <a class="text-dark">Tiket</a>
              <input class="form-control input-bb" name="ppn_percentage" id="ppn_percentage" type="text" autocomplete="off" value="{{ $data['ppn_percentage'] }}"/>
            </div>
          </div> --}}
          <div class="col-md-6">
            <div class="form-group">
                <input class="form-control input-bb" name="company_id" id="company_id" type="text" autocomplete="off" value="{{ $data['company_id'] }}" hidden/>
                  <a class="text-dark">Merchant</a>
                  {!! Form::select(0,  $tiket, $data->pluck('tiket_status','company_id'),['class' => 'selection-search-clear select-form', 'id' => 'tiket_status', 'name' => 'tiket_status']) !!}
              </div>
          </div>
        </div>
    </div>
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <button type="reset" name="Reset" class="btn btn-danger" onclick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
            <button type="button" onclick="$(this).addClass('disabled');$('form').submit();" name="Save" class="btn btn-success" title="Save"><i class="fa fa-check"></i> Simpan</button>
        </div>
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

@section('js')
    
@stop