$(document).ready(function() {
	function updateMode() {
		mode = $('#lst_auto_mode > option:selected').attr('value');
		if (mode == 'none') {
			$('#lst_auto_hour').parent('li').hide('slow');
			$('#lst_auto_week_days').parent('li').hide('slow');
			$('#lst_auto_month_day').parent('li').hide('slow');
		} else {
			$('#lst_auto_hour').parent('li').show('slow');
			if (mode == 'day') {
				$('#lst_auto_week_days').parent('li').hide('slow');
				$('#lst_auto_month_day').parent('li').hide('slow');
			} else {
				if (mode == 'week') {
					$('#lst_auto_week_days').parent('li').show('slow');
					$('#lst_auto_month_day').parent('li').hide('slow');
				} else {
					$('#lst_auto_week_days').parent('li').hide('slow');
					$('#lst_auto_month_day').parent('li').show('slow');
				}
			}
		}
	}
	$('#lst_auto_mode').bind('change', updateMode);
	updateMode();
});
