<?php

/**
 * Plugin Montant pour Spip 2.0
 * Licence GPL (c) 2009
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');


function action_editer_montant_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_montant = intval($securiser_action());

	if (!$id_montant) {
		$id_montant = sql_insertq("spip_montants");
	}

	// modifier le contenu via l'API
	include_spip('inc/modifier');

	$c = $opt = array();
	foreach (array(
		'objet', 'ids_objet', 'le_parent', 'prix_ht', 'taxe','descriptif'
		) as $champ){
		$c[$champ] = _request($champ);
		spip_log($champ ."=". _request($champ),"montants");
		}

	revision_montant($id_montant, $c);
	if ($redirect = _request('redirect')) {
		include_spip('inc/headers');
		redirige_par_entete(parametre_url(urldecode($redirect),
			'id_montant', $id_montant, '&'));
	} else
		return array($id_montant,'');
}


function revision_montant($id_montant, $c=false) {

	modifier_contenu('montant', $id_montant,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre'))
		),
		$c);
}

?>
