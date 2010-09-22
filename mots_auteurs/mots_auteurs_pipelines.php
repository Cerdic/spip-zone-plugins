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

	static $ou = array(
		'auteur_infos' => array(
			'objet' => 'auteur',
		)
	);
	
	// si on est sur une page ou il faut inserer les mots cles...
	if (in_array($flux['args']['exec'], array_keys($ou))) {

		$me = $ou[ $flux['args']['exec'] ];
		$objet = $me['objet']; // auteur
		$_id_objet = id_table_objet($objet); // id_auteur
		
		// on récupère l'identifiant de l'objet...
		if ($id_objet = $flux['args'][ $_id_objet ]) {
			$flux['data'] .= mots_objets_ajouter_selecteur_mots($objet, $id_objet, array(
				'exec_url' => $flux['args']['exec']
			));
			
		}
	}
	return $flux;
}



/**
 * Retourne le selecteur de mots pour un objet donnee
 *
 * @param string $objet : nom de l'objet
 * @param int $id_objet : identifiant de l'objet
 * @param array $opt options
 * 		@param string $cherche_mot	un mot cherché particulier
 * 		@param string $select_groupe	un/des groupe particulier ?
 * 		@param bool $editable	autorisé ou non à voir ajouter des mots
 * 		@param bool 
 * 		@param string $exec_url	url de exec de retour.
 * 
 * @return string	HTML produit.
**/
function mots_objets_ajouter_selecteur_mots($objet, $id_objet, $opt = array()) {

	if (!isset($opt['flag_editable'])) {
		$opt['flag_editable'] = autoriser('modifier', $objet, $id_objet);
	}
	// pas beau !
	if (!isset($opt['cherche_mot'])) {
		$opt['cherche_mot'] = _request('cherche_mot');
	}
	// pas beau !
	if (!isset($opt['select_groupe'])) {
		$opt['select_groupe'] = _request('select_groupe');
	}
	// pas beau !
	if (!isset($opt['exec_url'])) {
		$opt['exec_url'] = '';
	}
	
	$editer_mots = charger_fonction('editer_mots', 'inc');

	return $editer_mots(
		$objet, $id_objet,
		$opt['cherche_mot'], $opt['select_groupe'],
		$opt['flag_editable'], false, $opt['exec_url']
	);

}


// Ajout de l'objet de type auteur
function mots_auteurs_libelle_association_mots($flux){
	$flux['auteur'] = 'auteurs_mots:info_mots_auteurs_libelle_annonce';
	return $flux;
}


// Ajout du contenu aux formulaires CVT du core.
function mots_auteurs_editer_contenu_objet($flux){

	$liste = array(
		'auteurs' => _T('mots_auteurs:item_mots_cles_association_auteurs'),
	);
		
	// Concernant le formulaire CVT 'editer_groupe_mot', on veut faire apparaitre l'objet auteurs
	if ($flux['args']['type']=='groupe_mot') {
		// Si le formulaire concerne les groupes de mots-cles, alors recupere le resultat
		// de la compilation du squelette 'inc-groupe-mot-mots_objets.html' qui contient les lignes
		// a ajouter au formulaire CVT,
		$mots_objets_checkbox = '';
		foreach ($liste as $table_objet=>$nom) {
			$mots_objets_checkbox .=
				recuperer_fond('formulaires/inc-groupe-mot-mots_objets', array_merge($flux['args']['contexte'], array(
					'table' => $table_objet,
					'label' => $nom,
				)));
		}
		// que l'on insere ensuite a l'endroit approprie, a savoir avant le texte <!--choix_tables--> du formulaire
		$flux['data'] = preg_replace('%(<!--choix_tables-->)%is', $mots_objets_checkbox."\n".'$1', $flux['data']);
	}
	return $flux;
}


// compter le nombre d'auteurs sur les mots cles 
function mots_auteurs_afficher_nombre_objets_associes_a($flux){
	if ($flux['args']['objet'] == 'mot'
	  AND $id_mot = $flux['args']['id_objet'])
	{
		if ($nb = sql_countsel("spip_mots_auteurs", "id_mot=".intval($id_mot))) {
			$flux['data'][] = singulier_ou_pluriel($nb,
				"mots_auteurs:info_un_auteur",
				"mots_auteurs:info_nombre_auteurs"
			);
		}
	}
	return $flux;
}


?>
