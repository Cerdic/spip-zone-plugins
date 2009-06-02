
function marquepages_confirmer(quoi,message){
	if (!message)
		message='Êtes-vous sûr ?';
	$(quoi)
		.click(function(){
			if (confirm(message))
				return true;
			else
				return false;
		}
	);
}
