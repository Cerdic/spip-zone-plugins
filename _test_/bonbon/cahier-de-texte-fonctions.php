<?php
// Fonctions de «Bonbon !» le cahier de texte pour Spip.
// Réalisé par Bertrand MARNE (bmarne à ac-creteil.fr)
// Sous licence GPL (enfin bon, c'est bonbonware...)
// CopyLeft en octobre 2007

//Cette fonction matches les id_articles qui sont dans le PS des séances
//Puis les mets dans une chaîne (séparés par des virgules, en vue d'un
//explode)
function bonbon_matches_id_article ($a_matcher) {
	preg_match_all ("/->(\d+?)\]/",$a_matcher,$matches);
	$key = key($matches[1]);
	$val = current($matches[1]);
	$virgule="";
	while(list ($key, $val) = each ($matches[1])) {
	$sortie .= $virgule.$val;
	if ($virgule=="") $virgule=",";
 	};
	return $sortie;
}

//Cette fonction matches les id_documents qui sont dans le SOUSTITRE des séances
//Puis les mets dans une chaîne (séparés par des virgules, en vue d'un
//explode)
function bonbon_matches_id_document ($a_matcher) {
	preg_match_all ("/<doc(\d+?)>/",$a_matcher,$matches);
	$key = key($matches[1]);
	$val = current($matches[1]);
	$virgule="";
	while(list ($key, $val) = each ($matches[1])) {
	$sortie .= $virgule.$val;
	if ($virgule=="") $virgule=",";
 	};
	return $sortie;
}
//Cette fonction détermine l'année scolaire à partir de la date et retransmet sous forme 2007/2008 par exemple
function bonbon_annee_scolaire ($date,$date_debut=false,$mois_de_debut_annee=9) {
//quelle date est-on ?
		$num_month= mois($date);
		$num_month=(integer) $num_month;
		$num_annee=annee($date);
		$num_annee=(integer) $num_annee;
//déterminer dans quelle année scolaire on est (de sept à sept)
		if ($num_month<$mois_de_debut_annee) {
			$num_annee_1=$num_annee-1;
			$num_annee_2=$num_annee;
		} else {
			$num_annee_1=$num_annee;
			$num_annee_2=$num_annee+1;
		}
		$nom_rub_annee="$num_annee_1/$num_annee_2";
		if ($date_debut) $nom_rub_annee=mktime(0,0,0,$mois_de_debut_annee,1,$num_annee_1);
		return $nom_rub_annee;
}
//Ce filtre vérifie qu'une date est au format jj/mm/aaaa
function bonbon_verifie_date($p_text) {
	$l_ok=true;
	if (!preg_match('/\s*(\d+)[\s\/]+(\d+)[\s\/]+(\d{4})\s*$/',$p_text,$l_val)) {
		$l_ok=false;
	} else if (!checkdate($l_val[2],$l_val[1],$l_val[3])) {
		$l_ok=false;
	} else if (mktime(0,0,0,$l_val[1],$l_val[2],$l_val[3])<bonbon_annee_scolaire (date("d/m/Y"),true)) {
		$l_ok=false;
	}
	return($l_ok);
} 
//Ce filtre rend les tableaux fusionnés
function bonbon_fusion_tableau($tab,$autretab) {
 $final=array_merge((array)$tab,(array)$autretab);
 return $final;
}

//Quelques fonctions pour que Bonbon manipule la base de données:
//ajouter un groupe
function bonbon_ajoute_groupe ($nom_groupe){
	$result=false;
	$sql = "INSERT INTO spip_groupes_mots (titre,articles, breves,rubriques, syndic, minirezo, comite, forum) 
		VALUES ('".trim($nom_groupe)."','oui','oui','oui','oui','oui','oui','oui')";
		
	$result = spip_query($sql);
	if ($result) $result=spip_insert_id();
	return $result;
}
//Insère un mot-clé dans la base.
function bonbon_ajoute_mot ($titre,$id_groupe,$type){
	$result=false;
	$sql = "INSERT INTO spip_mots (titre, id_groupe,type) 
		VALUES ('".trim($titre)."','".trim($id_groupe)."','".trim($type)."')";
		
	$result = spip_query($sql);
	if ($result) $result=spip_insert_id();
	return $result;
}
//Ajoute des mots-clés (une liste de mots séparés par des virgules) dans un groupe de mots
function bonbon_remplit_groupe_mots ($id_groupe,$type,$liste_mots) {
	$tab_mots=explode(",",$liste_mots);
	while (list($key2,$val2)=each($tab_mots)) {
		if ($val2) {
			$id_mot=bonbon_ajoute_mot ($val2,$id_groupe,$type);
			echo "<p><i>$val2</i> est le mot-clé numéro $id_mot</p>";
		}
	}
	return "";
}
//Ajoute un mot-clé à un article par défaut ou une rubrique si précisé
function bonbon_lier_mot ($id_mot,$id_objet,$type_objet="article") {
	$result=false;
	$sql = "INSERT INTO spip_mots_".$type_objet."s (id_mot, id_". $type_objet .") VALUES (" . $id_mot . ", " . $id_objet . ")";
	$result = spip_query($sql);
	return $result;
}
//rompt le lien entre un article (par défaut ou rubrique si précisé) et un mot-clé
function bonbon_effacer_lien_mot ($id_mot,$id_objet,$type_objet="article") {
	$result=false;
	$sql = "DELETE FROM spip_mots_".$type_objet."s WHERE id_mot=$id_mot AND id_$type_objet=$id_objet";
	$result = spip_query($sql);
	return $result;
}
//Affecte un auteur à un article...
function bonbon_affecter_auteur ($id_article, $id_auteur) {
	$result=false;
	$sql = "INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES ($id_auteur,$id_article)";
	$result = spip_query($sql);
	return $result;
}
//désaffecte un auteur d'un article.
function bonbon_desaffecter_auteur ($id_article, $id_auteur) {
	$result=false;
	$sql = "DELETE FROM spip_auteurs_articles WHERE id_auteur=$id_auteur AND  id_article=$id_article";
	$result = spip_query($sql);
	return $result;
}
//Crée une fiche pour le prof (jointure prof-classe-matière)
function bonbon_creer_fiche_prof ($nom, $id_auteur, $id_rubrique) {
	$result=false;
	$descriptif="Cet article décrit grâce à ses mots-clés, les classes et les matières enseignées par $nom";
	$sql = "INSERT INTO spip_articles (titre, id_rubrique, statut, date, surtitre, descriptif,ps) VALUES ('$nom','$id_rubrique', 'publie', NOW(),'".addslashes("À propos d'un professeur")."','".addslashes($descriptif)."','$id_auteur')";
	$result = spip_query($sql);
	if ($result) {
		$id_article=spip_insert_id();
		$result=bonbon_affecter_auteur($id_article,$id_auteur);
		if ($result) $result=$id_article;
	}
	return $result;
}
//Crée une fiche pour la classe (jointure classe-matière-pp)
function bonbon_creer_fiche_classe ($nom_classe, $id_rubrique, $id_mot) {
	$result=false;
	$descriptif="Cet article décrit grâce à ses mots-clés, son auteur et son éventuel contenu la classe de $nom_classe";
	$sql = "INSERT INTO spip_articles (titre, id_rubrique, statut, date, surtitre, descriptif) VALUES ('$nom_classe','$id_rubrique', 'publie', NOW(),'".addslashes("À propos d'une classe")."','".addslashes($descriptif)."')";
	$result = spip_query($sql);
	if ($result) {
		$id_article=spip_insert_id();
		$result=bonbon_lier_mot($id_mot,$id_article);
	}
	return $result;
}
//Crée une sous rubrique.
function bonbon_creer_sous_rubrique ($id_parent, $titre, $descriptif) {
	$sql = "INSERT INTO spip_rubriques (titre, id_parent, descriptif , statut, date) 
	VALUES ('".addslashes($titre)."', '$id_parent','".addslashes($descriptif)."', 'publie',NOW())";
		
	$result = spip_query($sql);
	if ($result) $result=spip_insert_id();
	return $result;
}
//Crée un secteur
function bonbon_creer_rubrique ($titre, $descriptif) {
	$sql = "INSERT INTO spip_rubriques (titre, descriptif , statut, date) 
	VALUES ('".addslashes($titre)."','".addslashes($descriptif)."', 'publie',NOW())";
		
	$result = spip_query($sql);
	if ($result) $result=spip_insert_id();
	return $result;
}
//Vérifie qu'un mot existe...
function bonbon_mot_existe ($titre) {
	$result = spip_query($sql);
	$result = spip_optim_select(
		array("mots.id_mot"), # SELECT
		array('mots' => 'spip_mots'), # FROM
		array(
			array('=', 'mots.titre',"'$titre'")
		), # WHERE
		array(), # WHERE pour jointure
		'', # GROUP
		array(), # ORDER
		'', # LIMIT
		'', # sous
		array(), # HAVING
		'mots', # table
		'', # boucle
		''); # serveur
	while ($Pile[$SP] = @spip_abstract_fetch($result,"")) {
		print_r ($Pile[$SP]);
		$resultat .= $Pile[$SP]['id_mot'];
	}
	@spip_abstract_free($result,'');
	return $resultat;
}
//enregistre une séance et lui affecte son auteur
function bonbon_enregistrement_seance ($date,$titre,$contenu,$id_auteur,$id_rubrique_cdt,$surtitre_avec_docs="") {
//insertion de l'article du contenu du cours dans la base

//On prépare la date:
	$date_base_seance = date ("Y-m-d H:i:s", mktime(0,0,0,substr($date,3,2),substr($date,0,2),substr($date,6,4)));

//le contenu
	$sql = "INSERT INTO spip_articles (titre, texte, id_rubrique, statut, date, surtitre) 
	VALUES ('".addslashes($titre)."','".addslashes($contenu)."','$id_rubrique_cdt', 'publie', '".addslashes($date_base_seance)."','".addslashes($surtitre_avec_docs)."')";
	
	$result = spip_query($sql);
	$id_contenu_seance=spip_insert_id();
	echo ("<!--$result article n°$id_contenu_seance-->\n");
	
	// auteur
	$sql = "INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES (" . $id_auteur . ", " . $id_contenu_seance . ")";
	$result = spip_query($sql);
	echo ("<!--auteur: $result-->\n");
	return $id_contenu_seance;
}
//enregistre un devoir
function bonbon_enregistrement_devoir ($date,$fin_titre,$contenu,$id_auteur,$id_rubrique_cdt,$titre_seance,$id_seance,$no_devoir,$ps_seance,$surtitre_avec_docs="") {
	$fleche="->";
	//détermine la date au format de la base
	$date_base_devoir = date ("Y-m-d H:i:s", mktime(0,0,0,substr($date,3,2),substr($date,0,2),substr($date,6,4)));
	//insère le devoir avec titre, contenu, date et surtout un PS qui renvoie au contenu
	$sql = "INSERT INTO spip_articles (titre, texte, id_rubrique, statut, date, ps, surtitre) 
	VALUES ('".addslashes("Devoir à faire pour le $date$fin_titre")."','".addslashes($contenu)."','$id_rubrique_cdt', 'publie', '".addslashes($date_base_devoir)."','".addslashes("[Donné $titre_seance$fleche$id_seance]")."','".addslashes($surtitre_avec_docs)."')";
	
	$result = spip_query($sql);
	$id_contenu_devoir=spip_insert_id();
	echo ("<!--$result article n°$id_contenu_devoir-->\n");

	//préparation de la chaîne à inclure dans le contenu:
	$ps_seance .= "- [Devoir n°$no_devoir pour le $date$fleche$id_contenu_devoir]\n";
	
	// auteur
	$sql = "INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES (" . $id_auteur . ", " . $id_contenu_devoir . ")";
	$result = spip_query($sql);
	echo ("<!--auteur: $result-->\n");
//retourne deux valeurs: [0] est l'id_contenu_devoir et [1] est la chaîne du PS de l'article.
	return array ($id_contenu_devoir,$ps_seance);
}
//rajout des références aux devoirs dans le PS du contenu de la séance
function bonbon_ajout_devoirs_a_seance ($liste_devoirs,$id_seance) {
	$sql ="UPDATE spip_articles SET ps='".addslashes($liste_devoirs)."' WHERE id_article=$id_seance";
	$result = spip_query($sql);
	echo ("<!--update ps: $result-->\n");
	return $result;
}




//récupère le contenu entre balises et renvoie un tableau du contenu
function bonbon_recupere_balise($texte,$nombalise) {
	preg_match_all ("/<$nombalise.*?>(.*?)<\/$nombalise>/s",$texte,$matches);
	return $matches[1];
}

?>