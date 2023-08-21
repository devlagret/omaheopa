@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
    function function_elements_add(name, value){

		$.ajax({
				type: "POST",
				url : "{{route('price-type.elements-add')}}",
				data : {
                    'name'      : name,
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
                    console.log(msg);
			}
		});
	}
    function reset(){
        console.log('foo');
    $('#price_type_name').val('');
    }
</script>
@stop
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('room-type.index') }}">Daftar Tipe Harga</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Tipe Harga</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Edit Tipe Harga
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
            <button onclick="location.href='{{ route('price-type.index') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php
            if (empty($sessiondata)){
                $sessiondata['price_type_name'] = '';
            }
        ?>

    <form method="post" action="{{ route('price-type.process-edit') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="price-type">
                    <div class="row form-group mt-5">
                        <div class="col">
                            <div class="form-group">
                                <a class="text-dark">Nama Tipe Harga<a class='red'> *</a></a>
                                <input type="hidden" autocomplete="off" tabindex="-1" name="price_type_id" value="{{$pricetype->price_type_id}}"/>
                                <input placeholder="Masukan tipe kamar" autofocus="autofocus"  required class="form-control input-bb required" name="price_type_name" id="price_type_name" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{old('price_type_name',$sessiondata['price_type_name']?$sessiondata['price_type_name']:$pricetype->price_type_name) }}"/>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="button" class="btn btn-danger" onclick="$('#price_type_name').val('');"><i class="fa fa-times"></i> Batal</button>
                <button type="submit" class="btn btn-primary" title="Save"><i class="fa fa-check"></i> Simpan</button>
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