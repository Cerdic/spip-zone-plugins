<?php

/**
 * Plugin Coordonnées 
 * Licence GPL (c) 2010 Matthieu Marcillaud 
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_cat_variante_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas d'id ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_cat_variante = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_cat_variante = insert_cat_variante();
	}

	if ($id_cat_variante) $err = revisions_cat_variantes($id_cat_variante);
	return array($id_cat_variante,$err);
}


function insert_cat_variante() {
	$champs = array(
		'titre' => _T('item_nouveau_titre')
	);
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_cat_variantes',
		),
		'data' => $champs
	));
	
	$id_cat_variante = sql_insertq("spip_cat_variantes", $champs);
	return $id_cat_variante;
}


// Enregistrer certaines modifications d'une cat_variante
function revisions_cat_variantes($id_cat_variante, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array(
				'id_article',
				'titre', 'descriptif', 'statut',
				'prix_ht', 'tva', 'date', 'date_redac') as $champ
		) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}

	include_spip('inc/modifier');
	modifier_contenu('cat_variante', $id_cat_variante, array(
			'invalideur' => "id='id_cat_variante/$id_cat_variante'"
		),
		$c);


	$champs = array();
	
	// Changer le statut de la variante ?
	if ($statut = _request('statut', $c)) {
		$statut_ancien = sql_getfetsel("statut", "spip_cat_variantes", "id_cat_variante=$id_cat_variante");
		if ($statut != $statut_ancien) {
			$champs['statut'] = _request('statut', $c);
		}
	}
	
	if ($champs) {
		sql_updateq('spip_cat_variantes', $champs, "id_cat_variante=$id_cat_variante");
	}

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_cat_variante/$id_cat_variante'");
	
}
?>
