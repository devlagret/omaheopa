@inject('GeneralReser', 'App\Http\Controllers\SalesInvoiceController')
@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
    <script>
        function function_elements_add(name, value) {
            // console.log("name " + name);
            // console.log("value " + value);
            $.ajax({
                type: "POST",
                url: "{{ route('add-elements-sales-tiket') }}",
                data: {
                    'name': name,
                    'value': value,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(msg) {}
            });
        }
     
        $(document).ready(function() {
            // changeCategory('item_id_view')
            
    


        });



    

        function reset_add() {
            $.ajax({
                type: "GET",
                url: "{{ route('add-reset-sales-invoice') }}",
                success: function(msg) {
                    location.reload();
                }

            });
        }
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ url('sales-tiket') }}">Daftar Master Reservasi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Master Reservasi</li>
        </ol>
    </nav>

@stop

@section('content')

    <h3 class="page-title">
        Form Tambah Master Reservasi
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
                Form Tambah
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ url('general-reservation') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>
        <form method="post" action="" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Paket</a>
                            <input class="form-control input-bb" name="reservation_name" id="reservation_name" type="text"
                                autocomplete="off" value=""
                                onChange="function_elements_add(this.name, this.value);" />

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Harga</a>
                            <input class="form-control input-bb" name="reservation_price" id="reservation_price" type="text"
                                autocomplete="off" value=""
                                onChange="function_elements_add(this.name, this.value);" />
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">

                    <div class="form-actions float-right">
                        <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i
                                class="fa fa-times"></i> Reset Data</button>
                        <button type="submit" name="Save" class="btn btn-primary" title="Save"><i
                                class="fa fa-check"></i> Simpan</button>
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
