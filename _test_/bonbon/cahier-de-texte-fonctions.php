<?php
// Fonctions de �Bonbon !� le cahier de texte pour Spip.
// R�alis� par Bertrand MARNE (bmarne � ac-creteil.fr)
// Sous licence GPL (enfin bon, c'est bonbonware...)
// CopyLeft en octobre 2007

//Cette fonction matches les id_articles qui sont dans le PS des s�ances
//Puis les mets dans une cha�ne (s�par�s par des virgules, en vue d'un
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

//Cette fonction matches les id_documents qui sont dans le SOUSTITRE des s�ances
//Puis les mets dans une cha�ne (s�par�s par des virgules, en vue d'un
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
//Cette fonction d�termine l'ann�e scolaire � partir de la date et retransmet sous forme 2007/2008 par exemple
function bonbon_annee_scolaire ($date,$date_debut=false,$mois_de_debut_annee=9) {
//quelle date est-on ?
		$num_month= mois($date);
		$num_month=(integer) $num_month;
		$num_annee=annee($date);
		$num_annee=(integer) $num_annee;
//d�terminer dans quelle ann�e scolaire on est (de sept � sept)
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
//Ce filtre rend les tableaux fusionn�s
function bonbon_fusion_tableau($tab,$autretab) {
 $final=array_merge((array)$tab,(array)$autretab);
 return $final;
}

//Quelques fonctions pour que Bonbon manipule la base de donn�es:
function bonbon_ajoute_groupe ($nom_groupe){
	$result=false;
	$sql = "INSERT INTO spip_groupes_mots (titre,articles, breves,rubriques, syndic, minirezo, comite, forum) 
		VALUES ('".trim($nom_groupe)."','oui','oui','oui','oui','oui','oui','oui')";
		
	$result = spip_query($sql);
	if ($result) $result=spip_insert_id();
	return $result;
};
function bonbon_ajoute_mot ($titre,$id_groupe,$type){
	$result=false;
	$sql = "INSERT INTO spip_mots (titre, id_groupe,type) 
		VALUES ('".trim($titre)."','".trim($id_groupe)."','".trim($type)."')";
		
	$result = spip_query($sql);
	if ($result) $result=spip_insert_id();
	return $result;
}
function bonbon_lier_mot ($id_mot,$id_objet,$type_objet="article") {
	$result=false;
	$sql = "INSERT INTO spip_mots_".$type_objet."s (id_mot, id_". $type_objet .") VALUES (" . $id_mot . ", " . $id_objet . ")";
	$result = spip_query($sql);
	return $result;
}

function bonbon_effacer_lien_mot ($id_mot,$id_objet,$type_objet="article") {
	$result=false;
	$sql = "DELETE FROM spip_mots_".$type_objet."s WHERE id_mot=$id_mot AND id_$type_objet=$id_objet";
	$result = spip_query($sql);
	return $result;
}
function bonbon_affecter_auteur ($id_article, $id_auteur) {
	$result=false;
	$sql = "INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES ($id_auteur,$id_article)";
	$result = spip_query($sql);
	return $result;
}

function bonbon_desaffecter_auteur ($id_article, $id_auteur) {
	$result=false;
	$sql = "DELETE FROM spip_auteurs_articles WHERE id_auteur=$id_auteur AND  id_article=$id_article";
	$result = spip_query($sql);
	return $result;
}
function bonbon_creer_fiche_prof ($nom, $id_auteur, $id_rubrique) {
	$result=false;
	$descriptif="Cet article d�crit gr�ce � ses mots-cl�s, les classes et les mati�res enseign�es par $nom";
	$sql = "INSERT INTO spip_articles (titre, id_rubrique, statut, date, surtitre, descriptif,ps) VALUES ('$nom','$id_rubrique', 'publie', NOW(),'".addslashes("� propos d'un professeur")."','".addslashes($descriptif)."','$id_auteur')";
	$result = spip_query($sql);
	if ($result) {
		$id_article=spip_insert_id();
		$result=bonbon_affecter_auteur($id_article,$id_auteur);
		if ($result) $result=$id_article;
	}
	return $result;
}
function bonbon_creer_fiche_classe ($nom_classe, $id_rubrique, $id_mot) {
	$result=false;
	$descriptif="Cet article d�crit gr�ce � ses mots-cl�s, son auteur et son �ventuel contenu la classe de $nom_classe";
	$sql = "INSERT INTO spip_articles (titre, id_rubrique, statut, date, surtitre, descriptif) VALUES ('$nom_classe','$id_rubrique', 'publie', NOW(),'".addslashes("� propos d'une classe")."','".addslashes($descriptif)."')";
	$result = spip_query($sql);
	if ($result) {
		$id_article=spip_insert_id();
		$result=bonbon_lier_mot($id_mot,$id_article);
	}
	return $result;
}
function bonbon_creer_sous_rubrique ($id_parent, $titre, $descriptif) {
	$sql = "INSERT INTO spip_rubriques (titre, id_parent, descriptif , statut, date) 
	VALUES ('".addslashes($titre)."', '$id_parent','".addslashes($descriptif)."', 'publie',NOW())";
		
	$result = spip_query($sql);
	if ($result) $result=spip_insert_id();
	return $result;
}
function bonbon_creer_rubrique ($titre, $descriptif) {
	$sql = "INSERT INTO spip_rubriques (titre, descriptif , statut, date) 
	VALUES ('".addslashes($titre)."','".addslashes($descriptif)."', 'publie',NOW())";
		
	$result = spip_query($sql);
	if ($result) $result=spip_insert_id();
	return $result;
}
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

?>