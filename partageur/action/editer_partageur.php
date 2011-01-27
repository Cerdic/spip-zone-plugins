<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_partageur_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas de partageur ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_partageur = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_partageur = insert_partageur();
	}

	if ($id_partageur) $err = revisions_partageurs($id_partageur);
	return array($id_partageur,$err);
}


function insert_partageur() {
	$champs = array(
		'titre' => _T('partageur:item_nouveau_partageur')
	);
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_partageurs',
		),
		'data' => $champs
	));
	
	$id_partageur = sql_insertq("spip_partageurs", $champs);
	return $id_partageur;
}


// Enregistrer certaines modifications d'un partageur
function revisions_partageurs($id_partageur, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array('titre', 'url_site') as $champ) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}
	
	include_spip('inc/modifier');
	modifier_contenu('partageur', $id_partageur, array(
			'nonvide' => array('nom' => _T('info_sans_titre')),
			'invalideur' => "id='id_partageur/$id_partageur'"
		),
		$c);
}
?>
