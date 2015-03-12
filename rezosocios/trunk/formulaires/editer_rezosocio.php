<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

// http://doc.spip.org/@inc_editer_rezosocio_dist
function formulaires_editer_rezosocio_charger_dist($id_rezosocio='new', $id_parent=null, $retour='', $associer_objet='', $config_fonc='rezosocios_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('rezosocio',$id_rezosocio,$id_parent,'',$retour,$config_fonc,$row,$hidden);

	if ($associer_objet){
		if (intval($associer_objet)){
			// compat avec l'appel de la forme ajouter_id_article
			$objet = 'article';
			$id_objet = intval($associer_objet);
		}
		else {
			list($objet,$id_objet) = explode('|',$associer_objet);
		}
	}
	$valeurs['table'] = ($associer_objet?table_objet($objet):'');

	// Si nouveau et titre dans l'url : fixer le titre
	if ($id_rezosocio == 'oui'
		AND strlen($titre = _request('titre')))
			$valeurs['titre'] = $titre;

	$valeurs['changer_lang'] = _request('changer_lang') ? _request('changer_lang') : $valeurs['langue'];

	include_spip('inc/rezosocios');
	$valeurs['_types_rezosocios'] = rezosocios_liste();
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_rezosocio_identifier_dist($id_rezosocio='new', $id_parent=null, $retour='', $associer_objet='', $config_fonc='rezosocios_edit_config', $row=array(), $hidden=''){
	return serialize(array(intval($id_rezosocio),$associer_objet));
}

// Choix par defaut des options de presentation
// http://doc.spip.org/@articles_edit_config
function rezosocios_edit_config($row)
{
	global $spip_ecran, $spip_lang;

	$config = $GLOBALS['meta'];
	$config['lignes'] = ($spip_ecran == "large")? 8 : 5;
	$config['langue'] = $spip_lang;
	$config['restreint'] = false;
	return $config;
}

function formulaires_editer_rezosocio_verifier_dist($id_rezosocio='new', $id_parent=null, $retour='', $associer_objet='', $config_fonc='rezosocios_edit_config', $row=array(), $hidden=''){

	$erreurs = formulaires_editer_objet_verifier('rezosocio',$id_rezosocio,array('titre'));
	// verifier qu'un rezosocio du meme groupe n'existe pas avec le meme titre
	// la comparaison accepte un numero absent ou different
	// sinon avertir
	
	if (sql_countsel("spip_rezosocios", 
					"titre REGEXP ".sql_quote("^([0-9]+[.] )?".preg_quote(supprimer_numero(_request('titre')))."$")
					." AND id_rezosocio<>".intval($id_rezosocio)))
		$erreurs['titre'] =
					_T('rezosocios:avis_doublon_rezosocio_cle')
					." <input type='hidden' name='confirm_titre_rezosocio' value='1' />";
	
	if(sql_countsel("spip_rezosocios",'url_site ='.sql_quote(_request('url_site')))){
		$erreurs['url_site'] = _T('rezosocios:erreur_url_utilisee');
	}
	return $erreurs;
}

// http://doc.spip.org/@inc_editer_rezosocio_dist
function formulaires_editer_rezosocio_traiter_dist($id_rezosocio='new', $id_parent=null, $retour='', $associer_objet='', $config_fonc='rezosocios_edit_config', $row=array(), $hidden=''){
	$res = array();
	set_request('redirect','');
	$action_editer = charger_fonction("editer_rezosocio",'action');
	list($id_rezosocio,$err) = $action_editer();
	if ($err){
		$res['message_erreur'] = $err;
	}
	else {
		$res['message_ok'] = "";
		if ($retour){
			if (strncmp($retour,'javascript:',11)==0){
				$res['message_ok'] .= '<script type="text/javascript">/*<![CDATA[*/'.substr($retour,11).'/*]]>*/</script>';
				$res['editable'] = true;
			}
			else {
				$res['redirect'] = $retour;
				if (strlen(parametre_url($retour,'id_rezosocio')))
					$res['redirect'] = parametre_url($res['redirect'],'id_rezosocio',$id_rezosocio);
			}
		}

		if ($associer_objet){
			if (intval($associer_objet)){
				// compat avec l'appel de la forme ajouter_id_article
				$objet = 'article';
				$id_objet = intval($associer_objet);
			}
			else {
				list($objet,$id_objet) = explode('|',$associer_objet);
			}
			if ($objet AND $id_objet AND autoriser('modifier',$objet,$id_objet)){
				include_spip('action/editer_rezosocio');
				rezosocio_associer($id_rezosocio, array($objet=>$id_objet));
				if (isset($res['redirect']))
					$res['redirect'] = parametre_url ($res['redirect'], "id_lien_ajoute", $id_rezosocio, '&');
			}
		}

	}
	return $res;
}


?>
