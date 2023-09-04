@inject('PurchaseInvoice', 'App\Http\Controllers\PurchaseInvoiceController')
@extends('adminlte::page')

@section('title', "MOZAIC Omah'e Opa")
@section('js')
    <script>
        function function_elements_add(name, value) {
            $.ajax({
                type: "POST",
                url: "{{ route('add-elements-purchase-invoice') }}",
                data: {
                    'name': name,
                    'value': value,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(msg) {}
            });
        }

        $(document).ready(function() {
            $("#quantity").change(function() {
                var quantity = $("#quantity").val();
                var cost = $("#item_unit_cost").val();
                var subtotal = quantity * cost;

                $("#subtotal_amount").val(subtotal);
                $("#subtotal_amount_view").val(toRp(subtotal));
                $("#subtotal_amount_after_discount").val(subtotal);
                $("#subtotal_amount_after_discount_view").val(toRp(subtotal));

            });
            $("#item_unit_cost").change(function() {
                var quantity = $("#quantity").val();
                var cost = $("#item_unit_cost").val();
                var subtotal = quantity * cost;

                $("#subtotal_amount").val(subtotal);
                $("#subtotal_amount_view").val(toRp(subtotal));
                $("#subtotal_amount_after_discount").val(subtotal);
                $("#subtotal_amount_after_discount_view").val(toRp(subtotal));
            });
            $("#discount_percentage_total").change(function() {
                var discount_percentage_total = $("#discount_percentage_total").val();
                var subtotal_amount_total = $("#subtotal_amount_total").val();
                var discount_amount_total = (discount_percentage_total * subtotal_amount_total) / 100;
                var total_amount = subtotal_amount_total - discount_amount_total;

                $("#discount_amount_total").val(discount_amount_total);
                $("#discount_amount_total_view").val(toRp(discount_amount_total));
                $("#total_amount").val(total_amount);
                $("#total_amount_view").val(toRp(total_amount));
            });
            $("#paid_amount").change(function() {
                var paid_amount = $("#paid_amount").val();
                var total_amount = $("#total_amount").val();
                var owing_amount = paid_amount - total_amount;

                $("#owing_amount").val(owing_amount);
                $("#owing_amount_view").val(toRp(owing_amount));
            });
            $("#discount_percentage").change(function() {
                var subtotal = parseInt($("#subtotal_amount").val());
                var discount_percentage = parseInt($("#discount_percentage").val());
                var discount_amount = (subtotal * discount_percentage) / 100;

                $('#discount_amount_view').val(toRp(discount_amount));
                $('#discount_amount').val(discount_amount);

                var subtotal_amount_after_discount = parseInt($("#subtotal_amount_after_discount").val());
                var total_amount = subtotal - discount_amount;

                $("#subtotal_amount_after_discount_view").val(toRp(total_amount));
                $("#subtotal_amount_after_discount").val(total_amount);
            });
            $("#subtotal_amount_view").change(function() {
                var subtotal = parseInt($("#subtotal_amount").val());
                var discount_amount = parseInt($("#discount_amount_view").val());
                var total_amount = subtotal - discount_amount;

                $('#subtotal_amount_after_discount_view').val(toRp(total_amount));
                $('#subtotal_amount_after_discount').val(total_amount);

                var discount_percentage = (discount_amount / subtotal) * 100;

                $('#discount_percentage').val(discount_percentage.toFixed(2));
                $('#discount_amount').val(discount_amount);
                $('#discount_amount_view').val(toRp(discount_amount));
            });
            $("#discount_amount_view").change(function() {
                var subtotal = parseInt($("#subtotal_amount").val());
                var discount_amount = parseInt($("#discount_amount_view").val());
                var total_amount = subtotal - discount_amount;

                $('#subtotal_amount_after_discount_view').val(toRp(total_amount));
                $('#subtotal_amount_after_discount').val(total_amount);

                var discount_percentage = (discount_amount / subtotal) * 100;

                $('#discount_percentage').val(discount_percentage.toFixed(2));
                $('#discount_amount').val(discount_amount);
                $('#discount_amount_view').val(toRp(discount_amount));
            });
            $("#item_unit_cost_view").change(function() {
                var item_unit = $("#item_unit").val();
                var item_id = $("#item_id").val();
                var cost_new = $("#item_unit_cost_view").val();
                var cost = $("#item_unit_cost").val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('get-item-cost') }}",
                    dataType: "json",
                    data: {
                        'item_id': item_id,
                        'item_unit': item_unit,
                        '_token': '{{ csrf_token() }}',
                    },
                    success: function(price) {
                        console.log(price);
                        return 0;
                        if (price != '') {
                            if (cost != cost_new) {
                                $('#item_price_new_view').val(toRp(
                                    cost_new));
                                $('#item_price_new').val(cost_new);
                                $('#modal').modal('show');
                                $('#item_price_old_view').val(toRp(price.price));
                                $('#item_price_new_view').val(toRp(cost_new));
                                $('#item_cost_old_view').val(toRp(cost));
                                $('#item_cost_new_view').val(toRp(cost_new));
                                $('#item_price_old').val(price.price);
                                $('#item_cost_old').val(cost);
                                $('#item_cost_new').val(cost_new);
                            }
                            $('#item_unit_cost_view').val(toRp(cost_new));
                        }
                    }
                });
            });
            changeCategory();
        });

        function processAddArrayPurchaseInvoice() {
            $('.item-required').each(function() {
            if($( this ).val()==''){
                alert("Harap Semua Input Barang dengan Tanda Bintang Merah untuk Diisi!").
                $(this).focus();
            }
            });
            var merchant_id = $("#merchant_id").val();
            var item_category = $("#item_category").val();
            var item_id = $("#item_id").val();
            var item_unit = $("#item_unit").val();
            var quantity = $("#quantity").val();
            var item_unit_cost = $("#item_unit_cost").val();
            var subtotal_amount = $("#subtotal_amount").val();
            var discount_percentage = $("#item_unit_cost").val();
            var discount_amount = $("#discount_amount").val();
            var subtotal_amount_after_discount = $("#subtotal_amount_after_discount").val();
            var item_expired_date = $("#item_expired_date").val();
            $.ajax({
                type: "POST",
                url: "{{ route('add-array-purchase-invoice') }}",
                data: {
                    'merchant_id': merchant_id,
                    'item_category': item_category,
                    'item_id': item_id,
                    'item_unit': item_unit,
                    'item_unit_cost': item_unit_cost,
                    'quantity': quantity,
                    'discount_percentage': discount_percentage,
                    'discount_amount': discount_amount,
                    'subtotal_amount_after_discount': subtotal_amount_after_discount,
                    'subtotal_amount': subtotal_amount,
                    'item_expired_date': item_expired_date,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(msg) {
                    location.reload();
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function reset_add() {
            $.ajax({
                type: "GET",
                url: "{{ route('add-reset-purchase-invoice') }}",
                success: function(msg) {
                    location.reload();
                }

            });
        }

        function changeSatuan() {
            loading();
            var item_id = $("#item_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('get-item-unit') }}",
                dataType: "html",
                data: {
                    'item_id': item_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#item_unit').val(1);
                    $('#item_unit').html(return_data);
                    function_elements_add('item_id', item_id);
                    changeCost();
                },
                error: function(data) {
                    loading(0);
                    console.log(data);
                }
            });
        }

        function changeCategory() {
            loading();
            var merchant_id = $("#merchant_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('get-item-category') }}",
                dataType: "html",
                data: {
                    'merchant_id': merchant_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#item_category').html(return_data);
                    changeItem($('#item_category').val());
                    function_elements_add('merchant_id', merchant_id);
                },
                error: function(data) {
                    console.log(data);
                    loading(0);
                }
            });
        }

        function changeItem(category) {
            var id = $("#merchant_id").val();
            var no = $('.pkg-itm').length;
            loading();
            $.ajax({
                type: "POST",
                url: "{{ route('get-merchant-item') }}",
                dataType: "html",
                data: {
                    'no': no,
                    'merchant_id': id,
                    'item_category_id': category,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#item_id').val(1);
                    $('#item_id').html(return_data);
                    changeSatuan();
                    function_elements_add('merchant_id', id);
                    function_elements_add('item_category', category);
                },
                error: function(data) {
                    console.log(data);
                    loading(0);
                }
            });
        }

        function changeCost() {
            var item_unit = $("#item_unit").val();
            var item_id = $("#item_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('get-item-cost') }}",
                dataType: "json",
                data: {
                    'item_id': item_id,
                    'item_unit': item_unit,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    loading(0);
                    $('#item_unit_cost_view').val(return_data == '' ? '' : toRp(return_data.cost));
                    $('#item_unit_cost').val(return_data.cost);
                    window.setTimeout(loading(0), 5000);
                },
                error: function(data) {
                    console.log(data);
                    loading(0);
                    window.setTimeout(loading(0), 5000);

                }
            });
        }

        function process_change_cost() {
            var item_packge_id = document.getElementById("item_packge_id").value;
            var item_cost_new = document.getElementById("item_cost_new").value;
            var item_price_new = document.getElementById("item_price_new").value;
            var margin_percentage = document.getElementById("margin_percentage").value;

            $.ajax({
                type: "POST",
                url: "{{ route('process-edit-cost-item') }}",
                data: {
                    'item_packge_id': item_packge_id,
                    'item_cost_new': item_cost_new,
                    'item_price_new': item_price_new,
                    'margin_percentage': margin_percentage,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(msg) {
                    $('#modal').modal('hide');
                    $('#alert').html("<div class='alert alert-info' role='alert'>" + msg + "</div>");
                }
            });
        }
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ url('purchase-invoice') }}">Daftar Pembelian</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Pembelian</li>
        </ol>
    </nav>

@stop

@section('content')
    <div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="staticBackdropLabel">Informasi Perubahan Harga</h5>
                </div>
                <div class="modal-body">
                    <h6 class="text-bold">Harga Beli</h6>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Lama</a>
                                <input style="text-align: right" class="form-control input-bb" name="item_cost_old_view"
                                    id="item_cost_old_view" type="text" autocomplete="off" value="" readonly />
                                <input style="text-align: right" class="form-control input-bb" name="item_cost_old"
                                    id="item_cost_old" type="text" autocomplete="off" value="" hidden />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Baru</a>
                                <input style="text-align: right" class="form-control input-bb" name="item_cost_new_view"
                                    id="item_cost_new_view" type="text" autocomplete="off" value="" readonly />
                                <input style="text-align: right" class="form-control input-bb" name="item_cost_new"
                                    id="item_cost_new" type="text" autocomplete="off" value="" hidden />
                            </div>
                        </div>
                    </div>
                    <h6 class="text-bold">Harga Jual</h6>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Lama</a>
                                <input style="text-align: right" class="form-control input-bb" name="item_price_old_view"
                                    id="item_price_old_view" type="text" autocomplete="off" value="" readonly />
                                <input style="text-align: right" class="form-control input-bb" name="item_price_old"
                                    id="item_price_old" type="text" autocomplete="off" value="" hidden />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Baru</a>
                                <input style="text-align: right" class="form-control input-bb" name="item_price_new_view"
                                    id="item_price_new_view" type="text" autocomplete="off" value="" />
                                <input style="text-align: right" class="form-control input-bb" name="item_price_new"
                                    id="item_price_new" type="text" autocomplete="off" value="" hidden />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="process_change_cost();" class="btn btn-success">Iya</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tidak</button>
                </div>
            </div>
        </div>
    </div>

    <h3 class="page-title">
        Form Tambah Pembelian
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
                <button onclick="location.href='{{ url('purchase-invoice') }}'" name="Find"
                    class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>

        <?php
        // if (empty($coresection)){
        //     $coresection['section_name'] = '';
        // }
        ?>

        <form method="post" action="{{ route('process-add-purchase-invoice') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Pemasok<a class='red'> *</a></a>
                            {!! Form::select('supplier_id', $suppliers, $datases['supplier_id'], [
                                'class' => 'selection-search-clear select-form',
                                'id' => 'supplier_id',
                                'name' => 'supplier_id',
                                'onchange' => 'function_elements_add(this.name, this.value)',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Gudang<a class='red'> *</a></a>
                            {!! Form::select('warehouse_id', $warehouses, $datases['warehouse_id'], [
                                'class' => 'selection-search-clear select-form',
                                'id' => 'warehouse_id',
                                'name' => 'warehouse_id',
                                'onchange' => 'function_elements_add(this.name, this.value)',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <a class="text-dark">Tanggal Invoice Pembelian<a class='red'> *</a></a>
                            <input class="form-control input-bb" name="purchase_invoice_date" id="purchase_invoice_date"
                                type="date" data-date-format="dd-mm-yyyy" autocomplete="off"
                                onchange="function_elements_add(this.name, this.value)"
                                value="{{ $datases['purchase_invoice_date'] == '' ? date('Y-m-d') : $datases['purchase_invoice_date'] }}" />
                        </div>
                    </div>
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-9 mt-3">
                        <div class="form-group">
                            <a class="text-dark">Keterangan<a class='red'> *</a></a>
                            <textarea class="form-control input-bb" name="purchase_invoice_remark" id="purchase_invoice_remark" type="text"
                                autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{ $datases['purchase_invoice_remark'] }}</textarea>
                        </div>
                    </div>

                    <h6 class="col-md-8 mt-4 mb-3"><b>Data Invoice Pembelian Barang</b></h6>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Wahana / Merchant<a class='red'> *</a></a>
                            {!! Form::select('merchant_id', $merchant, $items['merchant_id'] ?? '', [
                                'class' => 'selection-search-clear item-required select-form',
                                'name' => 'merchant_id',
                                'id' => 'merchant_id',
                                'onchange' => 'changeCategory()',
                                'form' => 'form-paket',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Kategori<a class='red'> *</a></a>
                            <select class="selection-search-clear item-required select-form" required
                                placeholder="Masukan Kategori Barang" name="item_category" id="item_category"
                                onchange="changeItem(this.value)">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                            <select class="selection-search-clear item-required select-form" required
                                placeholder="Masukan Nama Barang" name="item_id" id="item_id"
                                onchange="changeSatuan()">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Satuan<a class='red'> *</a></a>
                            <select class="selection-search-clear item-required select-form"
                                placeholder="Masukan Kategori Barang" required name="item_unit" id="item_unit"
                                onchange="changeCost()">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Jumlah<a class='red'> *</a></a>
                            <input class="form-control input-bb item-required text-right" name="quantity"
                                placeholder="Masukan Jumlah" id="quantity" type="text" autocomplete="off"
                                value="" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Biaya Barang Satuan<a class='red'> *</a></a>
                            <input class="form-control input-bb" name="item_unit_cost_view" id="item_unit_cost_view"
                                type="text" autocomplete="off" value="" />
                            <input class="form-control input-bb" name="item_unit_cost" id="item_unit_cost"
                                type="hidden" autocomplete="off" value="" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Subtotal<a class='red'> *</a></a>
                            <input style="text-align: right" class="form-control input-bb" name="subtotal_amount_view"
                                id="subtotal_amount_view" type="text" autocomplete="off" value="" disabled />
                            <input class="form-control input-bb" name="subtotal_amount" id="subtotal_amount"
                                type="text" autocomplete="off" value="" hidden />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="text-dark">Diskon (%)</a>
                            <input style="text-align: right" class="form-control input-bb" name="discount_percentage"
                                id="discount_percentage" type="text" autocomplete="off" value="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="text-dark">Jumlah Diskon</a>
                            <input style="text-align: right" class="form-control input-bb" name="discount_amount_view"
                                id="discount_amount_view" type="text" autocomplete="off" value="" />
                            <input style="text-align: right" class="form-control input-bb" name="discount_amount"
                                id="discount_amount" type="text" autocomplete="off" value="" hidden />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Total<a class='red'> *</a></a>
                            <input style="text-align: right" class="form-control input-bb"
                                name="subtotal_amount_after_discount_view" id="subtotal_amount_after_discount_view"
                                type="text" autocomplete="off" value="" disabled />
                            <input style="text-align: right" class="form-control input-bb"
                                name="subtotal_amount_after_discount" id="subtotal_amount_after_discount" type="text"
                                autocomplete="off" value="" hidden />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <a class="text-dark ">Tanggal Kadaluarsa<a class='red'> *</a></a>
                            <input class="form-control required input-bb" required name="item_expired_date"
                                id="item_expired_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off"
                                value="{{ date('Y-m-d') }}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a type="submit" name="Save" class="btn btn-primary" title="Save"
                        onclick="processAddArrayPurchaseInvoice()"> Tambah</a>
                </div>
            </div>
    </div>
    </div>


    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Daftar
            </h5>
        </div>
        <div class="card-body">
            <div class="form-body form">
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center'>Barang</th>
                                <th style='text-align:center'>Jumlah</th>
                                <th style='text-align:center'>Harga Satuan</th>
                                <th style='text-align:center'>Subtotal</th>
                                <th style='text-align:center'>Kadaluarsa</th>
                                <th style='text-align:center'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                        $quantity = 0;
                        $subtotal_amount = 0;
                            if(!is_array($arraydatases)){
                                echo "<tr><th colspan='6' style='text-align  : center !important;'>Data Kosong</th></tr>";
                            } else {
                                foreach ($arraydatases AS $key => $val){
                                    echo"
                                    <tr>
                                                <td style='text-align  : left !important;'>".$PurchaseInvoice->getItemName($val['item_id'])."</td>
                                                <td style='text-align  : right !important;'>".$val['quantity']."</td>
                                                <td style='text-align  : right !important;'>".number_format($val['item_unit_cost'],2,',','.')."</td>
                                                <td style='text-align  : right !important;'>".number_format($val['subtotal_amount_after_discount'],2,',','.')."</td>
                                                <td style='text-align  : right !important;'>".date('d-m-Y', strtotime($val['item_expired_date']))."</td>";
                                                ?>

                            <td style='text-align  : center'>
                                <a href="{{ route('delete-array-purchase-invoice', ['record_id' => $key]) }}"
                                    name='Reset' class='btn btn-danger btn-sm'
                                    onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini ?')"></i> Hapus</a>
                            </td>

                            <?php
                                                echo"
                                            </tr>
                                        ";

                                    $quantity += $val['quantity'];
                                    $subtotal_amount += $val['subtotal_amount'];

                                }
                            }
                        ?>
                            <tr>
                                <td colspan="2">Sub Total</td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;"
                                        class="form-control input-bb" name="subtotal_item" id="subtotal_item"
                                        value="{{ $quantity }}" readonly />
                                </td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;"
                                        class="form-control input-bb" name="subtotal_amount_total_view"
                                        id="subtotal_amount_total_view"
                                        value="{{ number_format($subtotal_amount, 2, ',', '.') }}" readonly />
                                    <input type="text" style="text-align  : right !important;"
                                        class="form-control input-bb" name="subtotal_amount_total"
                                        id="subtotal_amount_total" value="{{ $subtotal_amount }}" hidden />
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2">Diskon (%)</td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;"
                                        class="form-control input-bb" name="discount_percentage_total"
                                        id="discount_percentage_total" value="" autocomplete="off" />
                                </td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;"
                                        class="form-control input-bb" name="discount_amount_total_view"
                                        id="discount_amount_total_view" value="" readonly />
                                    <input type="text" style="text-align  : right !important;"
                                        class="form-control input-bb" name="discount_amount_total"
                                        id="discount_amount_total" value="" hidden />
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3">Jumlah Total</td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;"
                                        class="form-control input-bb" name="total_amount_view" id="total_amount_view"
                                        value="{{ number_format($subtotal_amount, 2, ',', '.') }}" readonly />
                                    <input type="text" style="text-align  : right !important;"
                                        class="form-control input-bb" name="total_amount" id="total_amount"
                                        value="{{ $subtotal_amount }}" hidden />
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3">Di Bayar</td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;"
                                        class="form-control input-bb" name="paid_amount" id="paid_amount" value=""
                                        autocomplete="off" />
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3">Sisa</td>
                                <td style='text-align  : right !important;'>
                                    <input type="text" style="text-align  : right !important;"
                                        class="form-control input-bb" name="owing_amount_view" id="owing_amount_view"
                                        value="" readonly />
                                    <input type="text" style="text-align  : right !important;"
                                        class="form-control input-bb" name="owing_amount" id="owing_amount"
                                        value="" hidden />
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
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
        </form>
    </div>

@stop

@section('footer')

@stop

@section('css')

@stop
