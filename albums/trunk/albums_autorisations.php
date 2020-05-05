<?php
/**
 * Définit les autorisations du plugin Albums
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Autorisations
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function albums_autoriser() {
}


/**
 * Autorisation à afficher l'icone de création rapide.
 *
 * Il faut être autorisé à créer un album.
 *
 * @see autoriser_album_creer_dist()
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_albumcreer_menu_dist($faire, $type, $id, $qui, $opts) {
	return autoriser('creer', 'album', $id, $qui, $opts);
}

/**
 * Autorisation de voir les albums dans le menu d'édition.
 *
 * Il faut être autorisé à administrer les albums.
 *
 * @see autoriser_albums_administrer_dist()
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_albums_menu_dist($faire, $type, $id, $qui, $opts) {
	return autoriser('administrer', 'albumotheque', $id, $qui, $opts);
}

/**
 * Autorisation de créer un album
 *
 * Il faut être admin ou rédacteur.
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_album_creer_dist($faire, $type, $id, $qui, $opts) {
	$autoriser = (in_array($qui['statut'], array('0minirezo', '1comite'))) ? true : false;
	return $autoriser;
}

/**
 * Autorisation à voir les fiches complètes.
 *
 * Open bar pour tout le monde.
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_album_voir_dist($faire, $type, $id, $qui, $opts) {
	return true;
}


/**
 * Autorisation à accéder à l'albumothèque.
 *
 * Il faut être admin ou rédacteur.
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_albumotheque_administrer_dist($faire, $type, $id, $qui, $opts) {
	$autoriser = (in_array($qui['statut'], array('0minirezo', '1comite'))) ? true : false;
	return $autoriser;
}


/**
 * Autorisation à ajouter un album à un objet.
 *
 * Il faut que l'ajout d'albums sur le type d'objet soit activé,
 * et pouvoir modifier l'objet (ou l'éditer en cas de création par un rédacteur,
 * qui correspond au hack id_objet = 0-id_auteur)
 * ou être admin complet.
 *
 * @note
 * Attention, les 2ème et 3ème arguments se réfèrent à l'objet, pas à l'album.
 * Pour le hack de l'identifiant négatif, voir les notes du pipeline «affiche_gauche».
 * Autre hack pénible : des fois l'autorisation à modifier un objet renvoie un faux négatif,
 * c'est la cas dans un appel depuis le pipeline post_insertion, car l'auteur n'a pas encore été lié à l'objet.
 * Dans ce cas il faut contourner le problème en vérifiant si l'objet vient juste d'être créé.
 * cf. autorisation à modifier un album : même combat.
 *
 * TODO
 * Le problème du hack est réglé avec SPIP 3.1, n'y avoir recours que pour les versions antérieures.
 *
 * @example
 *     ```
 *     #AUTORISER{ajouteralbum,#OBJET,#ID_OBJET}
 *     ```
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet auquel on veut ajouter un album
 * @param  int    $id    Identifiant de l'objet auquel on veut ajouter un album
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_ajouteralbum_dist($faire, $type, $id, $qui, $opts) {

	include_spip('inc/config');
	$config = lire_config('albums/objets', array());
	$autoriser = (
		(
			// objet activé
			in_array(table_objet_sql($type), array_filter($config))
		)
		and
		(
			// identifiant positif : cas «normal»
			(
				$id>0
				and
				(
					($qui['statut'] == '0minirezo' and !$qui['restreint'])
					// il faut être autorisé à modifier l'objet...
					or autoriser('modifier', $type, $id, $qui, $opts)
					// ...mais ça donne un faux négatif depuis le pipeline «post_insertion» (cf. note),
					// dans ce cas là on vérifie si l'objet est récent et qu'on a le droit d'écrire.
					or (
						(time()-strtotime(generer_info_entite($id, $type, 'date'))) < (60*1) // age < 1 min, cf. note
						and autoriser('ecrire', $type, $id, $qui, $opts)
					)
				)
			)
			// identifiant négatif : objet nouveau pas encore enregistré en base (cf. note)
			or
			(
				$id<0
				and
				(
					abs($id) == $qui['id_auteur']
					and autoriser('ecrire', $type, $id, $qui, $opts)
				)
			)
		)
	) ? true : false;

	return $autoriser;
}


/**
 * Autorisation à modifier un album.
 *
 * Il faut être l'auteur et avoir le droit de modifier tous les objets auxquels l'album est lié,
 * ou qu'il s'agisse d'un album en cours de création (vide, pas d'auteur et récent, cf. note),
 * ou être admin complet,
 * ou que l'album soit lié à une rubrique wiki/ouverte ou un de ses article
 * et utilisé une seule fois (plugin autorité).
 *
 * @note
 * Hack pénible : quand on ajoute des documents à un nouvel album pas encore enregistré en base,
 * Des liens temporaires sont créés dans la table spip_documents_liens.
 * Ceux-ci seront modifiés une fois l'album inséré en base, via le pipeline «post_insertion» du plugin Médias.
 * Il vérifie avant de procéder qu'il y ait l'autorisation de modifier l'album.
 * Mais lorsque ce pipeline est appelé, aucun auteur n'a encore été lié à l'album.
 * Pour ne pas renvoyer un faux négatif dans ce cas là, on vérifie si l'album
 * vient juste d'être créé selon ces critères : âge, sans auteur et vide.
 * cf. autorisation à ajouter un album : même combat.
 *
 * TODO
 * Le problème du hack est réglé avec SPIP 3.1, n'y avoir recours que pour les versions antérieures.
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_album_modifier_dist($faire, $type, $id, $qui, $opts) {

	$id = intval($id);
	include_spip('action/editer_liens');

	// être un des auteurs de l'album
	$auteurs_album = array();
	if (is_array($liens_auteurs = objet_trouver_liens(array('auteur'=>'*'), array('album'=>$id)))) {
		foreach ($liens_auteurs as $a) {
			$auteurs_album[] = $a['id_auteur'];
		}
	}
	$auteur_album = in_array($qui['id_auteur'], $auteurs_album) ? true : false;

	// droit de modifier tous les objets auxquels l'album est lié
	// l'album peut être lié à un nouvel objet pas encore enregistré en base,
	// dans ce cas id_objet est négatif
	$autoriser_modifier_objets_lies = true;
	$liens_objets = objet_trouver_liens(array('album'=>$id), '*');
	if (is_array($liens_objets) and count($liens_objets)) {
		foreach ($liens_objets as $l) {
			$objet = $l['objet'];
			$id_objet = $l['id_objet'];
			if (($id_objet > 0 and !autoriser('modifier', $objet, $id_objet))
				or ($id_objet < 0 and !autoriser('ecrire', $objet, $id_objet))
			) {
				$autoriser_modifier_objets_lies = false;
				break;
			}
		}
	}

	// cas d'un album tout juste inséré en base (cf. note)
	// pas d'auteur, vide et créé il y a moins de 1 min
	$nouvel_album =
		(
			!count($auteurs_album)
			and !sql_countsel('spip_documents_liens', "objet='album' AND id_objet=".$id) // vide
			and ((time()-strtotime(sql_getfetsel('date', 'spip_albums', 'id_album='.$id))) < (60*1)) // age < 1 min
		) ?
		true : false;

	// les admins complets ont tous les droits !
	$admin_complet = ($qui['statut'] == '0minirezo' and !$qui['restreint']) ?
		true : false;

	// plugin autorité
	// album utilisé une seule fois et situé dans un secteur wiki/ouvert.
	// on vérifie que l'objet auquel il est lié possède un champ `id_secteur`, et qu'on a le droit de publier dedans.
	$album_wiki = false;
	if (defined('_DIR_PLUGIN_AUTORITE')) {
		if (is_array($liens_objets) // album utilisé une seule fois...
			and count($liens_objets) == 1
			// ...et lié à un objet...
			and $objet = $liens_objets[0]['objet']
			and $id_objet = $liens_objets[0]['id_objet']
			// ...qui possède un champ `id_secteur`
			and is_array($infos_objet = lister_tables_objets_sql(table_objet_sql($objet)))
			and
			((
				isset($infos_objet['field']['id_secteur'])
					and $id_secteur = sql_getfetsel('id_secteur', table_objet_sql($objet), id_table_objet($objet).'='.intval($id_objet)))
				or
					(isset($infos_objet['field']['id_rubrique'])
					and $id_rubrique = sql_getfetsel('id_rubrique', table_objet_sql($objet), id_table_objet($objet).'='.intval($id_objet))
					and $id_secteur = sql_getfetsel('id_secteur', 'spip_rubriques', 'id_rubrique='.intval($id_rubrique))
			))
		) {
			// on cherche à savoir si le secteur est wiki/ouvert.
			// faute de fonction générique, on reprend une partie du code de l'autorisation 'rubrique_publierdans'.
			// cf. inc/autoriser.php L291 à 317
			if ((
					$GLOBALS['autorite']['espace_publieur']
					and autorisation_publie_visiteur($qui, $id_secteur)
					and $qui['statut']
				)
				or (
					$GLOBALS['autorite']['espace_wiki']
					and autorisation_wiki_visiteur($qui, $id_secteur)
					and (
						$GLOBALS['autorite']['espace_wiki_rubrique_anonyme']
						or $qui['statut']
					)
				)
			) {
				$album_wiki = true;
			}
		}
	}

	$autoriser = (
		($auteur_album and $autoriser_modifier_objets_lies)
		or $nouvel_album
		or $admin_complet
		or $album_wiki
	) ? true : false;

	return $autoriser;
}


/**
 * Autorisation à supprimer définitivement un album.
 *
 * Il faut qu'il soit vide et inutilisé, et avoir le droit de le modifier ou être admin complet
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_album_supprimer_dist($faire, $type, $id, $qui, $opts) {

	$id = intval($id);
	$statut = sql_getfetsel('statut', 'spip_albums', "id_album=$id");
	$documents = sql_countsel('spip_documents_liens', 'objet='.sql_quote($type)."AND id_objet=$id");
	$liaisons = sql_countsel('spip_albums_liens', "id_album=$id");

	$autoriser = (
		!$liaisons # inutilisé
		and !$documents # vide
		and
		(
			(
				($statut != 'publie') #non publié
				and (autoriser('modifier', $type, $id, $qui)) #auteur ou admin
			)
			or
			(
				$qui['statut'] == '0minirezo' and !$qui['restreint'] #admin complet
			)
		)
	) ? true : false;

	return $autoriser;
}


/**
 * Autorisation à associer un album à un objet donné.
 *
 * Il faut pouvoir modifier l'objet
 * ou être admin complet
 *
 * @note
 * Les infos sur l'objet dont dans les options (5ème paramètre)
 *
 * @example
 *     ```
 *     #AUTORISER{associer,album,#ID_ALBUM,'',#ARRAY{objet,#OBJET,id_objet,#ID_OBJET}}
 *     ```
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet auquel on veut associer ou dissocier un album
 * @param  int    $id    Identifiant de l'objet auquel on veut associer ou dissocier un album
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 *                       Doit contenir les clés `objet` et `id_objet`
 *                       pour rensigner le type et l'identifiant de l'objet
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_album_associer_dist($faire, $type, $id, $qui, $opts) {

	$autoriser = (
		($qui['statut'] == '0minirezo' and !$qui['restreint'])
		or (autoriser('modifier', $opts['objet'], $opts['id_objet'], $qui))
	) ? true : false;

	return $autoriser;
}


/**
 * Autorisation à dissocier un album d'un objet donné.
 *
 * Il faut être autorisé à associer un album à l'objet,
 * et qu'il ne soit pas inséré dans le texte.
 *
 * @note
 * Les infos sur l'objet dont dans les options (5ème paramètre)
 *
 * @example
 *     ```
 *     #AUTORISER{dissocier,album,#ID_ALBUM,'',#ARRAY{objet,#OBJET,id_objet,#ID_OBJET}}
 *     ```
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 *                       Doit contenir les clés `objet` et `id_objet`
 *                       pour renseigner le type et l'identifiant de l'objet
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_album_dissocier_dist($faire, $type, $id, $qui, $opts) {

	$autoriser = (
		autoriser('associer', 'album', $id, $qui, $opts)
		and (sql_getfetsel('vu', 'spip_albums_liens', 'id_album='.intval($id).'
			AND objet='.sql_quote($opts['objet']).'
			AND id_objet='.intval($opts['id_objet']))=='non')
	) ? true : false;

	return $autoriser;
}


/**
 * Auto-association d'albums à du contenu éditorial qui le référence.
 *
 * Par défaut true pour tous les objets.
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_autoassocieralbum_dist($faire, $type, $id, $qui, $opts) {
	return true;
}


/**
 * Autorisation de vider un album
 *
 * Il faut qu'il y ait des documents, et être admin complet
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_album_vider_dist($faire, $type, $id, $qui, $opts) {

	include_spip('action/editer_liens');
	$rempli = count(objet_trouver_liens(array('document'=>'*'), array('album'=>$id))) > 0;
	$admin = $qui['statut']=='0minirezo' and !$qui['restreint'];
	$autoriser = ($admin and $rempli) ? true : false;

	return $autoriser;
}


/**
 * Autorisation de transvaser les documents d'un album
 *
 * Il faut que l'album soit lié à l'objet et
 * avoir le droit de modifier l'album et d'ajouter des documents ou être admin complet.
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 *                       Doit contenir les clés `objet` et `id_objet`
 *                       pour renseigner le type et l'identifiant de l'objet
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_album_transvaser_dist($faire, $type, $id, $qui, $opts) {

	include_spip('action/editer_liens');
	$objet = $opts['objet'];
	$id_objet = $opts['id_objet'];
	$liaison = (count(objet_trouver_liens(array('album'=>$id), array($objet=>$id_objet))) > 0) ? true : false;
	$autoriser_modifier = autoriser('modifier', 'album', $id);
	$autoriser_joindredocument = autoriser('joindredocument', $objet, $id_objet);
	$admin = $qui['statut']=='0minirezo' and !$qui['restreint'];

	$autoriser = (
		$liaison
		and
		(
			(
			$autoriser_modifier
			and $autoriser_joindredocument
			)
			or
			$admin
		)
	) ? true : false;

	return $autoriser;
}
