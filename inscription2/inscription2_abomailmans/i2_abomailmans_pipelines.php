<?php
	
function i2_abomailmans_i2_cfg_form($flux){
	//Le pavé de configuration dans le CFG d'inscription2
	$flux .= recuperer_fond('fonds/inscription2_abomailmans');
	return $flux;
}
	
function i2_abomailmans_i2_form_fin($flux){
	// Le pavé dédié aux listes dans le formulaire d'inscription 
	// ou de changement de profil
	if ((lire_config('inscription2/liste') == 'on') && (count(lire_config('inscription2/listes'))>0)){
		$flux['data'] .= recuperer_fond('formulaires/inscription2_form_abomailmans',$flux['args']);
	}

	return $flux;
}
	
function i2_abomailmans_i2_charger_formulaire($flux){

	// Ajouter un array() $listes dans les $valeurs envoyées au formulaire.
	if((is_numeric($flux['data']['id_auteur'])) && (lire_config('inscription2/liste') == 'on')){
		// selectionner les listes de l'auteur
		$res = sql_select('id_abomailman',  'spip_abomailmans',  'id_auteur='.$flux['data']['id_auteur']);

		// boucler les resultats
		while($liste = sql_fetch($res)){
			$listes[] = $liste['id_abomailman'];
		}
		$flux['data']['listes'] = $listes;
	}
	else{
		    $flux['data']['listes'] = _request('listes');
	}

	return $flux;
}



function i2_abomailmans_i2_traiter_formulaire($flux)
{
	// Pour l'envoi de l'email
	include_spip('inc/abomailmans');
	
	$nom = _request('nom');
	$email = _request('email');
	$listes = _request('listes', true);
	
	$nb_listes = 0;
	foreach($listes as $id_abomailman) {
		$nb_listes++;
	
		//on initialise l'envoi
		// on traite chaque liste via une fonction reutilisable ailleurs
		$traiter=abomailman_traiter_abonnement($id_abomailman);
		$titre = $traiter[0];
		$proprio_email=$traiter[1];
		$liste_email=$traiter[2];
		$sujet=$traiter[3];
		$body="$nom - $email ".$traiter[4];
		$headers=$traiter[5];

		abomailman_mail($nom, $email, $proprio_email,$liste_email, $sujet, $body,'',$headers);

	}

	return $flux;
}

function i2_abomailmans_i2_exceptions_des_champs_auteurs_elargis($flux){
   
	// On ne crée pas de champs dans la table auteurs_elargis pour ces inputs
	// $flux est un array à compléter
	$flux[] = 'liste';
	$flux[] = 'listes';
	$flux[] = 'optout';
		
	return $flux;
}

?>
