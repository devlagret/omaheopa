@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
    function function_elements_add(name, value){

		$.ajax({
				type: "POST",
				url : "{{route('room-price.elements-add')}}",
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
        if($('#room_price_price_view').val()!=''){
            var price = parseInt($('#room_price_price_view').val());
            $('#room_price_price').val(price);
            $('#room_price_price_view').val(toRp(price));
        }
        $('#room_price_price_view').on("change", function() {
            var price = parseInt($(this).val());
            function_elements_add('room_price_price', price)
            console.log(price);
            $('#room_price_price').val(price);
            $('#room_price_price_view').val(toRp(price));
        });
        $("#limited-price").click(function () {
            if(this.checked){
                $('#room_price_start_date').prop('disabled', false);
                $('#room_price_end_date').prop('disabled', false);
                $('#price_date').show();
            }else{
                $('#room_price_start_date').prop('disabled', true);
                $('#room_price_end_date').prop('disabled', true);
                $('#price_date').hide();
            }
        });
    });
</script>
@stop
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('room-price.index') }}">Daftar Harga Kamar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Harga Kamar</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Edit Harga Kamar
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
            <button onclick="location.href='{{ route('room-price.index') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php
            if (empty($sessiondata)){
                $sessiondata['room_id'] = '';
                $sessiondata['price_type_id'] = '';
                $sessiondata['room_price_price'] = '';
                $sessiondata['room_price_start_date'] = '';
                $sessiondata['room_price_end_date'] = '';
            }
        ?>

    <form method="post" action="{{ route('room-price.process-edit') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" autocomplete="off" name="room_price_id" value="{{$roomprice->room_price_id}}"/>
        <div class="card-body">
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="room-price">
                    <div class="row form-group mt-5">
                        <div class="col">
                            <div class="form-group">
                                <a class="text-dark">Nama Kamar<a class='red'> *</a></a>
                                {!! Form::select('room_id', $room, $sessiondata['room_id']?$sessiondata['room_id']:$roomprice->room_id, [
                                    'class' => 'form-control selection-search-clear select-form required',
                                    'name' => 'room_id',
                                    'id' => 'room_id',
                                    'onchange' => 'function_elements_add(this.name, this.value)',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row form-group mt-5">
                        <div class="col">
                            <div class="form-group">
                                <a class="text-dark">Tipe Harga<a class='red'> *</a></a>
                                {!! Form::select('price_type_id', $pricetype, $sessiondata['price_type_id']?$sessiondata['price_type_id']:$roomprice->price_type_id, [
                                    'class' => 'form-control selection-search-clear select-form required',
                                    'name' => 'price_type_id',
                                    'id' => 'price_type_id',
                                    'onchange' => 'function_elements_add(this.name, this.value)',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row form-group mt-5">
                        <div class="col">
                            <div class="form-group">
                                <a class="text-dark">Harga Kamar<a class='red'> *</a></a>
                                <input placeholder="Masukan harga kamar" required class="form-control input-bb required" name="room_price_price_view" id="room_price_price_view" type="text" autocomplete="off" value="{{old('room_price_price',$sessiondata['room_price_price']?$sessiondata['room_price_price']:$roomprice->room_price_price) }}" />
                                <input class="form-control input-bb required" name="room_price_price" id="room_price_price" type="hidden" autocomplete="off" value="{{old('room_price_price',$sessiondata['room_price_price']??'') }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" {{empty($roomprice->room_price_start_date)?'':'checked'}} id="limited-price" type="checkbox" >
                                <label class="form-check-label">Set Waktu Tersedia</label>
                             </div>
                        </div>
                    </div>
                    <div id="price_date" class = "row form-group mt-5" style="display: {{empty($roomprice->room_price_start_date)?"none":''}};">
                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input">
                                <section class="control-label">Tanggal Mulai
                                </section>
                                <input type ="date" {{empty($roomprice->room_price_start_date)?'':"disabled"}} class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="room_price_start_date" id="room_price_start_date" value="{{ old('room_price_start_date',$sessiondata['room_price_start_date']?$sessiondata['room_price_start_date']:$roomprice->room_price_start_date) }}" style="width: 15rem;" onchange="function_elements_add(this.name, this.value)"/>
                            </div>
                        </div>
                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input">
                                <section class="control-label">Tanggal Akhir
                                </section>
                                <input type ="date"  {{empty($roomprice->room_price_start_date)?'':"disabled"}} class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="room_price_end_date" id="room_price_end_date" value="{{ old('room_price_end_date',$sessiondata['room_price_end_date']?$sessiondata['room_price_end_date']:$roomprice->room_price_end_date) }}" style="width: 15rem;" onchange="function_elements_add(this.name, this.value)"/>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</button>
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