function filtre_niveau(niveau){
	for (i=1;i<10;i++){
		if (i<=niveau)
			$('.niveau-'+i).parents('tr.row').show();
		else
			$('.niveau-'+i).parents('tr.row').hide();
	}
}