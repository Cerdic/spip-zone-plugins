<?php
/**
 * Plugin mots-objets pour Spip 2.0
 * Licence GPL 
 * Adaptation Cyril MARION - (c) 2010 Ateliers CYM http://www.cym.fr
 * Grâce au soutien actif de Matthieu Marcillaud - Magraine
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gouverneur_de_mots');
 


/**
 * Ajout du bloc d'attribution de mot-clé
 * sur la page de visualisation d'un auteur
**/
function mots_objets_affiche_milieu($flux) {

	static $ou = false;
	if ($ou === false) {
		$ou = mots_objets_get_affiche_milieu();
	}

	// afficher les listes de nouveaux objets sur la page de mots
	if ($flux['args']['exec'] == 'mots_edit' AND !_request('edit')) {
		foreach (gouverneur_de_mots() as $objet=>$desc) {
			if (find_in_path("prive/listes/mots_$objet." . _EXTENSION_SQUELETTES)) {
				$flux['data'] .= recuperer_fond("prive/listes/mots_$objet", array(
					'id_mot' => $flux['args']['id_mot'],
					'titre' => _T($desc->titre_mot_objets),
				), array('ajax' => true));
			}
		}
	}

	// afficher le formulaire de liaison de mots sur les nouveaux objets
	// si on est sur une page ou il faut inserer les mots cles...
	if (in_array($flux['args']['exec'], array_keys($ou))) {

		$me = $ou[ $flux['args']['exec'] ];

		// on récupère l'identifiant de l'objet...
		if ($id_objet = $flux['args'][ $me->_id_objet ]
		OR  $id_objet = _request($me->_id_objet) ) {
			$flux['data'] .= mots_objets_ajouter_selecteur_mots($me->objet, $id_objet, array(
				'exec_url' => $flux['args']['exec']
			));
			
		}
	}

	// pour selecteur generique de Grappes... (toutati)
	if (defined('_DIR_PLUGIN_GRAPPES')) {
		$flux = mots_objets_ajouter_selecteur_mots_grappes($flux);
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


/**
 * (toutati [41274] et suivants)
 * 
 * Ajoute un selecteur générique d'auteur ou document sur la page mots
 * (avec le plugin grappes)
 *
 * @param 
 * @return 
**/
function mots_objets_ajouter_selecteur_mots_grappes($flux) {
	///alm
	if ($exec = $flux['args']['exec']) {
		switch ($exec){
			case 'mots_edit':
				$source = 'mots';
				$id_source = $flux['args']['id_mot'];
				break;
			default:
				$source = $id_source = '';
				break;
		}
		if ($source && $id_source) {
		// grappes recup du code +prive/lister_objets.html +prive/inc-lister-auteurs.html
						$lister_objet = charger_fonction('grappes_lister_objets','inc');

			
			//On affiche la liste des auteurs liŽs ou des documents sur la page du mot-clef (si auteur liŽ ˆ groupe)
			if($source == 'mots') {
				$plusource='documents';
				$parent = sql_fetsel('id_groupe','spip_mots',"id_mot=$id_source");
				$parent = $parent['id_groupe']; 
				$res = sql_allfetsel('id_groupe,titre','spip_groupes_mots',"tables_liees REGEXP '(^|,)$plusource($|,)' AND id_groupe=$parent");
				//retourne 1 seul groupe ou rien
				foreach($res as $row) {
				$flux['data'] .= $lister_objet($plusource,$source,$id_source);
				}
				
				$plusource='auteurs';
				$parent = sql_fetsel('id_groupe','spip_mots',"id_mot=$id_source");
				$parent = $parent['id_groupe']; 
				$res = sql_allfetsel('id_groupe,titre','spip_groupes_mots',"tables_liees REGEXP '(^|,)$plusource($|,)' AND id_groupe=$parent");
				//retourne 1 seul groupe ou rien
				foreach($res as $row) {
				 $flux['data'] .= $lister_objet('auteurs',$source,$id_source);	
				}
				
					
			}
			
		}
	 }
	 return $flux;
}




// Ajout de l'objet de type auteur
function mots_objets_libelle_association_mots($flux){
	$objets_mots = gouverneur_de_mots();
	foreach ($objets_mots as $objet) {
		if ($objet->libelle_objet) {
			$flux[ $objet->nom ] = _T( $objet->libelle_objet );
		}
	}
	return $flux;
}


// Ajout du contenu aux formulaires CVT du core.
function mots_objets_editer_contenu_objet($flux){

	// Concernant le formulaire CVT 'editer_groupe_mot', on veut faire apparaitre l'objet auteurs
	if ($flux['args']['type']=='groupe_mot') {

		$liste = gouverneur_de_mots();
		
		// Si le formulaire concerne les groupes de mots-cles, alors recupere le resultat
		// de la compilation du squelette 'inc-groupe-mot-mots_objets.html' qui contient les lignes
		// a ajouter au formulaire CVT,
		$mots_objets_checkbox = '';
		foreach ($liste as $objet) {
			$mots_objets_checkbox .=
				recuperer_fond('formulaires/inc-groupe-mot-mots_objets', array_merge($flux['args']['contexte'], array(
					'table' => $objet->nom,
					'label' => _T( $objet->libelle_liaisons_objets ),
				)));
		}
		// que l'on insere ensuite a l'endroit approprie, a savoir avant le texte <!--choix_tables--> du formulaire
		$flux['data'] = preg_replace('%(<!--choix_tables-->)%is', $mots_objets_checkbox."\n".'$1', $flux['data']);
	}
	return $flux;
}



// compter le nombre d'auteurs sur les mots cles 
function mots_objets_afficher_nombre_objets_associes_a($flux){
	
	if ($flux['args']['objet'] == 'mot'
	  AND $id_mot = $flux['args']['id_objet'])
	{
		$liste = gouverneur_de_mots();
		
		foreach ($liste as $objet) {
			if ($nb = sql_countsel('spip_mots_' . $objet->nom, "id_mot=".intval($id_mot))) {
				$flux['data'][] = singulier_ou_pluriel($nb,
					_T( $objet->singulier ),
					_T( $objet->pluriel )
				);
			}
		}

	}
	return $flux;
}


/**
 * Enlever les liaisons d'objets qui ont ete supprimes 
**/
function mots_objets_optimiser_base_disparus($flux){
	
	$objets_mots = gouverneur_de_mots();
	
	foreach ($objets_mots as $objet) {
		$liaison = 'spip_mots_' . $objet->nom;
		$_id = $objet->_id_objet;
		
		# les liens de mots-objets affectes a des objets effaces
		$res = sql_select(
			"L.$_id AS id",
			"$liaison AS L LEFT JOIN {$objet->table_sql} AS O ON L.$_id = O.$_id",
			"O.$_id IS NULL");

		$flux['data'] += optimiser_sansref($liaison, $_id, $res);


		# les liens de mots-objets sur des mots effaces
		$res = sql_select(
			"L.id_mot AS id",
			"$liaison AS L LEFT JOIN spip_mots AS mots ON L.id_mot = mots.id_mot",
			"mots.id_mot IS NULL");

		$n+= optimiser_sansref($liaison, 'id_mot', $res);
	}
	
	return $flux;
}


?>
