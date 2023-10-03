@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")

@section('js')
<script>
    function check(name,uri){
      if(confirm(`Yakin Ingin Menghapus Penggunaan Item '`+name+`' ?`)){
        window.location.href = uri;
      }
    }
    </script>
@stop

@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Penggunaan Barang Hotel</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Penggunaan Barang Hotel </b> <small>Kelola Penggunaan Barang  </small>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('hi.filter') }}" enctype="multipart/form-data">
    @csrf
        <div class="card border border-dark">
        <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                Filter
            </h5>
        </div>

        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class="row ">
                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Mulai
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="{{ $start_date ?? date('Y-m-d')}}" style="width: 15rem;"/>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Akhir
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="{{ $end_date ?? date('Y-m-d')}}" style="width: 15rem;"/>
                        </div>
                    </div>
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
<br/>
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
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar
    </h5>
    <div class="form-actions float-right">
        <button onclick="location.href='{{ route('hi.add') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Booking</button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table style="width:100%" class="table table-striped datatables table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 3%">No </th>
                        <th style="text-align: center; width: 8%">Tanggal</th>
                        <th style="text-align: center; width: 15%">Nama Barang</th>
                        <th style="text-align: center; width: 5%">Jumlah</th>
                        <th style="text-align: center; width: 20%">Keterangan</th>
                        <th style="text-align: center; width: 10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                  @foreach ($usage as $row)
                      <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ $row->date }}</td>
                        <td>{{ $row->item->item_name }}</td>
                        <td>{{ number_format($row->quantity).' '.$row->unit->item_unit_name}}</td>
                        <td>{{ $row->usage_remark}}</td>
                        <td style="text-align: center">
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ route('hi.edit',$row->invt_item_usage_id) }}">Edit</a>
                            <button type="button" onclick="$('this').attr('disabled');check('{{$row->item_name}}','{{ route('hi.delete',$row->invt_item_usage_id) }}')" class="btn btn-outline-danger btn-sm" >Hapus</button>
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