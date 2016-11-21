<?php
/**
 * Fonctions du plugin Albums
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * critère `{orphelins}`
 *
 * Sélectionne les albums sans lien avec un objet éditorial
 *
 * @critere
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_ALBUMS_orphelins_dist($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];
	$cond = $crit->cond;
	$not = $crit->not ? '' : 'NOT';

	$select = sql_get_select('DISTINCT id_album', 'spip_albums_liens as oooo');
	$where = "'" .$boucle->id_table.".id_album $not IN (SELECT * FROM($select) AS subquery)'";
	if ($cond) {
		$_quoi = '@$Pile[0]["orphelins"]';
		$where = "($_quoi) ? $where : ''";
	}

	$boucle->where[]= $where;
}


/**
 * critère {contenu}
 * sélectionne les albums en fonction de leur contenu (image, audio, file, video)
 * 	{contenu} -> albums remplis
 * 	{!contenu} -> albums vides
 * 	{contenu xxx} -> albums contenant des xxx : medias sous forme de regexp
 * 	en fonction de la valeur de *contenu* dans l environnement :
 * 	oui : albums remplis
 * 	non : albums vides
 * 	xxx -> albums contenant des xxx : medias sous forme de regexp
 *
 * @deprecated : utiliser plutot <BOUCLE_album(ALBUMS documents){documents.media=image}>
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_ALBUMS_contenu_dist($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];
	$cond = $crit->cond;
	$not = $crit->not ? 'NOT' : '';
	// par defaut, parametre adjacent au critere, sinon parametre present dans l environnement
	if (isset($crit->param[0])) {
		$_media = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	} else {
		$_media = $_env = '@$Pile[0]["contenu"]';
	}

	$where = "'" .$boucle->id_table.".id_album $not IN ('.albums_calculer_critere_contenu_select($_media).')'";
	if ($cond) {
		$where = "($_env) ? $where : ''";
	}

	$boucle->where[]= $where;
}


/**
 * fonction privée pour le calcul du critère {contenu}
 * renvoie un sql select en fonction des documents liés au albums
 *
 * note : la selection des albums vides (avec contenu=non) fait une requete a rallonge... a revoir
 *
 * @param string $media		types de medias contenus dans les albums, separes par des virgules
 * @return string		select
 */
function albums_calculer_critere_contenu_select($media = '') {

	// albums contenant un type de media en particulier
	if ($media and preg_match('#image|audio|video|file#', $media)) {
		$select = sql_get_select(
			'DISTINCT(id_album)',
			array(
				'spip_albums AS albums',
				'spip_documents AS docs',
				'spip_documents_liens AS liens',
			),
			array(
				"liens.objet = 'album'",
				'liens.id_objet = albums.id_album',
				'docs.id_document = liens.id_document',
				'docs.media REGEXP ' . sql_quote($media)
			)
		);
	// albums pleins ou vides
	} elseif (!$media or in_array($media, array('oui','non'))) {
		// albums pleins : contenant au moins un document
		$select_pleins = sql_get_select(
			'DISTINCT liens.id_objet AS id_album',
			'spip_documents_liens AS liens',
			"liens.objet = 'album'"
		);
		if (!$media or ($media == 'oui')) {
			$select = $select_pleins;
		}
		// albums vides
		if ($media == 'non') {
			$select = sql_get_select(
				'DISTINCT(id_album)',
				'spip_albums AS albums',
				"id_album NOT IN ($select_pleins)"
			);
		}
	}

	return "SELECT * FROM($select) AS subquery";
}
