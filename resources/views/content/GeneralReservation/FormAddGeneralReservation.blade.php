@inject('SalesInvoice', 'App\Http\Controllers\SalesInvoiceController')
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

        // function changeCategory(id, el) {
        //     loadingWidget();
        //     var item_id = $("#" + id).val();
        //     $('#item_id').val(item_id);
        //     console.log(id);
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ route('get-tiket-item') }}",
        //         dataType: "html",
        //         data: {
        //             'item_id': item_id,
        //             '_token': '{{ csrf_token() }}',
        //         },
        //         success: function(return_data) {
        //             function_elements_add(id, item_id);
        //             $('#' + el).html(return_data);
        //             changeItem($('#' + el).val());
        //         },
        //         error: function(data) {
        //             console.log(data);
        //         }
        //     });
        // }

        // function changeItem() {
        //     loadingWidget();
        //     var item_id = $("#item_id_view").val();
        //     $('#item_id').val(item_id);
        //     console.log(item_id)
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ route('get-tiket-unit') }}",
        //         dataType: "html",
        //         data: {
        //             'item_id': item_id,
        //             // 'item_category_id': category,
        //             '_token': '{{ csrf_token() }}',
        //         },
        //         success: function(return_data) {
        //             console.log(return_data)
        //             $('#item_unit').val(1);
        //             $('#item_unit').html(return_data);
        //             console.log('ci c')
        //             changeSatuan();
        //             // function_elements_add('item_category_id', category);
        //         }
        //     });
        // }

        function changeSatuan() {
            var item_id = $("#item_id_view").val();
            loadingWidget();
            $.ajax({
                type: "POST",
                url: "{{ route('get-tiket-unit') }}",
                dataType: "html",
                data: {
                    'item_id': item_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#item_unit').val(1);
                    $('#item_unit').html(return_data);
                    changeCost();
                    function_elements_add('item_id', item_id);
                },
                complete: function() {
                    loadingWidget(0);
                    setTimeout(function() {
                        loadingWidget(0);
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function changeCost() {
            loadingWidget();
            var item_unit = $("#item_unit").val();
            var item_id = $("#item_id_view").val();
            $.ajax({
                type: "POST",
                url: "{{ route('get-tiket-cost') }}",
                dataType: "json",
                data: {
                    'item_id': item_id,
                    'item_unit': item_unit,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    console.log(return_data);
                    loadingWidget(0);
                    $('#item_unit_price_view').val(return_data == '' ? '' : toRp(return_data.price));
                    $('#item_unit_price').val(return_data.price);
                    setTimeout(function() {
                        loadingWidget(0);
                    }, 200);

                },
                error: function(data) {
                    console.log(data);
                    loadingWidget(0);
                    setTimeout(function() {
                        loadingWidget(0);
                    }, 200);


                }
            });
        }

        function getItmPrice() {
            item_id = $('#item_id').val();
        }
        $(document).ready(function() {
            // changeCategory('item_id_view')
            $("#item_unit_price").change(function() {
                var unit_price = $("#item_unit_price").val();
                var quantity = $('#quantity').val();
                var subtotal_amount = unit_price * quantity;

                $("#subtotal_amount").val(subtotal_amount);
                $("#subtotal_amount_view").val(toRp(subtotal_amount));
            });

            $("#quantity").change(function() {
                var unit_price = $("#item_unit_price").val();
                var quantity = $('#quantity').val();
                var subtotal_amount = unit_price * quantity;

                $("#subtotal_amount").val(subtotal_amount);
                $("#subtotal_amount_view").val(toRp(subtotal_amount));
            });

            $("#quantity").change(function() {
                var unit_price = $("#item_unit_price").val();
                var quantity = $('#quantity').val();
                var subtotal_amount = unit_price * quantity;

                $("#subtotal_amount_after_discount").val(subtotal_amount);
                $("#subtotal_amount_after_discount_view").val(toRp(subtotal_amount));
            });

            $('#discount_percentage').change(function() {
                var subtotal_amount = $("#subtotal_amount").val();
                var discount_percentage = $("#discount_percentage").val();
                var discount_amount = (discount_percentage * subtotal_amount) / 100;
                var subtotal_amount_after_discount = subtotal_amount - discount_amount;

                $("#discount_amount").val(discount_amount);
                $("#discount_amount_view").val(toRp(discount_amount));
                $("#subtotal_amount_after_discount").val(subtotal_amount_after_discount);
                $("#subtotal_amount_after_discount_view").val(toRp(subtotal_amount_after_discount));
            });

            $("#discount_percentage_total").change(function() {
                var discount_percentage_total = $("#discount_percentage_total").val();
                var subtotal_amount1 = $("#subtotal_amount1").val();
                var discount_amount_total = (discount_percentage_total * subtotal_amount1) / 100;
                var total_amount = subtotal_amount1 - discount_amount_total;

                $("#discount_amount_total").val(discount_amount_total);
                $("#discount_amount_total_view").val(toRp(discount_amount_total));
                $("#total_amount").val(total_amount);
                $("#total_amount_view").val(toRp(total_amount));
            });

            $("#paid_amount").change(function() {
                var paid_amount = $("#paid_amount").val();
                var total_amount = $("#total_amount").val();
                var change_amount = paid_amount - total_amount;

                $("#change_amount").val(change_amount);
                $("#change_amount_view").val(toRp(change_amount));
            });



            $("#item_id").change(function() {
                var item_id = $("#item_id").val();

                $.ajax({
                    type: "POST",
                    url: "{{ route('select-item-category-sales-tiket') }}",
                    dataType: "html",
                    data: {
                        'item_id': item_id,
                        '_token': '{{ csrf_token() }}',
                    },
                    success: function(return_data) {
                        // console.log(item_category_id);
                        $('#item_category_id').html(return_data);
                        $('#item_unit').html('');
                        $('#item_unit_price').val('');
                        $('#quantity').val('');
                        $('#subtotal_amount_view').val('');
                        $('#subtotal_amount_after_discount_view').val('');
                        // console.log(return_data);
                    },
                    error: function(data) {
                        console.log(data);

                    }
                });
            });
            if ($('#item_id_view').val() != '') {
                $('#item_id').val($('#item_id_view').val());
            }


        });



        $("#item_id").change(function() {
            var item_id = $("#item_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('select-data-unit-sales-invoice') }}",
                dataType: "html",
                data: {
                    'item_id': item_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#item_unit_id').html(return_data);
                    console.log(return_data);
                },
                error: function(data) {
                    console.log(data);

                }
            });
        });

        function processAddArraySalesInvoice() {
            // var item_category_id = document.getElementById("item_category_id").value;
            var item_id = document.getElementById("item_id_view").value;
            var item_unit_id = document.getElementById("item_unit").value;
            var item_unit_price = document.getElementById("item_unit_price").value;
            var quantity = document.getElementById("quantity").value;
            var subtotal_amount = document.getElementById("subtotal_amount").value;
            var discount_percentage = document.getElementById("discount_percentage").value;
            var discount_amount = document.getElementById("discount_amount").value;
            var subtotal_amount_after_discount = document.getElementById("subtotal_amount_after_discount").value;

            $.ajax({
                type: "POST",
                url: "{{ route('add-array-sales-tiket') }}",
                data: {
                    // 'item_category_id': item_category_id,
                    'item_id': item_id,
                    'item_unit_id': item_unit_id,
                    'item_unit_price': item_unit_price,
                    'quantity': quantity,
                    'subtotal_amount': subtotal_amount,
                    'discount_percentage': discount_percentage,
                    'discount_amount': discount_amount,
                    'subtotal_amount_after_discount': subtotal_amount_after_discount,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(msg) {
                    location.reload();
                }
            });
        }

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
