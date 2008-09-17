<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL
 * 
 *
 */

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
			$where = calcul_mysql_in('id_article', join(",",$liste_art));
			$s = spip_query("SELECT id_document FROM spip_documents_articles WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$publique][$row['id_document']]=1;
			}
			// rattaches aux rubriques
			$liste_rub = AccesRestreint_liste_rubriques_exclues($publique, $id_auteur);
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			$s = spip_query("SELECT id_document FROM spip_documents_rubriques WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$publique][$row['id_document']]=1;
			}
			// rattaches aux breves
			$liste_breves = AccesRestreint_liste_breves_exclues($publique, $id_auteur);
			$where = calcul_mysql_in('id_breve', join(",",$liste_breves));
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

	
?>