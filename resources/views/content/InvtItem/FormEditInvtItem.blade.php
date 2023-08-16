@extends('adminlte::page')

@section('title', 'MOZAIC Point of Sales')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('item') }}">Daftar Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ubah Barang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Ubah Barang
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
            Form Tambah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('item') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php 
            // if (empty($coresection)){
            //     $coresection['section_name'] = '';
            // }
        ?>

    <form method="post" action="{{ route('process-edit-item') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" href="#barang" role="tab" data-toggle="tab">Data Barang</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#kemasan" role="tab" data-toggle="tab">Kemasan</a>
                </li>
              </ul>
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="barang">
                    <div class="row form-group mt-5">
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                                {!! Form::select('item_category_id',  $category, $items['item_id'], ['class' => 'selection-search-clear select-form']) !!}
                                {{-- <select name="item_category_id" id="category_id" class="form-control">
                                    @foreach ($category as $row )
                                        <option value="{{ $row['item_category_id'] }}">{{ $row['item_category_name'] }}</option>
                                    @endforeach
                                </select> --}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Status Barang<a class='red'> *</a></a>
                                <select name="item_status" id="item_status" class="form-control">
                                    <option value="0">Aktif</option>
                                    <option value="1">Not Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Kode Barang<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="item_code" id="item_code" type="text" autocomplete="off" value="{{ $items['item_code'] }}"/>
                                <input class="form-control input-bb" name="item_id" id="item_id" type="text" autocomplete="off" value="{{ $items['item_id'] }}" hidden/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="item_name" id="item_name" type="text" autocomplete="off" value="{{ $items['item_name'] }}"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Barcode Barang<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="item_barcode" id="item_barcode" type="text" autocomplete="off" value="{{ $items['item_barcode'] }}"/>
                            </div>
                        </div>
                        <div class="col-md-8 mt-3">
                            <div class="form-group">
                                <a class="text-dark">Keterangan<a class='red'> *</a></a>
                                <textarea class="form-control input-bb" name="item_remark" id="item_remark" type="text" autocomplete="off">{{ $items['item_remark'] }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="kemasan">
                    <h5 class="mt-3"><b>Kemasan 1</b></h5>
                    <div class="row form-group mt-2">
                        <div class="col-md-3">
                            <div class="form-group">
                                <a class="text-dark">Satuan Barang 1<a class='red'> *</a></a>
                                {!! Form::select('item_unit_id',  $itemunits, $items['item_id'], ['class' => 'selection-search-clear select-form']) !!}
                                {{-- <select name="item_unit_id" id="item_unit_id" class="form-control">
                                    @foreach ($itemunits as $row )
                                        <option value="{{ $row['item_unit_id'] }}">{{ $row['item_unit_name'] }}</option>   
                                    @endforeach
                                </select> --}}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <a class="text-dark">Kuantitas Standar 1<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="item_quantity" id="item_quantity" type="number" autocomplete="off" value="{{ $items['item_default_quantity'] }}"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <a class="text-dark">Harga Jual 1<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="item_price" id="item_price" type="number" autocomplete="off" value="{{ $items['item_unit_price'] }}"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <a class="text-dark">Harga Beli 1<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="item_cost" id="item_cost" type="number" autocomplete="off" value="{{ $items['item_unit_cost'] }}"/>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            {{-- <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kode Kategori Barang<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="category_code" id="category_code" type="text" autocomplete="off"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="category_name" id="category_name" type="text" autocomplete="off"/>
                    </div>
                </div>
                <div class="col-md-8 mt-3">
                    <div class="form-group">
                        <a class="text-dark">Keterangan<a class='red'> *</a></a>
                        <textarea class="form-control input-bb" name="category_remark" id="category_remark" type="text" autocomplete="off"></textarea>
                    </div>
                </div>
            </div> --}}
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onclick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
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