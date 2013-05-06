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
		if ($arg == _request('duplicate'))
			$id_seance = duplicate_seance($arg);
		else
			$id_seance = insert_seance();
	}
	if ($id_seance == intval($id_seance))
		$err = revisions_seance($id_seance);
	
	return array($id_seance,$err);
}

// inserer seances
function insert_seance() {
	$champs = array(
		'date_seance' => '0000-00-00 00:00:00'
	);

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_seances',
		),
		'data' => $champs
	));
	
	$id_seance = sql_insertq('spip_seances', $champs);
	return $id_seance;
}

// dupliquer une seance
function duplicate_seance($id_seance){
	// select * pour dupliquer aussi les champs extra eventuels
	$row = sql_fetsel('*', 'spip_seances', 'id_seance = '.$id_seance);
	// pour eviter erreur duplicate sur cle primaire autoincrement
	unset($row['id_seance']); 
	$id_seance = sql_insertq('spip_seances', $row);
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
	include_spip('inc/modifier');
	modifier_contenu ('seance', $id_seance, array(),$c);
}
?>