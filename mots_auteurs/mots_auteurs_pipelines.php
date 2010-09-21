<?php
/**
 * Plugin mots-auteurs pour Spip 2.0
 * Licence GPL 
 * Adaptation Cyril MARION - (c) 2010 Ateliers CYM http://www.cym.fr
 * Grâce au soutien actif de Matthieu Marcillaud - Magraine
 *
 */



/**
 * Ajout du bloc d'attribution de mot-clé
 * sur la page de visualisation d'un auteur
**/
function mots_auteurs_affiche_milieu($flux) {

	// si on est sur une page d'info d'un auteur
	if ($flux['args']['exec'] == 'auteur_infos') {
	
		// on récupère l'auteur en cours, et si on le récupère correctement...
		if ($id_auteur = $flux['args']['id_auteur']) {
			$contexte = array(
				'objet' => 'auteur',
				'id_objet' => $id_auteur
			);
			
			// ...on ajoute la boite de séletion des mots-clé dans le flux html
			$flag_editable = autoriser('modifier', 'auteur', $id_auteur);
			$editer_mots = charger_fonction('editer_mots', 'inc');
			$flux['data'] .= $editer_mots('auteur', $id_auteur, $cherche_mot, $select_groupe, $flag_editable, false, 'auteurs');

		}
	}
	return $flux;
}


// Ajout de l'objet de type auteur
function mots_auteurs_libelle_association_mots($flux){
	$flux['auteur'] = 'auteurs_mots:info_mots_auteurs_libelle_annonce';
	return $flux;
}


// Ajout du contenu aux formulaires CVT du core.
function mots_auteurs_editer_contenu_objet($flux){
	// Concernant le formulaire CVT 'editer_groupe_mot', on veut faire apparaitre l'objet auteurs
	if ($flux['args']['type']=='groupe_mot') {
		// Si le formulaire concerne les groupes de mots-cles, alors recupere le resultat
		// de la compilation du squelette 'inc-groupe-mot-mots_auteurs.html' qui contient les lignes
		// a ajouter au formulaire CVT,
		$mots_auteurs_gp_objets = recuperer_fond('formulaires/inc-groupe-mot-mots_auteurs', $flux['args']['contexte']);
		// que l'on insere ensuite a l'endroit approprie, a savoir avant le texte <!--choix_tables--> du formulaire
		$flux['data'] = preg_replace('%(<!--choix_tables-->)%is', $mots_auteurs_gp_objets."\n".'$1', $flux['data']);
	}
	return $flux;
}

?>