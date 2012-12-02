<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('noizetier_fonctions');
include_spip('base/abstract_sql');
include_spip('inc/saisies');
if (!function_exists('autoriser'))
	include_spip('inc/autoriser');	 // si on utilise le formulaire dans le public

function formulaires_editer_noisette_charger_dist($id_noisette, $retour=''){
	$valeurs = array();
	$valeurs['id_noisette'] = $id_noisette;
	$entree = sql_fetsel(
			'noisette, parametres, css',
			'spip_noisettes',
			'id_noisette = '.$id_noisette
		);
	$noisette = $entree['noisette'];
	$valeurs['noisette'] = $noisette;
	
	// Il faut aller recherche les parametres par defaut de la noisette
	// pour generer le tableau de saisie
	$infos_noisette = noizetier_info_noisette($noisette);
	$valeurs['_params'] = $infos_noisette['parametres'];
	$valeurs['_params'][] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'css',
			'label' => _T('noizetier:label_noizetier_css'),
			'explication' => _T('noizetier:explication_noizetier_css'),
			'defaut' => $entree['css']
		)
	);
	$valeurs['editable'] = autoriser('configurer','noizetier') ? 'on' : '';
	
	// Inserer dans le contexte les valeurs des parametres
	// NB : on doit passer par saisies_charger_champs() au cas ou la definition de la noisette a change et qu'il y a de nouveau champs a prendre en compte
	$parametres = unserialize($entree['parametres']);
	if (is_array($parametres))
		$valeurs = array_merge($valeurs, saisies_charger_champs($infos_noisette['parametres']), $parametres);
	$valeurs['css'] = $entree['css'];
	
	return $valeurs;
}

function formulaires_editer_noisette_verifier_dist($id_noisette, $retour=''){
	$noisette = _request('noisette');
	$infos_noisette = noizetier_info_noisette($noisette);
	return saisies_verifier($infos_noisette['parametres'],false);
}

function formulaires_editer_noisette_traiter_dist($id_noisette, $retour=''){
	if (!autoriser('configurer','noizetier'))
		return array('message_erreur' => _T('noizetier:probleme_droits'));
	
	$res = array();
	$css = _request('css');
	$noisette = _request('noisette');
	$infos_noisette = noizetier_info_noisette($noisette);
	$parametres = array();
	foreach (saisies_lister_champs($infos_noisette['parametres'],false) as $champ)
		$parametres[$champ] = _request($champ);
	if (sql_updateq('spip_noisettes',array('parametres' => serialize($parametres),'css' => $css),'id_noisette='.$id_noisette)) {
		// On invalide le cache
		include_spip('inc/invalideur');
		suivre_invalideur("id='noisette/$id_noisette'");
		$res['message_ok'] = _T('info_modification_enregistree');
		if ($retour) {
			if (strncmp($retour,'javascript:',11)==0){
				$res['message_ok'] .= '<script type="text/javascript">/*<![CDATA[*/'.substr($retour,11).'/*]]>*/</script>';
				$res['editable'] = true;
			}
			else
				$res['redirect'] = $retour;
		}
	}
	else
		$res['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
	return $res;
}

?>
