<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/config');
include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_produit_saisies($id_produit='new', $id_rubrique=0, $retour=''){
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'obligatoire' => 'oui',
				'label' => _T('produits:produit_champ_titre_label'),
				'defaut' => _T('info_sans_titre'),
				'class' => 'multilang',
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
				'nom' => 'id_parent',
				'obligatoire' => 'oui',
				'label' => _T('produits:produit_champ_rubrique_label'),
				'defaut' => 'rubrique|'.$id_rubrique
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'prix_ht',
				'obligatoire' => 'oui',
				'label' => _T('produits:produit_champ_prix_ht_label'),
				'defaut' => 0,
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
				'explication' => _T('produits:produit_champ_taxe_explication', array('taxe'=>lire_config('produits/taxe', 0))),
				'defaut' => '' // = null
			),
			'verifier' => array(
				'type' => 'decimal',
				'options' => array(
					'min' => 0,
					'max' => 1
				)
			)
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'descriptif',
				'rows' => '3',
				'label' => _T('produits:produit_champ_descriptif_label'),
				'class' => 'multilang',
			)
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'texte',
				'label' => _T('produits:produit_champ_texte_label'),
				'class' => 'multilang',
			)
		),
	);

    if (lire_config('produits/editer_ttc')) {
        $saisie_prix_ttc = array(
		    'saisie' => 'input',
		    'options' => array(
			    'nom' => 'prix_ttc',
			    'obligatoire' => 'oui',
			    'label' => _T('produits:produit_champ_prix_ttc_label'),
			    'defaut' => 0,
		    ),
		    'verifier' => array(
			    'type' => 'decimal'
		    )
	    );
        $saisies = saisies_inserer($saisies,$saisie_prix_ttc,'prix_ht');
        $saisies = saisies_modifier($saisies,'prix_ht',array('options' => array('nouveau_type_saisie' => 'hidden')));
    }

    return $saisies;
}

function formulaires_editer_produit_charger($id_produit='new', $id_rubrique=0, $retour=''){
	$config = lire_config('produits') ;
	// Si l'insertion est limité à une rubrique, on peut déjà la passer par defaut
	if($config['limiter_ajout'] && (count($config['limiter_ident_secteur']) == 1)) {
		$id_rubrique = $config['limiter_ident_secteur'][0] ;
	}
	$contexte = formulaires_editer_objet_charger('produit', $id_produit, $id_rubrique, 0, $retour, '');
	//Si on a déjà le $id_produit il faut afficher sa rubrique!
	if($id_produit>0) $id_rubrique=sql_getfetsel('id_rubrique','spip_produits',"id_produit=".sql_quote($id_produit));
	$contexte['id_parent'] = 'rubrique|'.($contexte['id_rubrique']?$contexte['id_rubrique']:$id_rubrique);
    //Calculer le prix TTC selon le contexte
    $taxe = $contexte['taxe'] ? $contexte['taxe'] : lire_config('produits/taxe', 0);
    $precision_ttc=lire_config('produits/precision_ttc',2);
    if(!is_int($precision_ttc)) { $precision_ttc = 0; }
    $contexte['prix_ttc'] = round($contexte['prix_ht'] * (1+$taxe),$precision_ttc);
	unset($contexte['id_produit']);
	unset($contexte['id_rubrique']);
	return $contexte;
}

function formulaires_editer_produit_verifier($id_produit='new', $id_rubrique=0, $retour=''){
	$erreurs = array();
	$erreurs = formulaires_editer_objet_verifier('produit', $id_produit);
	
	$config = lire_config('produits') ;
	// Vérifier que la rubrique choisie se trouve dans les secteurs autorisés
	if($config['limiter_ajout']) {
		$id_secteur = sql_getfetsel("id_secteur", "spip_rubriques", "id_rubrique=" . intval(produits_id_parent()));
		if(is_array($config['limiter_ident_secteur']) && !in_array($id_secteur,$config['limiter_ident_secteur'])) {
			$titres = "" ;
			foreach($config['limiter_ident_secteur'] as $id_secteur) {
				$titres .= sql_getfetsel("titre", "spip_rubriques", "id_rubrique=" . intval($id_secteur))." / " ;
			}
			$erreurs['id_parent'] .= _T("produits:secteurs_autorises", array('secteurs' => $titres));
		}
	}

	return $erreurs ;
}

function formulaires_editer_produit_traiter($id_produit='new', $id_rubrique=0, $retour=''){
	set_request('id_parent', produits_id_parent());
    if (lire_config('produits/editer_ttc')) {
        $prix_ht = _request('prix_ttc') / (1+_request('taxe',lire_config('produits/taxe',0)));
        set_request('prix_ht',$prix_ht);
    }
	$retours = formulaires_editer_objet_traiter('produit',$id_produit,$id_rubrique,0,$retour);
	return $retours;
}

function produits_id_parent() {
	// On reformule l'id_parent
	$id_parent = _request('id_parent');
	// La saisie retourne normalement un tableau, dans ce cas on considére
	// la premiere valeur comme vrai parent (logique issue de polyhierarchie)
	if (is_array($id_parent))
		$id_parent = array_shift($id_parent);
	return( str_replace('rubrique|', '', $id_parent));
}

?>
