<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_seance_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	// Teste si autorisation pour les actions d'editions
	$arg = $securiser_action();
	
	$id_seance = _request('id_seance');
	
	if ($id_seance == 'new') {
		if (($arg != 'oui') and ($arg != _request('duplicate'))) {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_seance = insert_seance();
	}
	if ($id_seance == intval($id_seance))
		$err = revisions_seance($id_seance);
	
	return array($id_seance,$err);
}

// inserer seances_endroit
function insert_seance() {
	$champs = array(
		'id_article' => 0
	);

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_seances',
		),
		'data' => $champs
	));
	
	$id_seance = sql_insertq("spip_seances", $champs);
	return $id_seance;
}

// Enregistrer modifications
function revisions_seance($id_seance, $c=false) {
	if ($c === false) {
		$c = array();
		foreach (array('id_endroit', 'id_article', 'date_seance', 'remarque_seance') as $champ) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}
	// si on passe par la voie officielle pb pour la duplication 
	// include_spip('inc/modifier');
	// modifier_contenu ('seance', $id_seance, array(),$c);
	sql_updateq('spip_seances',$c,'id_seance = '.intval($id_seance));
}
?>