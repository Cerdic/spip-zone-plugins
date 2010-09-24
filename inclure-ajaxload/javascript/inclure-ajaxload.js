$(document).ready(function() {
	$('.includeajax').each(function() {
		var me = $(this);
		var env = $('a', this).attr('rel');
		if (env) {
			$('a', this).attr('href','#');
			$.post(
				window.location.href,
				{ var_ajax: 'recuperer', var_ajax_env: env },
				function(c) { me.html(c); }
			);
		}
	});
});