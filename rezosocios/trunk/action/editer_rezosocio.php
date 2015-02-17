<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

// Editer (modification) d'un rezosocio-cle
// http://doc.spip.org/@action_editer_rezosocio_dist
function action_editer_rezosocio_dist($arg=null)
{
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$id_rezosocio = intval($arg);

	if (!$id_rezosocio) {
		$id_rezosocio = rezosocio_inserer();
	}

	// Enregistre l'envoi dans la BD
	if ($id_rezosocio > 0) $err = rezosocio_modifier($id_rezosocio);
	
	return array($id_rezosocio,$err);
}

/**
 * Insertion d'un rezosocio
 * @param int $id_groupe
 * @return int
 */
function rezosocio_inserer() {

	$champs = array();
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_rezosocios',
			),
			'data' => $champs
		)
	);

	$champs['date'] = date('Y-m-d H:i:s');
	$id_rezosocio = sql_insertq("spip_rezosocios", $champs);

	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_rezosocios',
				'id_objet' => $id_rezosocio
			),
			'data' => $champs
		)
	);

	return $id_rezosocio;
}

/**
 * Modifier un rezosocio
 * @param int $id_rezosocio
 * @param array $set
 * @return string
 */
function rezosocio_modifier($id_rezosocio, $set=null) {
	include_spip('inc/modifier');
	$c = collecter_requests(
		// white list
		array(
		 'titre', 'type_rezo', 'url_site','nom_compte', 'changer_lang'
		),
		// black list
		array('changer_lang'),
		// donnees eventuellement fournies
		$set
	);
	
	if(isset($c['changer_lang'])){
		$c['lang'] = $c['changer_lang'];
		unset($c['changer_lang']);
	}
	if ($err = objet_modifier_champs('rezosocio', $id_rezosocio,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre'))
		),
		$c))
		return $err;

	$c = array();
	$err = rezosocio_instituer($id_rezosocio, $c);
	return $err;
}

/**
 * Modifier le groupe parent d'un rezosocio
 * @param  $id_rezosocio
 * @param  $c
 * @return void
 */
function rezosocio_instituer($id_rezosocio, $c){
	$row = sql_fetsel("statut, date", "spip_rezosocios", "id_rezosocio=".intval($id_rezosocio));

	$statut_ancien = $statut = $row['statut'];
	$date_ancienne = $date = $row['date'];
	
	$champs = array();
	
	$d = isset($c['date'])?$c['date']:null;
	$s = isset($c['statut'])?$c['statut']:$statut;

	if ($s != $statut OR ($d AND $d != $date)) {
		if (autoriser('creer', 'rezosocio'))
			$statut = $champs['statut'] = $s;
		else if (autoriser('modifier', 'rezosocio', $id_rezosocio) AND $s != 'publie')
			$statut = $champs['statut'] = $s;

		// En cas de publication, fixer la date a "maintenant"
		// sauf si $c commande autre chose
		// ou si l'article est deja date dans le futur
		// En cas de proposition d'un article (mais pas depublication), idem
		if ($champs['statut'] == 'publie'
		 OR ($champs['statut'] == 'prop' AND ($d OR !in_array($statut_ancien, array('publie', 'prop'))))
		) {
			if ($d OR strtotime($d=$date)>time())
				$champs['date'] = $date = $d;
			else
				$champs['date'] = $date = date('Y-m-d H:i:s');
		}
	}

	// Envoyer aux plugins
	$champs = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_rezosocios',
				'id_objet' => $id_rezosocio,
				'action'=>'instituer',
			),
			'data' => $champs
		)
	);

	if (!$champs) return;

	sql_updateq('spip_rezosocios', $champs, "id_rezosocio=".intval($id_rezosocio));

	//
	// Post-modifications
	//

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='rezosocio/$id_rezosocio'");

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_rezosocios',
				'id_objet' => $id_rezosocio,
				'action'=>'instituer',
			),
			'data' => $champs
		)
	);

	// Notifications
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('instituerrezosocio', $id_rezosocio,
			array('id_groupe' => $champs['id_groupe'])
		);
	}

	return ''; // pas d'erreur
}

/**
 * Supprimer un rezosocio
 * @param int $id_rezosocio
 * @return void
 */
function rezosocio_supprimer($id_rezosocio) {
	sql_delete("spip_rezosocios", "id_rezosocio=".intval($id_rezosocio));
	rezosocio_dissocier($id_rezosocio, '*');
	pipeline('trig_supprimer_objets_lies',
		array(
			array('type'=>'rezosocio','id'=>$id_rezosocio)
		)
	);
}

/**
 * Associer un rezosocio a des objets listes sous forme
 * array($objet=>$id_objets,...)
 * $id_objets peut lui meme etre un scalaire ou un tableau pour une liste d'objets du meme type
 *
 * on peut passer optionnellement une qualification du (des) lien(s) qui sera
 * alors appliquee dans la foulee.
 * En cas de lot de liens, c'est la meme qualification qui est appliquee a tous
 *
 * Exemples:
 * rezosocio_associer(3, array('auteur'=>2));
 * rezosocio_associer(3, array('auteur'=>2), array('vu'=>'oui)); // ne fonctionnera pas ici car pas de champ 'vu' sur spip_rezosocios_liens
 * 
 * @param int $id_rezosocio
 * @param array $objets
 * @param array $qualif
 * @return string
 */
function rezosocio_associer($id_rezosocio,$objets, $qualif = null){
	include_spip('action/editer_liens');
	return objet_associer(array('rezosocio'=>$id_rezosocio), $objets, $qualif);
}

/**
 * Dossocier un rezosocio des objets listes sous forme
 * array($objet=>$id_objets,...)
 * $id_objets peut lui meme etre un scalaire ou un tableau pour une liste d'objets du meme type
 *
 * un * pour $id_rezosocio,$objet,$id_objet permet de traiter par lot
 *
 * @param int $id_rezosocio
 * @param array $objets
 * @return string
 */
function rezosocio_dissocier($id_rezosocio,$objets){
	include_spip('action/editer_liens');
	return objet_dissocier(array('rezosocio'=>$id_rezosocio), $objets);
}

/**
 * Qualifier le lien d'un rezosocio avec les objets listes
 * array($objet=>$id_objets,...)
 * $id_objets peut lui meme etre un scalaire ou un tableau pour une liste d'objets du meme type
 * exemple :
 * $c = array('vu'=>'oui');
 * un * pour $id_auteur,$objet,$id_objet permet de traiter par lot
 *
 * @param int $id_rezosocio
 * @param array $objets
 * @param array $qualif
 */
function rezosocio_qualifier($id_rezosocio,$objets,$qualif){
	include_spip('action/editer_liens');
	return objet_qualifier(array('rezosocio'=>$id_rezosocio), $objets, $qualif);
}

?>