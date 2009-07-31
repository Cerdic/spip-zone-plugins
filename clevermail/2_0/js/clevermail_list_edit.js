$(document).ready(function() {
	function updateMode() {
		mode = $('#lst_auto_mode > option[selected]').attr('value');
		if (mode == 'none') {
			$('#lst_auto_hour').parent('li').hide();
			$('#lst_auto_week_days').parent('li').hide();
			$('#lst_auto_month_day').parent('li').hide();
		} else {
			$('#lst_auto_hour').parent('li').show();
			if (mode == 'day') {
				$('#lst_auto_week_days').parent('li').hide();
				$('#lst_auto_month_day').parent('li').hide();
			} else {
				if (mode == 'week') {
					$('#lst_auto_week_days').parent('li').show();
					$('#lst_auto_month_day').parent('li').hide();
				} else {
					$('#lst_auto_week_days').parent('li').hide();
					$('#lst_auto_month_day').parent('li').show();
				}
			}
		}
	}
	$('#lst_auto_mode').bind('change', updateMode);
	updateMode();
});
