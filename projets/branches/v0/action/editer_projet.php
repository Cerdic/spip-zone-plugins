<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@action_editer_article_dist
function action_editer_projets_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// si id_article n'est pas un nombre, c'est une creation
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if (!$id_projet = intval($arg)) {
		$id_parent = _request('id_parent');
		if (!$id_parent){
			$id_parent = 0;
		}
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
		if (!($id_auteur)) {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_projet = insert_projet($id_parent);
	}

	// Enregistre l'envoi dans la BD
	if ($id_projet > 0) $err = projet_set($id_projet);

	if (_request('redirect')) {
		$redirect = parametre_url(urldecode(_request('redirect')),
			'id_projet', $id_projet, '&') . $err;

		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else
		return array($id_projet,$err);
}

// Appelle toutes les fonctions de modification d'un projet
// $err est de la forme '&trad_err=1'
// http://doc.spip.org/@articles_set
function projet_set($id_projet) {
	$err = '';

	// unifier $texte en cas de texte trop long
	trop_longs_articles();

	$c = array();
	foreach (array(
		'titre','descriptif','chapo', 'texte'
	) as $champ)
		$c[$champ] = _request($champ);

	include_spip('inc/modifier');
	revision_projet($id_projet, $c);

	// Modification de statut, changement de rubrique ?
	$c = array();
	foreach (array(
		'date', 'statut', 'id_parent'
	) as $champ)
		$c[$champ] = _request($champ);
	$err .= instituer_projet($id_projet, $c);

	return $err;
}

// http://doc.spip.org/@insert_article
function insert_projet($id_parent) {

	// La langue a la creation : si les liens de traduction sont autorises
	// dans les rubriques, on essaie avec la langue de l'auteur,
	// ou a defaut celle de la rubrique
	// Sinon c'est la langue de la rubrique qui est choisie + heritee


	$id_projet = sql_insertq("spip_projets", array(
		'id_parent' => $id_parent,
		'statut' =>  'prepa',
		'date' => date('Y-m-d H:i:s')
	));

	if ($id_projet > 0)
		sql_insertq('spip_projets_liens', array('id_projet'=>$id_projet, 'objet' => 'auteur', 'id_objet' => $GLOBALS['visiteur_session']['id_auteur'], 'type' => 'admin'));
	return $id_projet;
}


// $c est un array ('statut', 'id_parent' = changement de projet parent)
//
// statut et rubrique sont lies, car un admin restreint peut deplacer
// un article publie vers une rubrique qu'il n'administre pas
// http://doc.spip.org/@instituer_article
function instituer_projet($id_projet, $c, $calcul_rub=true) {

	include_spip('inc/autoriser');
	include_spip('inc/modifier');

	$row = sql_fetsel("statut, date, id_parent", "spip_projets", "id_projet=$id_projet");
	$id_parent = $row['id_parent'];
	$statut_ancien = $statut = $row['statut'];
	$date_ancienne = $date = $row['date'];
	$champs = array();

	$d = isset($c['date'])?$c['date']:null;
	$s = isset($c['statut'])?$c['statut']:$statut;

	// cf autorisations dans inc/instituer_article
	if ($s != $statut OR ($d AND $d != $date)) {
		if (autoriser('creer', 'projet'))
			$statut = $champs['statut'] = $s;
		else if (autoriser('modifier', 'article', $id_article) AND $s != 'publie')
			$statut = $champs['statut'] = $s;
		else
			spip_log("editer_article $id_article refus " . join(' ', $c));

		// En cas de publication, fixer la date a "maintenant"
		// sauf si $c commande autre chose
		// ou si l'article est deja date dans le futur
		// En cas de proposition d'un article (mais pas depublication), idem
		if ($champs['statut'] == 'publie'
		 OR ($champs['statut'] == 'prop' AND !in_array($statut_ancien, array('publie', 'prop')))
		) {
			if ($d OR strtotime($d=$date)>time())
				$champs['date'] = $date = $d;
			else
				$champs['date'] = $date = date('Y-m-d H:i:s');
		}
	}

	// Verifier que la rubrique demandee existe et est differente
	// de la rubrique actuelle
	if ($id_parent = $c['id_parent']
	AND (sql_fetsel('1', "spip_projets", "id_projet=$id_parent"))) {
		$champs['id_parent'] = $id_parent;

		// si l'article etait publie
		// et que le demandeur n'est pas admin de la rubrique
		// repasser l'article en statut 'propose'.
		if ($statut == 'publie'
		AND !autoriser('modifier', 'projet'))
			$champs['statut'] = 'prop';
	}


	// Envoyer aux plugins
	$champs = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_projets',
				'id_objet' => $id_projet,
				'action'=>'instituer'
			),
			'data' => $champs
		)
	);

	if (!count($champs)) return;

	// Envoyer les modifs.

	sql_updateq('spip_projets', $champs, "id_projet=$id_projet");

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_projets/$id_projet'");

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_projets',
				'id_objet' => $id_projet
			),
			'data' => $champs
		)
	);

	// Notifications
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('instituerprojet', $id_projet,
			array('statut' => $statut, 'statut_ancien' => $statut_ancien)
		);
	}

	return ''; // pas d'erreur
}

//
// Reunit les textes decoupes parce que trop longs
//

// http://doc.spip.org/@trop_longs_articles
function trop_longs_articles() {
	if (is_array($plus = _request('texte_plus'))) {
		foreach ($plus as $n=>$t) {
			$plus[$n] = preg_replace(",<!--SPIP-->[\n\r]*,","", $t);
		}
		set_request('texte', join('',$plus) . _request('texte'));
	}
}

?>