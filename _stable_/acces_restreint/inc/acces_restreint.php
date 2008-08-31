<?php

// * Acces restreint, plugin pour SPIP * //

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/vieilles_defs');

// Liste des zones a laquelle appartient le visiteur, au format '1,2,3'
// Cette fonction est appelee a chaque hit et peut etre completee (pipeline)
function AccesRestreint_liste_zones_autorisees($zones='', $id_auteur=NULL) {
	$id = NULL;
	if (!is_null($id_auteur))
		$id = $id_auteur;
	elseif (isset($GLOBALS['auteur_session']['id_auteur']) && $GLOBALS['auteur_session']['id_auteur'])
		$id = $GLOBALS['auteur_session']['id_auteur'];
	if (!is_null($id)) {
		$new = AccesRestreint_liste_zones_appartenance_auteur($id);
		if ($zones AND $new) {
			$zones = array_unique(array_merge(split(',',$zones),$new));
			sort($zones);
			$zones = join(',', $zones);
		} else if ($new) {
			$zones = join(',', $new);
		}
	}
	return $zones;
}

// liste des rubriques contenues dans une zone, directement
// pour savoir quelles rubriques on peut decocher
// si id_zone = 0 : toutes les rub en acces restreint
function AccesRestreint_liste_contenu_zone_rub_direct($id_zone){
	$liste_rubriques=array();
	// liste des rubriques directement liees a la zone
	$query = "SELECT zr.id_rubrique FROM spip_zones_rubriques AS zr INNER JOIN spip_zones AS z ON zr.id_zone=z.id_zone";
	if (is_numeric($id_zone))
		$query.=" WHERE zr.id_zone=".$id_zone;
	else
		$query .= " WHERE $id_zone";
	$s = spip_query($query);
	while ($row = spip_fetch_array($s))
		$liste_rubriques[$row['id_rubrique']]=1;
	return array_keys($liste_rubriques);
}

// liste des rubriques contenues dans une zone, directement ou par heritage
function AccesRestreint_liste_contenu_zone_rub($id_zone){
	include_spip('inc/rubriques');
	if ($rubs = AccesRestreint_liste_contenu_zone_rub_direct($id_zone))
		return explode(',', calcul_branche(join(',', $rubs)));
	else
		return array();
}

// liste des zones a laquelle appartient un auteur
function AccesRestreint_liste_zones_appartenance_auteur($id_auteur){
	$liste_zones=array();
	$s = spip_query("SELECT id_zone FROM spip_zones_auteurs WHERE id_auteur="._q($id_auteur));
	while ($row = spip_fetch_array($s))
			$liste_zones[]=$row['id_zone'];
	return $liste_zones;
}

// test si un auteur appartient a une zone
function AccesRestreint_test_appartenance_zone_auteur($id_zone,$id_auteur){
	$s = spip_query("SELECT id_zone FROM spip_zones_auteurs WHERE id_auteur="._q($id_auteur)." AND id_zone="._q($id_zone));
	if ($row = spip_fetch_array($s))
		return true;
	return false;
}

// liste des auteurs contenus dans une zone
function AccesRestreint_liste_contenu_zone_auteur($id_zone) {
	$liste_auteurs=array();
	$id_zone = intval($id_zone);
	// liste des rubriques directement liees a la zone
	$s = spip_query("SELECT id_auteur FROM spip_zones_auteurs WHERE id_zone=$id_zone");
	while ($row=spip_fetch_array($s))
		$liste_auteurs[] = $row['id_auteur'];
	return $liste_auteurs;
}

// fonctions de filtrage rubrique
// plus performant a priori : liste des rubriques exclues uniquement
// -> condition NOT IN
// Cette fonction renvoie la liste des rubriques interdites
// au visiteur courant
// d'ou le recours a $GLOBALS['AccesRestreint_zones_autorisees']
function AccesRestreint_liste_rubriques_exclues($publique=true, $id_auteur=NULL) {
	// cache static
	static $liste_rub_exclues = array();
	if (!isset($liste_rub_exclues[$publique]) || !is_array($liste_rub_exclues[$publique])) {

		// Ne selectionner que les zones pertinentes
		if ($publique)
			$cond = "publique='oui'";
		else
			$cond = "privee='oui'";

		// Si le visiteur est autorise sur certaines zones publiques,
		// on selectionne les rubriques correspondant aux autres zones,
		// sinon on selectionne toutes celles correspondant � une zone.
		if (is_null($id_auteur) AND $GLOBALS['AccesRestreint_zones_autorisees'])
			$cond .= " AND zr.id_zone NOT IN (".$GLOBALS['AccesRestreint_zones_autorisees'].")";
		elseif (!is_null($id_auteur))
			if ($zones_autorisees=AccesRestreint_liste_zones_autorisees('',$id_auteur))
				$cond .= " AND zr.id_zone NOT IN (".$zones_autorisees.")";

		$liste_rub_exclues[$publique] = AccesRestreint_liste_contenu_zone_rub($cond);
		$liste_rub_exclues[$publique] = array_unique($liste_rub_exclues[$publique]);
	}
	return $liste_rub_exclues[$publique];
}


function AccesRestreint_rubriques_accessibles_where($primary){
	include_spip('base/abstract_sql');
	$liste = AccesRestreint_liste_rubriques_exclues(_DIR_RESTREINT!="");
	return calcul_mysql_in($primary, join(",",$liste),"NOT");
}


	// fonctions de filtrage article
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_articles_exclus($publique=true, $id_auteur=NULL){
		include_spip('base/abstract_sql');
		static $liste_art_exclus=array();
		if (!isset($liste_art_exclus[$publique]) || !is_array($liste_art_exclus[$publique])){
			$liste_art_exclus[$publique] = array();
			$liste_rub = AccesRestreint_liste_rubriques_exclues($publique, $id_auteur);
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			$s = spip_query("SELECT id_article FROM spip_articles WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_art_exclus[$publique][] = $row['id_article'];
			}
		}
		return $liste_art_exclus[$publique];
	}
	function AccesRestreint_articles_accessibles_where($primary){
		include_spip('base/abstract_sql');
		$liste = AccesRestreint_liste_articles_exclus(_DIR_RESTREINT!="");
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage breves
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_breves_exclues($publique=true, $id_auteur=NULL){
		include_spip('base/abstract_sql');
		static $liste_breves_exclues=array();
		if (!isset($liste_breves_exclues[$publique]) || !is_array($liste_breves_exclues[$publique])){
			$liste_breves_exclues[$publique] = array();
			$liste_rub = AccesRestreint_liste_rubriques_exclues($publique, $id_auteur);
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			$s = spip_query("SELECT id_breve FROM spip_breves WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_breves_exclues[$publique][] = $row['id_breve'];
			}
		}
		return $liste_breves_exclues[$publique];
	}
	function AccesRestreint_breves_accessibles_where($primary){
		include_spip('base/abstract_sql');
		$liste = AccesRestreint_liste_breves_exclues(_DIR_RESTREINT!="");
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage forums
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_forum_exclus($publique=true, $id_auteur=NULL){
		include_spip('base/abstract_sql');
		static $liste_forum_exclus=array();
		if (!isset($liste_forum_exclus[$publique]) || !is_array($liste_forum_exclus[$publique])){
			$liste_forum_exclus[$publique] = array();
			// rattaches aux rubriques
			$liste_rub = AccesRestreint_liste_rubriques_exclues($publique, $id_auteur);
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			// rattaches aux articles
			$liste_art = AccesRestreint_liste_articles_exclus($publique, $id_auteur);
			$where .= " OR " . calcul_mysql_in('id_article', join(",",$liste_art));
			// rattaches aux breves
			$liste_breves = AccesRestreint_liste_breves_exclues($publique, $id_auteur);
			$where .= " OR " . calcul_mysql_in('id_breve', join(",",$liste_art));

			$s = spip_query("SELECT id_forum FROM spip_forum WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_forum_exclus[$publique][] = $row['id_forum'];
			}
		}
		return $liste_forum_exclus[$publique];
	}
	function AccesRestreint_forum_accessibles_where($primary){
		include_spip('base/abstract_sql');
		$liste = AccesRestreint_liste_forum_exclus(_DIR_RESTREINT!="");
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage signatures
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_signatures_exclues($publique=true, $id_auteur=NULL){
		include_spip('base/abstract_sql');
		static $liste_signatures_exclues=array();
		if (!isset($liste_signatures_exclues[$publique]) || !is_array($liste_signatures_exclues[$publique])){
			$liste_signatures_exclues[$publique] = array();
			// rattaches aux articles
			$liste_art = AccesRestreint_liste_articles_exclus($publique, $id_auteur);
			$where = calcul_mysql_in('id_article', join(",",$liste_art));
			$s = spip_query("SELECT id_signature FROM spip_signatures WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_signatures_exclues[$publique][] = $row['id_signature'];
			}
		}
		return $liste_signatures_exclues[$publique];
	}
	function AccesRestreint_signatures_accessibles_where($primary){
		include_spip('base/abstract_sql');
		$liste = AccesRestreint_liste_signatures_exclues(_DIR_RESTREINT!="");
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage documents
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_documents_exclus($publique=true, $id_auteur=NULL){
		include_spip('base/abstract_sql');
		static $liste_documents_exclus=array();
		if (!isset($liste_documents_exclus[$publique]) || !is_array($liste_documents_exclus[$publique])){
			$liste_documents_exclus[$publique] = array();
			// rattaches aux articles
			$liste_art = AccesRestreint_liste_articles_exclus($publique, $id_auteur);
			$where = calcul_mysql_in('id_objet', join(",",$liste_art));
			$s = spip_query("SELECT id_document FROM spip_documents_articles WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$publique][$row['id_document']]=1;
			}
			// rattaches aux rubriques
			$liste_rub = AccesRestreint_liste_rubriques_exclues($publique, $id_auteur);
			$where = calcul_mysql_in('id_objet', join(",",$liste_rub));
			$s = spip_query("SELECT id_document FROM spip_documents_rubriques WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$publique][$row['id_document']]=1;
			}
			// rattaches aux breves
			$liste_breves = AccesRestreint_liste_breves_exclues($publique, $id_auteur);
			$where = calcul_mysql_in('id_objet', join(",",$liste_breves));
			$s = spip_query("SELECT id_document FROM spip_documents_breves WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$publique][$row['id_document']]=1;
			}
			// rattaches aux syndic
			/*$liste_syn = AccesRestreint_liste_syndic_exclus($publique);
			$where = calcul_mysql_in('id_syndic', join(",",$liste_syn));
			$s = spip_query("SELECT id_document FROM spip_documents_syndic WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$publique][$row['id_document']]=1;
			}*/
			$liste_documents_exclus[$publique] = array_keys($liste_documents_exclus[$publique]);
		}
		return $liste_documents_exclus[$publique];
	}
	function AccesRestreint_documents_accessibles_where($primary){
		include_spip('base/abstract_sql');
		$liste = AccesRestreint_liste_documents_exclus(_DIR_RESTREINT!="");
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage syndic
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_syndic_exclus($publique=true, $id_auteur=NULL){
		include_spip('base/abstract_sql');
		static $liste_syndic_exclus=array();
		if (!isset($liste_syndic_exclus[$publique]) || !is_array($liste_syndic_exclus[$publique])){
			$liste_syndic_exclus[$publique] = array();
			$liste_rub = AccesRestreint_liste_rubriques_exclues($publique, $id_auteur);
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			$s = spip_query("SELECT id_syndic FROM spip_syndic WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_syndic_exclus[$publique][] = $row['id_syndic'];
			}
		}
		return $liste_syndic_exclus[$publique];
	}
	function AccesRestreint_syndic_accessibles_where($primary){
		include_spip('base/abstract_sql');
		$liste = AccesRestreint_liste_syndic_exclus(_DIR_RESTREINT!="");
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage syndic_articles
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_syndic_articles_exclus($publique=true, $id_auteur=NULL){
		include_spip('base/abstract_sql');
		static $liste_syndic_articles_exclus=array();
		if (!isset($liste_syndic_articles_exclus[$publique]) || !is_array($liste_syndic_articles_exclus[$publique])){
			$liste_syndic_articles_exclus[$publique] = array();
			$liste_syn = AccesRestreint_liste_syndic_exclus($publique, $id_auteur);
			$where = calcul_mysql_in('id_syndic', join(",",$liste_syn));
			$s = spip_query("SELECT id_syndic_article FROM spip_syndic_articles WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_syndic_articles_exclus[$publique][] = $row['id_syndic_article'];
			}
		}
		return $liste_syndic_articles_exclus[$publique];
	}
	function AccesRestreint_syndic_articles_accessibles_where($primary){
		include_spip('base/abstract_sql');
		$liste = AccesRestreint_liste_syndic_articles_exclus(_DIR_RESTREINT!="");
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage evenements
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_evenements_exclus($publique=true, $id_auteur=NULL){
		include_spip('base/abstract_sql');
		static $liste_evenements_exclus=array();
		if (!isset($liste_evenements_exclus[$publique]) || !is_array($liste_evenements_exclus[$publique])){
			$liste_evenements_exclus[$publique] = array();
			// rattaches aux articles
			$liste_art = AccesRestreint_liste_articles_exclus($publique, $id_auteur);
			$where = calcul_mysql_in('id_article', join(",",$liste_art));
			
			$s = spip_query("SELECT id_evenement FROM spip_evenements WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_evenements_exclus[$publique][] = $row['id_evenement'];
			}
		}
		return $liste_evenements_exclus[$publique];
	}
	function AccesRestreint_evenements_accessibles_where($primary){
		include_spip('base/abstract_sql');
		$liste = AccesRestreint_liste_evenements_exclus(_DIR_RESTREINT!="");
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}


	// filtre de securisation des squelettes
	// utilise avec [(#REM|AccesRestreint_securise_squelette)]
	// evite divulgation d'info si plugin desactive
	// par erreur fatale
	function AccesRestreint_securise_squelette($letexte){
		return "";
	}
	
	// filtre de test pour savoir si l'acces a un article est restreint
	function AccesRestreint_article_restreint($id_article){
		return
			@in_array($id_article,
				AccesRestreint_liste_articles_exclus(_DIR_RESTREINT!="")
			);
	}
	// filtre de test pour savoir si l'acces a une rubrique est restreinte
	function AccesRestreint_rubrique_restreinte($id_rubrique){
		return
			@in_array($id_rubrique,
				AccesRestreint_liste_rubriques_exclues(_DIR_RESTREINT!="")
			);
	}

?>
