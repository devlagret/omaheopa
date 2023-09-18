@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")

@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Check-In dan Check-Out </li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Check-In dan Check-Out </b> <small>Konfigurasi Check-In dan Check-Out</small>
</h3>
@if (session('msg'))
<div class="alert alert-{{session('type')??'info'}}" role="alert">
    {{ session('msg') }}
</div>
@endif
@if (count($errors) > 0)
<div class="alert alert-danger" role="alert">
    @foreach ($errors->all() as $error)
        {{ $error }}
    @endforeach
</div>
@endif
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('ct.process-edit-cc-time') }}" enctype="multipart/form-data">
    @csrf
        <div class="card border border-dark">
        <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                Jam Check-In dan Check-Out
            </h5>
        </div>

        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class="row ">
                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Jam Check-In
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="time" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="checkin_time" id="checkin_time" value="{{ $pref->checkin_time ?? ''}}" style="width: 15rem;"/>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Jam Check-Out
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="time" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="checkout_time" id="checkout_time" value="{{ $pref->checkout_time ?? ''}}" style="width: 15rem;"/>
                        </div>
                    </div>
                    {{--<div class = "col-md-6">
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
                    <button type="reset" name="Save" class="btn btn-danger" title="Simpan Data"><i class="fa fa-solid fa-times"></i> Reset</button>
                    <button type="submit" name="Save" class="btn btn-primary" title="Simpan Data"><i class="fa fa-solid fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
        </div>
    </form>
</div>
<br/>

@stop

@section('footer')

@stop

@section('css')

@stop

@section('js')

@stop