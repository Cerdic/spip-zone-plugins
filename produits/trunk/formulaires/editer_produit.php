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

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');
include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_produit_saisies_dist($id_produit = 'new', $id_rubrique = 0, $retour = '') {
	include_spip('inc/config');
	$editer_ttc = lire_config('produits/editer_ttc');
	$taxe_defaut = 100 * lire_config('produits/taxe', 0);

	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'label' => _T('produits:produit_champ_titre_label'),
				'obligatoire' => 'oui',
				'placeholder' => _T('info_sans_titre')
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reference',
				'label' => _T('produits:produit_champ_reference_label'),
			)
		),
		array(
			'saisie' => 'selecteur_rubrique',
			'options' => array(
				'nom' => 'parent',
				'label' => _T('produits:produit_champ_rubrique_label'),
				'multiple' => '0'
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => ($editer_ttc) ? 'prix_ttc' : 'prix_ht',
				'label' => ($editer_ttc) ?
				_T('produits:produit_champ_prix_ttc_label') :
				_T('produits:produit_champ_prix_ht_label'),
				'obligatoire' => 'oui'
			),
			'verifier' => array(
				'type' => 'decimal'
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'taxe',
				'label' => _T('produits:produit_champ_taxe_label'),
				'explication' => _T(
					'produits:produit_champ_taxe_explication',
					array('taxe' => $taxe_defaut.'&nbsp;&#37;')
				),
				'defaut' => $taxe_defaut,
				'inserer_fin' => '<span class="pourcent">&nbsp;&#37;</span>'
			),
			'verifier' => array(
				'type' => 'decimal'
			)
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'descriptif',
				'label' => _T('produits:produit_champ_descriptif_label'),
				'rows' => 5
			)
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'texte',
				'label' => _T('produits:produit_champ_texte_label'),
				'rows' => 7
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'conditionnement',
				'label' => _T('produits:legend_dimensions')
			),
			'saisies' => array(
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'immateriel',
						'label_case' => _T('produits:produit_champ_immateriel_label'),
						'li_class' => 'pleine_largeur',
						'valeur_oui' => '1'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'poids',
						'label' => _T('produits:produit_champ_poids_label'),
						'li_class' => 'unit size1of4',
						'afficher_si' => '@immateriel@==""'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'largeur',
						'label' => _T('produits:produit_champ_largeur_label'),
						'li_class' => 'unit size1of4',
						'afficher_si' => '@immateriel@==""'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'longueur',
						'label' => _T('produits:produit_champ_longueur_label'),
						'li_class' => 'unit size1of4',
						'afficher_si' => '@immateriel@==""'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'hauteur',
						'label' => _T('produits:produit_champ_hauteur_label'),
						'li_class' => 'unit size1of4',
						'afficher_si' => '@immateriel@==""'
					)
				)
			)
		)
	);

	return $saisies;
}

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
function formulaires_editer_produit_identifier_dist($id_produit = 'new', $id_rubrique = 0, $retour = '') {
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
function formulaires_editer_produit_charger($id_produit = 'new', $id_rubrique = 0, $retour = '', $lier_trad = 0) {
	$config = lire_config('produits') ;

	// Si pas de rubrique passée et que l'insertion est limitée à une seule rubrique, on peut déjà la passer par defaut
	if (!$id_rubrique && $config['limiter_ajout'] && (count($config['limiter_ident_secteur']) == 1)) {
		$id_rubrique = $config['limiter_ident_secteur'][0];
	}

	$contexte = formulaires_editer_objet_charger('produit', $id_produit, $id_rubrique, $lier_trad, $retour, '');

	//Si on a déjà le $id_produit il faut afficher sa rubrique!
	if ($id_produit > 0) {
		$id_rubrique = sql_getfetsel('id_rubrique', 'spip_produits', 'id_produit='.sql_quote($id_produit));
	}
	if ($id_rubrique != 0) {
		$contexte['parent'] = 'rubrique|'.($contexte['id_rubrique'] ? $contexte['id_rubrique'] : $id_rubrique);
	}

	//Calculer le prix TTC selon le contexte
	$taxe = floatval($contexte['taxe'] ? $contexte['taxe'] : lire_config('produits/taxe', 0));
	if (strlen($contexte['taxe'])) {
		$contexte['taxe'] = 100 * $contexte['taxe'];
	}
	$contexte['_taxe_defaut'] = 100 * lire_config('produits/taxe', 0);

	$precision_ttc = intval(lire_config('produits/precision_ttc', 2));
	$contexte['prix_ht'] = floatval($contexte['prix_ht']);
	$contexte['prix_ttc'] = round($contexte['prix_ht'] * (1+$taxe), $precision_ttc);

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
function formulaires_editer_produit_verifier($id_produit = 'new', $id_rubrique = 0, $retour = '', $lier_trad = 0) {
	$erreurs = array();
	$config = lire_config('produits');

	// Vérifier que la rubrique choisie se trouve dans les secteurs autorisés
	if (
		!empty($config['limiter_ajout'])
		and is_array($config['limiter_ident_secteur'])
		and $id_rubrique = produits_id_parent()
	) {
		$id_secteur = sql_getfetsel('id_secteur', 'spip_rubriques', 'id_rubrique=' . intval(produits_id_parent()));
		if (!in_array($id_secteur, $config['limiter_ident_secteur'])) {
			$titres = '';
			foreach ($config['limiter_ident_secteur'] as $id_secteur) {
				$titres .= sql_getfetsel('titre', 'spip_rubriques', 'id_rubrique=' . intval($id_secteur)).' / ';
			}
			$erreurs['parent'] .= _T('produits:secteurs_autorises', array('secteurs' => $titres));
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
function formulaires_editer_produit_traiter($id_produit = 'new', $id_rubrique = 0, $retour = '', $lier_trad = 0) {
	set_request('id_parent', produits_id_parent());
	if ($taxe = _request('taxe')) {
		set_request('taxe', $taxe/100);
	}

	if (lire_config('produits/editer_ttc')) {
		$prix_ht = _request('prix_ttc') / (1 + _request('taxe', lire_config('produits/taxe', 0)));
		set_request('prix_ht', $prix_ht);
	}

	$retours = formulaires_editer_objet_traiter('produit', $id_produit, $id_rubrique, $lier_trad, $retour);

	// Dans le cas d'une création on lie l'auteur au produit
	if(!is_numeric($id_produit)){
		include_spip('action/editer_liens');
		$id_auteur = session_get('id_auteur');
		objet_associer(array("auteur"=>$id_auteur), array("produit"=>$retours['id_produit']));
	}

	// cas d’erreur conserver la valeur de taxe saisie.
	if (!empty($retours['message_erreur'])) {
		if ($taxe = _request('taxe')) {
			set_request('taxe', $taxe*100);
		}
	}
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
	if (is_array($id_parent)) {
		$id_parent = array_shift($id_parent);
	}
	return( str_replace('rubrique|', '', $id_parent));
}
