@inject('SystemUser', 'App\Http\Controllers\SystemUserController')

@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")

@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Kategori Barang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Kategori Barang</b> <small>Kelola Kategori Barang </small>
</h3>
<br/>

@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif
@if ($admin)
<div id="accordion">
    <form  method="post" action="{{ route('category-filter') }}" enctype="multipart/form-data">
    @csrf
        <div class="card border border-dark">
        <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                Filter
            </h5>
        </div>

        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                    <div class="form-group">
                            <div class="form-group">
                                <a class="text-dark">Merchant<a class='red'> *</a></a>
                                {!! Form::select('mid', $merchant, $sessiondata ?? '', [
                                    'class' => 'selection-search-clear required select-form',
                                    'name' => 'mid',
                                    'id' => 'mid',
                                    'autofocus'=>'autofocus',
                                ]) !!}
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
@endif
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar
    </h5>
    <div class="form-actions float-right">
        <button onclick="location.href='{{ url('/item-category/add') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Kategori Barang </button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No</th>
                        <th width="10%" style='text-align:center'>Kode Kategori</th>
                        <th width="20%" style='text-align:center'>Nama Kategori</th>
                        <th width="20%" style='text-align:center'>Wahana / Merchant</th>
                        <th width="10%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($data as $row)
                    <tr>
                        <td style='text-align:center'>{{ $no++ }}</td>
                        <td>{{ $row['item_category_code'] }}</td>
                        <td>{{ $row['item_category_name'] }}</td>
                        <td>{{ $row->merchant->merchant_name }}</td>
                        <td class="">
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/item-category/edit-category/'.$row['item_category_id']) }}">Edit</a>
                            <button type="button" onclick="$('this').attr('disabled');check('{{$row->item_category_name}}','{{ route('delete-item-category',$row->item_category_id) }}','{{$row->item_category_id}}')" class="btn btn-outline-danger btn-sm" >Hapus</button>
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

@section('js')
    <script>
        function check(name,uri,id){
            $.ajax({
				type: "GET",
				url : "{{url('/item-category/check-delete-category/')}}"+'/'+id,
				success: function(msg){
                    console.log(msg);
                    if(msg!=0){
                        alert('Kategori ini memiliki barang');
                        return 0;
                    }
                    if(confirm(`Yakin Ingin Menghapus Kategori dengan Nama '`+name+`' ?`)){
                         window.location.href = uri;
                       }
			}

		});
}
    </script>
@stop