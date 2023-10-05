@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('js')
    <script>
        var table;
        $(document).ready(function(){
            table =  $('#tabel-card').DataTable({
            "columnDefs": [
                { className: "text-center", "targets": [ 0,2,4 ] }
                ],
             "processing": true,
             "serverSide": true,
             "pageLength": 5,
             "lengthMenu": [ [5, 15, 20, 10000], [5, 15, 20, "All"] ],
             "ajax": "{{ route('sc.table') }}",
             "columns":[
                {data: 'no'},
                {data: 'merchant'},
                {data: 'item_category_name'},
                {data: 'item_name'},
                {data: 'item_unit_name'},
                {data: 'opening_stock'},
                {data: 'stock_in'},
                {data: 'stock_out'},
                {data: 'last_balence'},
                {data: 'action'},
             ],
        
             });
        });
    </script>
@endsection

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Kartu Stok </li>
    </ol>
  </nav>

@stop

@section('content')
<h3 class="page-title">
    <b>Kartu Stok</b>
</h3>
<br/>
<div id="accordion">
    <form action="{{ route('sc.filter') }}" method="post">
        @csrf
        <div class="card border border-dark">
            <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <h5 class="mb-0">
                    Filter
                </h5>
            </div>
            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    <div class="row">
                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input">
                                <section class="control-label">Tanggal Awal
                                    <span class="required text-danger">
                                        *
                                    </span>
                                </section>
                                <input style="width: 50%" class="form-control input-bb" name="start_date" id="start_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $filter['start_date']??date('Y-m-d') }}"/>
                            </div>
                        </div>

                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input">
                                <section class="control-label">Tanggal Akhir
                                    <span class="required text-danger">
                                        *
                                    </span>
                                </section>
                                <input style="width: 50%" class="form-control input-bb" name="end_date" id="end_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $filter['end_date']??date('Y-m-d') }}"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <div class="form-actions float-right">
                        <a href="{{ route('sc.filter-reset') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
                        <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif 
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar
    </h5>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table style="width:100%" id="tabel-card" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="5%" style='text-align:center'>No</th>
                        <th style='text-align:center'>Wahana / Merchant</th>
                        <th style='text-align:center'>Kategori</th>
                        <th style='text-align:center'>Nama Barang</th>
                        <th style='text-align:center'>Satuan</th>
                        <th style='text-align:center'>Saldo Awal</th>
                        <th style='text-align:center'>Masuk</th>
                        <th style='text-align:center'>Keluar</th>
                        <th style='text-align:center'>Saldo Akhir</th>
                        <th style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                  <?php $no = 1 ?>
                  @foreach ($data as $val)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $val->item->merchant->merchant_name }}</td>
                        <td>{{ $val->category->item_category_name}}</td>
                        <td>{{ $val->item->item_name }}</td>
                        <td>{{ $val->unit->item_unit_name }}</td>
                        <td>{{ $no++ }}</td>
                        <td>{{ $no++ }}</td>
                        <td>{{ $no++ }}</td>
                        <td>{{ $no++ }}</td>
                        <td><a type='button' href="{{route('sc.print',$val->item_stock_id)}}" class='btn btn-secondary btn-sm'><i class='fa fa-file-pdf'></i> Kartu Stok</a></td>
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