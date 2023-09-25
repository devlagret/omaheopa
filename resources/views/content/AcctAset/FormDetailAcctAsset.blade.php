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
<div class="row">
    <div class="col-md-12">
        <div class="portlet"> 
           <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        Detail Aset
                    </div>

                    {{-- <div class="actions">
                        <a href="<?php echo base_url();?>asset" class="btn btn-success btn-sm">
                            <i class="fa fa-angle-left"></i>
                            <span class="hidden-480">
                                Kembali
                            </span>
                        </a>
                    </div> --}}
                </div>
                <!-- <?php
                    print_r('$acctasset');
                    print_r($acctasset);
                    ?> -->
                <div class="portlet-body ">
                    <div class="form-body">
                    
                        <div class="row">
                            <!-- <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="asset_type_name" id="asset_type_name" placeholder="123" value="<?php echo $acctasset['asset_type_name'];?>" readonly/>
                                    <label class="control-label">Tipe</label>
                                </div>
                            </div> -->
                        
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" id="asset_type_name" name="asset_type_name" onChange="function_elements_add(this.name, this.value);" value="<?php echo $acctasset['asset_type_name']; ?>" readonly/>	
                                    <label>Tipe Asset</label>
                                </div>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="asset_code" id="asset_code" placeholder="123" value="<?php echo $acctasset['asset_code'];?>" readonly/>
                                    <label class="control-label">Kode Asset</label>
                                </div>
                            </div>
                        
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="asset_name" id="asset_name" placeholder="Nama" value="<?php echo $acctasset['asset_name'];?>" readonly/>
                                    <label class="control-label">Nama Asset</label>
                                </div>
                            </div>
                        </div>
                    
                        
                        {{-- <div class="row">
                            <div class="col-md-3">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="asset_purchase_date" id="asset_purchase_date" placeholder="Name" value="<?php echo tgltoview($acctasset['asset_purchase_date']);?>" readonly/>
                                    <label class="control-label">Tanggal Pembelian</label>
                                </div>
                            </div> --}}

                            <div class="col-md-3">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="item_unit_code" id="item_unit_code" value="<?php echo number_format($acctasset['item_unit_code'], 2);?>" readonly/>
                                    <label class="control-label">Satuan</label>
                                </div>
                            </div>
                    
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="asset_purchase_value" id="asset_purchase_value" value="<?php echo number_format($acctasset['asset_purchase_value'], 2);?>" readonly/>
                                    <label class="control-label">Harga Pembelian</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="asset_purchase_value" id="asset_purchase_value" value="<?php echo $acctasset['asset_depreciation_type'];?>" readonly/>
                                    <label class="control-label">Metode Penyusutan</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="asset_book_value" id="asset_book_value" value="<?php echo number_format($acctasset['asset_book_value'], 2);?>" readonly/>
                                    <label class="control-label">Nilai Perolehan</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="asset_estimate_lifespan" id="asset_estimate_lifespan" placeholder="123" value="<?php echo $acctasset['asset_estimated_lifespan'];?>" readonly/>	
                                    <label>Taksiran Usia</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="asset_salvage_value" id="asset_salvage_value" value="<?php echo number_format($acctasset['asset_salvage_value'], 2);?>" readonly/>
                                    <label class="control-label">Nilai Residu</label>
                                </div>
                            </div>
                        </div>
                    
                        {{-- <div class="form-group form-md-line-input">
                            <?php echo form_textarea(array('name'=>'asset_location_detail', 'rows'=>'3', 'class'=>'form-control','id'=>'asset_location_detail','value'=>$acctasset['asset_location_detail']))?>
                            <label class="control-label">Detail Lokasi</label>
                    
                        </div>
                         --}}
                        {{-- <div class="form-group form-md-line-input">
                            <?php echo form_textarea(array('name'=>'asset_description', 'rows'=>'3', 'class'=>'form-control','id'=>'asset_description','value'=>$acctasset['asset_description']))?>
                            <label class="control-label">Deskripsi</label>
                    
                        </div> --}}
                    </div>
                </div>
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