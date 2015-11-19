$(function() {
	function charger_inclure_ajaxload() {
		$('.includeajax').each(function() {
			var me = $(this);
			var env = $('a', this).attr('rel').replace(/ nofollow$/, '');
			if (env) {
				$('a', this).attr('href','#');
				$.ajax({
					url: "spip.php",
					type: "GET",
					cache: true,
					data: { var_ajax: 'recuperer', var_ajax_env: env },
					success: function(c) { me.html(c).removeClass('includeajax_load').addClass('includeajax_loaded'); }
				});
			}
		}).removeClass('includeajax').addClass('includeajax_load');
		$('.includestatic').each(function() {
			var me = $(this);
			var env = $('a', this).attr('rel');
			if (env) {
				$('a', this).attr('href','#');
				$.ajax({
					url: env,
					type: "GET",
					cache: true,
					success: function(c) {  me.html(c).removeClass('includestatic_load').addClass('includestatic_loaded'); }
				});
			}
		}).removeClass('includestatic').addClass('includestatic_load');
	}
	charger_inclure_ajaxload();
	onAjaxLoad(charger_inclure_ajaxload);
});
