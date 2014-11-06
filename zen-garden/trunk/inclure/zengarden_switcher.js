jQuery(function() {
	jQuery("#switcher_zen select")
		.change(function(){
			jQuery(this).parents('form').get(0).submit();
		})
	  .siblings("input[type=submit]").hide();
        jQuery('#switcher_zen select option[value="'+theme_selected+'"]')
        .attr('selected', 'selected');
});