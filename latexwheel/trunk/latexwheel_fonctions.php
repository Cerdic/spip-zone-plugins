<?php

function latex_copier_img($img,$dossier,$chemin=''){
	// Convertir puis copier une image, en retournant le chemin à partir du fichier .tex principal
	
	/* Récupération de l'ext*/
	
	$match = array();
	if (preg_match(",\.([^.]+)$,", $img, $match)){
		$ext = $match[1];
	}
	
	/* Cas particulier des .gif qui seront converti en .png*/
	if ($ext == 'gif'){
		include_spip('filtres/images_transforme');
		$img = image_format($img);	
		$ext = 'png';
	}
	/*Préparation de l'adresse final*/
	if ($chemin ==''){
		$final = md5($img).'.'.$ext;
	}
	else{
		$final = $chemin.'/'.md5($img).'.'.$ext;
	}
	zippeur_copier_fichier($img,$dossier.'/'.$final);
	return $final;

}
function lang_polyglossia($lang){
	// function permettant de convertir une #LANG en nom du package polyglossia
	$tableau = array(
		'en' => 'english',
		'es' => 'spanish',
		'fr' => 'french');
	return $tableau[$lang];		
}

function propre_latex($t) {
	
	$t = latex_echappe_coloration($t);
	
	$t = echappe_html($t); 
	
	$t = appliquer_regles_wheel($t,array('latex/latex.yaml'));
	$t = latex_traiter_modeles($t);
	$t = echappe_retour(echappe_retour($t),'latex');
	$t = appliquer_regles_wheel($t,array('latex/latex-retour.yaml'));
	return $t;
}

function latex_proteger_index($texte){
	// Function qui sert à protéger les ! et @ dans une donnée à indexer, en remplacant par \textexclam et \textat (à définir)
	$texte = str_replace('!','\textexclam{}',$texte);
	$texte = str_replace('@','\textat{}',$texte);
	return $texte;
		
}

function latex_echappe_coloration($texte){
	//var_dump($texte);
	return appliquer_regles_wheel($texte,array('latex/latex-code.yaml'));
}

function appliquer_regles_wheel($texte,$regles){
	$ruleset = SPIPTextWheelRuleset::loader(
			$regles
		);
	$wheel = new TextWheel($ruleset);
	return  $wheel->text($texte);
}

function latex_recuperer_php($t){
	
	return str_replace('&lt;?','<?',$t);
	
}	

function latex_traiter_modeles($texte) {
	/* Je reprend le code des spip2latex_traiter_modeles du plugin spip2latex/*
	include_spip('inc/lien');

	/*
	 * code, cadre/frame et math sont deja traites et sont base64-encodes
	 * On ne devrait pas les voir ici.
	 */
	$modeles_builtin = array('<sc>', '<sup>', '<sub>', '<del>', '<quote>',
				 '<cadre>', '<frame>', '<poesie>', '<poetry>',
				 '<code>', '<math>');

	$modele_regex = sprintf("@%s@is", _RACCOURCI_MODELE);
	if (preg_match_all($modele_regex, $texte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $reg) {
			
			/*
			 * Seront traites plus tard.
			 */
			if (in_array(trim($reg[0]), $modeles_builtin))
				continue;

			/*	
			 * Supprimer les echappements dans l'appel du
			 * modele.
			 * XXX seulement _ ?
			 */
			$modele = sprintf("<latex_%s", 
					  substr($reg[0], 1));
			$s = array("@\\_");
			$r = array("_");
			$modele = str_replace($s, $r, $modele);
			
			$search[] = $reg[0];
			$replace[] = $modele;
		}
	
		$texte = str_replace($search, $replace, $texte);
		$texte = traiter_modeles($texte);
	}

	return $texte;
}


?>