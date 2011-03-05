jQuery(document).ready(function($) {
	$('.includeajax').each(function() {
		var me = $(this);
		var env = $('a', this).attr('rel');
		if (env) {
			$('a', this).attr('href','#');
			$.ajax({
				url: "spip.php",
				type: "GET",
				cache: true,
				data: { var_ajax: 'recuperer', var_ajax_env: env },
				success: function(c) { me.html(c); }
			});
		}
	});
	$('.includestatic').each(function() {
		var me = $(this);
		var env = $('a', this).attr('rel');
		if (env) {
			$('a', this).attr('href','#');
			$.ajax({
				url: env,
				type: "GET",
				cache: true,
				success: function(c) { me.html(c); }
			});
		}
	});
});