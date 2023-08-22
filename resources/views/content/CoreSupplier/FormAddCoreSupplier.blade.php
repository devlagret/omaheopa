@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
    <script>
        function function_elements_add(name, value) {

            $.ajax({
                type: "POST",
                url: "{{ route('supplier.elements-add') }}",
                data: {
                    'name': name,
                    'value': value,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(msg) {
                    console.log(msg);
                }
            });
        }

        function reset() {
            console.log('foo');
            $('#room_name').val('');
        }
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('supplier.index') }}">Daftar Supplier</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Supplier</li>
        </ol>
    </nav>

@stop

@section('content')

    <h3 class="page-title">
        Form Tambah Supplier
    </h3>
    <br />
    @if (session('msg'))
        <div class="alert alert-info" role="alert">
            {{ session('msg') }}
        </div>
    @endif

    @if (count($errors) > 0)
        <div class="alert alert-danger" role="alert">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
    @endif
    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Form Tambah Supplier
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ route('supplier.index') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>

        <?php
        if (empty($sessiondata)) {
            $sessiondata['supplier_name'] = '';
            $sessiondata['supplier_mobile_phone1'] = '';
            $sessiondata['supplier_address'] = '';
            $sessiondata['room_facility'] = '';
        }
        ?>

        <form method="post" action="{{ route('supplier.process-add') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade show active" id="supplier">
                     <div class="row form-group">
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Nama Supplier<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="supplier_name" id="supplier_name" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $suppliers['supplier_name'] ??''}}"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Telp Supplier</a>
                                <input class="form-control input-bb" name="supplier_mobile_phone1" id="supplier_mobile_phone1" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $suppliers['supplier_mobile_phone1'] ??''}}"/>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <a class="text-dark">Alamat</a>
                                <textarea class="form-control input-bb" name="supplier_address" id="supplier_address" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{ $suppliers['supplier_address'] ??''}}</textarea>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" class="btn btn-danger"><i
                            class="fa fa-times"></i> Batal</button>
                    <button type="submit" class="btn btn-primary" title="Save"><i class="fa fa-check"></i>
                        Simpan</button>
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
