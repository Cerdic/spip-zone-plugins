<?php


function vu_header_prive($flux){
	$flux = vu_insert_head($flux);
	return $flux;
}

// Pipeline. Pour l'entete des pages de l'espace prive
function vu_insert_head($flux)
{
	// Insertion dans l'entete des pages 'vu' d'un appel la feuille de style dediee
	$flux .= "<link rel='stylesheet' href='"._DIR_VU_PRIVE."vu_style_prive.css' type='text/css' media='all' />\n";

	return $flux;
}

// Pipeline. Pour ajouter du contenu aux formulaires CVT du core.
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

// Pipeline. Pour associer un libelle (etiquette) aux types d'objets.
// Dans la page listant tous les groupes de mots (exec/mots_tous),
// il est indique pour chacun d'entre eux les objets sur lesquels 
// ils s'appliquent (ex : '> Articles'). Pour que cela fonctionne,
// il est necessaire que ces objets aient un libelle, au risque sinon
// d'afficher '> info_table'. 
function vu_libelle_association_mots($flux){
	// On recupere le flux, ici le tableau des libelles,
	// et on ajoute nos trois objets.
	$flux['vu_annonces'] = 'vu:info_vu_libelle_annonce';
	$flux['vu_evenements'] = 'vu:info_vu_libelle_evenement';
	$flux['vu_publications'] = 'vu:info_vu_libelle_publication';

	return $flux;
}

// Pipeline. Pour permettre la recherche dans les nouveaux objets
function vu_rechercher_liste_des_champs($tables){
	// Prendre en compte les champs des annonces
	$tables['vu_annonce']['titre'] = 3;
	$tables['vu_annonce']['annonceur'] = 3;
	$tables['vu_annonce']['type'] = 3;
	$tables['vu_annonce']['descriptif'] = 3;
	$tables['vu_annonce']['source_nom'] = 3;
	// Prendre en compte les champs des evenements
	$tables['vu_evenement']['titre'] = 3;
	$tables['vu_evenement']['organisateur'] = 3;
	$tables['vu_evenement']['lieu_evenement'] = 3;
	$tables['vu_evenement']['type'] = 3;
	$tables['vu_evenement']['descriptif'] = 3;
	$tables['vu_evenement']['source_nom'] = 3;
	// Prendre en compte les champs des publications
	$tables['vu_publication']['titre'] = 3;
	$tables['vu_publication']['auteur'] = 3;
	$tables['vu_publication']['editeur'] = 3;
	$tables['vu_publication']['type'] = 3;
	$tables['vu_publication']['descriptif'] = 3;
	$tables['vu_publication']['source_nom'] = 3;

	return $tables;
}

?>
