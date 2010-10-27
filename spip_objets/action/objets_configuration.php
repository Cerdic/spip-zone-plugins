<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_actu_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas de chat ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_actu = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_actu = insert_actu();
	}

	if ($id_actu) $err = revisions_actus($id_actu);
	return array($id_actu,$err);
}


function insert_actu() {
	$champs = array(
		'titre' => _T('actus:item_nouveau_actu')
	);
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_actus',
		),
		'data' => $champs
	));
	
	$id_actu = sql_insertq("spip_actus", $champs);
	return $id_actu;
}


// Enregistrer certaines modifications d'une actu
function revisions_actus($id_actu, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		
		foreach (array('titre', 'descriptif', 'chapo', 'texte') as $champ) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}
	
	include_spip('inc/modifier');
	modifier_contenu('actus', $id_actu, array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => "id='id_actu/$id_actu'"
		),
		$c);
}
?>