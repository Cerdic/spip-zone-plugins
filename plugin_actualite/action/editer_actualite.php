<?php

/* 	On trouve ici les fonctions 'action' appelees par le formulaire		*/
/*	'editer_actualite' : insertion d'une nouvelle actualite, revision 		*/
/* 	d'une annonce existante...						*/

if (!defined("_ECRIRE_INC_VERSION")) return;

// Edition ? Revision ?
function action_editer_actualite() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();	
	if ($id_actualite = intval($arg)) { 
		revisions_actualites($id_actualite);
	}

	else if ($arg == 'oui') {
		$id_actualite = insert_actualite();
		if ($id_actualite) revisions_actualites($id_actualite);
	}
	else{
		include_spip('inc/headers');
		redirige_url_ecrire();
	}

	if (_request('redirect')) {
		$redirect = parametre_url(urldecode(_request('redirect')),'id_actualite', $id_actualite, '&');
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else 
		return array($id_actualite,'');

}

function insert_actualite() {
	$id_actualite = sql_insertq("spip_actualites", array('date' => date('Y-m-d')));
	return $id_actualite;
}


function revisions_actualites($id_actualite, $c=false) {

	if ($c === false) {
		$c = array();
		foreach (array('titre', 'statut') as $champ)
			if (($a = _request($champ)) !== null)
				$c[$champ] = $a;
	}
	
	$objet= 'actualites';
	$nom_objet='actualite';
	include_spip('inc/actualites_fonctions');

	$t = sql_getfetsel("statut", "spip_actualites", "id_actualite=$id_actualite");
	if ($t == 'publie') {
		$invalideur = "id='id_actualite/$id_actualite'";
		$indexation = true;
	}
	include_spip('inc/modifier');
	modifier_contenu('actualite', $id_actualite,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur,
			'indexation' => $indexation
		),
		$c);


	$row = sql_fetsel("statut", "spip_actualites", "id_actualite=$id_actualite");
	$statut_ancien = $statut = $row['statut'];
	if (_request('statut', $c) AND _request('statut', $c) != $statut) {
		$statut = $champs['statut'] = _request('statut', $c);
	}

	if (!$champs) {
		$champs = array(
		  'titre' => _request('titre'),
		  'statut'=> _request('statut')
		);
	}

	if (!$champs) return;

	sql_updateq('spip_actualites', $champs, "id_actualite=$id_actualite");
	
	actualites_set_parents($objet,$id_actualite,_request('parents'));
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_actualite/$id_actualite'");

}


?>
