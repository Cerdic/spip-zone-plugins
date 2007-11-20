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
function bonbon_annee_scolaire ($date,$mois_de_debut_annee=9) {
//quelle date est-on ?
		$num_month= mois($date);
		$num_month=(integer) $num_month;
		$num_annee=annee($date);
		$num_annee=(integer) $num_annee;
//déterminer dans quelle année scolaire on est (de sept à sept)
		if ($num_month<$mois_de_debut_annee) {
			$nom_rub_annee=($num_annee-1)."/".$num_annee;
		} else {
			$nom_rub_annee=$num_annee."/".($num_annee+1);
		}
		return $nom_rub_annee;
}
//Ce filtre rend les tableaux fusionnés
function bonbon_fusion_tableau($tab,$autretab) {
 $final=array_merge((array)$tab,(array)$autretab);
 return $final;
}
?>
