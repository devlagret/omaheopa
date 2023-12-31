@inject('SystemUser', 'App\Http\Controllers\SystemUserController')

@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")
@section('js')
<script>
 function deleteItem(id,name){
    $.ajax({
				type: "GET",
				url : "{{url('/item/check-delete-item/')}}"+'/'+id,
				success: function(msg){
                    console.log(msg);
                    if(msg!=0){
                        if(confirm('Barang "'+name+'" dipakai di paket. Anda yakin ingin tetap menghapus?')){
                         window.location.href = "{{ url('/item/delete-item/') }}"+'/'+id;
                        }
                        return 0;
                    }
                    if(confirm(`Yakin Ingin Menghapus Item dengan nama '`+name+`' ?`)){
                      window.location.href = "{{ url('/item/delete-item/') }}"+'/'+id;
                    }
                    return 0;
			}

		});
 }

function check(name,uri){
 if(confirm(`Yakin Ingin Menghapus Paket dengan nama '`+name+`' ?`)){
   window.location.href = uri;
 }
}
</script>
@stop
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Barang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Barang</b> <small>Kelola Barang </small>
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
        Daftar
    </h5>
    <div class="form-actions float-right">
        <button onclick="location.href='{{ url('/item/add-item') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Barang </button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No</th>
                        <th width="15%" style='text-align:center'>Nama Kategori Barang</th>
                        <th width="15%" style='text-align:center'>Kode Barang</th>
                        <th width="20%" style='text-align:center'>Wahana / Merchant</th>
                        <th width="20%" style='text-align:center'>Nama Barang</th>
                        <th width="12%" style='text-align:center'>Barcode Barang</th>
                        <th width="15%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    {{-- @foreach($paket as $val)
                    <tr>
                        <td style='text-align:center'>{{ $no++ }}</td>
                        <td>PAKET</td>
                        <td>{{ $val->package_code }}</td>
                        <td>{{ $val->merchant->merchant_name }}</td>
                        <td>{{ $val->package_name }}</td>
                        <td class='text-center'>{{ $val->package_barcode }}</td>
                        <td class="text-center">
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ route('package.edit',$val->item_package_id) }}">Edit</a>
                            <a type="button" class="btn btn-outline-danger btn-sm"  onclick="check('{{$val->package_name}}','{{ route('package.delete',$val->item_package_id) }}')">Hapus</a>
                        </td>
                    </tr>
                    @endforeach --}}
                    @foreach($data as $row)
                    <tr>
                        <td style='text-align:center'>{{ $no++ }}</td>
                        <td>{{ $row->category->item_category_name }}</td>
                        <td>{{ $row['item_code'] }}</td>
                        <td>{{ $row->merchant->merchant_name??'Kategori Umum' }}</td>
                        <td>{{ $row['item_name'] }}</td>
                        <td class='text-center'><a type='button' class='btn btn-outline-dark btn-sm' href="{{route('item-barcode.index', $row['item_id'])}}"><i class='fa fa-barcode'></i> Barcode</a></td>
                        <td class="text-center">
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/item/edit-item/'.$row['item_id']) }}">Edit</a>
                            <a type="button" class="btn btn-outline-danger btn-sm" onclick="deleteItem('{{$row['item_id']}}','{{$row['item_name']}}')">Hapus</a>
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

@stop