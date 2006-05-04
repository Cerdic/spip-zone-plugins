<?php

function mots_de_chaine($rech_chaine){
	// TODO desaccentuer les mots
	return array_unique(preg_split(",\s+,",$rech_chaine));
}

function compte_recherches($fichier,&$recherches,&$recherche_a){
	
	// Noter la visite du site (article 0)
	$recherches ++;

	$content = array();
	if (lire_fichier($fichier, $content))
		$content = @unserialize($content);
	if (!is_array($content)) return;

	foreach ($content as $rech_chaine => $pages) {
		$mots = mots_de_chaine($rech_chaine);
		foreach($mots as $mot)
			$recherche_a[$mot][$rech_chaine] ++;
	}
	
}

function recherche_etendue_stats(){
	// Initialisations
	$recherches = ' '; # visites du site
	$recherche_a = array(); # visites du site

	// charger un certain nombre de fichiers de visites,
	// et faire les calculs correspondants

	// Traiter jusqu'a 100 sessions datant d'au moins 30 minutes
	$sessions = preg_files(sous_repertoire(_DIR_SESSIONS, 'visites'));

	$compteur = 100;
	$date_init = time()-30*60;

	foreach ($sessions as $item) {
		if (@filemtime($item) < $date_init) {
			spip_log("traite la session recherche $item");
			compte_fichier_visite($item,$recherches,$recherche_a);
			@unlink($item);
			if (--$compteur <= 0)
				break;
		}
	}

	if (!$recherches) return;
	spip_log("analyse $visites visites");	

	// Agreger les recherches dans une table SQL
	foreach($recherche_a as $mot => $item){
		$mot = addslashes($mot);
		foreach($item as $chaine => $nb){
			$chaine = addslashes($chaine);
			spip_query("INSERT INTO spip_recherche SET (mot,requete,recherches,date,maj) VALUES ('$mot','$chaine',$nb,'$date_init',NOW())");
		}
	}
	
}
?>