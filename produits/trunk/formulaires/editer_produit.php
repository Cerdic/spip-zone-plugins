<?php
/**
 * Gestion du formulaire de d'édition de produit
 *
 * @plugin     Produits
 * @copyright  2015
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Produits\Formulaires
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/config');
include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_produit
 *     Identifiant du produit. 'new' pour un nouveau produit.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_produit_identifier_dist($id_produit='new', $id_rubrique=0, $retour=''){
	return serialize(array(intval($id_produit)));
}

/**
 * Chargement du formulaire d'édition de produit
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_produit
 *     Identifiant du produit. 'new' pour un nouveau produit.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_produit_charger($id_produit='new', $id_rubrique=0, $retour=''){
	$config = lire_config('produits') ;

	// Si l'insertion est limitée à une rubrique, on peut déjà la passer par defaut
	if($config['limiter_ajout'] && (count($config['limiter_ident_secteur']) == 1)) {
		$id_rubrique = $config['limiter_ident_secteur'][0] ;
	}
	$contexte = formulaires_editer_objet_charger('produit',$id_produit,$id_rubrique,$lier_trad=0,$retour,'');

	//Si on a déjà le $id_produit il faut afficher sa rubrique!
	if($id_produit>0) $id_rubrique=sql_getfetsel('id_rubrique','spip_produits',"id_produit=".sql_quote($id_produit));
	$contexte['parent'] = 'rubrique|'.($contexte['id_rubrique']?$contexte['id_rubrique']:$id_rubrique);

	//Calculer le prix TTC selon le contexte
	$taxe = $contexte['taxe'] ? $contexte['taxe'] : lire_config('produits/taxe', 0);
	if (strlen($contexte['taxe'])){
		$contexte['taxe'] = 100 * $contexte['taxe'];
	}
	$contexte['_taxe_defaut'] = 100 * lire_config('produits/taxe', 0);
	$precision_ttc=lire_config('produits/precision_ttc',2);
	if(!is_int($precision_ttc)) { $precision_ttc = 0; }
	$contexte['prix_ttc'] = round($contexte['prix_ht'] * (1+$taxe),$precision_ttc);

	unset($contexte['id_produit']);
	unset($contexte['id_rubrique']);

	return $contexte;
}

/**
 * Vérifications du formulaire d'édition de produit
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_produit
 *     Identifiant du produit. 'new' pour un nouveau produit.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_produit_verifier($id_produit='new', $id_rubrique=0, $retour=''){
	$erreurs = array();
	$config = lire_config('produits');

	$erreurs = formulaires_editer_objet_verifier('produit', $id_produit);

	$verifier = charger_fonction('verifier','inc');
	$champ_prix = 'prix_ht';
	if (isset($config['editer_ttc']) AND $config['editer_ttc']){
		$champ_prix = 'prix_ttc';
	}

	if ($err=$verifier(_request($champ_prix),'decimal')){
		$erreurs[$champ_prix] = $err;
	}

	if ($err=$verifier(_request('taxe'),'decimal',array('min' => 0,'max' => 100))){
		$erreurs['taxe'] = $err;
	}

	// Vérifier que la rubrique choisie se trouve dans les secteurs autorisés
	if(isset($config['limiter_ajout']) AND $config['limiter_ajout']) {
		$id_secteur = sql_getfetsel("id_secteur", "spip_rubriques", "id_rubrique=" . intval(produits_id_parent()));
		if(is_array($config['limiter_ident_secteur']) && !in_array($id_secteur,$config['limiter_ident_secteur'])) {
			$titres = "" ;
			foreach($config['limiter_ident_secteur'] as $id_secteur) {
				$titres .= sql_getfetsel("titre", "spip_rubriques", "id_rubrique=" . intval($id_secteur))." / " ;
			}
			$erreurs['parent'] .= _T("produits:secteurs_autorises", array('secteurs' => $titres));
		}
	}

	return $erreurs ;
}

/**
 * Traitement du formulaire d'édition de produit
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_produit
 *     Identifiant du produit. 'new' pour un nouveau produit.
 * @param int $id_rubrique
 *     Identifiant de la rubrique parente (si connue)
 * @param string $retour
 *     URL de redirection après le traitement
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_produit_traiter($id_produit='new', $id_rubrique=0, $retour=''){
	set_request('id_parent', produits_id_parent());
	if ($taxe = _request('taxe')){
		set_request('taxe',$taxe/100);
	}
	if (lire_config('produits/editer_ttc')) {
		$prix_ht = _request('prix_ttc') / (1+_request('taxe',lire_config('produits/taxe',0)));
		set_request('prix_ht',$prix_ht);
	}
	$retours = formulaires_editer_objet_traiter('produit',$id_produit,$id_rubrique,$lier_trad=0,$retour);
	return $retours;
}

/**
 * Fonction qui retourne l'identifiant de la rubrique choisie via le sélecteur de rubrique
 *
 * Le sélecteur retourne un tableau : `array('rubrique|10')`
 *
 * @return int
 *     Identifiant de la rubrique
 */
function produits_id_parent() {
	// On reformule l'id_parent
	$id_parent = _request('parent');
	// La saisie retourne normalement un tableau, dans ce cas on considére
	// la premiere valeur comme vrai parent (logique issue de polyhierarchie)
	if (is_array($id_parent))
		$id_parent = array_shift($id_parent);
	return( str_replace('rubrique|', '', $id_parent));
}

?>
