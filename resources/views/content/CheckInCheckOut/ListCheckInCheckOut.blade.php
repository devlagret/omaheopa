@extends('adminlte::page')

@section('title',  "MOZAIC Omah'e Opa")

@section('js')
<script>
    function check(name,uri){
        if(confirm(`Yakin Ingin Membatalkan Check-in atas nama '`+name+`' ?`)){
        window.location.href = uri;
        }
    }
    function getPenalty(id){
        loadingWidget();
        $.ajax({
                type: "POST",
                url: "{{ route('cc.get-penalty') }}",
                data: {
                    'sales_order_id': id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    console.log(return_data);
                    $('#overtime').data('penalty-amount',return_data);
                    $('#pinalty').val(return_data);
                   loadingWidget(0);
                   setTimeout(function() {
                    loadingWidget(0);
                   }, 200);
                },
                complete: function() {
                    loadingWidget(0);
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
    function checkout(id,total){
        loadingWidget();
        $.ajax({
                type: "POST",
                url: "{{ route('cc.check') }}",
                data: {
                    'sales_order_id': id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    console.log(return_data);
                    if(return_data.status){
                        getPenalty(id);
                        $('#toleransi').data('id',id);
                        $('#overtime').data('id',id);
                        $('#overtime').data('total',return_data.needtopay);
                        $('#toleransi').data('total',return_data.needtopay);
                        $('#checkoutlateModal').modal('show');
                    }else{
                        $('#total-label').html('Total');
                        $('#penalty').hide();
                        $('#total-w-pinalty').hide();
                        $('#id_modal').val(id);
                        $('#total_amount_view').val(toRp(return_data.needtopay));
                        $('#total_amount').val(return_data.needtopay);
                        $('#checkoutModal').modal('show');
                   }
                   loadingWidget(0);
                   setTimeout(function() {
                    loadingWidget(0);
                   }, 200);
                },
                complete: function() {
                    loadingWidget(0);
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

    $(document).ready(function() {
        $('#pinalty_view').change(function() {
            $("#pinalty").val(this.value);
            console.log(this.value);
            $('#total_w_pinalty_view').val(toRp(parseInt($('#total_amount').val())+parseInt($('#pinalty').val())));
            $('#total_w_pinalty').val(parseInt($('#total_amount').val())+parseInt($('#pinalty').val()));
            if($("#paid_amount").val()!=''){
            $("#change_amount_view").val(toRp($("#paid_amount").val()-$('#total_w_pinalty').val()));
            $("#change_amount").val($("#paid_amount").val()-$('#total_w_pinalty').val());
            }
            $("#pinalty_view").val(toRp(this.value));
        });
        $("#paid_amount_view").change(function() {
            $("#paid_amount").val(this.value);
            if($('#use_penalty').val()==1){
                $("#change_amount_view").val(toRp(this.value-$('#total_w_pinalty').val()));
                $("#change_amount").val(this.value-$('#total_w_pinalty').val());
            }
            else{
                $("#change_amount_view").val(toRp(this.value-$('#total_amount').val()));
                $("#change_amount").val(this.value-$('#total_amount').val());
            }
            $("#paid_amount_view").val(toRp(this.value));
        });
        $('#checkoutModal').on('shown.bs.modal', function (event) {
            $('#paid_amount_view').trigger('focus');
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var penalty = button.data('penalty-amount');
            var total = button.data('total');
            $(this).find('#use_penalty').val(0);
            $(this).find('#pinalty_view').prop('required',false);
            if(id!=''){
                $(this).find('#id_modal').val(id);
                $(this).find('#total_amount_view').val(toRp(total));
                $(this).find('#total_amount').val(total);
            }
            if(button.data('penalty')){
                $(this).find('#pinalty_view').prop('required',true);
                $(this).find('#use_penalty').val(1);
                $(this).find('#total-label').html('Subtotal');
                $(this).find('#penalty').show();
                $(this).find('#total-w-pinalty').show();
                $(this).find('#total_w_pinalty_view').val(toRp(parseInt(penalty)+parseInt(total)));
                $(this).find('#total_w_pinalty').val(parseInt(penalty)+parseInt(total));
                $(this).find('#pinalty').val(penalty);
                $(this).find('#pinalty_view').val(toRp(penalty));
            }else{
                $(this).find('#total-label').html('Total');
                $(this).find('#penalty').hide();
                $(this).find('#total-w-pinalty').hide();
                $(this).find('#pinalty_view').val('');
                $(this).find('#pinalty').val('');
            }
        });
        $('#checkoutModal').on('hide.bs.modal', function (event) {
            $('#paid_amount_view').val('');
            $('#pinalty_view').val('');
            $('#pinalty').val('');
            $('#paid_amount').val('');
            $('#change_amount_view').val('');
            $('#change_amount').val('');
            $('#id_modal').val('');
        });
    });
</script>
@stop

@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Check-In dan Check-Out </li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Check-In dan Check-Out </b> <small>Kelola Check-In dan Check-Out</small>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('cc.filter') }}" enctype="multipart/form-data">
    @csrf
        <div class="card border border-dark">
        <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                Filter
            </h5>
        </div>

        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class="row ">
                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Mulai
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="{{ $start_date ?? date('Y-m-d')}}" style="width: 15rem;"/>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Akhir
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="{{ $end_date ?? date('Y-m-d')}}" style="width: 15rem;"/>
                        </div>
                    </div>
                    {{--<div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Gudang
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <select class="form-control"  type="text" name="end_date" id="end_date" onChange="function_elements_add(this.name, this.value);" value="">
                                <option value=""></option>
                            </select>
                        </div>
                    </div> --}}
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a href="{{ route('list-reset-stock-adjustment') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
                    <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
                </div>
            </div>
        </div>
        </div>
    </form>
</div>
<br/>
@if (session('msg'))
<div class="alert alert-{{session('type')??'info'}}" role="alert">
    {{ session('msg') }}
</div>
@endif
@if (count($errors) > 0)
<div class="alert alert-danger" role="alert">
    @foreach ($errors->all() as $error)
        {{ $error }}
    @endforeach
</div>
@endif
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar
    </h5>
    <div class="form-actions float-right">
        <button onclick="location.href='{{ route('cc.add') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Check-In Baru</button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 5%">No </th>
                        <th style="text-align: center; width: 10%">Tanggal Check-In</th>
                        <th style="text-align: center; width: 10%">Tanggal Check-Out</th>
                        <th style="text-align: center; width: 20%">Atas Nama</th>
                        <th style="text-align: center; width: 10%">Kamar Dipesan</th>
                        <th style="text-align: center; width: 10%">Uang Muka</th>
                        <th style="text-align: center; width: 10%">Total</th>
                        <th style="text-align: center; width: 20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                  @foreach ($booking as $row)
                      <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $row->checkin_date }}</td>
                        <td>{{ $row->checkout_date }}</td>
                        <td>{{ $row->sales_order_name }}</td>
                        <td>{{ $row->rooms->count() }}</td>
                        <td>
                            @if($row->sales_order_type==0)
                            {{  number_format($row->down_payment) }}
                            @else
                            <div class="text-center px-auto rounded-pill mx-auto bg-info" style="font-size:0.9rem;">Langsung Check-In<div>
                            @endif
                        </td>
                        <td>{{ number_format($row->sales_order_price) }}</td>
                        <td style="text-align: center">
                            @if ($row->sales_order_status==1)
                            <a type="button" class="btn btn-outline-success btn-sm" onclick="proses('{{ $row->sales_order_name}}','{{route('dp.process-add',$row->sales_order_id)}}')">Check-in</a>
                            @elseif ($row->sales_order_status==2)
                            <a type="button" class="btn btn-outline-danger btn-sm" onclick="checkout('{{ $row->sales_order_id}}',{{$row->sales_order_price}})">Check-Out</a>
                            <a type="button" class="btn btn-outline-primary btn-sm" href="{{route('cc.extend',$row->sales_order_id)}}">Perpanjangan</a>
                            @else
                                <div class="w-75 px-1 rounded-pill mx-auto bg-info">Sudah Check-Out<div>
                            @endif
                            @if($row->sales_order_type==1&& $row->checkin_date == date('Y-m-d'))
                            <a type="button" class="btn btn-outline-secondary btn-sm" onclick="check('{{ $row->sales_order_name}}','{{route('cc.delete',$row->sales_order_id)}}')">Batal</a>
                            @endif
                        </td>
                      </tr>
                  @endforeach
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>

  <!-- Modal -->
  <div class="modal fade" id="checkoutlateModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="checkoutlateLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="checkoutlateLabel">Peringatan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Waktu Check-Out Telah Melebihi Jam yang Diatur.
        </div>
        <div class="modal-footer">
          <button type="button" data-toggle="modal" data-target="#checkoutModal" id="toleransi" class="btn btn-secondary">Toleransi Waktu</button>
          <button type="button" data-toggle="modal" data-penalty="true" id="overtime" data-target="#checkoutModal" class="btn btn-primary">Overtime</button>
        </div>
      </div>
    </div>
  </div>
 
  <!-- Modal -->
  <div class="modal fade" id="checkoutModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="checkoutLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="checkoutLabel">Bayar Check-Out</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
         <form action="{{route('cc.process-checkout')}}" id="form-checkout" method="post">
            @csrf
            <input name="sales_order_id" type="text" id="id_modal" hidden/>
            <input name="use_penalty" type="text" id="use_penalty" hidden/>
            <div class="row mb-3">
                <div class="col-3">
                    <a class="text-dark col-form-label" id="total-label">Total</a><a class='red'> *</a></a>
                </div>
                <div class="col-auto">
                    :
                </div>
                <div class="col-8">
                    <input class="form-control input-bb" id="total_amount_view" name="total_amount_view" autocomplete="off" readonly/>
                    <input class="form-control input-bb" id="total_amount" type="hidden" name="total_amount" autocomplete="off" />
                </div>
            </div>
            <div class="row mb-3" id="penalty" style="display: none;">
                <div class="col-3">
                    <a id="label-payment" class="text-dark col-form-label">Penalti</a><a class='red'> *</a></a>
                </div>
                <div class="col-auto">
                    :
                </div>
                <div class="col-8">
                    <input class="form-control required input-bb" autocomplete="off" id="pinalty_view" name="pinalty_view" />
                    <input class="form-control input-bb" id="pinalty" name="pinalty" hidden/>
                </div>
            </div>
            <div class="row mb-3" id="total-w-pinalty" style="display: none;">
                <div class="col-3">
                    <a id="label-payment" class="text-dark col-form-label">Total</a><a class='red'> *</a></a>
                </div>
                <div class="col-auto">
                    :
                </div>
                <div class="col-8">
                    <input class="form-control required input-bb" required autocomplete="off" id="total_w_pinalty_view" name="pinalty_view" />
                    <input class="form-control input-bb" id="total_w_pinalty" name="pinalty" hidden/>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-3">
                    <a id="label-payment" class="text-dark col-form-label">Bayar</a><a class='red'> *</a></a>
                </div>
                <div class="col-auto">
                    :
                </div>
                <div class="col-8">
                    <input class="form-control required input-bb" required autocomplete="off" id="paid_amount_view" name="paid_amount_view" />
                    <input class="form-control input-bb" id="paid_amount" value="{{$sessiondata['paid_amount_view']??''}}" name="paid_amount" hidden/>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-3">
                    <a id="label-payment" class="text-dark col-form-label">Kembalian</a><a class='red'> *</a></a>
                </div>
                <div class="col-auto">
                    :
                </div>
                <div class="col-8">
                    <input class="form-control required input-bb" required autocomplete="off" id="change_amount_view" name="change_amount_view" readonly />
                    <input class="form-control input-bb" id="change_amount" name="change_amount" hidden/>
                </div>
            </div>
         </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="Submit" form="form-checkout" class="btn btn-success">Bayar</button>
        </div>
      </div>
    </div>
  </div>
@stop

@section('footer')

@stop

@section('css')

@stop