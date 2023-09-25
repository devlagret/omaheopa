@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
    function function_elements_add(name, value){

		$.ajax({
				type: "POST",
				url : "{{route('aset-type.elements-add')}}",
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
</script>
@stop
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('aset-type.index') }}">Daftar Kamar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Kamar</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Edit Kamar
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
            <button onclick="location.href='{{ route('aset-type.index') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php
            if (empty($sessiondata)){
                $sessiondata['asset_type_code'] = '';
                $sessiondata['asset_type_name'] = '';
                $sessiondata['asset_type_description'] = '';
            }
        ?>

    <form method="post" action="{{ route('aset-type.process-edit') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="supplier">
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Nama Supplier<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="asset_type_code" id="asset_type_code" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ old('asset_type_code',$sessiondata['asset_type_code']?$sessiondata['asset_type_code']:$supplier->asset_type_code) }}"/>
                                <input class="form-control input-bb" name="asset_type_id" id="asset_type_id" type="text" autocomplete="off" value="{{ $supplier['asset_type_id'] }}" hidden/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Telp Supplier</a>
                                <input class="form-control input-bb" name="asset_type_name" id="asset_type_name" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{old('asset_type_name',$sessiondata['asset_type_name']?$sessiondata['asset_type_name']:$supplier->asset_type_name) }}"/>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <a class="text-dark">Alamat</a>
                                <textarea class="form-control input-bb" name="asset_type_description" id="asset_type_description" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{old('asset_type_description',$sessiondata['asset_type_description']?$sessiondata['asset_type_description']:$supplier->asset_type_description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="button" class="btn btn-danger" onclick="$('#room_name').val('');"><i class="fa fa-times"></i> Batal</button>
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