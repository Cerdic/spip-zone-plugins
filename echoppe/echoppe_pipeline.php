<?php


if (!defined("_ECRIRE_INC_VERSION")) return;
	
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ECHOPPE',(_DIR_PLUGINS.end($p)));


function echoppe_ajouter_boutons($flux){
	$flux['naviguer']->sousmenu['echoppe']= new Bouton("../"._DIR_PLUGIN_ECHOPPE."/images/echoppe_blk_24.png",_T('echoppe:gerer_echoppe'));
	return $flux;	
}
function echoppe_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/echoppe.css').'" type="text/css" media="all" /> <!-- CSS Echoppe --> ';
	return $flux;	
}

function echoppe_insert_head($flux){
	return $flux;	
}

function echoppe_I2_cfg_form($flux){
    //$flux .= recuperer_fond('fonds/inscription2_echopppe');

	return $flux;	
}

function echoppe_formulaire_charger($flux){
	if ($flux["args"]["form"] == "login"){
		$flux["data"]["_hidden"] .= '<input name="echoppe_token_panier" value="'.session_get('echoppe_token_panier').'" type="hidden">';
		$flux["data"]["_hidden"] .= '<input name="echoppe_token_client" value="'.session_get('echoppe_token_client').'" type="hidden">';
		$flux["data"]["_hidden"] .= '<input name="echoppe_statut_panier" value="'.session_get('echoppe_statut_panier').'" type="hidden">';
	}
	return $flux;
}

function echoppe_formulaire_verifier($flux){
	if ($flux["args"]["form"] == "login"){
		session_set('echoppe_token_panier', _request('echoppe_token_panier') );
		session_set('echoppe_token_client', _request('echoppe_token_client') );
		session_set('echoppe_statut_panier', _request('echoppe_statut_panier') );
	}
	
	if (sql_count(sql_select(array("id_auteur"),"spip_echoppe_clients","token_client = '"._request('echoppe_token_client')."'")) < 1){
		$res_lien = sql_insertq('spip_echoppe_clients',array('id_auteur' => session_get('id_auteur'), 'token_client' => _request('echoppe_token_client')));
		spip_log('liaison de l\'auteur '.$contexte['id_auteur'].' au token *** from login => '.$res_lien,'echoppe');
	}
	return $flux;
}

function echoppe_formulaire_traiter($flux){
	/*//var_dump($flux["data"]);
	spip_log("plop");
	//die();
	if ($flux["args"]["form"] == "login"){
		session_set('echoppe_token_panier', $flux["data"]["echoppe_token_panier"] );
		session_set('echoppe_token_client', $flux["data"]["echoppe_token_client"] );
		session_set('echoppe_statut_panier', $flux["data"]["echoppe_statut_panier"] );
	}*/
	return $flux;
}

function echoppe_taches_generales_cron($taches_generales){
	$jours = lire_config('echoppe/duree_de_vie_paniers_temp', 2);
	$taches_generales['echoppe'] = 60*60*24*$jours; // par exemple toutes les 10 minutes, ne pas descendre en dessous de 30 secondes !
	return $taches_generales;
}

?>
