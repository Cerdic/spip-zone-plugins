<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_exporter_dist(){
	
}

/*
	Exporter un article dans un fichier texte
	$f : ligne d'un tableau résultat d'un sql_fetch sur spip_articles
	$dest : repertoire ou exporter le fichiers
*/

function exporter_article($f,$dest){
	include_spip("inc/rubriques");
	
	$id_article = $f['id_article'] ;
	$id_rubrique = $f['id_rubrique'] ;
	
	// Exporter les champs spip_articles
	$fichier = "" ;
	$ins_auteurs = array();
	$ins_mc = array();
	$ins_doc = array();
	
	// mettre les champs dans un fichier texte balisé avec des <ins class="champ">.
	foreach($f as $k => $v){
		if($k == "texte" or $v == "" or $v == "0" or $v == "non" or $v == "0000-00-00 00:00:00")
			continue ;
		$fichier .= "<ins class='$k'>" . trim($v) ."</ins>\n" ;
	}
	$fichier .= "\n\n" . $f['texte'] . "\n\n" ;
	
	// Ajouter des métadonnées (hierarchie, auteurs, mots-clés...)
	
	// hierarchie
	$hierarchie = array();
	$ariane = preg_replace("/^0,/","", calcul_hierarchie_in($id_rubrique));
	
	$ariane = sql_allfetsel("titre","spip_rubriques","id_rubrique in($ariane)");
	foreach($ariane as $a)
		$hierarchie[] = str_replace("/","\/",$a['titre']) ; // Echapper les / car creer_rubrique_nommee pourrait se tromper à l'import.
	
	$hierarchie = implode("@@", $hierarchie);
	
	$rubrique = sql_fetsel("texte,descriptif", "spip_rubriques", "id_rubrique=$id_rubrique");
	
	if($texte_rubrique = $rubrique['texte'])
		$texte_rubrique = "<ins class='texte_rubrique'>$texte_rubrique</ins>\n" ;
	
	if($descriptif_rubrique = $rubrique['descriptif'])
		$descriptif_rubrique = "<ins class='descriptif_rubrique'>$descriptif_rubrique</ins>\n" ;
	
	// auteurs spip 3
	if($GLOBALS['spip_version_branche'] > "3")
		$auteurs = sql_allfetsel("a.nom, a.bio", "spip_auteurs_liens al, spip_auteurs a", "al.id_objet=$id_article and al.objet='article' and al.id_auteur=a.id_auteur");
	else // spip 2
		$auteurs = sql_allfetsel("a.nom, a.bio", "spip_auteurs_articles aa, spip_auteurs a", "aa.id_article=$id_article and aa.id_auteur=a.id_auteur");
	
	foreach($auteurs as $a)
		if($a['nom'])
			$ins_auteurs[] = $a ;
	
	$auteurs = "" ;
	foreach($ins_auteurs as $k => $a){
		if($k == 0)
			$sep = "" ;
		else
			$sep = "@@" ;
		$bio = ($a['bio'] != "") ? "::" . $a['bio'] : "" ;
		$auteurs .= $sep . $a['nom'] . $bio ;
	}
	
	$auteurs_m = substr($auteurs, 0, 100) ;
	
	// mots-clés
	if($GLOBALS['spip_version_branche'] > "3")
		$motscles = sql_allfetsel("*", "spip_mots_liens ml, spip_mots m", "ml.id_objet=$id_article and ml.objet='article' and ml.id_mot=m.id_mot");
	else // spip 2
		$motscles = sql_allfetsel("*", "spip_mots_articles ma, spip_mots m", "ma.id_article=$id_article and ma.id_mot=m.id_mot");
	
	foreach($motscles as $mc){
		if($mc['titre'])
			$ins_mc[] = $mc['type'] . "::" . $mc['titre'] ;
	}
	if(is_array($ins_mc)){
		$motscles = join("@@", $ins_mc) ;
		$motscles_m = substr($motscles, 0, 100) ;
	}
	
	// documents joints
	$documents = sql_allfetsel("*", "spip_documents d, spip_documents_liens dl", "dl.id_objet=$id_article and dl.objet='article' and dl.id_document=d.id_document");
	foreach($documents as $doc)
			$ins_doc[] = json_encode($doc) ;
	
	if(is_array($ins_doc)){
		$documents = join("@@", $ins_doc) ;
		$docs_m = substr($documents, 0, 100);
	}
	// url
	include_spip("inc/utils");
	$url_article = generer_url_entite($id_article, 'article') ;
	
	// Ajouter les métadonnées
	if($url_article)
		$fichier = "<ins class='url_article'>$url_article</ins>\n" . $fichier ;
	if($auteurs)
		$fichier = "<ins class='auteurs'>$auteurs</ins>\n" . $fichier ;
	if($motscles)
		$fichier = "<ins class='mots_cles'>$motscles</ins>\n" . $fichier ;
	if($documents)
		$fichier = "<ins class='documents'>$documents</ins>\n" . $fichier ;
	if($hierarchie){
		$fichier = "<ins class='hierarchie'>$hierarchie</ins>\n" .
		$descriptif_rubrique .
		$texte_rubrique .
		$fichier ;
	}
	
	// Créer un fichier txt
	$date = ($f['date_redac'] != "0000-00-00 00:00:00")? $f['date_redac'] : $f['date'] ;
	preg_match("/^(\d\d\d\d)-(\d\d)/", $date, $m);
	$annee = $m[1] ;
	$mois = $m[2] ;
	
	include_spip("inc/charsets");
	$nom_fichier = translitteration($f['titre']) ;
	$nom_fichier = preg_replace("/[^a-zA-Z0-9]/i", "-", $nom_fichier);
	$nom_fichier = preg_replace("/-{2,}/i", "-", $nom_fichier);
	$nom_fichier = preg_replace("/^-/i", "", $nom_fichier);
	$nom_fichier = preg_replace("/-$/i", "", $nom_fichier);
	$nom_fichier = substr($nom_fichier, 0, 80) ;
	
	$nom_fichier = "$dest/$annee/$annee-$mois/$annee-$mois"."_$nom_fichier"."_$id_article.txt" ;
	
	// Créer les répertoires
	if(!is_dir("$dest/$annee"))
		mkdir("$dest/$annee");
	if(!is_dir("$dest/$annee/$annee-$mois"))
		mkdir("$dest/$annee/$annee-$mois");
	
	if(ecrire_fichier("$nom_fichier", $fichier)){
		return array(
				"motscles_m" => $motscles_m,
				"auteurs_m" => $auteurs_m,
				"docs_m" => $docs_m,
				"nom_fichier" => $nom_fichier,
		);
	}
	else{
		return false ;
	}
}
