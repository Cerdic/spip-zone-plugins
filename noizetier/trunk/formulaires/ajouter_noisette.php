<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('noizetier_fonctions');
include_spip('base/abstract_sql');
if (!function_exists('autoriser'))
	include_spip('inc/autoriser');	 // si on utilise le formulaire dans le public

// Note : $retour indique la page à charger en cas d'ajout
//        @id_noisette@ étant alors remplacer par la bonne valeur, connue seulement après ajout de la noisette

function formulaires_ajouter_noisette_charger_dist($page, $bloc, $retour=''){
	return array(
		'page' => $page,
		'bloc' => $bloc,
		'editable' => autoriser('configurer','noizetier') ? 'on' : ''
	);
}

function formulaires_ajouter_noisette_traiter_dist($page, $bloc, $retour=''){
	if (!autoriser('configurer','noizetier'))
		return array('message_erreur' => _T('noizetier:probleme_droits'));
	
	$res = array();
	$noisette = _request('noisette');
	
	if (!$noisette)
		return array('message_erreur' => _T('noizetier:erreur_aucune_noisette_selectionnee'));
	
	if ($noisette) {
		if( $id_noisette = noizetier_ajouter_noisette($noisette, $page, $bloc)) {
			$res['message_ok'] = _T('info_modification_enregistree');
			if ($retour) {
				$retour = str_replace('&amp;', '&', $retour); // Grrr, y a surement plus propre
				$retour = str_replace('@id_noisette@', $id_noisette, $retour);
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
	}
	return $res;
}

?>
