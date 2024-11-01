//jquery tab
jQuery(document).ready(function(){
    //slider setting options by tabbing
    jQuery('ul.tabs li').click(function(){
        var tab_id = jQuery(this).attr('data-tab');
        jQuery('ul.tabs li').removeClass('current');
        jQuery('.tab-content').removeClass('current');
        jQuery(this).addClass('current');
        jQuery("#"+tab_id).addClass('current');
    })
})

//Copy shortcode
function ocpf_select_data(id)
{
	    var copyText = id;
	    jQuery("#"+copyText).select();
        document.execCommand("copy");
}

//default display
jQuery(document).ready(function(){
    jQuery("input[name='ocpf_default_display']").click(function(){
        var radioValue = jQuery("input[name='ocpf_default_display']:checked").val();
        var selValue = jQuery(this).closest(".ocpf_tax_div").find(".ocpf_tax_sel").attr("tax_name");
        jQuery(".ocpf_tax_sel").hide();
        jQuery(this).closest(".ocpf_tax_div").find(".ocpf_tax_sel").css("display","block");
    });
});

jQuery( function() {
    jQuery( "#sortable" ).sortable();
    jQuery( "#sortable" ).disableSelection();
} );

