@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
    function function_elements_add(name, value){

		$.ajax({
				type: "POST",
				url : "{{route('room.elements-add')}}",
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
    $('#room_name').val('');
    }
</script>
@stop
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('room.index') }}">Daftar Kamar</a></li>
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
            <button onclick="location.href='{{ route('room.index') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php
            if (empty($sessiondata)){
                $sessiondata['room_name'] = '';
                $sessiondata['room_type_id'] = '';
                $sessiondata['building_id'] = '';
                $sessiondata['room_facility'] = '';
            }
        ?>

    <form method="post" action="{{ route('room.process-edit') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="room">
                    <div class="row form-group mt-5">
                        <div class="col">
                            <div class="form-group">
                                <input type="hidden" name="room_id" value="{{$room->room_id}}"/>
                                <a class="text-dark">Nama Kamar<a class='red'> *</a></a>
                                <input placeholder="Masukan nama kamar" required class="form-control input-bb required"
                                    name="room_name" id="room_name" type="text" autocomplete="off"
                                    onchange="function_elements_add(this.name, this.value)" required
                                    value="{{ old('room_name', $sessiondata['room_name'] ?$sessiondata['room_name']:$room->room_name) }}" />
                            </div>
                        </div>
                    </div>
                    <div class="row form-group mt-5">
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Tipe Kamar<a class='red'> *</a></a>
                                {!! Form::select('room_type_id', $roomtype, $sessiondata['room_type_id'] ?$sessiondata['room_type_id']:$room->room_type_id, [
                                    'class' => 'form-control selection-search-clear select-form required',
                                    'name' => 'room_type_id',
                                    'id' => 'room_type_id',
                                    'onchange' => 'function_elements_add(this.name, this.value)',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row form-group mt-5">
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Bangunan<a class='red'> *</a></a>
                                {!! Form::select('building_id', $building, $sessiondata['building_id'] ?$sessiondata['building_id']:$room->building_id, [
                                    'class' => 'form-control selection-search-clear select-form required',
                                    'name' => 'building_id',
                                    'id' => 'building_id',
                                    'onchange' => 'function_elements_add(this.name, this.value)',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row form-group mt-5">
                        <div class="col">
                            <div class="form-group">
                                <a class="text-dark">Fasilitas Kamar</a>
                                <textarea placeholder="Masukan fasilitas kamar" class="form-control input-bb"
                                    name="room_facility" id="room_facility" type="text" autocomplete="off"
                                    onchange="function_elements_add(this.name, this.value)"
                                   >{{ old('room_facility', $sessiondata['room_facility'] ?$sessiondata['room_facility']:$room->room_facility) }}</textarea>
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