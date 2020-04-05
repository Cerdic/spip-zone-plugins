<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_seances_endroit_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	// Teste si autorisation pour les actions d'editions
	$arg = $securiser_action();
	
	if (!$id_endroit = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_endroit = insert_seances_endroit();
	}
	if ($id_endroit)
		$err = revisions_seances_endroit($id_endroit);
	
	return array($id_endroit,$err);
}

// inserer seances_endroit
function insert_seances_endroit() {
	$champs = array(
		'nom_endroit' => _T('seances:item_nouvel_seances_endroit')
	);
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_seances_endroits',
		),
		'data' => $champs
	));
	
	$id_endroit = sql_insertq("spip_seances_endroits", $champs);
	return $id_endroit;
}

// Enregistrer modifications
function revisions_seances_endroit($id_endroit, $c=false) {
	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array('nom_endroit', 'id_article', 'descriptif_endroit') as $champ) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}
	
	include_spip('inc/modifier');
	modifier_contenu ('seances_endroit', $id_endroit, array(
			'nonvide' => array('nom' => _T('info_sans_titre')),
			'invalideur' => "id='id_endroit/$id_endroit'"),
		$c);
}
?>