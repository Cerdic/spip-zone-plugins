<?php
include_spip("inc/accesgroupes_fonctions");

function autoriser_rubrique_creerarticledans($faire, $type, $id, $qui, $opt) {
	static $rub_exclues=NULL;
	if ($rub_exclues===NULL){
		$rub_exclues = accesgroupes_liste_rubriques_restreintes();
		$rub_exclues = array_flip($rub_exclues);
	}
	
	if (isset($rub_exclues[$id]))
	return $id AND false;
	return $id AND true;
}

function autoriser_rubrique_voir($faire, $type, $id, $qui, $opt) {
	static $rub_exclues=NULL;
	if ($rub_exclues===NULL){
		$rub_exclues = accesgroupes_liste_rubriques_restreintes();
		$rub_exclues = array_flip($rub_exclues);
	}
	//echo "<pre>".print_r($rub_exclues)."</pre>";
	if (isset($rub_exclues[$id]))
	return false;
	return true;
}

function autoriser_article_voir($faire, $type, $id, $qui, $opt) {
	static $art_exclus=NULL;
	//echo "$faire $type $id $qui $opt";
	if ($art_exclus===NULL){
		$art_exclus = accesgroupes_liste_articles_restreints();
		$art_exclus = array_flip($art_exclus);
	}
	//echo "<pre>".print_r($art_exclus)."</pre>";
	if (isset($art_exclus[$id]))
	return false;
	return true;
}

function autoriser_breves_voir($faire, $type, $id, $qui, $opt) {
	static $bre_exclues=NULL;
	//echo "$faire $type $id $qui $opt";
	if ($bre_exclues===NULL){
		$bre_exclues = accesgroupes_liste_breves_restreintes();
		$bre_exclues = array_flip($bre_exclues);
	}
	//echo "<pre>".print_r($bre_exclues)."</pre>";
	if (isset($bre_exclues[$id]))
	return false;
	return true;
}

function autoriser_forums_voir($faire, $type, $id, $qui, $opt) {
	static $for_exclus=NULL;
	//echo "$faire $type $id $qui $opt";
	if ($for_exclus===NULL){
		$for_exclus = accesgroupes_liste_forums_restreints();
		$for_exclus = array_flip($for_exclus);
	}
	//echo "<pre>".print_r($bre_exclues)."</pre>";
	if (isset($for_exclus[$id]))
	return false;
	return true;
}
?>