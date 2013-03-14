<?php
function urledit_affiche_milieu($flux){
	$exec = $flux["args"]["exec"];
	
	$type_objet = "";
	if ($flux["args"]["id_rubrique"] and $exec=='naviguer') {
		$type_objet = 'rubrique';
		$id_objet = $flux["args"]["id_rubrique"];
	}
	if ($flux["args"]["id_article"] and $exec=='articles') {
		$type_objet = 'article';
		$id_objet = $flux["args"]["id_article"];
	}
	if ($flux["args"]["id_breve"] and $exec=='breves_voir') {
		$type_objet = 'breve';
		$id_objet = $flux["args"]["id_breve"];
	}
	if ($flux["args"]["id_auteur"] and $exec=='auteur_infos') {
		$type_objet = 'auteur';
		$id_objet = $flux["args"]["id_auteur"];
	}
	if ($flux["args"]["id_mot"] and $exec=='mots_edit') {
		$type_objet = 'mot';
		$id_objet = $flux["args"]["id_mot"];
	}
	if ($flux["args"]["id_syndic"] and $exec=='sites') {
		$type_objet = 'syndic';
		$id_objet = $flux["args"]["id_syndic"];
	}
	if ($flux["args"]["id_evenement"] and $exec=='evenements_edit') {
		$type_objet = 'evenement';
		$id_objet = $flux["args"]["id_evenement"];
	}
  // --------------
	if (in_array($type_objet,array('article','rubrique','breve','auteur','mot','syndic','evenement'))) {
		include_spip('inc/urledit');
		include_spip('urls/propres');
    include_spip('urls/arbo');
		include_spip('inc/autoriser');  
		if (autoriser($type_objet,'urledit',$id_objet)){
			$redirect = self();
			$args=$type_objet."-".$id_objet;
			$urlpropre = pipeline('creer_chaine_url',
				array(
					'data' => $url_propre,  // le vieux url_propre
					'objet' => array('type' => $type, 'id_objet' => $id_objet, 'titre'=>$flux['args']['row']['titre'])
				)
			);
			$contexte = array('urlpropre'=>$urlpropre,'args'=>$args,'redirect'=>$redirect,'id_objet' => $id_objet,'type_objet' => $type_objet, 'erreur_urledit' => _request('erreur_urledit'));
			$fond = recuperer_fond("prive/formulaires/urledit", $contexte);
			
			$flux['data'].=$fond;
		}	
	}
	// ---------------

	return $flux;
}

?>