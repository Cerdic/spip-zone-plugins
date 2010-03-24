<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_banniere_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas de banniere ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_banniere = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_banniere = insert_banniere();
	}

	if ($id_banniere) $err = revisions_bannieres($id_banniere);
	return array($id_banniere,$err);
}


function insert_banniere() {
	$champs = array(
		'nom' => _T('bannieres:item_nouveau_banniere')
	);
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_bannieres',
		),
		'data' => $champs
	));
	
	// On enregistre la date de création de la campagne
	$champs['creation'] = date('Y-m-d H:i:s');
	
	$id_banniere = sql_insertq("spip_bannieres", $champs);
	// on fait comme dans le core
		# cf. bannieres_pipelines.php
		# rattrapper les documents associes à cette nouvelle bannière
		# ils ont un id = 0-$id_auteur

		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
		sql_updateq("spip_documents_liens", array("id_objet" => $id_banniere), array("id_objet = ".(0-$id_auteur),"objet='banniere'"));

	return $id_banniere;
}


// Enregistrer certaines modifications d'un banniere
function revisions_bannieres($id_banniere, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array('nom', 'email', 'site', 'alt', 'debut', 'fin', 'commentaires', 'position', 'rayon', 'diffusion') as $champ) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}
	
	include_spip('inc/modifier');
	modifier_contenu('banniere', $id_banniere, array(
			'nonvide' => array('nom' => _T('info_sans_titre')),
			'invalideur' => "id='id_banniere/$id_banniere'"
		),
		$c);
}
?>
