<?php
/**
 * Pieplines utilisées par le plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Pipelines
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Insertion de la feuille de style CSS sur les pages publiques
 *
 * @pipeline insert_head_css
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function commandes_insert_head_css($flux){
	$css = find_in_path('css/commandes.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	return $flux;
}


/**
 * Optimiser la base de donnée en supprimant toutes les commandes en cours qui sont trop vieilles
 * 
 * Le délai de "péremption" est défini dans les options de configuration du plugin
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function commandes_optimiser_base_disparus($flux){
	include_spip('inc/config');
	// On cherche la date depuis quand on a le droit d'avoir fait la commande (par défaut 1h)
	$depuis = date('Y-m-d H:i:s', time() - 3600*intval(lire_config('commandes/duree_vie', 1)));
	// On récupère les commandes trop vieilles
	$commandes = sql_allfetsel(
		'id_commande',
		'spip_commandes',
		'statut = '.sql_quote('encours').' and date<'.sql_quote($depuis)
	);

	// S'il y a bien des commandes à supprimer
	if ($commandes) {
		$commandes = array_map('reset', $commandes);
		include_spip('inc/commandes');
		commandes_supprimer($commandes);
		$flux['data'] += count($commandes);
	}

	return $flux;
}


/**
 * Ajout de contenu sur certaines pages
 *
 * - Formulaires pour modifier les dates sur la fiche d'une commande
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function commandes_affiche_milieu($flux) {

	if (
		$exec = trouver_objet_exec($flux['args']['exec'])
		and $exec['edition'] == false 
		and $exec['type'] == 'commande'
		and $id_table_objet = $exec['id_table_objet']
		and (isset($flux['args'][$id_table_objet]) and $id_commande = intval($flux['args'][$id_table_objet]))
	) {
		$texte = recuperer_fond('prive/squelettes/contenu/commande_affiche_milieu',array('id_commande'=>$id_commande));
	}

	if (isset($texte)) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Ajout de contenu dans la liste des éléments en attente de validation
 *
 * - Liste des commandes aux statuts définis comme "actifs" dans les options de configuration
 *
 * @pipeline accueil_encours
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline 
 */
function commandes_accueil_encours($flux) {

	include_spip('inc/config');
	$activer = lire_config('commandes/accueil_encours');
	$statuts = lire_config('commandes/statuts_actifs');
	if ($activer and is_array($statuts)) {
		foreach($statuts as $statut){
			if ($nb_{$statut} = sql_countsel(table_objet_sql('commande'), "statut=".sql_quote($statut))) {
				$titre_{$statut} = singulier_ou_pluriel($nb_{$statut}, 'commandes:info_1_commande_statut_'.$statut, 'commandes:info_nb_commandes_statut_'.$statut);
				$texte .= recuperer_fond('prive/objets/liste/commandes', array(
					'titre' => $titre_{$statut},
					'statut' => $statut,
					'cacher_tri' => true,
					'nb' => 5),
					array('ajax' => true)
				);
			}
		}
	}

	if (isset($texte)) {
		$flux .= $texte;
	}

	return $flux;
}


/**
 * Ajout de liste sur la vue d'un auteur
 *
 * - Liste des commandes de l'auteur
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function commandes_affiche_auteurs_interventions($flux) {

	if ($id_auteur = intval($flux['args']['id_auteur'])) {
		$texte .= recuperer_fond('prive/objets/liste/commandes', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('commandes:titre_commandes_auteur'),
			'cacher_tri' => true
			),
			array('ajax' => true)
		);
	}
	if (isset($texte)) {
		$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Compléter la liste des types d'adresses du plugin Coordonnées
 *
 * Ajout de 2 types d'adresses : facturation et livraison
 *
 * @pipeline types_coordonnees
 * @param  array $liste Données du pipeline
 * @return array        Données du pipeline
**/
function commandes_types_coordonnees($liste) {

	$types_adresses = $liste['adresse'];
	if (!$types_adresses or !is_array($types_adresses)) $types_adresses = array();

	// on définit les couples types + chaînes de langue à ajouter
	$types_adresses_commandes = array(
		'livraison' => _T('commandes:type_adresse_livraison'),
		'facturation' => _T('commandes:type_adresse_facturation')
	);
	// on les rajoute à la liste des types des adresses
	$liste['adresse'] = array_merge($types_adresses, $types_adresses_commandes);

	return $liste;
}


?>
