@extends('adminlte::page')
<?php
if (empty($items)) {
    $items['kemasan'] = 1;
    $items['max_kemasan'] = 4;
}
if (empty($invtpaket)) {
    $invtpaket = '';
}
if (empty($pktitem)) {
    $pktitem = collect();
}
?>
@section('title', "MOZAIC Omah'e Opa")
@section('js')
    <script src="{{ asset('resources/js/paketHelper.js') }}"></script>
    <script>
        function function_elements_add(name, value) {
            $.ajax({
                type: "POST",
                url: "{{ route('add-item-elements') }}",
                data: {
                    'name': name,
                    'value': value,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(msg) {},
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function function_change_quantity(item_packge_id, unit_id, value) {
            if (value != '') {
                $("#simpan-brg").prop('disabled', true);
                $.ajax({
                    url: "{{ url('package/item/change-qty') }}" + '/' + item_packge_id + '/' + unit_id + '/' +
                        value,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {

                    },
                    complete: function() {
                        $("#simpan-brg").prop('disabled', false);
                        setTimeout(function() {
                            $("#simpan-brg").prop('disabled', false);
                        }, 20);
                    }
                });
            }
        }

        function changeCategory(id, el, from_paket = 0) {
            console.log($('#' + id).val());
            if ($('#' + id).val() != '') {
                $('#merchant_id').val($('#' + id).val());
            } else if ($('#' + id).val() === null || $('#' + id).val() === undefined) {
                loading(0);
                return 0;
            }
            loading();
            var merchant_id = $("#" + id).val();
            $.ajax({
                type: "POST",
                url: "{{ route('get-item-category') }}",
                dataType: "html",
                data: {
                    'from_paket': from_paket,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    if (from_paket) {
                        $('#' + el).html(return_data);
                        changeItem($('#' + el).val());
                        return 0;
                    } else {
                        loading(0);
                        setTimeout(function() {
                            loading(0);
                        }, 2000);
                        $('#' + el).html(return_data);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function changeItem(category) {
            loading();
            var no = $('.pkg-itm').length;
            $.ajax({
                type: "POST",
                url: "{{ route('get-merchant-item') }}",
                dataType: "html",
                data: {
                    'no': no,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#package_item_id').val(1);
                    $('#package_item_id').html(return_data);
                    changeSatuan();
                }
            });
        }

        function changeSatuan() {
            var package_item_id = $("#package_item_id").val();
            loading();
            $.ajax({
                type: "POST",
                url: "{{ route('get-item-unit') }}",
                dataType: "html",
                data: {
                    'item_id': package_item_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#package_item_unit').val(1);
                    $('#package_item_unit').html(return_data);
                    function_elements_add('package_item_id', package_item_id);
                },
                complete: function() {
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function checkMerchant() {
            if ($('#item_default_quantity_0').val() == '') {
                $('#navigator-itm li:nth-child(2) a').tab('show');
                $('item_default_quantity_0').focus();
                alert('Harap Masukan Satuan')
                return 0;
            }
            var id = $("#merchant_id").val();
            $("#create_warehouse").val(0);
            $.ajax({
                type: "post",
                url: "{{ route('check-warehouse-dtl') }}",
                data: {
                    'merchant_id': id,
                    '_token': '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (response.count == 0) {
                        $("#mname").html(response.merchant);
                        $("#wname").html(response.merchant);
                        $('#confirmModal').modal('show')
                    } else {
                        $("#create_warehouse").val(0);
                        $('#form-barang').submit();
                    }
                }
            });
        }

        function save() {
            $("#create_warehouse").val(1);
            $('#confirmModal').modal('hide')
            $('#form-barang').submit();
        }
        $(document).ready(function() {
            changeCategory('package_merchant_id', 'package_item_category', 1);
            checkKemasan();
            if ($('#package_price_view').val() != '') {
                formatRp();
            }
            $("#simpan-brg").click(function(e) {
                e.preventDefault();
                checkMerchant();
            });
            $("#confirm-save-w-whs").click(function(e) {
                e.preventDefault();
                save();
            });
            if ($('#merchant_id_view').val() != '') {
                console.log($('#merchant_id_view').val());
                $('#merchant_id').val($('#merchant_id_view').val());
            }
        });
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ url('general-ticket') }}">Daftar Tiket</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ubah Tiket</li>
        </ol>
    </nav>

@stop

@section('content')

    <h3 class="page-title">
        Form Ubah Tiket
    </h3>
    <br />

    @if (session('msg'))
        <div class="alert alert-info" role="alert">
            {{ session('msg') }}
        </div>
    @endif

    @if (!empty($msg))
        <div class="alert alert-warning" role="alert">
            <i class="fa fa-exclamation"></i> &nbsp; {{ $msg }}
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
                Form Ubah
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ url('general-reservation') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>
        <form method="post" id="form-barang" action="{{ route('process-edit-reservation') }}" enctype="multipart/form-data">
            @csrf

            <input hidden class="form-control input-bb" name="reservation_id" id="reservation_id"
            type="text" autocomplete="off" value="{{ $reservation->reservation_id }}"
            onChange="function_elements_add(this.name, this.value);" />
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Paket</a>
                            <input class="form-control input-bb" name="reservation_name" id="reservation_name"
                                type="text" autocomplete="off" value="{{ $reservation->reservation_name }}"
                                onChange="function_elements_add(this.name, this.value);" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Harga</a>
                            <input class="form-control input-bb" name="reservation_price" id="reservation_price"
                                type="text" autocomplete="off" value="{{ $reservation->reservation_price }}"
                                onChange="function_elements_add(this.name, this.value);" />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <a class="text-dark">Keterangan</a>
                    <input class="form-control input-bb" name="reservation_remark" id="reservation_remark" type="text"
                        autocomplete="off" value="{{ $reservation->reservation_remark }}" onChange="function_elements_add(this.name, this.value);" />
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i
                            class="fa fa-times"></i> Batal</button>
                    <button type="button" id="simpan-brg" class="btn btn-primary"><i class="fa fa-check"></i>
                        Simpan</button>
                </div>
            </div>
    </div>
    </form>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Perhatian !</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="d-inline">Wahana "<b class="d-inline" id="mname">Merchant</b>" tidak memiliki gudang.
                        Apakah anda ingin sistem mebuat gudang otomatis?</p> <small>(Gudang akan diberi nama "<b
                            class="d-inline">Gudang <div class="d-inline" id="wname">Merchant</div></b>")</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <a type="button" href="{{ route('add-warehouse') }}" class="btn btn-info">Buat Gudang Manual</a>
                    <button type="button" class="btn btn-primary" id="confirm-save-w-whs">Ya</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer')

@stop

@section('css')

@stop
