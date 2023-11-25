@inject('PurchasePayment', 'App\Http\Controllers\PurchasePaymentController')
@extends('adminlte::page')
@section('title', "MOZAIC Omah'e Opa")
<link rel="shortcut icon" href="{{ asset('resources/assets/logo_pbf.ico') }}" />
@section('js')
    <script>
       
        function elements_add(name, value) {
            $.ajax({
                type: "POST",
                url: "{{ route('purchase-payment.elements-add') }}",
                dataType: "html",
                data: {
                    'name': name,
                    'value': value,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    console.log(return_data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
        $("#payment_total_cash_amount").change(function() {
            calculateTotal();
        });
        function calculateTotal() {
            calculateAllocation();
            var payment_total_cash_amount = $("#payment_total_cash_amount").val();
            var payment_total_transfer_amount = $("#payment_total_transfer_amount").val();
            if (isNaN(payment_total_cash_amount)) {
                payment_total_cash_amount = 0;
            }
            if (isNaN(payment_total_transfer_amount)) {
                payment_total_transfer_amount = 0;
            }
            var total = parseFloat(payment_total_cash_amount) + parseFloat(payment_total_transfer_amount);
            $("#payment_amount_view").val(toRp(total));
            $("#payment_amount").val(total);
        }
        function calculateAllocation(name='',value='') {
            if(name!=''){
            elements_add(name, value);
            }
            var payment_amount = $("#payment_amount").val();
            var item_total = $("#item_total").val();
            var allocationtotal = 0;
            var shortovertotal = 0;
            for (i = 0; i < item_total; i++) {
                var lastbalance = 0;
                var owing_amount = $("#" + i + "_owing_amount").val();
                var allocation = $("#" + i + "_allocation").val();
                var shortover = $("#" + i + "_shortover").val();
                if (isNaN(allocation)) {
                    allocation = 0;
                }
                if (isNaN(shortover)) {
                    shortover = 0;
                }
                allocationtotal += parseFloat(allocation);
                shortovertotal += parseFloat(shortover);
                lastbalance = parseFloat(owing_amount) - parseFloat(allocation) - parseFloat(shortover);
                $("#" + i + "_last_balance_view").val(toRp(lastbalance));
                $("#" + i + "_last_balance").val(lastbalance);
            }
            $("#allocation_total").val(allocationtotal);
            $("#shortover_total").val(shortovertotal);
            console.log(allocationtotal||0);
            console.log('foo');
            console.log(allocationtotal);
            $("#allocation_total_view").val(toRp(allocationtotal));
            $("#shortover_total_view").val(toRp(shortovertotal));
            $("#payment_allocated_move_view").val(toRp(parseFloat(payment_amount) - parseFloat(allocationtotal) -
                parseFloat(shortovertotal)));
            $("#payment_allocated_move").val(parseFloat(payment_amount) - parseFloat(allocationtotal) - parseFloat(
                shortovertotal));
        }
        function toRp(number) {
            var number = number.toString(),
                rupiah = number.split('.')[0],
                cents = (number.split('.')[1] || '') + '00';
            rupiah = rupiah.split('').reverse().join('')
                .replace(/(\d{3}(?!$))/g, '$1.')
                .split('').reverse().join('');
            return rupiah + ',' + cents.slice(0, 2);
        }
        function processAddArrayPurchasePaymentTransfer() {
            var bank_id = document.getElementById("bank_id").value;
            var name = $("#bank_id option:selected").text();
            var payment_transfer_account_name = document.getElementById("payment_transfer_account_name").value;
            var payment_transfer_account_no = document.getElementById("payment_transfer_account_no").value;
            var payment_transfer_amount = document.getElementById("payment_transfer_amount").value;
            body = $('#warning-modal-body')
            modal = $('#warningModal')
            if(bank_id==''){
                body.html('<p>Harap pilih No. perkiraan bank</p>')
                modal.modal('show')
                return false;
            }else if(name==""){
                body.html('<p>Harap masukan nama akun atau penerima</p>')
                modal.modal('show')
                return false;
            }else if(payment_transfer_amount==''){
                body.html('<p>Harap masukan jumlah trasfer</p>')
                modal.modal('show')
                return false;
            }else if(payment_transfer_amount<=0){
                body.html('<p>Harap masukan jumlah trasfer lebih dari 0</p>')
                modal.modal('show')
                return false;
            }
            $.ajax({
                type: "POST",
                url: "{{ route('purchase-payment.add-transfer-array') }}",
                data: {
                    'bank_id': bank_id,
                    'name': name,
                    'payment_transfer_account_name': payment_transfer_account_name,
                    'payment_transfer_account_no': payment_transfer_account_no,
                    'payment_transfer_amount': payment_transfer_amount,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(msg) {
                    location.reload();
                }
            });
        }
        function addBank() {
            var bank_code = $("#bank_code").val();
            var bank_name = $("#bank_name").val();
            var account_id = $("#account_id").val();
            var bank_remark = $("#bank_remark").val();
            $.ajax({
                type: "POST",
                url: "{{ route('purchase-payment.add-bank') }}",
                dataType: "html",
                data: {
                    'bank_code': bank_code,
                    'bank_name': bank_name,
                    'account_id': account_id,
                    'bank_remark': bank_remark,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#bank_id').html(return_data);
                    $('#cancel_btn_bank').click();
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
        function checkAll(){
            body = $('#warning-modal-body')
            modal = $('#warningModal')
            if($('#payment_amount').val()==0||$('#payment_amount').val()==''){
                body.html('Harap Masukan Jumlah Pelunasan Tunai atau Transfer')
                modal.modal('show');
            }else
            if($('#allocation_total').val()==0||$('#allocation_total').val()==''){
                body.html('Harap Masukan Alokasi')
                modal.modal('show');
            }else
            if($('#payment_allocated_move').val()>0){
                $('#confirm-modal-body').html('Total Alokasi Masih Tersisa Apakah Anda yakin Ingin Melanjutkan?')
                $('#confirmModal').modal('show');
            }else
            if($('#payment_allocated_move').val()<0){
                $('#confirm-modal-body').html('Total Alokasi Bernilai Negatif Apakah Anda yakin Ingin Melanjutkan?')
                $('#confirmModal').modal('show');
            }else{
               $('#form-payment').submit();
            }
            
        }
        $(document).ready(function() {
            $("#bank_id").select2("val", "0");
            $("#account_id").select2("val", "0");
            var elements = {!! json_encode($purchasepaymentelements) !!};
            if (!elements || elements == '') {
                elements = [];
            }
            if (!elements['cash_account_id']) {
                $("#cash_account_id").select2("val", "0");
            }
            if (!elements['payment_total_cash_amount']) {
                $("#payment_total_cash_amount").val(0);
            }
            calculateTotal();
            $('#sv-btn').click(function (e) { 
                e.preventDefault();
                checkAll()
            });
            $('#sv-btn-modal').click(function (e) { 
                e.preventDefault();
               $('#form-payment').submit();
            });
        });
        $('#warningModal').on('hide.bs.modal', function (event) {
            $(this).find('.modal-body').html('What are you doing here?')
        })
    </script>
@stop
@section('content_header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ url('purchase-payment') }}">Daftar Pelunasan Hutang</a></li>
            <li class="breadcrumb-item"><a href="{{ url('purchase-payment/search') }}">Daftar Pemasok</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Pelunasan Hutang</li>
        </ol>
    </nav>
@stop
@section('content')
    <h3 class="page-title">
        Form Tambah Pelunasan Hutang
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
                <button onclick="location.href='{{ route('purchase-payment.index') }}'" name="Find"
                    class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>
        <form method="post" action="{{ route('purchase-payment.process-add') }}" id="form-payment" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-6">
                        <section class="control-label">Tanggal Pelunasan
                            <span class="required text-danger">
                                *
                            </span>
                        </section>
                        <input type ="date" class="form-control form-control-inline input-medium date-picker input-date"
                            data-date-format="dd-mm-yyyy" type="text" name="payment_date" id="payment_date"
                            onChange="elements_add(this.name, this.value);"
                            value="{{ empty($purchasepaymentelements['payment_date']) ? date('Y-m-d') : $purchasepaymentelements['payment_date'] }}" />
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Pemasok</a>
                            <input class="form-control input-bb" type="text" name="supplier_name" id="supplier_name"
                                value="{{ $supplier['supplier_name'] }}" readonly />
                            <input class="form-control input-bb" type="hidden" name="supplier_id" id="supplier_id"
                                value="{{ $supplier['supplier_id'] }}" readonly />
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12 ">
                        <a class="text-dark">Keterangan</a>
                        <div class="">
                            <textarea rows="3" type="text" class="form-control input-bb" name="payment_remark"
                                onChange="elements_add(this.name, this.value);" id="payment_remark" autocomplete='off'>{{ $purchasepaymentelements == null ? '' : $purchasepaymentelements['payment_remark'] }}</textarea>
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <h4 class="form-section"><b>Detail Pelunasan Hutang</b></h4>
                </div>
                <hr style="margin:0;">
                <br />
                <div class="row form-group">
                    <div class="col-md-6">
                        <a class="text-dark">No Perkiraan</a>
                        {!! Form::select(
                            'cash_account_id',
                            $acctaccount,
                            $purchasepaymentelements == null ? '' : $purchasepaymentelements['cash_account_id'],
                            [
                                'class' => 'selection-search-clear select-form',
                                'id' => 'cash_account_id',
                                'onchange' => 'elements_add(this.name, this.value);',
                            ],
                        ) !!}
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Tunai</a>
                            <input class="form-control input-bb" type="text" name="payment_total_cash_amount"
                                id="payment_total_cash_amount"
                                value="{{ $purchasepaymentelements == null ? '' : $purchasepaymentelements['payment_total_cash_amount'] }}"
                                onChange="elements_add(this.name, this.value);" autocomplete="off" style='text-align:right' />
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <h5 class="form-section"><b>Transfer Bank</b></h5>
                </div>
                <hr style="margin:0;">
                <br />
                <div class="row form-group">
                    <div class="col-md-6">
                        <a class="text-dark">No. Perkiraan Bank</a>
                        {!! Form::select('bank_id', $corebank, 0, ['class' => 'selection-search-clear select-form', 'id' => 'bank_id']) !!}
                    </div>
                    {{-- <div class="col-md-1">
                        <a class="text-dark"></a>
                        <a href='#addbank' data-toggle='modal' name="Find" class="btn btn-success add-btn"
                            title="Add Data">Tambah</a>
                    </div> --}}
                </div>
                <br />
                <div class="row">
                    <p class="form-section"><b>Data Bank Pemasok</b></p>
                </div>
                <br />
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Akun</a>
                            <input class="form-control input-bb" type="text" name="payment_transfer_account_name"
                                id="payment_transfer_account_name" value="" autocomplete='off' />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">No Rekening</a>
                            <input class="form-control input-bb" type="text" name="payment_transfer_account_no"
                                id="payment_transfer_account_no" value="" autocomplete='off' />
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12">
                        <div class="form-group">
                            <a class="text-dark">Jumlah Transfer</a>
                            <input class="form-control input-bb" type="text" name="payment_transfer_amount"
                                id="payment_transfer_amount" value="" autocomplete='off' />
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12" style='text-align:right'>
                        <div class="form-actions float-right">
                            <a type="submit" name="Save" class="btn btn-primary" title="Save"
                                id="buttonAddArrayPurchasePaymentTransfer"
                                onclick="processAddArrayPurchasePaymentTransfer()">Tambah</a>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-advance table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th style='text-align:center' width="20%">No. Perkiraan Bank</th>
                                        <th style='text-align:center' width="20%">Nama Akun</th>
                                        <th style='text-align:center' width="20%">No. Akun</th>
                                        <th style='text-align:center' width="20%">Jumlah Transfer</th>
                                        <th style='text-align:center' width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $total_transfer = 0;
                                    @endphp
                                    @if(empty($purchasepaymenttransfer))
                                       <tr><th colspan='9' style='text-align:center'>Data Kosong</th></tr>
                                    @else
                                    @foreach ($purchasepaymenttransfer as $key=>$val)
                                                <tr>
                                                    <td style='text-align  : left !important;'>{{$val['name']}}</td>
                                                    <td style='text-align  : left !important;'>{{$val['payment_transfer_account_name']}}</td>
                                                    <td style='text-align  : right !important;'>{{$val['payment_transfer_account_no'] }}</td>
                                                    <td style='text-align  : right !important;'>{{number_format($val['payment_transfer_amount'], 2)}}</td>
                                    <td style='text-align  : center !important;'>
                                        <a href="{{ route('purchase-payment.delete-transfer-array', ['record_id' => $key, 'supplier_id' => $supplier_id]) }}"
                                            name='Reset' class='btn btn-danger btn-sm'
                                            onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'></i>
                                            Hapus</a>
                                    </td>
                                    </tr>
                                    @php
                                        $total_transfer += $val['payment_transfer_amount'];
                                    @endphp
                                    @endforeach
                                    @endif
                                    <input class='form-control input-bb' type='hidden' name='payment_total_transfer_amount' id='payment_total_transfer_amount' value='{{$total_transfer}}' autocomplete='off'/>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <div class="row form-group">
                    <div class="col-md-4">
                        <div class="form-group">
                            <a class="text-dark">Jumlah</a>
                            <input class="form-control input-bb" type="text" style='text-align:right'
                                name="payment_amount_view" id="payment_amount_view" value="" readonly />
                            <input type="hidden" style='text-align:right'
                                name="payment_amount" id="payment_amount"  />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <a class="text-dark">Total Alokasi</a>
                            <input class="form-control input-bb" type="text" style='text-align:right'
                                name="payment_allocated_move_view" id="payment_allocated_move_view" value=""
                                readonly />
                            <input class="form-control input-bb" type="hidden" style='text-align:right'
                                name="payment_allocated_move" id="payment_allocated_move" value="" />
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </div>
    <br />
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
                                <th style='text-align:center'>Tanggal</th>
                                <th style='text-align:center'>No. Invoice</th>
                                <th style='text-align:center'>Jumlah Invoice</th>
                                <th style='text-align:center'>Jumlah yang telah Dibayar</th>
                                <th style='text-align:center'>Jumlah Sisa Piutang</th>
                                <th style='text-align:center'>Alokasi</th>
                                <th style='text-align:center'>Pembulatan</th>
                                <th style='text-align:center'>Saldo Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($purchaseinvoiceowing) == 0)
                                <tr><th colspan='9' style='text-align  : center !important;'>Data Kosong</th></tr>
                            @else
                            @php
                                $no =0;
                                $allocation_total = 0;
                                $shortover_total  = 0;
                            @endphp
                            @foreach ($purchaseinvoiceowing AS $key => $val)
                            <tr>
                                <td style='text-align  : center'>{{ $val['purchase_invoice_date'] }}</td>
                                <td style='text-align  : center'>{{ $val['purchase_invoice_no'] }}</td>
                                <td style='text-align  : center'>{{ number_format($val['total_amount'], 2) }}</td>
                                <td style='text-align  : center'>{{ number_format($val['paid_amount'], 2) }}</td>
                                <td style='text-align  : center'>{{ number_format($val['owing_amount'], 2) }}</td>
                                <td style='text-align  : center'>
                                    <input class="form-control" type="text" style='text-align:right'
                                        name="item[{{ $no }}][allocation]" id="{{ $no }}_allocation"
                                        value="{{$purchasepaymentelements["item[{$no}][allocation]"]??0}}" onChange="calculateAllocation(this.name,this.value)" autocomplete="off" />
                                </td>
                                <td style='text-align  : center'>
                                    <input class="form-control" type="text" style='text-align:right'
                                        name="item[{{ $no }}][shortover]" id="{{ $no }}_shortover"
                                        value="{{$purchasepaymentelements["item[{$no}][shortover]"]??0}}" onChange="calculateAllocation(this.name,this.value)" autocomplete="off" />
                                </td>
                                <td style='text-align  : center'>
                                    <input class="form-control" type="text" style='text-align:right'
                                        name="item[{{ $no }}][last_balance_view]"
                                        id="{{ $no }}_last_balance_view"
                                        value="{{ number_format($val['owing_amount']) }}" readonly />
                                    <input class="form-control" type="hidden" style='text-align:right'
                                        name="item[{{ $no }}][last_balance]" id="{{ $no }}_last_balance"
                                        value="{{ $val['owing_amount'] }}" readonly />
                                    <input class="form-control" type="hidden" style='text-align:right'
                                        name="item[{{ $no }}][purchase_invoice_id]"
                                        id="{{ $no }}_purchase_invoice_id"
                                        value="{{ $val['purchase_invoice_id'] }}" readonly />
                                    <input class="form-control" type="hidden" style='text-align:right'
                                        name="item[{{ $no }}][purchase_invoice_amount]"
                                        id="{{ $no }}_purchase_invoice_amount"
                                        value="{{ $val['total_amount'] }}" readonly />
                                    <input class="form-control" type="hidden" style='text-align:right'
                                        name="item[{{ $no }}][purchase_invoice_date]"
                                        id="{{ $no }}_purchase_invoice_date"
                                        value="{{ $val['purchase_invoice_date'] }}" readonly />
                                    <input class="form-control" type="hidden" style='text-align:right'
                                        name="item[{{ $no }}][purchase_invoice_no]"
                                        id="{{ $no }}_purchase_invoice_no"
                                        value="{{ $val['purchase_invoice_no'] }}" readonly />
                                    <input class="form-control" type="hidden" style='text-align:right'
                                        name="item[{{ $no }}][total_amount]" id="{{ $no }}_total_amount"
                                        value="{{ $val['total_amount'] }}" readonly />
                                    <input class="form-control" type="hidden" style='text-align:right'
                                        name="item[{{ $no }}][paid_amount]" id="{{ $no }}_paid_amount"
                                        value="{{ $val['paid_amount'] }}" readonly />
                                    <input class="form-control" type="hidden" style='text-align:right'
                                        name="item[{{ $no }}][owing_amount]" id="{{ $no }}_owing_amount"
                                        value="{{ $val['owing_amount'] }}" readonly />
                                </td>
                            </tr>
                            @php
                            $no++
                            @endphp
                            @endforeach
                                    <th style='text-align  : center' colspan='5'>Total</th>
                                    <th style='text-align  : right'>
                                        <input class='form-control' type='text' style='text-align:right' name='allocation_total_view' id='allocation_total_view' value='{{$allocation_total}}' readonly/>
                                        <input class='form-control' type='hidden' style='text-align:right' name='allocation_total' id='allocation_total' value='{{$allocation_total}}' readonly/>
                                    </th>
                                    <th>
                                        <input class='form-control' type='text' style='text-align:right' name='shortover_total_view' id='shortover_total_view' value='{{$shortover_total}}' readonly/>
                                        <input class='form-control' type='hidden' style='text-align:right' name='shortover_total' id='shortover_total' value='{{$shortover_total}}' readonly/>
                                    </th>
                                    <th>
                                        <input class='form-control input-bb' type='hidden' name='item_total' id='item_total' value='{{$no}}'/>
                                    </th>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onClick="window.location.reload();"><i
                        class="fa fa-times"></i> Batal</button>
                <button type="button" id="sv-btn" class="btn btn-primary" title="Save"><i
                        class="fa fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>
    </form>
    <br />
    <div class="modal fade bs-modal-lg" id="addbank" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style='text-align:left !important'>
                    <h4>Form Tambah Bank</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Kode Bank</a>
                                <input class="form-control input-bb" type="text" name="bank_code" id="bank_code"
                                    value="" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Nama Bank</a>
                                <input class="form-control input-bb" type="text" name="bank_name" id="bank_name"
                                    value="" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a class="text-dark">No Perkiraan</a>
                                {!! Form::select('account_id', $acctaccount, 0, [
                                    'class' => 'selection-search-clear select-form',
                                    'id' => 'account_id',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a class="text-dark">Keterangan</a>
                                <textarea class="form-control input-bb" type="text" name="bank_remark" id="bank_remark"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"
                        id='cancel_btn_bank'>Batal</button>
                    <a class="btn btn-primary" onClick="addBank()">Simpan</a>
                </div>
            </div>
        </div>
    </div>
    <br>
    <br>
    <br>
    <!-- Modal -->
<div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="warningModalLabel">Peringatan !</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="warning-modal-body">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
          {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
      </div>
    </div>
  </div>
    <!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmModalLabel">Peringatan !</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="confirm-modal-body">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Tidak</button>
          <button type="button" id="sv-btn-modal" class="btn btn-success">Ya</button>
        </div>
      </div>
    </div>
  </div>
@stop
@section('footer')
@stop
@section('css')
@stop
