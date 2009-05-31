<?php
include_spip('inc/charsets');
include_spip('inc/filtres');
function attribut_opml($texte){
	$texte = preg_replace(",</?[\w]+[^<>]*>,US", "", $texte);
	$texte = texte_backend($texte);
	$texte = str_replace(array('<','>'),array('&lt;','&gt;'),$texte);
	$texte = unicode2charset($texte);
	return $texte;
}
function nom_attribut_opml($nom){
	$nom = supprimer_tags($nom);
	$nom = preg_replace(",[\s\"\']+,","",$nom);
	return $nom;
}
function indente_tabs($niveau){
	return substr("\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t",0,max(0,$niveau+1));
}
function indente($niveau,$niveau_prec){
	$texte = "";
	// ouvrir les niveaux manquants
	for($i=$niveau_prec+1;$i<$niveau;$i++)
		$texte .= indente_tabs($i)."<outline>\n";
	// fermer les niveaux ouverts
	for($i=$niveau_prec;$i>=$niveau;$i--)
		$texte .= indente_tabs($i)."</outline>\n";
	$texte .= indente_tabs($niveau);
	return $texte;
}

?>