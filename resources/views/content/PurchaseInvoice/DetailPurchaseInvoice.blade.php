@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")
@section('js')
<script>

@if(!empty($purchaseinvoice->item))
        @php
            $count = count($purchaseinvoice->item);
            $no = 1;
        @endphp
        const datamdl = {
            @foreach ($purchaseinvoice->item as $key => $val)
                {{$val->purchase_invoice_item_id}}:{"Kategori": "{{$val->category->item_category_name??'-'}}",
                    "Gudang": "{{$val->warehouse->warehouse_name??'-'}}",
                            "Merchant": "{{$val->merchant->merchant_name??'-'}}"
                            @if (!empty($val['discount_percentage'])&&$val['discount_percentage']!=0)
                            ,"Diskon":"{{$val['discount_amount']}} ({{$val['discount_percentage']}}%)"
                            @endif
                            }@if ($count>1&&$no!=$count){{','}}@endif @php $no++; @endphp
            @endforeach
            };
        @endif
        $('#detailModal').on('show.bs.modal', function (event) {
            var id = $(event.relatedTarget).data('id')
             $(this).find('.modal-title').text('Detail Item')
             $(this).find('.row-body').html('')
            var arr = datamdl[id];
            for (var key in arr) {
                var value = arr[key];
                 $(this).find('.row-body').append("<div class='col-3'>"+key+"</div><div class='col-auto'>:</div><div class='col-8'>"+value+"</div>");
             }
        })
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

<h3 class="page-title">
    Form Tambah Pembelian
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
            <button onclick="location.href='{{ url('purchase-invoice') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php
            // if (empty($coresection)){
            //     $coresection['section_name'] = '';
            // }
        ?>

        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Pemasok<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_invoice_supplier" id="purchase_invoice_supplier" type="text" autocomplete="off" value="{{ $purchaseinvoice->supplier->supplier_name }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Gudang<a class='red'> *</a></a>
                        {!! Form::select('warehouse_id', $warehouses, $purchaseinvoice['warehouse_id'], ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_id', 'name' => 'warehouse_id', 'onchange' => 'function_elements_add(this.name, this.value)', 'disabled']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Invoice Pembelian<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_invoice_date" id="purchase_invoice_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $purchaseinvoice['purchase_invoice_date'] }}" readonly/>
                    </div>
                </div>
                @if($purchaseinvoice['purchase_payment_method'])
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Jatuh Tempo<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_invoice_due_date" id="purchase_invoice_due_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $purchaseinvoice['purchase_invoice_due_date'] }}" {{isset($eddate)?'':'readonly'}}/>
                        <input name="purchase_invoice_due_date_real" id="purchase_invoice_due_date_real" type="hidden" autocomplete="off" value="{{ $purchaseinvoice['purchase_invoice_due_date'] }}" />
                    </div>
                </div>
                @endif
            </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Metode Pembayaran<a class='red'> *</a></a>
                            {!! Form::select(0, $purchase_payment_method, $purchaseinvoice['purchase_payment_method'] ??'', ['class' => 'form-control selection-search-clear select-form', 'id' => 'purchase_payment_method', 'name' => 'purchase_payment_method', 'onchange' => 'function_elements_add(this.name, this.value)', 'disabled']) !!}
                    </div>
                </div>
                <div class="col-md-9 mt-3">
                    <div class="form-group">
                        <a class="text-dark">Keterangan<a class='red'> *</a></a>
                        <textarea class="form-control input-bb" name="purchase_invoice_remark" id="purchase_invoice_remark" type="text" autocomplete="off" readonly>{{ $purchaseinvoice['purchase_invoice_remark'] }}</textarea>
                    </div>
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
                            @foreach ($purchaseinvoice->item AS $key => $val)
                                    <tr>
                                                <td style='text-align  : left !important;'>{{$val->item->item_name}}</td>
                                                <td style='text-align  : right !important;'>{{number_format($val['quantity'],0)}} {{$val->unit->item_unit_name??''}}</td>
                                                <td style='text-align  : right !important;'>{{number_format($val['item_unit_cost'],2,',','.')}}</td>
                                                <td style='text-align  : right !important;'>{{number_format($val['subtotal_amount'],2,',','.')}}</td>
                                                <td style='text-align  : right !important;'>{{date('d-m-Y', strtotime($val['item_expired_date']))}}</td>
                                                <td style='text-align  : center'>
                                                        <a class='btn btn-success btn-sm' data-toggle="modal" data-target="#detailModal" data-id="{{$val->purchase_invoice_item_id}}"> Detail</a>
                                                </td>
                                    </tr>
                            @endforeach

                        <tr>
                            <td colspan = "4"><b>Subtotal</b></td>
                            <td colspan = "2" style='text-align  : right !important;'>{{ number_format($purchaseinvoice['subtotal_amount_total'],2,',','.') }}</td>
                        </tr>
                        <tr>
                            <td colspan = "4"><b>Diskon</b></td>
                            <td colspan = "1" style='text-align  : right !important;'>{{ $purchaseinvoice['discount_percentage_total'] }}
                            </td>
                            <td colspan = "1" style='text-align  : right !important;'>{{ number_format($purchaseinvoice['discount_amount_total'],2,',','.') }}</td>
                        </tr>
                        <tr>
                            <td colspan = "4"><b>Total Jumlah</b></td>
                            <td colspan = "2" style='text-align  : right !important;'>{{ number_format($purchaseinvoice['total_amount'],2,',','.') }}</td>
                        </tr>
                        <tr>
                            <td colspan = "4"><b>Dibayar</b></td>
                            <td colspan = "2" style='text-align  : right !important;'>{{ number_format($purchaseinvoice['paid_amount'],2,',','.') }}</td>
                        </tr>
                        <tr>
                            <td colspan = "4"><b>Sisa</b></td>
                            <td colspan = "2" style='text-align  : right !important;'>{{ number_format($purchaseinvoice['owing_amount'],2,',','.') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row row-body">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

@stop

@section('footer')

@stop

@section('css')

@stop