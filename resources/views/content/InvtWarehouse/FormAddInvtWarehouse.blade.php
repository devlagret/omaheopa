@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")
@section('js')
<script>
    function function_elements_add(name, value){
        console.log("name " + name);
        console.log("value " + value);
		$.ajax({
				type: "POST",
				url : "{{route('add-warehouse-elements')}}",
				data : {
                    'name'      : name, 
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
			}
		});
	}

    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('add-reset-warehouse')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}
    $(document).ready(function() {
        if($('#merchant_id_view').val()!=''){
            $('#merchant_id').val($('#merchant_id_view').val());
        }
        $("#merchant_id_view").change(function() {
            $('#merchant_id').val(this.value);
            function_elements_add(this.name, this.value)
        });
        $("#gp").click(function () {
            if(this.checked){
                $('#merchant_id').prop('disabled', true);
                $('#input-merchant').hide();
                $('#gp-input').addClass('col-md-6').removeClass('col-md-1');
            }else{
                $('#merchant_id').prop('disabled',false);
                $('#input-merchant').show();
                $('#gp-input').addClass('col-md-1').removeClass('col-md-6');
            }
        });
    });
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('/warehouse') }}">Daftar Gudang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Gudang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Gudang
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
            Form Tambah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('warehouse') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php 
            // if (empty($coresection)){
            //     $coresection['section_name'] = '';
            // }
        ?>

    <form method="post" action="{{ route('process-add-warehouse') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="card-body">
            <div class="row form-group">
                @if ($merchant->count()>1)
                <div id="gp-input" class="col-md-1">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="gp" class="form-check-input" id="gp">
                        <label class="form-check-label" for="exampleCheck1">Gudang Pusat</label>
                    </div>
                </div>
                @endif
                <div class="col-md-5" id="input-merchant">
                    <div class="form-group">
                        <a class="text-dark">Wahana / Merchant<a class='red'> *</a></a>
                        {!! Form::select('merchant_id', $merchant, $warehouses['merchant_id'] ?? '', [
                            'class' => 'selection-search-clear required select-form '.($merchant->count()==1?"disabled":""),
                            'name' => 'merchant_id_view',
                            'id' => 'merchant_id_view',
                            'autofocus' => 'autofocus',
                            'required',
                            $merchant->count()==1?"disabled":''
                        ]) !!}
                        <input type="hidden" name="merchant_id" id="merchant_id">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kode Gudang<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="warehouse_code" id="warehouse_code" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $warehouses['warehouse_code'] ?? '' }}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Gudang<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="warehouse_name" id="warehouse_name" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $warehouses['warehouse_name'] ?? ''}}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Telp Gudang<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="warehouse_phone" id="warehouse_phone" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $warehouses['warehouse_phone'] ?? ''}}"/>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <a class="text-dark">Alamat<a class='red'> *</a></a>
                        <textarea class="form-control input-bb" name="warehouse_address" id="warehouse_address" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{ $warehouses['warehouse_address'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i class="fa fa-times"></i> Batal</button>
                <button type="submit" name="Save" class="btn btn-primary" title="Save"><i class="fa fa-check"></i> Simpan</button>
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