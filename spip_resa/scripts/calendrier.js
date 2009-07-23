function reserverJour(id_cal, ts)
{
	$.ajax({
		type: 'POST',
		url: '?exec=reservation',
		data: 'id_cal=' + id_cal + '&ts=' + ts,
		success:
			function(msg) {
				$('#td_' + ts).addClass('reserve') ;
				alert(msg) ;
			}
	}) ;
}
