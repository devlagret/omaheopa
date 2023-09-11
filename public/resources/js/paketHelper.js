
function reset_add() {
    $.ajax({
        type: "GET",
        url: "{{ route('add-reset-item') }}",
        success: function(msg) {
            location.reload();
        }

    });
}

function changeCategory(url,token) {
    var merchant_id = $("#merchant_id").val();

    $.ajax({
        type: "POST",
        url: url,
        dataType: "html",
        data: {
            'merchant_id': merchant_id,
            '_token': token,
        },
        success: function(return_data) {
            $('#item_category_id').val(1);
            $('#item_category_id').html(return_data);
            function_elements_add('merchant_id', merchant_id);
        },
        error: function(data) {
            console.log(data);
        }
    });
}

function changeItem(url,token,ulrItem = 0, itemToken = 0,from = 1) {
    var id = $("#package_merchant_id").val();
    var no = $('.pkg-itm').length;
    $.ajax({
        type: "POST",
        url: url,
        dataType: "html",
        data: {
            'no': no,
            'merchant_id': id,
            '_token': token,
        },
        success: function(return_data) {
            if(from){clearIsiPaket();}
            $('#package_item_id').val(1);
            $('#package_item_id').html(return_data);
            changeSatuan(ulrItem,itemToken);
            function_elements_add('package_merchant_id', id);
        }
    });
}

function checkKemasan() {
    const max = 4;
    var no = $('.input-kemasan').length;
    while (no > max) {
        removeKemasan('input-kemasan-' + no)
    }
    if (no >= max) {
        $('#add-kmsn').addClass('disabled');
    } else {
        $('#add-kmsn').removeClass('disabled');
    }
}

function addKemasan(url) {
    const max = 4;
    var no = $('.input-kemasan').length;
    var noa = $('.input-kemasan').length + 1;
    if (no != max) {
        $.ajax({
            type: "get",
            url: url,
            dataType: "html",
            success: function(return_data) {
                location.reload();
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
}

function removeKemasan(el) {
    $.ajax({
        type: "get",
        url: "{{ route('remove-kemasan') }}",
        dataType: "html",
        success: function(return_data) {
            $('#' + el).remove();
            checkKemasan()
        },
        error: function(data) {
            console.log(data);
        }
    });
}

function addCategory(route) {
    location.href =  route + '/' + $('#merchant_id').val();
}

function addPackageItem(url,token,changeurl,qty = 1) {
    var package_item_id = $('#package_item_id').val();
    var package_item_unit = $('#package_item_unit').val();
    var package_item_id = $("#package_item_id").val();
    if ($('#item_package_' + package_item_id+'_'+package_item_unit+'_quantity').length) {
        $('#item_package_' + package_item_id+'_'+package_item_unit+'_quantity').val(function(i, oldval) {
            var newval = ++oldval;
            function_change_quantity(package_item_id,package_item_unit,newval,changeurl);
            return newval;
        });
        return 0;
    }
    $.ajax({
        type: "post",
        url: url,
        dataType: "html",
        data: {
            'item_id': package_item_id,
            'item_unit': package_item_unit,
            'qty': qty,
            '_token': token,
        },
        success: function(return_data) {
            if($('.pkg-itm').length==0){
            $('#package-table').html(return_data);}
            else{
            $('#package-table').append(return_data);
            }
        },
        error: function(data) {
            console.log(data);
        }
    });
}

function checkIsiPaket(){
    var length = $('.pkg-itm').length;
    if(length==null||length==0||length==''){
        alert('Harap Tambah Barang dalam Paket');
        return 0;
    }
    $('#form-paket').submit();
}

function clearIsiPaket(){
    $.ajax({
        type: "get",
        url: "{{ route('package.clear-item') }}",
        dataType: "html",
        success: function(return_data) {
            $('.pkg-itm').each(function( index ) {
               $(this).remove();
            });
            $('#package-table').html('<td valign="top" colspan="7" class="dataTables_empty">No data available in table</td>');
        },
        error: function(data) {
            console.log(data);
        }
    });
}

function deleteIsiPaket(item_id,item_unit,url){
    $.ajax({
        type: "get",
        url: url+'/'+item_id+'/'+item_unit,
        dataType: "html",
        success: function(return_data) {
            $('#col-package-item-'+item_id).remove();
        },
        error: function(data) {
            console.log(data);
        }
    });
}


function formatRp(){
    var harga = $('#package_price_view').val();
    function_elements_add('package_price_view', harga);
    $('#package_price_view').val(toRp(harga));
    $('#package_price').val(harga);
}