@inject('LAR', 'App\Http\Controllers\AcctAssetReportController')
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
      <li class="breadcrumb-item active" aria-current="page">Daftar Aset</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Aset</b> <small>Kelola Aset </small>
</h3>
<br/>

@if(session('msg'))
<div class="alert alert-{{session('type')?session('type'):''}}" role="alert">
    {{session('msg')}}
</div>
@endif
<div id="accordion">
    <form  method="post" action="{{ route('report-aset.filter') }}" enctype="multipart/form-data">
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
                            {!! Form::select(0, $yearlist, $year_period,['class' => 'selection-search-clear select-form','name'=>'year_period','id'=>'year_period']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i class="fa fa-times"></i> Batal</button>
                    <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
                </div>
            </div>
        </div>
        </div>
    </form>
</div>
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar Aset
    </h5>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Nama Tipe Aset</th>
                        <th width="10%">Nama Aset</th>
                        <th width="6%">Satuan</th>
                        <th width="15%">Deskripsi Aset</th>
                        <th width="10%">Tahun Perolehan</th>
                        <th width="6%">Jml Tahun</th>
                        <th width="6%">%</th>
                        <th width="12%">Harga Perolehan <?php echo $last_year; ?> </th>
                        <th width="12%">Harga Perolehan <?php echo $year_period; ?></th>
                        <th width="12%">Akum. Penyusutan Tahun s.d. <?php echo $last_year; ?></th></th>
                        <th width="12%">Nilai Buku Tahun s.d. <?php echo $last_year; ?></th></th>
                        <th width="12%">Penyusutan Tahun <?php echo $year_period; ?></th>
                        <th width="12%">Akum. Penyusutan Tahun s.d. <?php echo $year_period; ?></th>
                        <th width="12%">Nilai Buku Tahun s.d.<?php echo $year_period; ?></th>
					</tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($data_assetreport as $val)
                    <tr>									
                        <td style='text-align:center'>{{  $no++ }}.</td>
                        <td>{{ $val['asset_type_name']}}</td>
                        <td>{{ $val['asset_name']}} </td>
                        <td>{{ $val['item_unit_code'] }} </td>
                        <td>{{ $val['asset_description'] }} </td>
                        <td>{{ $val['asset_purchase_date'] }}</td>
                        <td>{{ $val['asset_estimated_lifespan'] }} </td>
                        <td>{{ $val['asset_estimated_lifespan_percentage'] }} </td>
                        <td style='text-align:right'>{{ number_format((int)$val['asset_purchase_value_then']) }} </td>
                        <td style='text-align:right'>{{ number_format($val['asset_purchase_value_now']) }} </td>
                        <td style='text-align:right'>{{ number_format($val['asset_depreciation_accumulation_last_year'], 2) }} </td>
                        <td style='text-align:right'>{{ number_format($val['asset_depreciation_book_value_last_year'], 2) }}</td>
                        <td style='text-align:right'>{{ number_format($val['asset_depreciation_amount'], 2) }} </td>
                        <td style='text-align:right'>{{ number_format($val['asset_depreciation_accumulation_this_year'], 2) }} </td>
                        <td style='text-align:right'>{{ number_format($val['asset_depreciation_book_value_this_year'], 2) }} </td>
                        
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