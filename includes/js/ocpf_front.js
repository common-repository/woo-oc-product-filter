jQuery(document).ready(function(){
    var id = jQuery("#pro_table").data("id");


    var dataTable = jQuery('#pro_table').DataTable({
        'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': '<div class="spinner"><img src="'+ object_name +'/includes/images/5.gif"></div>'
        },
        'responsive': (jQuery(window).width() < 767) ? true: false, 
        'serverSide': true,
        'order':[],
        'columnDefs': [ {
            'targets': [0], /* column index */
            'searchable': false,
            'orderable': false,
        }],
        "ajax": {
            "url":ajax_url,
            'data': function(data){
                var pid = id;
                var sel = new Array();
                var fil_val = new Array();
                jQuery('.filter_tbl tr td div').each(function() {
                    var customerId = jQuery(this).find('select').attr('id');   
                    sel.push(jQuery(this).find('select').attr('id')); 
                    fil_val.push(jQuery("#"+customerId).val());
                });
                var min = jQuery("#min").val();
                var max = jQuery("#max").val();

                data.pid = pid;
                data.sel = sel;
                data.fil_val = fil_val;
                data.min = min;
                data.max = max;
           }
        },   
    });
  

    jQuery('.filter_tbl tr td div').each(function() { 
        var sel = jQuery(this).find('select').attr('id'); 
        jQuery('#'+sel).change(function(){
            dataTable.draw();
        });
    });


    jQuery('.filter_tbl #max').change(function() {
        dataTable.draw();
    });


    jQuery('.filter_tbl #min').change(function() {
        dataTable.draw();
    });


    jQuery('.filter_tbl #max').keyup(function() {
        dataTable.draw();
    });


    jQuery('.filter_tbl #min').keyup(function() {
        dataTable.draw();
    });


    jQuery('.example-select-all').on('click', function(){
        var rows = dataTable.rows({ 'search': 'applied' }).nodes();
        jQuery('input[type="checkbox"]', rows).prop('checked', this.checked);
    });


    jQuery("#add_mul").click(function(){
        getValueUsingClass();
    });

    jQuery(document).on('click','.single_add',function(){
        var id = jQuery(this).attr('pids');
        var qty = jQuery(this).closest('tr').find('.qty_box').val();
        var currrenc = jQuery(this);
        var data = {
            action: 'woocommerce_ajax_add_to_cart_single',
            product_id: id,
            qty: qty,
        };

        jQuery.ajax({
            type: 'post',
            url: wc_add_to_cart_params.ajax_url,
            data: data,
            success: function (response) {
            	currrenc.html("Added");
            },
        });
        return false;
    });   
});
   

function getValueUsingClass(){
    var chkArray = {};
    jQuery(".ids:checked").each(function() {
        var qty = jQuery(this).closest('tr').find('.qty_box').val();
        if(typeof qty === 'undefined') {
            var qty = 1;
        }
        chkArray[jQuery(this).val()] = qty;
        jQuery(this).closest('tr').find('td').find('.single_add').html("Added");
    });
    var data = {
        action: 'woocommerce_ajax_add_to_cart',
        product_id: chkArray,
    };
    jQuery.ajax({
        type: 'post',
        url: wc_add_to_cart_params.ajax_url,
        data: data,
        success: function (response) {
            
        },
    });
    return false;
}

