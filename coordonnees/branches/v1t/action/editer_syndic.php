<?php
/**
 * Plugin Coordonnées
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/

if (!defined("_ECRIRE_INC_VERSION")) return;


// Prend en charge le formulaire de base du core :
// http://doc.spip.org/@action_editer_site_dist
// Mais ne gere ni l'URL automatique ni le logo...
// Raison pour laquelle on ne surcharge pas ;-P
function action_editer_syndic_dist($arg=NULL) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// pas de site ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_syndic = intval($arg)) {
		if (!in_array($arg, array('oui', 'new'))) {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_syndic = insert_site();
	}

	if ($id_syndic)
		$err = revisions_syndic($id_syndic);

	return array($id_syndic, $err);
}


// Referencer un nouveau site
// (en grande partie compatible avec le core
// http://doc.spip.org/@insert_syndic
// mais prend en charge les liaisons !
// tandis que ne prend pas la rubrique en argument
// -- c'est puise dans la requete-- et n'en impose
// pas si absente --cependant sait parfois recuperer.)
function insert_site() {
	$objet = _request('objet');
	$id_objets = intval(_request('id_objet'));
	// Rien avec formulaire editer_syndic car on veut lier les sites a n'importe quel objet et independament des rubriques.
	// Mais avec le formulaire natif editer_site il ne faut pas perdre cette valeur !
	$id_rubrique = intval(_request('id_parent'));
	if (!$id_rubrique) {
		// mais il y a quelques cas particuliers ou il n'est pas mal de rester compatible
		switch ($objet) {
			case 'rubrique':
			case 'rub' :
				$id_rubrique = $id_objets;
				break;
			case 'article':
			case 'art' :
				$id_rubrique = intval(sql_getfetsel('id_rubrique', 'spip_articles', "id_article=$id_objet")); // -1 pour "Page" unique/isolee
				break;
			case 'breve' :
				$id_rubrique = intval(sql_getfetsel('id_rubrique', 'spip_breves', "id_breve=$id_objet"));
				break;
			case 'syndic' :
			case 'site' :
				$id_rubrique = intval(sql_getfetsel('id_rubrique', 'spip_syndic', "id_syndic=$id_objet"));
				break;
			default :
				break;
		}
	}
	// Le secteur a la creation, c'est le secteur de la rubrique (normalement on en a pas, et si la rubrique change mettre a jour.)
	$id_secteur = intval(sql_getfetsel('id_secteur', 'spip_rubriques', "id_rubrique=$id_rubrique"));
	// Avec le formulaire natif editer_site il ne faut pas perdre l'adresse du flux de syndication
	$url_syndic = _request('url_syndic');
	$syndication = _request('syndication');
	if (!$syndication && !$url_syndic)
		$syndication = $GLOBALS['meta']['activer_syndic']; // 'oui'||'non'

	$champs = array(
		'id_rubrique' => $id_rubrique,
		'id_secteur' => $id_secteur,
		'statut' => '0', // on ne PROPose pas un site, on a rattache un qui normalement ne doit pas apparaitre dans la boucle des PUBLIEs... bonus: semble permettre de ne pas le lister dans les sites references ;-p
		'date' => date('Y-m-d H:i:s'), // maintenant
		'syndication' => $syndication,
		'url_syndic' => $url_syndic,
		'moderation' => $GLOBALS['meta']['moderation'], // 'oui'||'non'
		'resume' => $GLOBALS['meta']['resume'], // 'oui'||'non'
		'nom_site' => _request('nom_site'), // pas pris en compte si pas dans la liste ?!?
		'url_site' => _request('url_site'), // pas pris en compte si pas dans la liste ?!?
		'descriptif' => _request('descriptif'), // pas pris en compte si pas dans la liste ?!?
	);

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_syndic',
		),
		'data' => $champs
	));

	// Ajouter les champs
	$id_syndic = sql_insertq('spip_syndic', $champs);

	// Renvoyer aux plugins
	pipeline('post_insertion', array(
		'args' => array(
			'table' => 'spip_syndic',
		),
		'data' => $champs
	));

	// Ajouter la liaison si presente
	if ($objet AND $id_objet) {
		sql_insertq('spip_syndic_liens', array(
			'id_syndic' => $id_syndic,
			'objet' => $objet,
			'id_objet' => $id_objet,
			'type' => _request('type'),
		));
	}

	return $id_syndic;
}


// Enregistrer certaines modifications d'un site
// (grandement compatible avec le core
// http://doc.spip.org/@revisions_sites
// mais prend en charge les liaisons !)
function revisions_syndic($id_syndic, $c=FALSE) {

	// Le gros du boulot est fait par le plugin "Sites" ; mais l'appel :
	// revisions_sites($id_syndic); // se vautre... (page blanche !)

	// Recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === FALSE) {
		$c = array();
		foreach (array(
			'nom_site', 'url_site', 'descriptif', 'url_syndic', 'syndication', 'statut', 'id_parent'
		) as $champ) {
			if (($a = _request($champ)) !== NULL) {
				$c[$champ] = $a;
			}
		}
	}

	// Si le site est publie, invalider les caches et demander sa reindexation
	$t = sql_getfetsel('statut', 'spip_syndic', "id_syndic=$id_syndic");
	if ($t == 'publie') {
		$invalideur = "id='id_syndic/$id_syndic'";
		$indexation = true;
	}

	// on enregistre les modifications de base transmises
	include_spip('inc/modifier');
	modifier_contenu('syndic', $id_syndic,
		array(
			'nonvide' => array('nom_site' => _T('info_sans_titre')),
			'invalideur' => $invalideur,
			'indexation' => $indexation,
		),
		$c);

	// Ces champs donnent lieu a des traitements particuliers s'ils changent.
	$row = sql_fetsel('statut, id_rubrique, id_secteur', 'spip_syndic', "id_syndic=$id_syndic");

	// changer de statut ?
	if ($c['statut'] AND $c['statut']!=$row['statut'] AND autoriser('publierdans','rubrique',$row['id_rubrique'])) {
		$champs['statut'] = $c['statut'];
		if ($c['statut']=='publie') {
			// modifier la date de publication ?
			if ($d = _request('date', $c)) {
				$champs['date'] = $d;
			} else {
				$champs['date'] = date('Y-m-d H:i:s');
			}
		}
	} else {
		// le nouveau statut est l'ancien
		$c['statut'] = $row['statut'];
	}

	// Changer de rubrique ?
	if ($id_parent = intval(_request('id_parent', $c)) AND $id_parent!=$row['id_rubrique'] AND ($id_secteur = sql_getfetsel('id_secteur', 'spip_rubriques', "id_rubrique=$id_parent"))) {
		$champs['id_rubrique'] = $id_parent;
		// Si la rubrique demandee existe et est differente de l'actuelle, recuperer son secteur
		if ($row['id_secteur']!=$id_secteur)
			$champs['id_secteur'] = $id_secteur;
		// Si le site est publie et que le demandeur n'est pas admin de la
		// rubrique repasser le site en statut 'prop'.
		if ($row['statut']=='publie') {
			if (!autoriser('publierdans','rubrique',$id_parent))
				$champs['statut'] = $row['statut'] = 'prop';
		}
	}

	// En cas de changement de statut ou de rubrique donc
	// (c'est pour la compatibilite avec editer_site ve que editer_syndic ne les prend pas en compte)
	if ($champs) {
		// Enregistrer ces modifications
		sql_updateq('spip_syndic', $champs, "id_syndic=$id_syndic");
		// Invalider les caches
		if ($row['statut']=='publie') {
			include_spip('inc/invalideur');
			suivre_invalideur("id='id_syndic/$id_syndic'");
		}
		// Notifications
		if ($notifications = charger_fonction('notifications', 'inc')) {
			$notifications('instituersite', $id_syndic, array(
				'statut' => $c['statut'],
				'statut_ancien' => $row['statut'],
				'date' => ($champs['date']?$champs['date']:$row['date']),
			));
		}
		// Actualiser l'etat de la rubrique
		include_spip('inc/rubriques');
		calculer_rubriques_if($row['id_rubrique'], $champs, $row['statut']);
	}
	// On prend en compte le changement du typage de la liaison
	sql_update("spip_syndic_liens", array(
			'type'=>sql_quote(_request('type'))
		), "id_syndic=".intval($id_syndic)." AND id_objet=".intval(_request('id_objet'))." AND objet=".sql_quote(_request('objet')) );
}

?>