@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
    function function_elements_add(name, value){

		$.ajax({
				type: "POST",
				url : "{{route('division.elements-add')}}",
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
        <li class="breadcrumb-item"><a href="{{ route('division.index') }}">Daftar Divisi</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Divisi</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Divisi
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
            Form Tambah Divisi
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ route('division.index') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php
            if (empty($sessiondata)){
                $sessiondata['division_name'] = '';
                $sessiondata['division_code'] = '';
            }
        ?>

    <form method="post" action="{{ route('division.process-add') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="division">
                    <div class="row form-group mt-5">
                        <div class="col">
                            <div class="form-group">
                                <a class="text-dark">Kode Divisi<a class='red'> *</a></a>
                                <input placeholder="Masukan kode divisi" required class="form-control input-bb required" name="division_code" id="division_code" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{old('division_code',$sessiondata['division_code']??'') }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group mt-5">
                        <div class="col">
                            <div class="form-group">
                                <a class="text-dark">Nama Divisi<a class='red'> *</a></a>
                                <input placeholder="Masukan nama divisi" autofocus="autofocus" required class="form-control input-bb required" name="division_name" id="division_name" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{old('division_name',$sessiondata['division_name']??'') }}"/>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" class="btn btn-danger" ><i class="fa fa-times"></i> Batal</button>
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