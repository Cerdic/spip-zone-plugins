<?php
/*
 * Plugin Encarts
 * (c) 2011 Camille Lafitte, Cyril Marion
 * Avec l'aide de Matthieu Marcillaud
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_encart_charger_dist($id_encart='new', $objet='', $id_objet='', $retour=''){
	$valeurs = formulaires_editer_objet_charger('encart', $id_encart, '', '', $retour, '');
	$valeurs['maintenant'] = date('Y-m-d H:i:s');
	$valeurs['objet'] = $objet;
	$valeurs['id_objet'] = $id_objet;
	return $valeurs;
}


function formulaires_editer_encart_verifier_dist($id_encart='new', $objet='', $id_objet='', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('encart', $id_encart);
	return $erreurs;
}


function formulaires_editer_encart_traiter_dist($id_encart='new', $objet='', $id_objet='', $retour=''){
	// si redirection demandee, on refuse le traitement en ajax
	if ($retour) refuser_traiter_formulaire_ajax();
	$res = formulaires_editer_objet_traiter('encart', $id_encart, '', '', $retour, '');
	if ($retour) {
		$res['redirect'] = parametre_url(parametre_url($retour, 'id_encart', ''), 'ouvrir', '').'#encarts';
	}
	return $res;
}



?>
