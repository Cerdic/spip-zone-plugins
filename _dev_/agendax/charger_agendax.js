	$("div#agendax div.titre_calendrier span.mois_precedent a").click(function(){
		mois = mois - 1;
		if (mois == 0){
			mois = 12;
			annee = annee -1;
		}
		remplire_agenda(annee,mois);
		return false;
		}
	);
	
	$("div#agendax div.titre_calendrier span.mois_suivant a").click(function(){
		mois = mois + 1;
		if (mois == 13){
			mois = 1;
			annee = annee + 1;
		}
		remplire_agenda(annee,mois);
		return false;
		}
	);
