@extends('adminlte::page')
<?php
if (empty($items)) {
    $items['item_code'] = '';
    $items['item_name'] = '';
    $items['item_barcode'] = '';
    $items['item_remark'] = '';
    $items['item_quantity'] = '';
    $items['item_price'] = '';
    $items['item_cost'] = '';
    $items['package_item_id'] = 1;
    $items['kemasan'] = 1;
    $items['max_kemasan'] = 4;
}
if (empty($paket)) {
    $paket = [];
}
?>
@section('title', "MOZAIC Omah'e Opa")
@section('js')
    <script>
        function function_elements_add(name, value) {
            $.ajax({
                type: "POST",
                url: "{{ isset($ci)?route('cc.elements-add'):route('booking.elements-add') }}",
                data: {
                    'ci' : {{$ci??0}},
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
        var index = {{ session('tab-index') ?? $sessiondata['tab-index'] ?? 1 }};

        function next() {
            index++;
            function_elements_add('tab-index', index)
            if(index == 3&&$('#atas_nama').val()==''){
                alert("Harap Mengisi Kolom Atas Nama");
                $('#atas_nama').focus();
                return 0;
            }
            $("#card-total-all").hide();
            if(index == 4){
                $("#card-total-all").show();
            }
            (index > 4)?index=4:(index<1)?index=1:'';
            $('#navigator-booking li:nth-child(' + index + ') a').tab('show');
            return index;
        }

        function preft() {
            index--;
            function_elements_add('tab-index', index)
            $("#card-total-all").hide();
            if(index == 4){
                $("#card-total-all").show();
            }
            (index > 4)?index=4:(index<1)?index=1:'';
            $('#navigator-booking li:nth-child(' + index + ') a').tab('show');
            return index;

        }

        function disableNav() {
            loadingWidget();
            $(".booking-nav").each(function() {
                $(this).prop('disabled', true);
                $(this).addClass('disabled');
            });
        }

        function enableNav() {
            loadingWidget(0);
            $(".booking-nav").each(function() {
                $(this).prop('disabled', false);
                $(this).removeClass('disabled');
            });
        }

        function changeType() {
            loading();
            var building_id = $("#building_id").val();
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            $.ajax({
                type: "POST",
                url: "{{ route('booking.get-room-type') }}",
                dataType: "html",
                data: {
                    'building_id': building_id,
                    'start_date': start_date,
                    'end_date': end_date,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    console.log(return_data);
                    function_elements_add('building_id', building_id);
                    $('#room_type_id').html(return_data);
                    changeRoom($('#room_type_id').val());
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function changeRoom(room_type_id) {
            loading();
            var building_id = $("#building_id").val();
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            $.ajax({
                type: "POST",
                url: "{{ route('booking.get-room') }}",
                dataType: "html",
                data: {
                    'room_type_id': room_type_id,
                    'building_id': building_id,
                    'start_date': start_date,
                    'end_date': end_date,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    console.log(return_data);
                    function_elements_add('room_type_id', room_type_id);
                    $('#room_id').html(return_data);
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 100);
                },
                complete: function() {
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                }
            });
        }

        function addRoom() {
            var room_id = $("#room_id").val();
            var days_booked = $("#days_booked").val();
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var no =$('.booked-room').length;
            if ($('.room-' + room_id).length) {
                return 0;
            }
            loading();
            $.ajax({
                type: "POST",
                url: "{{ route('booking.add-room') }}",
                dataType: "html",
                data: {
                    'no': no,
                    'ci' : {{$ci??0}},
                    'start_date' : start_date,
                    'end_date' : end_date,
                    'room_id': room_id,
                    'days_booked' : days_booked,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    if ($('.booked-room').length == 0) {
                        $('#room-table').html(return_data);
                    } else {
                        $('#room-table').append(return_data);
                    }
                    $(".select-form").each(function() {
                        $(this).select2();
                    });
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 100);
                },
                complete: function() {
                    subtotal()
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                }
            });
        }

        function clearBooked() {
            $.ajax({
                type: "get",
                url: "{{ route('booking.clear-booked') }}",
                dataType: "html",
                success: function(return_data) {
                    $('.booked-room').each(function(index) {
                        $(this).remove();
                    });
                    $('#room-table').html(
                        '<td valign="top" colspan="7" class="dataTables_empty">No data available in table</td>'
                    );
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function changeHowManyPerson(id, qty) {
            loadingWidget();
            disableNav();
            $.ajax({
                type: "POST",
                url: "{{ route('booking.add-person') }}",
                dataType: "html",
                data: {
                    'id': id,
                    'ci' : {{$ci??0}},
                    'qty': qty,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    loadingWidget(0);
                    setTimeout(function() {
                        enableNav();
                        loadingWidget(0);
                    }, 100);
                    enableNav();
                },
                complete: function() {
                    loadingWidget(0);
                    setTimeout(function() {
                        loadingWidget(0);
                        enableNav();
                    }, 200);
                    enableNav();
                },
                error: function(data) {
                    console.log(data);
                    loadingWidget(0);
                    setTimeout(function() {
                        loadingWidget(0);
                        enableNav();
                    }, 200);
                }
            });
        }

        function changeFacilityQty(id, qty) {
            loadingWidget();
            disableNav();
            $.ajax({
                type: "POST",
                url: "{{ route('booking.facility-qty') }}",
                dataType: "html",
                data: {
                    'id': id,
                    'ci' : {{$ci??0}},
                    'qty': qty,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    loadingWidget(0);
                    setTimeout(function() {
                        enableNav();
                        loadingWidget(0);
                    }, 100);
                    enableNav();
                },
                complete: function() {
                    subtotalFasilitas();
                    loadingWidget(0);
                    setTimeout(function() {
                        loadingWidget(0);
                        enableNav();
                    }, 200);
                    enableNav();
                },
                error: function(data) {
                    console.log(data);
                    loadingWidget(0);
                    setTimeout(function() {
                        loadingWidget(0);
                        enableNav();
                    }, 200);
                }
            });
        }

        function addFacility() {
            var room_facility_id = $("#room_facility_id").val();
            if ($('.facility-' + room_facility_id).length) {
                $('#facility_qty_' + room_facility_id).val(function(i, oldval) {
                    var newval = ++oldval;
                    changeFacilityQty(room_facility_id, newval);
                    return newval;
                });
                return 0;
            }
            loading();
            $.ajax({
                type: "POST",
                url: "{{ route('booking.add-facility') }}",
                dataType: "html",
                data: {
                    'no': $('.room-facility').length,
                    'ci' : {{$ci??0}},
                    'room_facility_id': room_facility_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    if ($('.room-facility').length == 0) {
                        $('#facility-table').html(return_data);
                    } else {
                        $('#facility-table').append(return_data);
                    }
                    $(".select-form").each(function() {
                        $(this).select2();
                    });
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 100);
                },
                complete: function() {
                    subtotalFasilitas();
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                }
            });
        }

        function clearFacility() {
            $.ajax({
                type: "get",
                url: "{{ route('booking.clear-facility') }}",
                dataType: "html",
                success: function(return_data) {
                    $('.room-facility').each(function(index) {
                        $(this).remove();
                    });
                    $('#facility-table').html(
                        '<td valign="top" colspan="7" class="dataTables_empty">No data available in table</td>'
                    );
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function changePrice(id, room_price_id = null, save = 1) {
            disableNav();
            save_id = null;
            if(save){
                save_id = id;
            }
            $.ajax({
                type: "post",
                url: "{{ route('booking.get-room-price') }}",
                dataType: "html",
                data: {
                    'room_price_id': room_price_id,
                    'ci' : {{$ci??0}},
                    'room_id': save_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $("#room_price_" + id).val(return_data);
                    if(save){
                    function_elements_add('room_price_id_'+id,room_price_id)
                    }
                    $("#room_price_view_" + id).val(toRp(return_data));
                },
                complete: function() {
                    subtotal()
                    enableNav();
                    setTimeout(function() {
                        enableNav();
                    }, 200);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function subtotal() {
            let sbstotal = 0;
            let total = 0;
            var days = $("#days_booked").val();
            $(".room_price_price").each(function() {
                sbstotal = Number($(this).val()) * days;
                $("#sbs-room-booked-"+$(this).data('id')).html(toRp(sbstotal));
                $("#sbs-rbook-input--"+$(this).data('id')).val(sbstotal);
                total += sbstotal;
            });
            $("#sbs-room-view").html(toRp(total));
            $("#subtotal_all_room").val(toRp(total));
            $("#sbs-room").val(total);
            count_total();
            return total;
        }

        function subtotalFasilitas() {
            let total = 0;
            $(".facility_price_price").each(function() {
                $("#sbs-facility-itm-" + $(this).data('id')).html(toRp(Number($(this).val()) * Number($(
                    "#facility_qty_" + $(this).data('id')).val())));
                total += (Number($(this).val()) * Number($("#facility_qty_" + $(this).data('id')).val()));
            });
            $("#sbs-facility-view").html(toRp(total));
            $("#subtotal_all_facility").val(toRp(total));
            $("#sbs-facility").val(total);
            count_total();
            return total;
        }

        function subtotalMenu() {
            let total = 0;
            $(".menu_price_price").each(function() {
                $("#sbs-menu-itm-" + $(this).data('id')).html(toRp(Number($(this).val()) * Number($(
                    "#menu_qty_" + $(this).data('id')).val())));
                total += (Number($(this).val()) * Number($("#menu_qty_" + $(this).data('id')).val()));
            });
            $("#sbs-menu-view").html(toRp(total));
            $("#subtotal_all_menu").val(toRp(total));
            $("#sbs-menu").val(total);
            count_total();
            return total;
        }

        function changeMenu() {
            loading();
            var room_menu_type = $("#room_menu_type").val();
            $.ajax({
                type: "post",
                url: "{{ route('booking.get-room-menu') }}",
                dataType: "html",
                data: {
                    'room_menu_type': room_menu_type,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#room_menu_id').html(return_data);
                    loading(0);
                     setTimeout(function() {
                        loading(0);
                    }, 300);
                },
                complete: function() {
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 300);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function clearMenu() {
            $.ajax({
                type: "get",
                url: "{{ route('booking.clear-menu') }}",
                dataType: "html",
                success: function(return_data) {
                    $('.room-menu-item').each(function(index) {
                        $(this).remove();
                    });
                    $('#menu-table').html(
                        '<td valign="top" colspan="7" class="dataTables_empty">No data available in table</td>'
                    );
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function addMenuItem() {
            var room_menu_id = $("#room_menu_id").val();
            if ($('.menu-item-' + room_menu_id).length) {
                $('#menu_qty_' + room_menu_id).val(function(i, oldval) {
                    var newval = ++oldval;
                    changeMenuQty(room_menu_id, newval);
                    return newval;
                });
                return 0;
            }
            loading();
            $.ajax({
                type: "post",
                url: "{{ route('booking.add-menu-item') }}",
                dataType: "html",
                data: {
                    'ci' : {{$ci??0}},
                    'room_menu_id': room_menu_id,
                    'no': $('.menu-item').length,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    if ($('.menu-item').length == 0) {
                        $('#menu-itm-table').html(return_data);
                    } else {
                        $('#menu-itm-table').append(return_data);
                    }
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 300);
                    return 0;
                },
                complete: function() {
                    loading(0);
                    subtotalMenu();
                    setTimeout(function() {
                        loading(0);
                    }, 500);
                },
                error: function(data) {
                    console.log(data);
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 500);
                }
            });
        }

        function changeMenuQty(id, qty) {
            loadingWidget();
            disableNav();
            $.ajax({
                type: "POST",
                url: "{{ route('booking.menu-qty') }}",
                dataType: "html",
                data: {
                    'id': id,
                    'ci' : {{$ci??0}},
                    'qty': qty,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    loadingWidget(0);
                    setTimeout(function() {
                        enableNav();
                        loadingWidget(0);
                    }, 100);
                    enableNav();
                },
                complete: function() {
                    loadingWidget(0);
                    subtotalMenu();
                    setTimeout(function() {
                        loadingWidget(0);
                        enableNav();
                    }, 200);
                    enableNav();
                },
                error: function(data) {
                    console.log(data);
                    loadingWidget(0);
                    setTimeout(function() {
                        loadingWidget(0);
                        enableNav();
                    }, 200);
                }
            });
        }

        function deleteBooked(room_id) {
            loading();
            $.ajax({
                type: "get",
                url: "{{ route('booking.delete-booked-room') }}" +'/'+ room_id +'/'+ {{$ci??0}},
                dataType: "html",
                success: function(return_data) {
                    $("#booked-room-" + room_id).remove();
                    if ($('.booked-room').length == 0) {
                        $('#room-table').html(
                        '<td valign="top" colspan="9" class="dataTables_empty">No data available in table</td>'
                    );
                    }
                    loading(0);
                    return 0;
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

        function deleteFacilityItm(id) {
            loading();
            $.ajax({
                type: "get",
                url: "{{ route('booking.delete-facility') }}" +'/' + id +'/'+ {{$ci??0}},
                dataType: "html",
                success: function(return_data) {
                    $("#facility-" + id).remove();
                    if ($('.room-facility').length == 0) {
                        $('#facility-table').html(
                        '<td valign="top" colspan="7" class="dataTables_empty">No data available in table</td>'
                    );
                    }
                    loading(0);
                    return 0;
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

        function deleteMenuItm(id) {
            loading();
            $.ajax({
                type: "get",
                url: "{{ route('booking.delete-menu') }}" +'/' + id +'/'+ {{$ci??0}},
                dataType: "html",
                success: function(return_data) {
                    $("#menu-item-" + id).remove();
                    if ($('.menu-item').length == 0) {
                        $('#menu-itm-table.').html(
                        '<td valign="top" colspan="7" class="dataTables_empty">No data available in table</td>'
                    );
                    }
                    loading(0);
                    return 0;
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

        function count_total(){
            var subtotal_room    = $("#sbs-room").val();
            var subtotal_facility= $("#sbs-facility").val();
            var subtotal_menu    = $("#sbs-menu").val();
            var discount_percentage = $("#discount_percentage_total").val();
            if(discount_percentage>100){
                discount_percentage = 100;
                $("#discount_percentage_total").val(discount_percentage);
            }
            sbsAll = Number(subtotal_room)+Number(subtotal_facility)+Number(subtotal_menu);
            $("#subtotal_all_view").val(toRp(sbsAll));
            $("#subtotal_all").val(sbsAll);
            diskon =  (sbsAll * discount_percentage) / 100;
            $("#discount_amount_view").val(toRp(diskon));
            $("#discount_amount").val(diskon);
            $("#total_amount_view").val(toRp(sbsAll - diskon));
            $("#total_amount").val(sbsAll - diskon);
            $('#change_amount_view').attr('min',sbsAll - diskon);
            if($("#payed_amount").val()!=''){
                $("#change_amount_view").val(toRp($("#payed_amount").val()-$('#total_amount').val()));
                $("#change_amount").val($("#payed_amount").val()-$('#total_amount').val());
                $("#payed_amount_view").val(toRp($("#payed_amount").val()));
            }
        }
        function reset_add(){
            clearBooked();
            clearFacility();
            clearMenu();
            loading();
            $.ajax({
				type: "GET",
				url : "{{route('booking.reset')}}",
				complete: function(msg){
                    location.reload();
			}

		    });
        }
        function changeDate(){
            var start_date = moment($("#start_date").val());
            var end_date = moment($("#end_date").val());
            var start_dater = moment($("#start_date").val()).format('Y-MM-DD');
            var days = end_date.diff(start_date,'days');
            $("#end_date").attr('min',start_date.add(1,'d').format('Y-MM-DD'));
            if(days <= 0){
                // alert("Tanggal Check-Out Tidak Boleh Sebelum Tanggal Check-In");
                $("#end_date").val(start_date.format('Y-MM-DD'));
                end_date = moment($("#end_date").val());
                days = 1;
            }
            $("#days_booked").val(days);
            $(".room-id").each(function() {
                id = $(this).val();
                getRoomPriceList(id);
            });
            disableNav();
            loadingWidget();
            $.ajax({
                type: "POST",
                url: "{{ route('booking.check-room') }}",
                data: {
                    'start_date': start_dater,
                    'end_date': end_date.format('Y-MM-DD'),
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    return_data.forEach(element => {
                        if($('#booked-room-'+element).length){
                        deleteBooked(element);
                        }
                    });
                    loadingWidget(0);
                    setTimeout(function() {
                        enableNav();
                        loadingWidget(0);
                    }, 100);
                    enableNav();
                },
                complete: function() {
                    loadingWidget(0);
                    subtotalMenu();
                    setTimeout(function() {
                        loadingWidget(0);
                        enableNav();
                    }, 200);
                    enableNav();
                },
                error: function(data) {
                    console.log(data);
                    loadingWidget(0);
                    setTimeout(function() {
                        loadingWidget(0);
                        enableNav();
                    }, 200);
                }
            });
            changeType();
            return subtotal();
        }
        function getRoomPriceList(id){
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            $.ajax({
                type: "post",
                url: "{{ route('booking.get-price-list') }}",
                dataType: "html",
                data: {
                    'start_date' : start_date,
                    'end_date' : end_date,
                    'ci' : {{$ci??0}},
                    'room_id': id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#room_price_id_'+id).html(return_data);
                    loading(0);
                    return 0;
                },
                complete: function() {
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 200);
                    setRoomPrice();
                },
                error: function(data) {
                    console.log(data);
                    loading(0);
                    setTimeout(function() {
                        loading(0);
                    }, 500);
                }
            });
        }
        function setRoomPrice(){
            $(".room-price-select").each(function() {
                        id=$(this).data('id');
                        changePrice(id,$(this).val(),0);
            });
            subtotal();
        }
        $(document).ready(function() {
            $('#navigator-booking li:nth-child(' + index + ') a').tab('show');
            (index > 4)?index=4:(index<1)?index=1:'';
            changeMenu();
            subtotalFasilitas();
            subtotalMenu();
            changeDate();
            changeType();
            $("input").each(function() {
                $(this).change(function() {
                    function_elements_add(this.name, this.value)
                });
            });
            $(".prev-btn").each(function() {
                $(this).click(function() {
                    preft()
                })
            });
            $(".next-btn").each(function() {
                $(this).click(function() {
                    next()
                })
            });
            $(".nav-tabs-booking").each(function() {
                $(this).click(function() {
                    $("#card-total-all").hide();
                    function_elements_add('tab-index', $(this).data('id'))
                    index =  $(this).data('id');
                    if($(this).data('id') >= 4){
                        $("#card-total-all").show();
                    }
                })
            });
            $(".booked-room").each(function() {
                id = $(this).data('id');
                getRoomPriceList(id);
            });
            $("#discount_amount_view").change(function() {
            $("#discount_amount").val(this.value);
            $("#discount_amount_view").val(toRp(this.value));
            var discount_percentage = (parseInt($(this).val()) / parseInt($("#total_amount").val())) * 100;
            $("#discount_percentage_total").val(discount_percentage);
            count_total();
            });
            if($("#down_payment").val()!=''){
                $("#down_payment_view").val(toRp($("#down_payment").val()));
            }
            $("#down_payment_view").change(function() {
                function_elements_add(this.name,this.value);
                $("#down_payment").val(this.value);
                $("#down_payment_view").val(toRp(this.value));
            });
            $("#payed_amount_view").change(function() {
                function_elements_add(this.name,this.value);
                $("#payed_amount").val(this.value);
                $("#change_amount_view").val(toRp(this.value-$('#total_amount').val()));
                $("#change_amount").val(this.value-$('#total_amount').val());
                $("#payed_amount_view").val(toRp(this.value));
            });
            if($('#sales_order_type').val()!=''){
                var sot = $('#sales_order_type').val();
                if(sot == 0){
                $('#down-payment-el').show();
                $("#down_payment_view").prop('required',true);
                $('#without-dp').hide();
            }else if(sot == 3){
                $("#down_payment_view").prop('required',false);
                $('#down-payment-el').hide();
                $('#without-dp').hide();
            }else if(sot == 4){
                $("#down_payment_view").prop('required',false);
                $('#down-payment-el').hide();
                $('#without-dp').show();
            }
            }
            $("#sales_order_type").change(function () {
            if(this.value == 0){
                $("#down_payment_view").prop('required',true);
                $('#down-payment-el').show();
                $('#without-dp').hide();
            }else if(this.value == 3){
                $("#down_payment_view").prop('required',false);
                $('#down-payment-el').hide();
                $('#without-dp').hide();
            }else if(this.value == 4){
                $("#down_payment_view").prop('required',false);
                $('#down-payment-el').hide();
                $('#without-dp').show();
            }
            });
            if(index >= 4){
                $("#card-total-all").show();
            }
            setTimeout(function () {
                $(".modal-backdrop.fade").remove();
            },6000);

        });
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ isset($ci)?route('cc.index'):route('booking.index') }}">Daftar {{isset($ci)?'Check-In':'Booking'}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah {{isset($ci)?'Check-In':'Booking'}}</li>
        </ol>
    </nav>

@stop
@section('content')
    <h3 class="page-title">
        Form Tambah {{isset($ci)?'Check-In':'Booking'}}
    </h3>
    <br />
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
    <form method="post" id="form-booking" action="{{  isset($ci)?route('cc.process-add'):route('booking.process-add')  }}" enctype="multipart/form-data">
        <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Form Tambah
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ isset($ci)?route('cc.index'):route('booking.index') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>
            @csrf
            <div class="card-body">
                <ul class="nav nav-tabs" id="navigator-booking" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link nav-tabs-booking active booking-nav" data-id="1"
                            href="#tanggal" role="tab" data-toggle="tab">Tanggal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-tabs-booking booking-nav" data-id="2"  href="#room"
                            role="tab" data-toggle="tab">Kamar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-tabs-booking booking-nav" data-id="3"  href="#facility"
                            role="tab" data-toggle="tab">Fasilitas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-tabs-booking booking-nav" data-id="4"  href="#menus"
                            role="tab" data-toggle="tab">Menu</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade show active" id="tanggal">
                        <div class="row form-group mt-5">
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <section class="control-label">Tanggal Check-In
                                        <span class="required text-danger">
                                            *
                                        </span>
                                    </section>
                                    <input type="date"
                                        class="form-control form-control-inline input-medium date-picker input-date"
                                        data-date-format="dd-mm-yyyy" type="text" name="start_date" min="{{date('Y-m-d')}}" {{isset($ci)?'readonly':''}} id="start_date"
                                        value="{{ $sessiondata['start_date'] ?? date('Y-m-d') }}" onchange="changeDate()" style="width: 15rem;" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <section class="control-label">Tanggal Check-Out
                                        <span class="required text-danger">
                                            *
                                        </span>
                                    </section>
                                    <input type="date"
                                        class="form-control form-control-inline input-medium date-picker input-date"
                                        data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date"
                                        value="{{ $sessiondata['end_date'] ?? date('Y-m-d') }}" onchange="changeDate()" style="width: 15rem;" />
                                    <input type="text" name="days_booked" id="days_booked" hidden/>
                                </div>
                            </div>
                            <div class="col d-none">
                                <div class="form-group">
                                    <a class="text-dark">Malam<a class='red'> *</a></a>
                                    <input class="form-control required input-bb" required form="form-barang" name="night"
                                        id="night" type="number" min="0" autocomplete="off"
                                        onchange="function_elements_add(this.name, this.value)"
                                        value="{{ $sessiondata['night'] ?? '' }}" />
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <div class="form-actions float-right">
                                <button type="button" class="btn next-btn btn-primary">
                                    Berikutnya <i class="fa fa-solid fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="room">
                        <div class="row form-group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Atas Nama<a class='red'> *</a></a>
                                    <input class="form-control required input-bb" required name="atas_nama"
                                        id="atas_nama" type="text" autocomplete="off"
                                        onchange="function_elements_add(this.name, this.value)"
                                        value="{{ $sessiondata['atas_nama'] ?? '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">No HP</a>
                                    <input class="form-control input-bb" name="phone_number"
                                        id="phone_number" type="text" autocomplete="off"
                                        onchange="function_elements_add(this.name, this.value)"
                                        value="{{ $sessiondata['phone_number'] ?? '' }}" />
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <a class="text-dark">Alamat</a>
                                    <textarea  class="form-control input-bb" name="address"
                                        id="address" autocomplete="off"
                                        onchange="function_elements_add(this.name, this.value)"
                                        >{{ $sessiondata['address'] ?? '' }}</textarea >
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Bagunan<a class='red'> *</a></a>
                                    {!! Form::select('building_id', $building, $sessiondata['building_id'] ?? '', [
                                        'class' => 'selection-search-clear required select-form',
                                        'name' => 'building_id',
                                        'id' => 'building_id',
                                        'onchange' => 'changeType()',
                                        'form' => 'form-barang',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <a class="text-dark">Tipe Kamar<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form" required
                                        form="form-barang" placeholder="Pilih Tipe" name="room_type_id"
                                        id="room_type_id" onchange="changeRoom(this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <a class="text-dark">Nama Kamar<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form" required
                                        form="form-barang" placeholder="Pilih Nama" name="room_id" id="room_id"
                                        onchange="function_elements_add(this.name, this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto justify-content-center">
                                <button class="btn btn-sm btn-primary mt-4" type="button" onclick="addRoom()"><i
                                        class="fa fa-plus" id="add-package-item"></i> Tambah Kamar</button>
                            </div>
                        </div>
                        <div class="card border border-dark">
                            <div class="card-header bg-dark clearfix">
                                <h5 class="mb-0 float-left">
                                    Daftar Kamar yang Dipesan
                                </h5>
                                <div class="form-actions float-right">
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="room-table-parent"
                                        class="table table-striped table-bordered datatables table-hover table-full-width">
                                        <thead>
                                            <tr>
                                                <th width="1%" style='text-align:center'>No</th>
                                                <th width="15%" style='text-align:center'>Nama Kamar</th>
                                                <th width="15%" style='text-align:center'>Tipe Kamar</th>
                                                <th width="15%" style='text-align:center'>Bangunan</th>
                                                <th width="13%" style='text-align:center'>Jumlah Orang</th>
                                                <th width="10%" style='text-align:center'>Tipe Harga</th>
                                                <th width="30%" style='text-align:center'>Harga Kamar</th>
                                                <th width="20%" style='text-align:center'>Subtotal</th>
                                                <th width="10%" style='text-align:center'>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="room-table">
                                            @isset($room)
                                                @php $no = 1; @endphp
                                                @foreach ($room as $val)
                                                    <tr class="booked-room room-{{ $val->room_id }}"
                                                        data-id="{{$val->room_id}}" id="booked-room-{{ $val->room_id }}">
                                                        <input type='hidden' class="room-id" name="item[{{$no-1}}][room_id]"
                                                        value="{{ $val->room_id}}" />
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ $val->room_name }}</td>
                                                        <td>{{ $val->roomType->room_type_name }}</td>
                                                        <td>{{ $val->building->building_name }}</td>
                                                        <td>
                                                            <div class='row'>
                                                                <div class="col-5">
                                                                    <input
                                                                        oninput='changeHowManyPerson({{ $val->room_id }}, this.value)'
                                                                        type='number' name='room_qty_{{ $val->room_id }}'
                                                                        id='room_qty_{{ $val->room_id }}'
                                                                        style='text-align: center; height: 30px; font-weight: bold; font-size: 15px'
                                                                        class='form-control input-bb' min='1'
                                                                        value='{{ $booked[$val->room_id] ?? 1 }}'
                                                                        autocomplete='off'>
                                                                </div>
                                                                <div class='col-auto'>Orang</div>
                                                            </div>
                                                        </td>
                                                        <td width="15%">
                                                            <select class="selection-search-clear room-price-select required select-form" required
                                                             placeholder="Pilih Nama" name="item[{{$no-1}}][room_price_id]" id="room_price_id_{{$val->room_id}}"
                                                            onchange="changePrice({{$val->room_id}},this.value)"  data-id="{{$val->room_id}}" >
                                                            </select>
                                                        </td>
                                                        <td width="10%">
                                                            <input type="text"
                                                                class="form-control input-bb readonly room_price_price_view"
                                                                name="room_price_view_{{ $val->room_id }}"
                                                                id="room_price_view_{{ $val->room_id }}" readonly />
                                                            <input type="hidden" class="room_price_price"
                                                                name="room_price_{{ $val->room_id }}"
                                                                id="room_price_{{ $val->room_id }}"
                                                                data-id="{{ $val->room_id }}"
                                                                />
                                                            <input type="hidden" class="sbs-room-booked" id="sbs-rbook-input-{{ $val->room_id}}"/>
                                                        </td>
                                                        <td id="sbs-room-booked-{{ $val->room_id }}">
                                                        </td>
                                                        <td class='text-center'><button type='button'
                                                                class='btn btn-outline-danger btn-sm'
                                                                onclick='deleteBooked({{ $val->room_id }})'>Hapus</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endisset
                                            @empty($room)
                                                <tr>
                                                    <td align="center" valign="top" colspan="9"
                                                        class="dataTables_empty">No data available in table</td>
                                                </tr>
                                            @endempty
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="6" class="font-weight-bold text-center fs-4">Subtotal</td>
                                                <td colspan="3" class="font-weight-bold text-center fs-4">
                                                    <h5 id="subtotal"> Rp. <div class="sbs-room-view d-inline"
                                                            id="sbs-room-view"></div> - </h5>
                                                    <input type="hidden" name="subtotal_room" id="sbs-room" />
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <div class="form-actions float-left">
                                <button type="button" class="btn prev-btn btn-primary booking-nav"> <i
                                        class="fa fa-solid fa-arrow-left"></i>
                                    Kembali</button>
                            </div>
                            <div class="form-actions float-right">
                                <button type="button" class="btn next-btn btn-primary booking-nav">
                                    Berikutnya <i class="fa fa-solid fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="facility">
                        <div class="row form-group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Fasilitas<a class='red'> *</a></a>
                                    {!! Form::select('room_facility_id', $facility, $sessiondata['room_facility_id'] ?? '', [
                                        'class' => 'selection-search-clear required select-form',
                                        'name' => 'room_facility_id',
                                        'id' => 'room_facility_id',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-auto justify-content-center">
                                <button class="btn btn-sm btn-primary mt-4" type="button" onclick="addFacility()"><i
                                        class="fa fa-plus" id="add-facility-item"></i> Tambah Fasilitas</button>
                            </div>
                        </div>
                        <div class="card border border-dark">
                            <div class="card-header bg-dark clearfix">
                                <h5 class="mb-0 float-left">
                                    Daftar Fasilitas yang Dipesan
                                </h5>
                                <div class="form-actions float-right">
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="facility-table-parent" style="width:100%"
                                        class="table table-striped table-bordered datatables table-hover table-full-width">
                                        <thead>
                                            <tr>
                                                <th width="2%" style='text-align:center'>No</th>
                                                <th width="15%" style='text-align:center'>Nama Fasilitas</th>
                                                <th width="15%" style='text-align:center'>Keterangan</th>
                                                <th width="15%" style='text-align:center'>Harga</th>
                                                <th width="5%" style='text-align:center'>Jumlah</th>
                                                <th width="10%" style='text-align:center'>Subtotal</th>
                                                <th width="5%" style='text-align:center'>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="facility-table">
                                            @isset($facilityitm)
                                                @php $nofas = 1; @endphp
                                                @foreach ($facilityitm as $fas)
                                                    <tr class="room-facility facility-{{ $fas->room_facility_id }}"
                                                        id="facility-{{ $fas->room_facility_id }}">
                                                        <td>{{ $nofas++ }}
                                                            <input type='hidden' id="room_facility_id[]"
                                                                value="{{ $fas->room_facility_id }}" />
                                                        </td>
                                                        <td>{{ $fas->facility_name }}</td>
                                                        <td>{{ $fas->facility_remark }}</td>
                                                        <td>
                                                            <input type="text"
                                                                class="form-control input-bb readonly facility_price_view"
                                                                name="facility_price_view_{{ $fas->room_facility_id }}"
                                                                id="facility_price_view_{{ $fas->room_facility_id }}"
                                                                value="{{ number_format($fas->facility_price, 2, ',', '.') }}"
                                                                readonly />
                                                            <input type="hidden"
                                                                class="form-control input-bb readonly facility_price_price"
                                                                data-id="{{ $fas->room_facility_id }}"
                                                                name="facility_price_{{$fas->room_facility_id}}"
                                                                id="facility_price_{{ $fas->room_facility_id }}"
                                                                value="{{ $fas->facility_price }}" readonly />
                                                        </td>
                                                        <td>
                                                            <input
                                                                oninput='changeFacilityQty({{ $fas->room_facility_id }}, this.value)'
                                                                type='number'
                                                                name='facility_qty_{{ $fas->room_facility_id }}'
                                                                id='facility_qty_{{ $fas->room_facility_id }}'
                                                                style='text-align: center; height: 30px; font-weight: bold; font-size: 15px'
                                                                class='form-control input-bb' min='1'
                                                                value='{{ $facilityqty[$fas->room_facility_id] ?? 1 }}'
                                                                autocomplete='off' />
                                                        </td>
                                                        <td align="right"
                                                            id="sbs-facility-itm-{{ $fas->room_facility_id }}">
                                                        </td>
                                                        <td class='text-center'><button type='button'
                                                                class='btn btn-outline-danger btn-sm'
                                                                onclick='deleteFacilityItm({{ $fas->room_facility_id }})'>Hapus</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endisset
                                            @empty($facilityitm)
                                                <tr>
                                                    <td align="center" valign="top" colspan="6"
                                                        class="dataTables_empty">No data available in table</td>
                                                </tr>
                                            @endempty
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="font-weight-bold text-center fs-4">Subtotal</td>
                                                <td colspan="2" class="font-weight-bold text-center fs-4">
                                                    <h5 id="subtotal"> Rp. <div class="sbs-facility-view d-inline"
                                                            id="sbs-facility-view"></div> - </h5>
                                                    <input type="hidden" name="subtotal_facility" id="sbs-facility" />
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <div class="form-actions float-left">
                                <button type="button" class="btn prev-btn booking-nav btn-primary"> <i
                                        class="fa fa-solid fa-arrow-left"></i>
                                    Kembali</button>
                            </div>
                            <div class="form-actions float-right">
                                <button type="button" class="btn next-btn booking-nav btn-primary">
                                    Berikutnya <i class="fa fa-solid fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="menus">
                        <div class="row form-group">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <a class="text-dark">Tipe<a class='red'> *</a></a>
                                    {!! Form::select('room_menu_type', $menutype, $sessiondata['room_menu_type'] ?? '', [
                                        'class' => 'selection-search-clear required select-form',
                                        'name' => 'room_menu_type',
                                        'id' => 'room_menu_type',
                                        'onchange' => 'changeMenu()',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="text-dark">Name<a class='red'> *</a></a>
                                    <select class="selection-search-clear required select-form"
                                        placeholder="Masukan Kategori Barang" name="room_menu_id" id="room_menu_id"
                                        onchange="function_elements_add(this.name, this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-primary mt-4" type="button" onclick="addMenuItem()"><i
                                        class="fa fa-plus" id="add-menu-item"></i>Tambah</button>
                            </div>
                        </div>
                        <div class="card border border-dark">
                            <div class="card-header bg-dark clearfix">
                                <h5 class="mb-0 float-left">
                                    Daftar
                                </h5>
                                <div class="form-actions float-right">
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table style="width:100%"
                                        class="table table-striped table-bordered datatables table-hover table-full-width">
                                        <thead>
                                            <tr>
                                                <th width="2%" style='text-align:center'>No</th>
                                                <th width="20%" style='text-align:center'>Tipe Menu</th>
                                                <th width="20%" style='text-align:center'>Nama Menu</th>
                                                <th width="20%" style='text-align:center'>Harga</th>
                                                <th width="20%" style='text-align:center'>Jumlah</th>
                                                <th width="20%" style='text-align:center'>Subtotal</th>
                                                <th width="10%" style='text-align:center'>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="menu-itm-table">
                                            @isset($menuItm)
                                                @php $nomen = 1; @endphp
                                                @foreach ($menuItm as $men)
                                                <tr class="menu-item menu-item-{{ $men->room_menu_id }}"
                                                    id="menu-item-{{ $men->room_menu_id }}">
                                                    <td>{{ $nomen++ }}
                                                        <input type='hidden' id="room_menu_id[]"
                                                            value="{{ $men->room_menu_id }}" />
                                                    </td>
                                                    <td>{{ $menutype[$men->room_menu_type] }}</td>
                                                    <td>{{ $men->room_menu_name }}</td>
                                                    <td>
                                                        <input type="text"
                                                            class="form-control input-bb readonly menu_price_view"
                                                            name=" menu_price_view_{{ $men->room_menu_id }}"
                                                            id=" menu_price_view_{{ $men->room_menu_id }}"
                                                            value="{{ number_format($men->room_menu_price, 2, ',', '.') }}"
                                                            readonly />
                                                        <input type="hidden"
                                                            class="form-control input-bb readonly menu_price_price"
                                                            data-id="{{ $men->room_menu_id }}"
                                                            name=" menu_price_{{$men->room_menu_id}}"
                                                            id=" menu_price_{{ $men->room_menu_id }}"
                                                            value="{{ $men->room_menu_price }}" readonly />
                                                    </td>
                                                    <td>
                                                        <input
                                                            oninput='changeMenuQty({{ $men->room_menu_id }}, this.value)'
                                                            type='number'
                                                            name='menu_qty_{{ $men->room_menu_id }}'
                                                            id='menu_qty_{{ $men->room_menu_id }}'
                                                            style='text-align: center; height: 30px; font-weight: bold; font-size: 15px'
                                                            class='form-control input-bb' min='1'
                                                            value='{{ $menuqty[$men->room_menu_id] ?? 1 }}'
                                                            autocomplete='off' />
                                                    </td>
                                                    <td align="right" id="sbs-menu-itm-{{ $men->room_menu_id }}">
                                                    </td>
                                                    <td class='text-center'><button type='button'
                                                            class='btn btn-outline-danger btn-sm'
                                                            onclick='deleteMenuItm({{ $men->room_menu_id }})'>Hapus</button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endisset
                                            @empty($menuItm)
                                                <tr>
                                                    <td align="center" valign="top" colspan="7"
                                                        class="dataTables_empty">No data available in table</td>
                                                </tr>
                                            @endempty
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="font-weight-bold text-center fs-4">Subtotal</td>
                                                <td colspan="2" class="font-weight-bold text-center fs-4">
                                                    <h5 id="subtotal"> Rp. <div class="sbs-menu-view d-inline"
                                                            id="sbs-menu-view"></div> - </h5>
                                                    <input type="hidden" name="subtotal_menu" id="sbs-menu" />
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <div class="form-actions float-left">
                                <button type="button" class="btn prev-btn booking-nav btn-primary"> <i
                                        class="fa fa-solid fa-arrow-left"></i>
                                    Kembali</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <div class="row justify-content-end">
    <div style="display: none;" class="card col-sm-auto col-md-6 align-self-end border border-dark h-100" id="card-total-all">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-3">
                    <a class="text-dark col-form-label">Sub Total Kamar</a>
                </div>
                <div class="col-auto">
                    :
                </div>
                <div class="col-8">
                    <input class="form-control input-bb" id="subtotal_all_room" name="subtotal_all_room" autocomplete="off" readonly/>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-3">
                    <a class="text-dark col-form-label">Sub Total Fasilitas</a>
                </div>
                <div class="col-auto">
                    :
                </div>
                <div class="col-8">
                    <input class="form-control input-bb" id="subtotal_all_facility" name="subtotal_all_facility" autocomplete="off" readonly/>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-3">
                    <a class="text-dark col-form-label">Sub Total Menu</a>
                </div>
                <div class="col-auto">
                    :
                </div>
                <div class="col-8 pr-0 border-bottom border-secondary">
                    <input class="form-control input-bb" id="subtotal_all_menu" name="subtotal_all_menu" autocomplete="off" readonly/>
                </div>
                <div class="col-auto pt-3 pl-1">
                    <i class="fa fa-solid fa-plus fa-xs"></i>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-3">
                    <a class="text-dark col-form-label">Sub Total</a>
                </div>
                <div class="col-auto">
                    :
                </div>
                <div class="col-8">
                    <input class="form-control input-bb" id="subtotal_all_view" name="subtotal_all_view" autocomplete="off" readonly/>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-3">
                    <a class="text-dark col-form-label">Diskon (%)</a>
                </div>
                <div class="col-auto">
                    :
                </div>
                <div class="col-8">
                    <input type="number" min="0" max="100" class="form-control input-bb" id="discount_percentage_total" name="discount_percentage_total" autocomplete="off" onchange="count_total()"/>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-3">
                    <a class="text-dark col-form-label">Jumlah Diskon</a>
                </div>
                <div class="col-auto">
                    :
                </div>
                <div class="col-8">
                    <input type="text" class="form-control input-bb" id="discount_amount_view" name="discount_amount_view" autocomplete="off" />
                    <input type="hidden" class="form-control input-bb" id="discount_amount" name="discount_amount" autocomplete="off" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-3">
                    <a class="text-dark col-form-label">Total</a><a class='red'> *</a></a>
                </div>
                <div class="col-auto">
                    :
                </div>
                <div class="col-8">
                    <input class="form-control input-bb" id="total_amount_view" name="total_amount_view" autocomplete="off" readonly/>
                    <input class="form-control input-bb" id="total_amount" type="hidden" name="total_amount" autocomplete="off" />
                </div>
            </div>
            <div class="row form-group {{isset($ci)?'d-none':''}}">
                <div class="col-3">Tipe Booking</div>
                <div class="col-auto">:</div>
                <div class="col-8">
                    @if (!isset($ci))
                        {!! Form::select('sales_order_type', $ordertype, $sessiondata['sales_order_type'] ?? '', [
                            'class' => 'selection-search-clear required select-form',
                            'name' => 'sales_order_type',
                            'id' => 'sales_order_type',
                            'onchange' => 'function_elements_add(this.name,this.value)',
                            'required',
                        ]) !!}
                    @endif
                </div>
            </div>
            <div class="row mb-3 {{isset($ci)?'d-none':''}}" id="down-payment-el">
                <div class="col-3">
                    <a id="label-payment" class="text-dark col-form-label">Uang Muka</a class="red"> *</a></a>
                </div>
                <div class="col-auto">
                    :
                </div>
                <div class="col-8">
                    <input class="form-control required input-bb" required autocomplete="off" id="down_payment_view" name="down_payment_view" />
                    <input class="form-control input-bb" id="down_payment" value="{{$sessiondata['down_payment_view']??''}}" name="down_payment" hidden/>
                </div>
            </div>
            <div id="without-dp" style="display: none;">
                <div class="row mb-3">
                    <div class="col-3">
                        <a id="label-payment" class="text-dark col-form-label">Bayar</a><a class='red'> *</a></a>
                    </div>
                    <div class="col-auto">
                        :
                    </div>
                    <div class="col-8">
                        <input class="form-control required input-bb" required autocomplete="off" id="payed_amount_view" name="payed_amount_view" />
                        <input class="form-control input-bb" id="payed_amount" value="{{$sessiondata['payed_amount_view']??''}}" name="payed_amount" hidden/>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-3">
                        <a id="label-change" class="text-dark col-form-label">Kembalian</a><a class='red'> *</a></a>
                    </div>
                    <div class="col-auto">
                        :
                    </div>
                    <div class="col-8">
                        <input class="form-control required input-bb" required autocomplete="off" id="change_amount_view" name="change_amount_view" readonly />
                        <input class="form-control input-bb" id="change_amount" name="change_amount" hidden/>
                    </div>
                </div>
            </div>
            <br>
            <div class="">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" autocomplete="off" id="form-reset" onclick="reset_add();"><i class="fa fa-times"></i> Batal</button>
                    <button type="button" name="Save" class="btn btn-success button-prevent" onclick="$(this).addClass('disabled');$('#form-booking').submit();" title="Save"><i class="fa fa-check"></i> Simpan</button>
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
