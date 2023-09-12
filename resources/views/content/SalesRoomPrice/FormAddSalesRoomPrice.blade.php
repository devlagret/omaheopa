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
     function changeType() {
            loading();
            var building_id = $("#building_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('room-price.get-room-type') }}",
                dataType: "html",
                data: {
                    'building_id': building_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    function_elements_add('building_id', building_id);
                    $('#room_type_id').html(return_data);
                    changeRoom($('#room_type_id').val());
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function changeRoom(room_type_id) {
            loading();
            var building_id = $("#building_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('room-price.get-room') }}",
                dataType: "html",
                data: {
                    'room_type_id': room_type_id,
                    'building_id': building_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    function_elements_add('room_type_id', room_type_id);
                    $('#room_id').html(return_data);
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 100);
                },
                complete: function() {
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                }
            });
        }
    $(document).ready(function(){
        changeType();
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
                $('#room_price_start_date').prop('disabled', true);
                $('#room_price_end_date').prop('disabled', true);
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
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('room-price.index') }}">Daftar Harga Kamar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Harga Kamar</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Harga Kamar
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
            Form Tambah Harga Kamar
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
                $sessiondata['room_id'] = '';
                $sessiondata['room_id'] = '';
            }
        ?>

    <form method="post" action="{{ route('room-price.process-add') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="room-price">
                    <div class="row form-group mt-5">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <a class="text-dark">Bagunan<a class='red'> *</a></a>
                                {!! Form::select('building_id', $building, $sessiondata['building_id'] ?? '', [
                                    'class' => 'selection-search-clear required select-form',
                                    'name' => 'building_id',
                                    'id' => 'building_id',
                                    'onchange' => 'changeType()',
                                    'form' => 'form-barang',
                                    'required',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <a class="text-dark">Tipe Kamar<a class='red'> *</a></a>
                                <select class="selection-search-clear required select-form" required
                                    form="form-barang" placeholder="Pilih Tipe" name="room_type_id"
                                    id="room_type_id" onchange="changeRoom(this.value)">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <a class="text-dark">Nama Kamar<a class='red'> *</a></a>
                                <select class="selection-search-clear required select-form" required
                                    placeholder="Pilih Nama" name="room_id" id="room_id"
                                    onchange="function_elements_add(this.name, this.value)">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <a class="text-dark">Tipe Harga<a class='red'> *</a></a>
                                {!! Form::select('price_type_id', $pricetype, $sessiondata['price_type_id']??'', [
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
                                <input placeholder="Masukan harga kamar" required class="form-control input-bb required" name="room_price_price_view" id="room_price_price_view" type="text" autocomplete="off" value="{{old('room_price_price',$sessiondata['room_price_price']??'') }}" />
                                <input class="form-control input-bb required" name="room_price_price" id="room_price_price" type="hidden" autocomplete="off" value="{{old('room_price_price',$sessiondata['room_price_price']??'') }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" id="limited-price" type="checkbox" >
                                <label class="form-check-label">Set Waktu Tersedia</label>
                             </div>
                        </div>
                    </div>
                    <div id="price_date" class="row form-group mt-5" style="display: none;">
                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input">
                                <section class="control-label">Tanggal Mulai
                                </section>
                                <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="room_price_start_date" id="room_price_start_date" value="{{ old('room_price_start_date',$sessiondata['room_price_start_date']??'') }}" style="width: 15rem;" onchange="function_elements_add(this.name, this.value)"/>
                            </div>
                        </div>
                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input">
                                <section class="control-label">Tanggal Akhir
                                </section>
                                <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="room_price_end_date" id="room_price_end_date" value="{{ old('room_price_end_date',$sessiondata['room_price_end_date']??'') }}" style="width: 15rem;" onchange="function_elements_add(this.name, this.value)"/>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="button" class="btn btn-danger" onclick="$('#room_price_name').val('');"><i class="fa fa-times"></i> Batal</button>
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