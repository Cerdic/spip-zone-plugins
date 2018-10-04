<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Formulaire de signalement
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');
include_spip('inc/signalement');
include_spip('inc/config');

/**
 *
 * @param string $objet
 * @param int $id_objet
 * @return array
 */
function formulaires_signalement_charger_dist($objet, $id_objet){
	$valeur = array(
		'editable'=>true,
		'_deja_signale'=>false,
		'objet'=>$objet,
		'id_objet' => $id_objet,
		'motif' => _request('motif'),
		'texte' => _request('texte')
	);
	if (!isset($GLOBALS['visiteur_session']['statut'])){
		$valeur['editable'] = false;
	}
	else {
		include_spip('inc/signalement');
		$table = table_objet_sql($objet);
		$id_table_objet = id_table_objet($table);
		$id_objet = sql_getfetsel("$id_table_objet","$table","$id_table_objet = ".intval($id_objet));
		if(intval($id_objet)){ 
			$signalement = signalement_trouver($id_objet,$objet,$GLOBALS['visiteur_session']['id_auteur']);
			if($signalement['id_signalement']){
				$valeur['_deja_signale'] = true;
				if($signalement['motif'])
					$valeur['motif'] = $signalement['motif'];
				if($signalement['texte'])
					$valeur['texte'] = $signalement['texte'];
			}
		}else{
			$valeur['editable'] = false;	
		}
	}
	return $valeur;
}

function formulaires_signalement_verifier_dist($objet, $id_objet){
	$erreurs = array();
	$signalement = signalement_trouver($id_objet,$objet,$GLOBALS['visiteur_session']['id_auteur']);
	if (!$signalement['id_signalement']){
		$erreurs = formulaires_editer_objet_verifier('signalement','',array('motif','texte'));
	}
	if(count($erreurs) > 0 && (lire_config('signalement/mediabox') != "on" OR !defined('_DIR_PLUGIN_MEDIABOX')))
		$erreurs['message_erreur'] = '<script type="text/javascript">if (window.jQuery) jQuery.colorbox({inline:true,href:"#formulaire_signalement_'.$objet.'_'.$id_objet.' .formulaire_signalement"});</script>';

	return $erreurs;
}

function formulaires_signalement_traiter_dist($objet, $id_objet){
	$res = array('message_ok'=>' ');
	$signalement = false;
	if ($id_auteur = intval($GLOBALS['visiteur_session']['id_auteur'])){
		$signalement = signalement_trouver($id_objet,$objet,$GLOBALS['visiteur_session']['id_auteur']);
		if ($signalement['id_signalement']){
			include_spip('action/editer_signalement');
			signalement_instituer($signalement['id_signalement'],array('statut'=>'poubelle'));
		}
		else{
			set_request('objet',$objet);
			set_request('id_objet',$id_objet);
			$res = formulaires_editer_objet_traiter('signalement','',"",$lier_trad,$retour,$config_fonc,$row,$hidden);
		}
	}
	
	$autoclose = (lire_config('signalement/mediabox') == "on" OR !defined('_DIR_PLUGIN_MEDIABOX')) ? '' : "<script type='text/javascript'>if (window.jQuery) jQuery.modalboxclose();</script>";
	if (!isset($res['message_erreur'])){
		$res['message_ok'] = $autoclose;
		$res['editable'] = false;
	}

	if ($res['message_ok'])
		$res['message_ok'].=(lire_config('signalement/mediabox') == "on" OR !defined('_DIR_PLUGIN_MEDIABOX')) ? '' : '<script type="text/javascript">if (window.jQuery) jQuery("#formulaire_signalement_'.$objet.'_'.$id_objet.' .formulaire_spip").ajaxReload();</script>';
	
	spip_log($res,'elix');
	return $res;
}
?>