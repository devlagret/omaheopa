
@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
function check(name,uri){
  if(confirm(`Yakin Ingin Menghapus Kamar dengan nama '`+name+`' ?`)){
    window.location.href = uri;
  }
}
</script>
@stop
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Booking</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Booking</b> <small>Kelola Booking </small>
</h3>
<br/>
<div id="accordion">
  <form  method="post" action="{{ route('filter-list-stock-adjustment') }}" enctype="multipart/form-data">
  @csrf
      <div class="card border border-dark">
      <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          <h5 class="mb-0">
              Filter
          </h5>
      </div>

      <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
          <div class="card-body">
              <div class = "row">
                  <div class = "col-md-6">
                      <div class="form-group form-md-line-input">
                          <section class="control-label">Tanggal Mulai
                              <span class="required text-danger">
                                  *
                              </span>
                          </section>
                          <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="{{ $start_date }}" style="width: 15rem;"/>
                      </div>
                  </div>

                  <div class = "col-md-6">
                      <div class="form-group form-md-line-input">
                          <section class="control-label">Tanggal Akhir
                              <span class="required text-danger">
                                  *
                              </span>
                          </section>
                          <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="{{ $end_date }}" style="width: 15rem;"/>
                      </div>
                  </div>

                  {{-- <div class = "col-md-6">
                      <div class="form-group form-md-line-input">
                          <section class="control-label">Nama Pemasok
                              <span class="required text-danger">
                                  *
                              </span>
                          </section>
                          <select  class="form-control "  type="text" name="end_date" id="end_date" onChange="function_elements_add(this.name, this.value);" value="" >
                              <option value=""></option>
                          </select>
                      </div>
                  </div>

                  <div class = "col-md-6">
                      <div class="form-group form-md-line-input">
                          <section class="control-label">Nama Gudang
                              <span class="required text-danger">
                                  *
                              </span>
                          </section>
                          <select class="form-control"  type="text" name="end_date" id="end_date" onChange="function_elements_add(this.name, this.value);" value="">
                              <option value=""></option>
                          </select>
                      </div>
                  </div> --}}
              </div>
          </div>
          <div class="card-footer text-muted">
              <div class="form-actions float-right">
                  <a href="{{ route('list-reset-stock-adjustment') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
                  <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
              </div>
          </div>
      </div>
      </div>
  </form>
</div>
@if(session('msg'))
<div class="alert alert-{{session('type')?session('type'):''}}" role="alert">
    {{session('msg')}}
</div>
@endif
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar Booking
    </h5>
    <div class="form-actions float-right">
        <button onclick="location.href='{{ route('room.add') }}'" name="add" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Booking </button>
    </div>
  </div>

    <div class="card-body">

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