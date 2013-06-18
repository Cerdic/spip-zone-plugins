<?php
/**
 * Fonctions du plugin Albums
 *
 * @plugin     Albums
 * @copyright  2013
 * @author     Romy Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Fonctions
**/

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * critère {orphelins}
 * selectionne les albums sans lien avec un objet editorial
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_ALBUMS_orphelins_dist($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];
	$cond = $crit->cond;
	$not = $crit->not?"":"NOT";

	$select = sql_get_select("DISTINCT id_album","spip_albums_liens as oooo");
	$where = "'" .$boucle->id_table.".id_album $not IN ($select)'";
	if ($cond){
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
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_ALBUMS_contenu_dist($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];
	$cond = $crit->cond;
	$not = $crit->not ? "NOT" : "";
	// par defaut, parametre adjacent au critere, sinon parametre present dans l environnement
	if (isset($crit->param[0]))
		$_media = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	else
		$_media = $_env = '@$Pile[0]["contenu"]';

	$where = "'" .$boucle->id_table.".id_album $not IN ('.albums_calculer_critere_contenu_select($_media).')'";
	if ($cond)
		$where = "($_env) ? $where : ''";

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
function albums_calculer_critere_contenu_select ($media='') {

	// albums contenant un type de media en particulier
	if ($media AND preg_match('#image|audio|video|file#', $media)) {
		$select = sql_get_select(
			"DISTINCT(id_album)",
			array(
				"spip_albums AS albums",
				"spip_documents AS docs",
				"spip_documents_liens AS liens",
			),
			array(
				"liens.objet = 'album'",
				"liens.id_objet = albums.id_album",
				"docs.id_document = liens.id_document",
				"docs.media REGEXP " . sql_quote($media)
			)
		);
	// albums pleins ou vides
	} else if (!$media OR in_array($media, array('oui','non'))) {
		// albums pleins : contenant au moins un document
		$select_pleins = sql_get_select(
			"DISTINCT liens.id_objet AS id_album",
			"spip_documents_liens AS liens",
			"liens.objet = 'album'"
		);
		if (!$media OR ($media == 'oui')) {
			$select = $select_pleins;
		}
		// albums vides
		if ($media == 'non') {
			$select = sql_get_select(
				"DISTINCT(id_album)",
				"spip_albums AS albums",
				"id_album NOT IN ($select_pleins)"
			);
		}
	}

	return $select;
}


/**
 * Fonction privée generant un tableau qui contient les types de medias présents dans un album, et leur nombre
 * exemple: array(file=>5, image=>2, ...)
 *
 * @param string $id_album	identifiant de l'album
 * @param boolean $grouper	grouper les types de media ?
 * @return array		tableau des types de medias contenus
 * 				array ( media => nombre, ... )
 */
function album_determiner_contenu($id_album) {
	if (intval($id_album)){

		// selection des medias (sans doublons) contenus dans les documents lies a l album
		// select from where groupby orderby limit having
		$res_medias = sql_select(
			array(
				"docs.media AS media",
				"COUNT(*) AS nombre"
			),
			array(
				"spip_documents AS docs", 
				"spip_documents_liens AS lien", 
				"spip_albums AS albums"
			),
			array(
				"docs.id_document = lien.id_document",
				"lien.objet = 'album'",
				"albums.id_album = lien.id_objet", 
				"albums.id_album = $id_album"
			),
			"media", 
			"media DESC"
		);

		// tableau
		while ($row = sql_fetch($res_medias)) {
			$medias[$row['media']] = $row['nombre']; // (file => 5, image => 2, ...)
		}
	}

	return $medias;
}


/**
 * filtre |album_contenu
 * Renvoie des infos sur les types de medias contenus dans un album (image, audio, video, file).
 * Simplifie la vie pour l ecriture des squelettes.
 * On peut avoir :
 * 	true ou false pour savoir si l'album est vide ou pas
 * 	une liste simple des types de media
 * 	une liste detaillee (icone + nombre pour chaque type de media)
 * 	les icones des types de media
 * 	le nombre total de documents
 * 	une qualification du contenu pour choisir le fichier du logo (vide, mixte, ou type de media)
 *
 * @param string $id_album	identifiant de l'album
 * @param string $info		'' / liste / liste_detaillee / icones / nombre / logo
 * @param string $format	'' / tableau' : format du retour
 * @return string/array/boolean
 */
function filtre_album_contenu($id_album, $info='', $format='') {
	if (intval($id_album)){

		$medias = array();
		$medias = album_determiner_contenu($id_album); // renvoit array(media=>nb, ...)

		switch ($info){

			// qualification de l album en fonction des medias (vide, mixte ou type de media)
			case 'logo';
				$nb_types = count($medias);
				echo $medias[0];
				if ($nb_types == 0)
					$retour = 'vide';
				if ($nb_types == 1)
					$retour = key($medias);
				/*if ($nb_types > 1)
					$retour = 'mixte';*/
				break;

			// liste des medias
			case 'liste';
				if (!empty($medias)) {
					if ($format=='tableau') {
						$retour = array_keys($medias);
					} else {
						$retour = '<ul>';
						foreach ($medias AS $media=>$nombre) {
							$retour .= '<li>'
								. _T('medias:media_'.$media)
								. '</li>';
						}
						$retour .= '</ul>';
					}
				}
				break;

			// liste des medias
			case 'liste_detaillee';
				if (!empty($medias)) {
					if ($format == 'tableau') {
						$retour = $medias;
					} else {
						$balise_img = charger_filtre('balise_img');
						include_spip('inc/filtres');
						$retour = '<ul>';
						foreach ($medias AS $media=>$nombre) {
							$retour .= '<li>'
								. $balise_img(chemin_image('media-'. $media . '-16.png'))
								. '&nbsp;'. singulier_ou_pluriel($nombre, 'medias:un_'.$media, 'medias:des_'.$media.'s') 
								. '</li>';
						}
						$retour .= '</ul>';
					}
				}
				break;

			// nombre des medias
			case 'nombre';
				if (!empty($medias)) {
					$nb = '';
					foreach ($medias AS $media=>$nombre) {
						$nb += $nombre;
					}
					$retour = $nb;
				}
				break;

			// liste des medias
			case 'icones';
				if (!empty($medias)) {
					$balise_img = charger_filtre('balise_img');
					include_spip('inc/filtres');
					$retour = '<ul>';
					foreach ($medias AS $media=>$nombre) {
						$retour .= inserer_attribut($balise_img(chemin_image('media-'. $media . '-24.png')),'title', _T('medias:media_'.$media));
					}
					$retour .= '</ul>';
				}
				break;

			// vrai ou faux
			default;
				if (empty($medias))
					$retour = false;
				else
					$retour = true;
				break;
				

		}

	}

	return $retour;
}


/**
 * filtre |album_liaison
 * Renvoie des infos sur les objet lies a un album.
 * Simplifie la vie pour l ecriture des squelettes.
 * On peut avoir :
 * 	une liste : id-objet, objet (+icone)
 * 	une liste detaillee: id-objet, objet, titre (+icone)
 * 	une liste compacte: regroupement par objets (+icone)
 * 	le nombre total d objets lies
 *
 * @param string $id_album		identifiant de l'album
 * @param string $format		liste / liste_detaillee / liste_compacte / nombre
 * @param string $afficher_icone	si 'icone', affiche les icones des objets
 * @return string
 */
function filtre_album_liaison($id_album, $format='liste', $afficher_icone='') {

	include_spip('action/editer_liens');
	$balise_img = charger_filtre('balise_img');

	if ($res= objet_trouver_liens(array('album'=>intval($id_album)),array('*'=>'*'))) {
		while ($row = array_shift($res)) {
			$liste[] = array($row['objet'], $row['id_objet'], $row['vu']);
		}

		// liste simple
		if ($format == 'liste' OR $format == 'liste_detaillee') {
			$retour = "<ul>";
			foreach ($liste as $k=>$v) {
				$objet = $v[0];
				$id_objet = $v[1];
				$icone = ($afficher_icone == "icone") ? objet_icone($objet, 16) . '&nbsp;' : '';
				$vu = ($v[2] == 'oui') ? $balise_img(chemin_image('vu-16-10.png')) . '&nbsp;' : '';
				$titre = ($format == 'liste_detaillee') ? '&nbsp;<span class=\'titre\'>' .generer_info_entite($id_objet,$objet,'titre') . '</span>' : '';
				$retour .= "<li>"
					. "<a href='".generer_url_entite($id_objet, $objet)."'>"
					. $icone
					. $vu
					. $objet
					. "&nbsp;n°"
					. $id_objet
					. "</a>"
					. $titre
					. "<li>";
			};
			$retour .= "</ul>";
		}
		// liste compacte
		else if ($format == 'liste_compacte') {
			foreach ($liste as $k){
				$objets[] = $k[0];
			}
			$retour = "<ul>";
			foreach (array_count_values($objets) as $k=>$v) {
				$icone = ($afficher_icone == "icone") ? objet_icone($k, 16) . '&nbsp;' : '';
				$retour .= "<li>"
					. $icone
					. singulier_ou_pluriel($v, objet_info($k,'info_1_objet'), objet_info($k, 'info_nb_objets'))
					. "</li>";
			}
			$retour .= "</ul>";
		}
		// nombre
		else if ($format == 'nombre') {
			$retour = count($liste) . " objets";
		}
	}

	return $retour;
}


?>
