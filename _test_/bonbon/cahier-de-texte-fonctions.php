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
//Ce filtre rend les tableaux fusionnés
function bonbon_fusion_tableau($tab,$autretab) {
 $final=array_merge((array)$tab,(array)$autretab);
 return $final;
}

//Quelques fonctions pour que Bonbon manipule la base de données:
function bonbon_ajoute_groupe ($nom_groupe){
	$sql = "INSERT INTO spip_groupes_mots (titre,articles, breves,rubriques, syndic, minirezo, comite, forum) 
		VALUES ('".trim($nom_groupe)."','oui','oui','oui','oui','oui','oui','oui')";
		
	$result = spip_query($sql);
	$id_groupe=spip_insert_id();
	return $id_groupe;
};
function bonbon_ajoute_mot ($titre,$option_descript="",$id_groupe,$nom_groupe,$descript){
	$phrase="";
	if ($option_descript!="") {
		$option_descript .= trim($descript);
		$phrase=" de <b>$descript</b>";
	};
	$sql = "INSERT INTO spip_mots (titre, descriptif, texte , id_groupe, type) 
		VALUES ('".trim($titre)."','Sous-thème de ".$option_descript."','','".$id_groupe."','".trim($nom_groupe)."')";
		
	$result = spip_query($sql);
	$id_mot=spip_insert_id();
	return $id_mot;
}
function bonbon_lier_mot ($id_mot,$id_objet,$type_objet="article") {
	$result=false;
	$sql = "INSERT INTO spip_mots_".$type_objet."s (id_mot, id_". $type_objet .") VALUES (" . $id_mot . ", " . $id_objet . ")";
		$result = spip_query($sql);
		if ($result) {
			echo ("<!--le mot n°$id_mot est rattaché à $type_objet n°$id_objet-->");
		} else {
			echo "<!--problème pour lier le mot n°$id_mot à $type_objet n°$id_objet ! Faites-le à la main !-->";
		}
	return $result;
}

function bonbon_effacer_lien_mot ($id_mot,$id_objet,$type_objet="article") {
	$result=false;
	$sql = "DELETE FROM spip_mots_".$type_objet."s WHERE id_mot=$id_mot AND id_$type_objet=$id_objet";
		$result = spip_query($sql);
		if ($result) {
			echo ("<!--le mot n°$id_mot est détaché de $type_objet n°$id_objet-->");
		} else {
			echo "<!--problème pour détacher le mot n°$id_mot à $type_objet n°$id_objet ! Faites-le à la main !-->";
		}
	return $result;
}
function bonbon_affecter_auteur ($id_article, $id_auteur) {
	$result=false;
	$sql = "INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES ($id_auteur,$id_article)";
	$result = spip_query($sql);
	if ($result) {
		echo ("<!--l'auteur n°$id_mot est attaché à l'article n°$id_article-->");
	} else {
			echo "<!--problème pour attacher l'auteur n°$id_mot à l'article n°$id_article ! Faites-le à la main !-->";
	}
	return $result;
}

function bonbon_desaffecter_auteur ($id_article, $id_auteur) {
	$result=false;
	$sql = "DELETE FROM spip_auteurs_articles WHERE id_auteur=$id_auteur AND  id_article=$id_article";
	$result = spip_query($sql);
	if ($result) {
		echo ("<!--l'auteur n°$id_mot est détaché de l'article n°$id_article-->");
	} else {
			echo "<!--problème pour détacher l'auteur n°$id_mot de l'article n°$id_article ! Faites-le à la main !-->";
	}
	return $result;
}
function bonbon_creer_fiche_prof ($nom, $id_auteur, $id_rubrique) {
	$result=false;
	$descriptif="Cet article décrit grâce à ses mots-clés, les classes et les matières enseignées par $nom";
	$sql = "INSERT INTO spip_articles (titre, id_rubrique, statut, date, surtitre, descriptif,ps) VALUES ('$nom','$id_rubrique', 'publie', NOW(),'".addslashes("À propos d'un professeur")."','".addslashes($descriptif)."','$id_auteur')";
	$result = spip_query($sql);
	if ($result) {
		$id_article=spip_insert_id();
		bonbon_affecter_auteur($id_article,$id_auteur);
	}
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