@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")
@section('js')
<script>
    function function_elements_add(name, value){
        console.log("name " + name);
        console.log("value " + value);
		$.ajax({
				type: "POST",
				url : "{{route('elements-add-category')}}",
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
				url : "{{route('add-reset-category')}}",
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
    })
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('item-category') }}">Daftar Kategori Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Kategori Barang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Kategori Barang
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
            <button onclick="location.href='{{ route($url) }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
        </div>
    </div>

    <?php 
            if (empty($datacategory)){
            $datacategory['item_category_code']    = '';
            $datacategory['item_category_name']    = '';
            $datacategory['item_category_remark']  = '';
            $datacategory['merchant_id']  = '';
            $datacategory['from_item']  = 0;
            }
        ?>

    <form method="post" action="{{ route('process-add-item-category') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kode Kategori<a class='red'> *</a></a>
                        <input class="form-control input-bb required" placeholder="Masukan Kode Kategori" autofocus="autofocus" name="item_category_code" id="category_code" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value);" value="{{ $datacategory['item_category_code'] }}"/>
                        <input name="from_item" id="from_item" type="hidden"  value="{{ $datacategory['from_item'] }}"/>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Kategori<a class='red'> *</a></a>
                        <input class="form-control input-bb required" placeholder="Masukan Nama Kategori" required name="item_category_name" id="category_name" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value);" value="{{ $datacategory['item_category_name'] }}"/>
                    </div>
                </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Wahana / Merchant<a class='red'> *</a></a>
                            {!! Form::select('merchant_id', $merchant, $datacategory['merchant_id_view'] ?? '', [
                                'class' => 'form-control selection-search-clear select-form required '.($merchant->count() == 1||$datacategory['from_item']?'disabled':''),
                                'name' => 'merchant_id_view',
                                'id' => 'merchant_id_view',
                                $merchant->count() == 1||$datacategory['from_item']?'disabled':''
                            ]) !!}
                                <input type="hidden" name="merchant_id" id="merchant_id"/>
                        </div>
                    </div>
                <div class="col-md-8 mt-3">
                    <div class="form-group">
                        <a class="text-dark">Keterangan</a>
                        <textarea class="form-control input-bb" placeholder="Masukan Keterangan Kategori" name="item_category_remark" id="category_remark" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value);">{{ $datacategory['item_category_remark'] }}</textarea>
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