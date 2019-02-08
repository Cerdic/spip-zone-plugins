<?php
/**
 * Ce fichier contient les constantes et les fonctions de l'API du plugin Taxonomie non utilisées dans les squelettes.
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


$GLOBALS['_taxonomie']['regnes'] = array('animalia', 'plantae', 'fungi');

if (!defined('_TAXONOMIE_TYPE_RANG_PRINCIPAL')) {
	/**
	 * Type de rang selon la nomenclature taxonomique.
	 */
	define('_TAXONOMIE_TYPE_RANG_PRINCIPAL', 'principal');
}
if (!defined('_TAXONOMIE_TYPE_RANG_SECONDAIRE')) {
	/**
	 * Type de rang selon la nomenclature taxonomique.
	 */
	define('_TAXONOMIE_TYPE_RANG_SECONDAIRE', 'secondaire');
}
if (!defined('_TAXONOMIE_TYPE_RANG_INTERCALAIRE')) {
	/**
	 * Type de rang selon la nomenclature taxonomique.
	 */
	define('_TAXONOMIE_TYPE_RANG_INTERCALAIRE', 'intercalaire');
}

// TODO : vérifier les rangs stirp, morph, aberration, unspecified.
// TODO : vérifier pourquoi le rang serie n'est pas dans la liste de ITIS
$GLOBALS['_taxonomie']['rangs'] = array(
	'kingdom'       => array('type' => _TAXONOMIE_TYPE_RANG_PRINCIPAL, 'est_espece' => false, 'synonyme' => ''),
	'subkingdom'    => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'infrakingdom'  => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'superphylum'   => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => 'superdivision'),
	'phylum'        => array('type' => _TAXONOMIE_TYPE_RANG_PRINCIPAL, 'est_espece' => false, 'synonyme' => 'division'),
	'subphylum'     => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => 'subdivision'),
	'infraphylum'   => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => 'infradivision'),
	'superdivision' => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => 'superphylum'),
	'division'      => array('type' => _TAXONOMIE_TYPE_RANG_PRINCIPAL, 'est_espece' => false, 'synonyme' => 'phylum'),
	'subdivision'   => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => 'subphylum'),
	'infradivision' => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => 'infraphylum'),
	'superclass'    => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'class'         => array('type' => _TAXONOMIE_TYPE_RANG_PRINCIPAL, 'est_espece' => false, 'synonyme' => ''),
	'subclass'      => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'infraclass'    => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'superorder'    => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'order'         => array('type' => _TAXONOMIE_TYPE_RANG_PRINCIPAL, 'est_espece' => false, 'synonyme' => ''),
	'suborder'      => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'infraorder'    => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'section'       => array('type' => _TAXONOMIE_TYPE_RANG_SECONDAIRE, 'est_espece' => false, 'synonyme' => ''),
	'subsection'    => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'superfamily'   => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'family'        => array('type' => _TAXONOMIE_TYPE_RANG_PRINCIPAL, 'est_espece' => false, 'synonyme' => ''),
	'subfamily'     => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'tribe'         => array('type' => _TAXONOMIE_TYPE_RANG_SECONDAIRE, 'est_espece' => false, 'synonyme' => ''),
	'subtribe'      => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'genus'         => array('type' => _TAXONOMIE_TYPE_RANG_PRINCIPAL, 'est_espece' => false, 'synonyme' => ''),
	'subgenus'      => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'species'       => array('type' => _TAXONOMIE_TYPE_RANG_PRINCIPAL, 'est_espece' => true, 'synonyme' => ''),
	'subspecies'    => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => true, 'synonyme' => ''),
	'variety'       => array('type' => _TAXONOMIE_TYPE_RANG_SECONDAIRE, 'est_espece' => true, 'synonyme' => ''),
	'subvariety'    => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => true, 'synonyme' => ''),
	'form'          => array('type' => _TAXONOMIE_TYPE_RANG_SECONDAIRE, 'est_espece' => true, 'synonyme' => ''),
	'subform'       => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => true, 'synonyme' => ''),
	'race'          => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => true, 'synonyme' => 'variety'),
	'stirp'         => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => true, 'synonyme' => ''),
	'morph'         => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => true, 'synonyme' => ''),
	'aberration'    => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => true, 'synonyme' => ''),
	'unspecified'   => array('type' => _TAXONOMIE_TYPE_RANG_INTERCALAIRE, 'est_espece' => true, 'synonyme' => '')
);

if (!defined('_TAXONOMIE_RANG_REGNE')) {
	/**
	 * Nom anglais du rang principal `règne`.
	 */
	define('_TAXONOMIE_RANG_REGNE', 'kingdom');
}
if (!defined('_TAXONOMIE_RANG_GENRE')) {
	/**
	 * Nom anglais du rang principal `genre`.
	 */
	define('_TAXONOMIE_RANG_GENRE', 'genus');
}
if (!defined('_TAXONOMIE_RANG_ESPECE')) {
	/**
	 * Nom anglais du rang principal `espèce`.
	 */
	define('_TAXONOMIE_RANG_ESPECE', 'species');
}

if (!defined('_TAXONOMIE_LANGUES_POSSIBLES')) {
	/**
	 * Liste des langues utilisables pour les noms communs et les textes des taxons.
	 */
	define('_TAXONOMIE_LANGUES_POSSIBLES', 'fr:en:es:pt:de:it');
}


// -----------------------------------------------------------------------
// ---------------------- API GESTION DES REGNES -------------------------
// -----------------------------------------------------------------------

/**
 * Charge tous les taxons d'un règne donné fourni dans le fichier ITIS, du règne lui-même jusqu'aux taxons de genre.
 * Les nom communs anglais, français, espagnols, etc, peuvent aussi être chargés en complément mais
 * ne couvrent pas l'ensemble des taxons.
 * Le modifications effectuées manuellement sur ces taxons sont conservées.
 *
 * @package SPIP\TAXONOMIE\REGNE
 *
 * @api
 *
 * @uses regne_existe()
 * @uses taxon_preserver()
 * @uses regne_vider()
 * @uses itis_read_hierarchy()
 * @uses itis_find_language()
 * @uses itis_read_vernaculars()
 *
 * @param string $regne
 *        Nom scientifique du règne en lettres minuscules : `animalia`, `plantae`, `fungi`.
 * @param array  $codes_langue
 *        Tableau des codes des langues (au sens SPIP) à charger pour les noms communs des taxons.
 *
 * @return bool
 *        `true` si le chargement a réussi, `false` sinon
 */
function regne_charger($regne, $codes_langue = array()) {

	$retour = false;
	$taxons_preserves = array();

	// Vérifie si le règne existe bien dans la table spip_taxons
	$regne_existe = regne_existe($regne, $meta_regne);
	if ($regne_existe) {
		// Sauvegarde des taxons ayant été modifiés manuellement suite à leur création automatique.
		$taxons_preserves = taxon_preserver($regne);

		// Vider le règne avant de le recharger
		regne_vider($regne);
	}

	// Lire le fichier json fournissant la hiérarchie des rangs du règne en cours de chargement.
	$meta_regne = array();
	include_spip('services/itis/itis_api');
	$meta_regne['rangs']['hierarchie'] = itis_read_ranks($regne, $meta_regne['rangs']['sha']);

	// Lecture de la hiérarchie des taxons à partir du fichier texte extrait de la base ITIS
	$taxons = itis_read_hierarchy($regne, $meta_regne['rangs']['hierarchie'], $meta_regne['sha']);

	// Ajout des noms communs extraits de la base ITIS dans la langue demandée
	if ($taxons) {
		$meta_regne['compteur'] = count($taxons);
		$traductions = array();
		foreach ($codes_langue as $_code_langue) {
			$langue = itis_find_language($_code_langue);
			if ($langue) {
				$noms = itis_read_vernaculars($langue, $sha_langue);
				if ($noms) {
					$meta_regne['traductions']['itis'][$_code_langue]['sha'] = $sha_langue;
					$nb_traductions_langue = 0;
					foreach ($noms as $_tsn => $_nom) {
						if (array_key_exists($_tsn, $taxons)) {
							// On ajoute les traductions qui sont de la forme [xx]texte
							// On sauvegarde le tsn concerné afin de clore les traductions
							// avec les balises multi et d'optimiser ainsi les traitements
							// sachant qu'il y a très peu de traductions comparées aux taxons
							$taxons[$_tsn]['nom_commun'] .= $_nom;
							$nb_traductions_langue += 1;
							$traductions[$_tsn] = $_tsn;
						}
					}
					$meta_regne['traductions']['itis'][$_code_langue]['compteur'] = $nb_traductions_langue;
				}
			}
		}

		// Clore les traductions avec les balises multi
		if ($traductions) {
			foreach ($traductions as $_tsn) {
				$taxons[$_tsn]['nom_commun'] = '<multi>' . $taxons[$_tsn]['nom_commun'] . '</multi>';
			}
		}

		// Ré-injection des modifications manuelles effectuées sur les taxons importés via le fichier ITIS du règne.
		// -- descriptif, texte, sources: remplacement
		// -- nom commun: merge en considérant que la mise à jour manuelle est prioritaire
		// -- edite: positionné à 1, on conserve bien sur l'indicateur d'édition
		if (!empty($taxons_preserves['edites'])) {
			foreach ($taxons_preserves['edites'] as $_taxon_edite) {
				if (($tsn = $_taxon_edite['tsn']) and (array_key_exists($tsn, $taxons))) {
					$taxons[$tsn]['descriptif'] = $_taxon_edite['descriptif'];
					$taxons[$tsn]['texte'] = $_taxon_edite['texte'];
					$taxons[$tsn]['sources'] = $_taxon_edite['sources'];
					$taxons[$tsn]['nom_commun'] = taxon_merger_traductions(
						$_taxon_edite['nom_commun'],
						$taxons[$tsn]['nom_commun']);
					$taxons[$tsn]['edite'] = 'oui';
				}
			}
		}

		// On formate le taxon pour l'insertion en BD.
		$taxons = array_values($taxons);
		spip_log("Insertion règne `${regne}` - nombre de taxons : " . count($taxons), 'taxonomie');

		// Insertion dans la base de données
		$retour = sql_insertq_multi('spip_taxons', $taxons);
		if ($retour) {
			// Insérer les informations de chargement dans une meta propre au règne.
			// Ca permettra de tester l'utilité ou pas d'un rechargement du règne
			$meta_regne['maj'] = date('Y-m-d H:i:s');
			$meta_regne['fichier'] = "${regne}_genus.txt";

			// Mise à jour de la meta du règne.
			include_spip('inc/config');
			ecrire_config("taxonomie_$regne", $meta_regne);
		}
	}

	return $retour;
}


/**
 * Supprime de la base de données tous les taxons importés à partir du rapport hiérarchique d'un règne donné.
 * La meta concernant les informations de chargement du règne est aussi effacée.
 * Les modifications manuelles effectuées sur ces taxons sont effacées : elles doivent donc être préservées au préalable.
 *
 * @package SPIP\TAXONOMIE\REGNE
 *
 * @api
 *
 * @param string $regne
 *        Nom scientifique du règne en lettres minuscules : `animalia`, `plantae`, `fungi`.
 *
 * @return bool
 *        `true` si le vidage a réussi, `false` sinon
 */
function regne_vider($regne) {

	$where = array('regne=' . sql_quote($regne), 'importe=' . sql_quote('oui'));
	$retour = sql_delete('spip_taxons', $where);
	if ($retour !== false) {
		// Supprimer la meta propre au règne.
		effacer_meta("taxonomie_$regne");
		$retour = true;
	}

	return $retour;
}


/**
 * Retourne l'existence ou pas d'un règne en base de données.
 * La fonction scrute les taxons importés de la table `spip_taxons` et non la meta propre au règne.
 *
 * @package SPIP\TAXONOMIE\REGNE
 *
 * @api
 *
 * @param string $regne
 *        Nom scientifique du règne en lettres minuscules : `animalia`, `plantae`, `fungi`.
 * @param array  $meta_regne
 *        Meta propre au règne, créée lors du chargement de celui-ci et retournée si le règne
 *        existe.
 *
 * @return bool
 *        `true` si le règne existe, `false` sinon.
 */
function regne_existe($regne, &$meta_regne) {

	$meta_regne = array();
	$existe = false;

	$where = array('regne=' . sql_quote($regne), 'importe=' . sql_quote('oui'));
	$retour = sql_countsel('spip_taxons', $where);
	if ($retour) {
		// Récupérer la meta propre au règne afin de la retourner.
		include_spip('inc/config');
		$meta_regne = lire_config("taxonomie_$regne");
		$existe = true;
	}

	return $existe;
}

/**
 * Renvoie la liste des règnes supportés par le plugin.
 *
 * @package SPIP\TAXONOMIE\REGNE
 *
 * @api
 *
 * @return array
 *        Liste des noms scientifiques en minuscules des règnes supportés.
 */
function regne_lister() {

	return $GLOBALS['_taxonomie']['regnes'];
}


/**
 * Renvoie le type de rang principal, secondaire ou intercalaire.
 *
 * @package SPIP\TAXONOMIE\RANG
 *
 * @api
 *
 * @param string $rang
 *        Nom anglais du rang en minuscules.
 *
 * @return string
 *        `principal`, `secondaire` ou `intercalaire` si le rang est valide, chaine vide sinon.
 */
function rang_informer_type($rang) {

	// Initialisation à chaine vide pour le cas où le rang n'est pas dans la liste des rangs admis.
	$type = '';

	if (!empty($GLOBALS['_taxonomie']['rangs'][$rang])) {
		$type = $GLOBALS['_taxonomie']['rangs'][$rang]['type'];
	}

	return $type;
}


/**
 * Détermine si un rang est celui d'une espèce ou d'un taxon de rang inférieur.
 *
 * @package SPIP\TAXONOMIE\RANG
 *
 * @api
 *
 * @param string $rang
 *        Nom anglais du rang en minuscules.
 *
 * @return bool
 *        `true` si le rang est celui d'une espèce ou d'un taxon de rang inférieur, `false` sinon.
 */
function rang_est_espece($rang) {

	// Initialisation à false pour le cas où le rang n'est pas dans la liste des rangs admis.
	$est_espece = false;

	if (!empty($GLOBALS['_taxonomie']['rangs'][$rang])) {
		$est_espece = $GLOBALS['_taxonomie']['rangs'][$rang]['est_espece'];
	}

	return $est_espece;
}


// -----------------------------------------------------------------------
// ---------------------- API GESTION DES TAXONS -------------------------
// -----------------------------------------------------------------------

/**
 * Extrait, de la table `spip_taxons`, la liste des taxons non espèce d'un règne donné - importés via un fichier ITIS -
 * ayant fait l'objet d'une modification manuelle et la liste des taxons non espèce créés lors de l'ajout d'une espèce
 * et donc non importés avec le fichier ITIS.
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 *
 * @param string $regne
 *        Nom scientifique du règne en lettres minuscules : `animalia`, `plantae`, `fungi`.
 *
 * @return array
 *        Liste des taxons modifiées manuellement et créés suite à l'ajout d'une espèce.
 *        Chaque élément de la liste est un tableau composé, pour les taxons modifiés manuellement des index
 *        `tsn`, `nom_commun`, `descriptif` et pour les taxons créés via une espèce de tous les champs de l'objet
 *        taxon, à l'exception de l'id (`id_taxon`) et de la date de mise à jour (`maj`).
 */
function taxon_preserver($regne) {

	// Récupération de la description de la table spip_taxons afin de connaitre la liste des colonnes.
	include_spip('base/objets');
	$description_table = lister_tables_objets_sql('spip_taxons');

	// Récupération de la liste des taxons importés via le fichier ITIS du règne concerné et édités manuellement.
	// Ces champs éditables (nom_commun, descriptif, texte et sources) seront réinjectés après le chargement du règne
	// via un update.
	$from = array('spip_taxons');
	$select = array_merge($description_table['champs_editables'], array('tsn'));
	$where = array(
		'regne=' . sql_quote($regne),
		'edite=' . sql_quote('oui'),
		'importe=' . sql_quote('oui'),
		'espece=' . sql_quote('non')
	);
	$taxons['edites'] = sql_allfetsel($select, $from, $where);

	// Récupération de la liste des taxons non importés via le fichier ITIS du règne concerné mais créés lors de l'ajout
	// d'une espèce.
	// Ces taxons préservés uniquement pour le besoin de l'exportation par IEConfig car il ne sont pas effacés
	// lors du rechargement du règne.
	// -- on récupère tous les champs du taxons sauf ceux qui seront mis à jour automatique lors de l'insertion de
	//    l'objet en BD (id_taxon, maj).
	$select = array_diff(array_keys($description_table['field']), array('id_taxon', 'maj'));
	$where = array(
		'regne=' . sql_quote($regne),
		'importe=' . sql_quote('non'),
		'espece=' . sql_quote('non')
	);
	$taxons['crees'] = sql_allfetsel($select, $from, $where);

	return $taxons;
}


/**
 * Fusionne les traductions d'une balise `<multi>` avec celles d'une autre balise `<multi>`.
 * L'une des balise est considérée comme prioritaire ce qui permet de régler le cas où la même
 * langue est présente dans les deux balises.
 * Si on ne trouve pas de balise `<multi>` dans l'un ou l'autre des paramètres, on considère que
 * le texte est tout même formaté de la façon suivante : texte0[langue1]texte1[langue2]texte2...
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 *
 * @param string $multi_prioritaire
 *        Balise multi considérée comme prioritaire en cas de conflit sur une langue.
 * @param string $multi_non_prioritaire
 *        Balise multi considérée comme non prioritaire en cas de conflit sur une langue.
 *
 * @return string
 *        La chaine construite est toujours une balise `<multi>` complète ou une chaine vide sinon.
 */
function taxon_merger_traductions($multi_prioritaire, $multi_non_prioritaire) {

	$multi_merge = '';

	// On extrait le contenu de la balise <multi> si elle existe.
	$multi_prioritaire = trim($multi_prioritaire);
	$multi_non_prioritaire = trim($multi_non_prioritaire);

	// Si les deux balises sont identiques on sort directement avec le multi prioritaire ce qui améliore les
	// performances.
	if ($multi_prioritaire == $multi_non_prioritaire) {
		$multi_merge = $multi_prioritaire;
	} else {
		include_spip('inc/filtres');
		if (preg_match(_EXTRAIRE_MULTI, $multi_prioritaire, $match)) {
			$multi_prioritaire = trim($match[1]);
		}
		if (preg_match(_EXTRAIRE_MULTI, $multi_non_prioritaire, $match)) {
			$multi_non_prioritaire = trim($match[1]);
		}

		if ($multi_prioritaire) {
			if ($multi_non_prioritaire) {
				// On extrait les traductions sous forme de tableau langue=>traduction.
				$traductions_prioritaires = extraire_trads($multi_prioritaire);
				$traductions_non_prioritaires = extraire_trads($multi_non_prioritaire);

				// On complète les traductions prioritaires avec les traductions non prioritaires dont la langue n'est pas
				// présente dans les traductions prioritaires.
				foreach ($traductions_non_prioritaires as $_lang => $_traduction) {
					if (!array_key_exists($_lang, $traductions_prioritaires)) {
						$traductions_prioritaires[$_lang] = $_traduction;
					}
				}

				// On construit le contenu de la balise <multi> mergé à partir des traductions prioritaires mises à jour.
				// Les traductions vides sont ignorées.
				ksort($traductions_prioritaires);
				foreach ($traductions_prioritaires as $_lang => $_traduction) {
					if ($_traduction) {
						$multi_merge .= ($_lang ? '[' . $_lang . ']' : '') . trim($_traduction);
					}
				}
			} else {
				$multi_merge = $multi_prioritaire;
			}
		} else {
			$multi_merge = $multi_non_prioritaire;
		}

		// Si le contenu est non vide on l'insère dans une balise <multi>
		if ($multi_merge) {
			$multi_merge = '<multi>' . $multi_merge . '</multi>';
		}
	}

	return $multi_merge;
}


/**
 * Traduit un champ de la table `spip_taxons` dans la langue du site.
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 *
 * @param string $champ
 *        Nom du champ dans la base de données.
 *
 * @return string
 *        Traduction du champ dans la langue du site.
 */
function taxon_traduire_champ($champ) {

	$traduction = '';
	if ($champ) {
		$traduction = _T("taxon:champ_${champ}_label");
	}

	return $traduction;
}


/**
 * Renvoie la liste des services de taxonomie utilisés par le plugin en tenant compte de la configuration
 * choisi par le webmestre.
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 *
 * @return array
 *        Tableau des services utilisés sous la forme [alias] = titre du service.
 */
function taxon_lister_services() {

	// On initialise la liste avec le service ITOS qui est toujours utilisé.
	$services = array('itis');

	// On lit la configuration pour voir quels autres services sont autorisés à l'utilisation
	include_spip('inc/config');
	$services = array_flip(array_merge($services, lire_config('taxonomie/services_utilises')));

	// On met à jour la liste avec le titre de chaque service
	foreach ($services as $_service => $_index) {
		$services[$_service] = _T("taxonomie:label_service_${_service}");
	}

	return $services;
}
