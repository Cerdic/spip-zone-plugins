<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/abomailmans');
include_spip('inc/distant');

// chargement des valeurs par defaut des champs du formulaire
function formulaires_abomailman_envoi_liste_charger_dist(){
	global $visiteur_session;
	
	if($visiteur_session['statut'] == '0minirezo'){
		//initialise les variables d'environnement pas défaut
		$valeurs = array();
		$valeurs['editable'] = true;
		
		$valeurs['sujet'] = _request('sujet');
		$valeurs['template'] = _request('template');
		$valeurs['message'] = _request('message');
		$valeurs['date'] = _request('date');
		$valeurs['id_rubrique'] = _request('id_rubrique');
		$valeurs['id_mot'] = _request('id_mot');
		$valeurs['templates'] = "";
		
		$liste_templates = find_all_in_path("templates/","[.]html$");
		foreach($liste_templates as $titre_option) {
			$titre_option = basename($titre_option,".html");
			$value_option = "templates/$titre_option";
			$valeurs['templates'] .= "<option value='".$value_option."'>".$titre_option."</option>\n";
		}
	}else{
		$valeurs['editable'] = false;
		$valeurs['message_erreur'] = _T('abomailmans:envoi_droits_insuffisants');
	}
	return $valeurs;
}

function formulaires_abomailman_envoi_liste_verifier_dist(){

	//charge la fonction de controle du login et mail
	//$test_inscription = charger_fonction('test_inscription');

	//initialise le tableau des erreurs
	$erreurs = array();

	$valeurs['sujet'] = _request('sujet');
	$valeurs['template'] = str_replace('\'','',_request('template'));
	$valeurs['message'] = _request('message');
	$valeurs['date'] = _request('date');
	$valeurs['id_rubrique'] = _request('id_rubrique');
	$valeurs['id_mot'] = _request('id_mot');
	
	if(!$valeurs['sujet']){
		$erreurs['sujet'] = _T('abomailmans:sujet_obligatoire');
	}

    //message d'erreur genéralisé
    if (count($erreurs)) {
        $erreurs['message_erreur'] .= _T('abomailmans:verifier_formulaire');
    }
	if (!count($erreurs) AND !_request('confirmer_previsu_abomailman')){
		if ($afficher_texte != 'non') {
			$previsu = abomailmain_inclure_previsu($valeurs['sujet'], $valeurs['message'], $valeurs['template'], $valeurs['date'], $valeurs['id_mot'], $valeurs['id_rubrique']);
			$erreurs['previsu'] = $previsu;
		}
	}
    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function abomailmain_inclure_previsu($sujet, $message, $template, $date, $id_mot, $id_rubrique){
	$bouton = _T('abomailmans:envoi_confirmer');
	include_spip('public/assembler');
	$datas =  array(
			'sujet' => $sujet,
			'message' => $message,
			'template' => $template,
			'id_mot' => $id_mot,
		    'id_rubrique' => $id_rubrique,
		    'date' => $date,
			'erreur' => $erreur,
			'bouton' => $bouton
			);
	$texte_template = generer_url_public('abomailman_template').'&'.abomailman_http_build_query($datas,"","&");
	$datas['texte_template'] = recuperer_page($texte_template,true);
	// supprimer les <form> de la previsualisation
	// (sinon on ne peut pas faire <cadre>...</cadre> dans les forums)
	return preg_replace("@<(/?)form\b@ism",
			    '<\1div',
		inclure_balise_dynamique(array('formulaires/inc-previsu_mail',
		      0,
		      $datas
			),
		false));
}

function formulaires_abomailman_envoi_liste_traiter_dist(){
    $message = '';
	$message['editable'] = true;
    
	$datas = array();
    
    // Récupération des données
	$datas['sujet'] = _request('sujet');
	$datas['template'] = str_replace('\'','',_request('template'));
	$datas['message'] = _request('message');
	$datas['date'] = _request('date');
	$datas['id_rubrique'] = _request('id_rubrique');
	$datas['id_mot'] = _request('id_mot');
	$datas['charset'] = lire_meta('charset');
	
	$texte_template = generer_url_public('abomailman_template').'&'.abomailman_http_build_query($datas,"","&");
	$datas['texte_template'] = recuperer_page($texte_template,true);
	
	$datas['email_liste'] = _request('email_liste');
	$datas['nomsite'] = lire_meta("nom_site");
	$datas['email_webmaster'] = lire_meta("email_webmaster");
	spip.log($datas['texte_template']);
	if (abomailman_mail($datas['nomsite'], $datas['email_webmaster'], "", $datas['email_liste'], $datas['sujet'], $datas['texte_template'], true, $datas['charset'])) {
		$message['message_ok'] = _T('abomailman:email_envoye',array('liste'=>$datas['email_liste']));
	}

	$message['editable'] = false;
	$message['redirect'] = self();

    return $message;
}

?>
