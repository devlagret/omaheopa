@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
<script>
    function function_elements_add(name, value){

		$.ajax({
				type: "POST",
				url : "{{route('sales-room-facility.elements-add')}}",
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
        if($('#facility_price_view').val()!=''){
            var price = parseInt($('#facility_price_view').val());
            $('#facility_price').val(price);
            $('#facility_price_view').val(toRp(price));
        }
        $('#facility_price_view').on("change", function() {
            var price = parseInt($(this).val());
            function_elements_add('facility_price', price)
            console.log(price);
            $('#facility_price').val(price);
            $('#facility_price_view').val(toRp(price));
        });
    });
</script>
@stop
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales-room-facility.index') }}">Daftar Menu Kamar</a></li>
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
            <button onclick="location.href='{{ route('sales-room-facility.index') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{ route('sales-room-facility.process-edit') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="sales-room-menu">
                    <div class="row form-group mt-5">
                        <div class="col-6">
                            <div class="form-group">
                                <a class="text-dark">Nama<a class='red'> *</a></a>
                                <input  name="room_facility_id" id="room_menu_id" type="hidden"value="{{$facility->room_facility_id}}"/>
                                <input class="form-control required input-bb" required name="facility_name"
                                        id="facility_name" type="text" autocomplete="off"
                                        onchange="function_elements_add(this.name, this.value)"
                                        value="{{ $sessiondata['facility_name'] ?? $facility->facility_name  }}" />
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <a class="text-dark">Harga<a class='red'> *</a></a>
                                <input placeholder="Masukan harga menu" required class="form-control input-bb required" name="facility_price_view" id="facility_price_view" type="text" autocomplete="off" value="{{old('facility_price',$sessiondata['facility_price']??$facility->facility_price) }}" />
                                <input class="form-control input-bb required" name="facility_price" id="facility_price" type="hidden" autocomplete="off"/>
                            </div>
                        </div>
                        <div class="col-md-8 mt-3">
                            <div class="form-group">
                                <a class="text-dark">Keterangan</a>
                                <textarea class="form-control input-bb" name="facility_remark" id="facility_remark" type="text"
                                    autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{ $items['facility_remark']  ?? '' }}</textarea>
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