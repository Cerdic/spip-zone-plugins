<?php

// Pipeline pour l'entete des pages de l'espace prive
function vu_header_prive($flux)
{
	// Insertion dans l'entete des pages 'vu' d'un appel la feuille de style dediee
	$flux .= "<link rel='stylesheet' href='"._DIR_VU_PRIVE."vu_style_prive.css' type='text/css' media='all' />\n";

	return $flux;
}

// Pipeline pour ajouter du contenu aux formulaires CVT du core
function vu_editer_contenu_objet($flux){
	// Concernant le formulaire CVT 'editer_groupe_mot', on veut faire apparaitre les nouveaux objets
	if ($flux['args']['type']=='groupe_mot') {
		// Si le formulaire concerne les groupes de mots-cles, alors recupere le resultat
		// de la compilation du squelette 'inc-groupe-mot-vu.html' qui contient les lignes
		// a ajouter au formulaire CVT,
		$vu_gp_objets = recuperer_fond('formulaires/inc-groupe-mot-vu', $flux['args']['contexte']);
		// que l'on insere ensuite a l'endroit approprie, a savoir avant le texte <!--choix_tables--> du formulaire
		$flux['data'] = preg_replace('%(<!--choix_tables-->)%is', $vu_gp_objets."\n".'$1', $flux['data']);
	}
	return $flux;
}


?>
