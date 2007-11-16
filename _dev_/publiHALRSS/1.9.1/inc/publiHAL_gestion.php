<?php
/*
 *INSERT INTO `spiploc_groupes_mots` VALUES (1, 'evenements', '', '', 'non', 'non', 'oui', 'oui', '', 'oui', '', 'oui', 'oui', 'non', '20070213161052');
 *INSERT INTO `spiploc_groupes_mots` (`id_groupe`, `titre`, `descriptif`, `texte`, `unseul`, `obligatoire`, `articles`, `breves`, `rubriques`, `syndic`, `evenements`, `minirezo`, `comite`, `forum`, `maj`) VALUES (1, 'evenements', '', '', 'non', 'non', 'oui', 'oui', '', 'oui', '', 'oui', 'oui', 'non', '20070213161052');
 *  
 */
function publiHAL_install(){
	spip_log("+++++++++ passe par publiHAL_installation ++++++++ ???");// je ne sais pas si c'est un point de passage ?
}
function publiHAL_installation(){
	include_spip('inc/texte');
	include_spip('base/create');
	include_spip('base/abstract_sql');
	include_spip('base/mots_syndic_articles');
	$r=0;
	if(!isset($GLOBALS['meta']['publiHAL_base_mots_syndic_articles'])){
		creer_base();
		// ajout du champ evenements a la table spip_groupe_mots
		// si pas deja existant
		$desc = spip_abstract_showtable("spip_groupes_mots", '', true);
		if (!isset($desc['field']['syndic_articles'])){
			spip_query("ALTER TABLE spip_groupes_mots ADD `syndic_articles` VARCHAR(3) NOT NULL AFTER `syndic`");
		}
		ecrire_meta('publiHAL_base_mots_syndic_articles',0.1);
		$r|=8;
	}
	
	$n = spip_num_rows(spip_query("SELECT titre FROM spip_groupes_mots WHERE titre='publiHAL_Type_de_document' LIMIT 1"));
	if(!$n){
		$id_groupe=spip_abstract_insert('spip_groupes_mots', "(titre, texte, descriptif, unseul,  obligatoire, articles, breves, rubriques, syndic, minirezo, comite, forum, syndic_articles)", 
		"( " . spip_abstract_quote('publiHAL_Type_de_document') . 
		" , " . spip_abstract_quote('Indique quel est le type du document') . 
		" , " . spip_abstract_quote("Attention ne pas changer le titre!\n_ Indique quel est le type de document ou de publication : article, conférence, livre ...") . 
		" , 'non' , 'non' , 'oui' , '' , 'non' , 'oui' , 'oui' , 'oui' , 'non' , 'oui' )");
		// codes HAL
		publiHAL_ajoute_mot($id_groupe,'10. Articles de revues','ART_ACL','Articles dans des revues avec comité de lecture');
		publiHAL_ajoute_mot($id_groupe,'15. Articles de revues sans comité de lecture','ART_SCL','Articles dans des revues sans comité de lecture');
		publiHAL_ajoute_mot($id_groupe,'20. CONF_INV','CONF_INV','Conférences invitées');
		publiHAL_ajoute_mot($id_groupe,'25. COMM_ACT','COMM_ACT','Communications avec actes');
		publiHAL_ajoute_mot($id_groupe,'30. COMM_SACT','COMM_SACT','Communications sans actes');
		publiHAL_ajoute_mot($id_groupe,'35. OUVS','OUVS','Ouvrages scientifiques');
		publiHAL_ajoute_mot($id_groupe,'40. Chapitres d\'ouvrages scientifiques','COVS','Chapitres d\'ouvrages scientifiques');
		publiHAL_ajoute_mot($id_groupe,'45. DOUV','DOUV','Directions d\'ouvrages');
		publiHAL_ajoute_mot($id_groupe,'50. BREVET','BREVET','Brevets');
		publiHAL_ajoute_mot($id_groupe,'55. HDR','HDR','Habilitations à diriger des recherches');
		publiHAL_ajoute_mot($id_groupe,'60. Thèses','THESE','Thèses');
		publiHAL_ajoute_mot($id_groupe,'65. COURS','COURS','Cours');
		publiHAL_ajoute_mot($id_groupe,'70. AUTRE','AUTRE','Autres publications');
		publiHAL_ajoute_mot($id_groupe,'75. RAPPORT','RAPPORT','Rapport de recherche');
		publiHAL_ajoute_mot($id_groupe,'80. UNDEFINED','UNDEFINED','Documents sans référence de publication');
		// codes HAL-INRIA
		publiHAL_ajoute_mot($id_groupe,'17. ARTJOURNAL','ARTJOURNAL','Article de revue scientifique / vulgarisation');
		publiHAL_ajoute_mot($id_groupe,'22. ARTCOLLOQUE','ARTCOLLOQUE','Article de conférence-workshop');
		publiHAL_ajoute_mot($id_groupe,'32. OUVRAGE','OUVRAGE','Ouvrage - Livre et Congrès');
		publiHAL_ajoute_mot($id_groupe,'42. ARTOUVRAGE','ARTOUVRAGE','Chapitre de livre');
		publiHAL_ajoute_mot($id_groupe,'67. FICHELOGICIEL','FICHELOGICIEL','Fiche logiciel');
		publiHAL_ajoute_mot($id_groupe,'77. PREPUB','PREPUB','Document non publié');

//ARTJOURNAL  	Article de revue scientifique / vulgarisation
//ARTCOLLOQUE 	Article de conférence-workshop
//OUVRAGE 	Ouvrage - Livre et Congrès
//ARTOUVRAGE 	Chapitre de livre
//RAPPORT 	Rapport
//COURS 	Cours, tutorial
//BREVET 	Brevet
//FICHELOGICIEL 	Fiche logiciel
//PREPUB 	Document non publié
		
		ecrire_meta('publiHAL_Type_de_document',$id_groupe);
		$r|=1;
	}
	$n = spip_num_rows(spip_query("SELECT titre FROM spip_groupes_mots WHERE titre='publiHAL_auteurs_publi' LIMIT 1"));
	if(!$n){
		$id_groupe=spip_abstract_insert('spip_groupes_mots', "(titre, texte, descriptif, unseul,  obligatoire, articles, breves, rubriques, syndic, minirezo, comite, forum, syndic_articles)", 
		"( " . spip_abstract_quote('publiHAL_auteurs_publi') . 
		" , " . spip_abstract_quote("un auteur d'une publication de document") . 
		" , " . spip_abstract_quote("Attention ne pas changer le titre!\n_ Indique un auteur d'une publication de document.\n_ Mettre plusieurs variantes du nom séparées par des virgules") . 
		" , 'non' , 'non' , 'oui' , '' , 'non' , 'oui' , 'oui' , 'oui' , 'non' , 'oui' )");
		ecrire_meta('publiHAL_auteurs_publi',$id_groupe);
		$r|=2;
	}
	$n = spip_num_rows(spip_query("SELECT titre FROM spip_groupes_mots WHERE titre='publiHAL_Labo_publi' LIMIT 1"));
	if(!$n){
		$id_groupe=spip_abstract_insert('spip_groupes_mots', "(titre, texte, descriptif, unseul,  obligatoire, articles, breves, rubriques, syndic, minirezo, comite, forum, syndic_articles)", 
		"( " . spip_abstract_quote('publiHAL_Labo_publi') . 
		" , " . spip_abstract_quote("Labo d'une publication de document") . 
		" , " . spip_abstract_quote("Attention ne pas changer le titre!\n_ Indique un labo d'une publication de document.\n_ Mettre plusieurs variantes du nom séparées par des virgules") . 
		" , 'non' , 'non' , 'oui' , '' , 'non' , 'oui' , 'oui' , 'oui' , 'non' , 'oui' )");
		$id_mot=publiHAL_ajoute_mot($id_groupe,
			trim(supprimer_tags(typo($GLOBALS['meta']['nom_site']))),
			trim(supprimer_tags(typo($GLOBALS['meta']['descriptif_site']))),
			'Nom du site : il regroupe des labos et des équipes ?','publiHAL_Labo_publi');
		ecrire_meta('publiHAL_Labo_publi',$id_groupe);
		ecrire_meta('publiHAL_Ce_Labo_publi',$id_mot);
		$r|=4;
	}
	$n = spip_num_rows(spip_query("SELECT titre FROM spip_groupes_mots WHERE titre='publiHAL_Keywords' LIMIT 1"));
	if(!$n){
		$id_groupe=spip_abstract_insert('spip_groupes_mots', "(titre, texte, descriptif, unseul,  obligatoire, articles, breves, rubriques, syndic, minirezo, comite, forum, syndic_articles)", 
		"( " . spip_abstract_quote('publiHAL_Keywords') . 
		" , " . spip_abstract_quote("Keywords d'une publication de document") . 
		" , " . spip_abstract_quote("Mots clef de la publication") . 
		" , 'non' , 'non' , 'oui' , '' , 'non' , 'oui' , 'oui' , 'oui' , 'non' , 'oui' )");
		ecrire_meta('publiHAL_Keywords',$id_groupe);
		$r|=16;
	}
	if($r) ecrire_metas();
	return $r;
}

/**
 * Ajoute un mot 
 */
function publiHAL_ajoute_mot($id_groupe,$titre_mot,$descriptif,$texte,$type = 'publiHAL_Type_de_document'){
	// ATTENTION function indexer_objet()
	$id_mot = spip_abstract_insert("spip_mots", '(id_groupe)', "($id_groupe)");
	$result = spip_query("SELECT titre FROM spip_groupes_mots WHERE id_groupe=$id_groupe");
	// comme dans mots_edit.php ligne 60
	if ($row = spip_fetch_array($result)) $type = (corriger_caracteres($row['titre']));
	// finalise
	spip_query("UPDATE spip_mots SET titre=" . spip_abstract_quote($titre_mot) . 
	" , texte=" . spip_abstract_quote($texte) . 
	" , descriptif=" . spip_abstract_quote($descriptif) . 
	" , type=" . spip_abstract_quote($type) .  //	" , idx='non' " . 
	" , id_groupe=$id_groupe" . 
	" WHERE id_mot=$id_mot");
	return $id_mot;
}

/**
 * Pas utilisé, juste pour mémoire.
 * Voir http://listes.rezo.net/archives/spip-zone/2006-07/msg00212.html
 */
function publiHAL_uninstall(){
	$ecrire=0;
	if(isset($GLOBALS['meta']['publiHAL_Type_de_document'])){
		$id_groupe=$GLOBALS['meta']['publiHAL_Type_de_document'];
		spip_query("DELETE FROM spip_mots WHERE id_groupe=$id_groupe");
		spip_query("DELETE FROM spip_groupes_mots WHERE id_groupe=$id_groupe");
		effacer_meta('publiHAL_Type_de_document');
		$ecrire|=1;
	}
	if(isset($GLOBALS['meta']['publiHAL_auteurs_publi'])){
		$id_groupe=$GLOBALS['meta']['publiHAL_auteurs_publi'];
		spip_query("DELETE FROM spip_mots WHERE id_groupe=$id_groupe");
		spip_query("DELETE FROM spip_groupes_mots WHERE id_groupe=$id_groupe");
		effacer_meta('publiHAL_auteurs_publi');
		$ecrire|=2;
	}
	if(isset($GLOBALS['meta']['publiHAL_Keywords'])){
		$id_groupe=$GLOBALS['meta']['publiHAL_Keywords'];
		spip_query("DELETE FROM spip_mots WHERE id_groupe=$id_groupe");
		spip_query("DELETE FROM spip_groupes_mots WHERE id_groupe=$id_groupe");
		effacer_meta('publiHAL_Keywords');
		$ecrire|=16;
	}
	if(isset($GLOBALS['meta']['publiHAL_Labo_publi'])){
		$id_groupe=$GLOBALS['meta']['publiHAL_Labo_publi'];
		spip_query("DELETE FROM spip_mots WHERE id_groupe=$id_groupe");
		spip_query("DELETE FROM spip_groupes_mots WHERE id_groupe=$id_groupe");
		effacer_meta('publiHAL_Labo_publi');
		effacer_meta('publiHAL_Ce_Labo_publi');
		$ecrire|=4;
	}
	if(isset($GLOBALS['meta']['publiHAL_base_mots_syndic_articles'])){
		effacer_meta('publiHAL_base_mots_syndic_articles');
		spip_query("DELETE FROM spip_mots_syndic_articles");
		spip_query("DELETE FROM spip_syndic_articles");
		spip_query("DROP TABLE IF EXISTS spip_mots_syndic_articles");
		// suppression du champ syndic_articles a la table spip_groupe_mots
		spip_query("ALTER TABLE `spip_groupes_mots` DROP `syndic_articles`");
		$ecrire|=8;
	}
	if($ecrire) ecrire_metas();
	return $ecrire;
}

/**
 * retourne vrai si tout est installé comme il faut
 */
function publiHAL_test_installation(){
	if (isset($GLOBALS['meta']['publiHAL_Type_de_document']) &&
		isset($GLOBALS['meta']['publiHAL_auteurs_publi']) &&
		isset($GLOBALS['meta']['publiHAL_Labo_publi']) &&
		isset($GLOBALS['meta']['publiHAL_Ce_Labo_publi']) &&
		isset($GLOBALS['meta']['publiHAL_base_mots_syndic_articles']) &&
		($GLOBALS['meta']['publiHAL_base_mots_syndic_articles']==0.1)
		) return true;
	return false;
}

/**
 * Insertion des mots
 */
function publiHAL_traite_mots_auteurs($id_syndic_article,$chaine_auteurs=NULL){
	include_spip('inc/mots');
	// cas d'un appel après syndication rss pas de $chaine_auteurs
	if(is_null($chaine_auteurs)){
		$result=spip_query("SELECT lesauteurs FROM spip_syndic_articles WHERE id_syndic_article=$id_syndic_article");
		list($chaine_auteurs)=spip_fetch_array($result);
	};
	//
	if(!$chaine_auteurs) return;
	if(!isset($GLOBALS['meta']['publiHAL_auteurs_publi'])) return;
	// debut
	$id_groupe=$GLOBALS['meta']['publiHAL_auteurs_publi'];
	$auteurs= explode(';',$chaine_auteurs);
	$req="SELECT id_mot, descriptif FROM spip_mots WHERE id_groupe=$id_groupe ";
	$result=spip_query($req);
	while($row=spip_fetch_array($result)){
		$id_mot=$row['id_mot'];
		$descriptif=$row['descriptif'];
		publiHAL_met_mot_si_auteur_publi($id_syndic_article,$descriptif,$auteurs,$id_mot);
	}	
}
/**
 * met le mot clef si auteur de la publi $id_syndic_article
 * attention $auteurs est soit un tableau de noms soit la chaine lesauteurs
 * $descriptif de l'auteur associé au mot clef (exp. reg.)
 */
function publiHAL_met_mot_si_auteur_publi($id_syndic_article,$descriptif,$auteurs,$id_mot){
	include_spip('inc/mots');
	if(is_string($auteurs)) $auteurs= explode(';',$auteurs);
	// pour assouplir la recherche si il y a des espaces: attention "u" pour UTF-8 sinon é<>é
	if(!(strlen($descriptif)>0)) {
		spip_log('publiHAL_traite_mots_auteurs descriptif vide :"'.$descriptif.'"');
		return;
	}
	$descriptif=preg_replace('/\s*,\s*/sui','\\s*,\\s*',$descriptif);
	if(preg_grep("/\s*$descriptif\s*/sui",$auteurs)){
		//spip_log('publiHAL_traite_mots_auteurs *********');
		inserer_mot('spip_mots_syndic_articles', 'id_syndic_article', $id_syndic_article, $id_mot);
		//spip_log('publiHAL_traite_mots_auteurs ----------');
	}
}

?>