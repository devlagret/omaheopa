@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
    function function_elements_add(name, value){

		$.ajax({
				type: "POST",
				url : "{{route('sales-room-menu.elements-add')}}",
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
    $(document).ready(function(){
        if($('#room_menu_price_view').val()!=''){
            var price = parseInt($('#room_menu_price_view').val());
            $('#room_menu_price').val(price);
            $('#room_menu_price_view').val(toRp(price));
        }
        $('#room_menu_price_view').on("change", function() {
            var price = parseInt($(this).val());
            function_elements_add('room_menu_price', price)
            console.log(price);
            $('#room_menu_price').val(price);
            $('#room_menu_price_view').val(toRp(price));
        });
    });
</script>
@stop
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales-room-menu.index') }}">Daftar Menu Kamar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Menu Kamar</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Edit Menu Kamar
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
            <button onclick="location.href='{{ route('sales-room-menu.index') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php
            if (empty($sessiondata)){
                $sessiondata['room_menu_name'] = '';
                $sessiondata['room_menu_type'] = '';
                $sessiondata['room_menu_price'] = '';
            }
        ?>

    <form method="post" action="{{ route('sales-room-menu.process-edit') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="sales-room-menu">
                    <div class="row form-group mt-5">
                        <div class="col">
                            <div class="form-group">
                                <a class="text-dark">Nama<a class='red'> *</a></a>
                                <input  name="room_menu_id" id="room_menu_id" type="hidden"value="{{$roommenu->room_menu_id}}"/>
                                <input placeholder="Masukan nama menu" autofocus="autofocus" required class="form-control input-bb required" name="room_menu_name" id="room_menu_name" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{old('room_menu_name',$sessiondata['room_menu_name']?$sessiondata['room_menu_name']:$roommenu->room_menu_name) }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group mt-5">
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Tipe Menu<a class='red'> *</a></a>
                                {!! Form::select('room_menu_type', $tipemenu, $sessiondata['room_menu_type'] ?$sessiondata['room_menu_type']:$roommenu->room_menu_type, [
                                    'class' => 'form-control selection-search-clear select-form required',
                                    'name' => 'room_menu_type',
                                    'id' => 'room_menu_type',
                                    'onchange' => 'function_elements_add(this.name, this.value)',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row form-group mt-5">
                        <div class="col">
                            <div class="form-group">
                                <a class="text-dark">Harga<a class='red'> *</a></a>
                                <input placeholder="Masukan harga menu" required class="form-control input-bb required" name="room_menu_price_view" id="room_menu_price_view" type="text" autocomplete="off" value="{{old('room_menu_price',$sessiondata['room_menu_price']?$sessiondata['room_menu_price']:$roommenu->room_menu_price) }}" />
                                <input class="form-control input-bb required" name="room_menu_price" id="room_menu_price" type="hidden" autocomplete="off"/>
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