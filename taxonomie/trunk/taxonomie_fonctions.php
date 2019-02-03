<?php
/**
 * Ce fichier contient l'ensemble des fonctions implémentant l'API du plugin Taxonomie accessible depuis
 * les squelettes.
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Charge tous les taxons d'un règne donné fourni dans le fichier ITIS, du règne lui-même jusqu'aux taxons de genre.
 * Les nom communs anglais, français, espagnols, etc, peuvent aussi être chargés en complément mais
 * ne couvrent pas l'ensemble des taxons.
 * Le modifications effectuées manuellement sur ces taxons sont conservées.
 *
 * @package SPIP\TAXONOMIE\REGNE
 *
 * @api
 * @filtre
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
	include_spip('inc/taxonomie');
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
 * @filtre
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
 * @filtre
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
 * Fournit l'ascendance taxonomique d'un taxon donné, par consultation dans la base de données.
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 * @filtre
 *
 * @param int    $id_taxon
 *        Id du taxon pour lequel il faut fournir l'ascendance.
 * @param int    $tsn_parent
 *        TSN du parent correspondant au taxon id_taxon. Ce paramètre permet d'optimiser le traitement
 *        mais n'est pas obligatoire. Si il n'est pas connu lors de l'appel il faut passer `null`.
 * @param string $ordre
 *        Classement de la liste des taxons : `descendant`(défaut) ou `ascendant`.
 *
 * @return array
 *        Liste des taxons ascendants. Chaque taxon est un tableau associatif contenant les informations
 *        suivantes : `id_taxon`, `tsn_parent`, `nom_scientifique`, `nom_commun`, `rang`, `statut` et l'indicateur
 *        d'espèce `espèce`.
 */
function taxon_informer_ascendance($id_taxon, $tsn_parent = null, $ordre = 'descendant') {

	$ascendance = array();

	// Si on ne passe pas le tsn du parent correspondant au taxon pour lequel on cherche l'ascendance
	// alors on le cherche en base de données.
	// Le fait de passer ce tsn parent est uniquement une optimisation.
	if (is_null($tsn_parent)) {
		$tsn_parent = sql_getfetsel('tsn_parent', 'spip_taxons', 'id_taxon=' . intval($id_taxon));
	}

	while ($tsn_parent > 0) {
		$select = array('id_taxon', 'tsn_parent', 'nom_scientifique', 'nom_commun', 'rang_taxon', 'statut', 'espece');
		$where = array('tsn=' . intval($tsn_parent));
		$taxon = sql_fetsel($select, 'spip_taxons', $where);
		if ($taxon) {
			$ascendance[] = $taxon;
			$tsn_parent = $taxon['tsn_parent'];
		}
	}

	if ($ascendance	and ($ordre == 'descendant')) {
		$ascendance = array_reverse($ascendance);
	}

	return $ascendance;
}


/**
 * Fournit les phrases de crédit des sources d'information ayant permis de compléter le taxon.
 * La référence ITIS n'est pas répétée dans le champ `sources` de chaque taxon car elle est
 * à la base de chaque règne. Elle est donc insérée par la fonction.
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 * @filtre
 *
 * @param int    $id_taxon
 *        Id du taxon pour lequel il faut fournir les crédits
 * @param string $sources_specifiques
 *        Tableau sérialisé des sources possibles autres qu'ITIS (CINFO, WIKIPEDIA...) telles qu'enregistrées
 *        en base de données dans le champ `sources`.
 *        Ce paramètre permet d'optimiser le traitement mais n'est pas obligatoire.
 *
 * @return array
 *        Tableau des phrases de crédits indexées par source.
 */
function taxon_crediter($id_taxon, $sources_specifiques = null) {

	$sources = array();

	// Si on ne passe pas les sources du taxon concerné alors on le cherche en base de données.
	// Le fait de passer ce champ sources est uniquement une optimisation.
	if (is_null($sources_specifiques)) {
		$sources_specifiques = sql_getfetsel('sources', 'spip_taxons', 'id_taxon=' . intval($id_taxon));
	}

	// On merge ITIS et les autres sources
	$liste_sources = array('itis' => array());
	if ($sources_specifiques) {
		$liste_sources = array_merge($liste_sources, unserialize($sources_specifiques));
	}

	// Puis on construit la liste des sources pour l'affichage
	foreach ($liste_sources as $_service => $_infos_source) {
		include_spip("services/${_service}/${_service}_api");
		if (function_exists($citer = "${_service}_credit")) {
			$sources[$_service] = $citer($id_taxon, $_infos_source);
		}
	}

	return $sources;
}


/**
 * Affiche la puce de statut d'un taxon sans proposer le formulaire de changement de statut.
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 * @filtre
 *
 * @param string $statut
 *        Statut du taxon, `prop`, `publie`ou `poubelle`.
 * @param int    $id_taxon
 *        Id du taxon.
 *
 * @return array
 *        Image de la puce.
 */
function taxon_afficher_statut($statut, $id_taxon = 0) {

	// On évite de charger la fonction n fois.
	static $afficher_puce = null;

	if (!$afficher_puce) {
		// Chargement de la fonction d'affichage
		$afficher_puce = charger_fonction('puce_statut', 'inc');
	}

	// On affiche la puce sans proposer le formulaire rapide de changement de statut qui pose un problème avec
	// l'ajax sachant qu'un changement peut en provoquer d'autres, la liste n'est plus à jour.
	$puce = $afficher_puce($id_taxon, $statut, 0, 'taxon', false, false);

	return $puce;
}


/**
 * Renvoie la liste des services de taxonomie utilisés par le plugin en tenant compte de la configuration
 * choisi par le webmestre.
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 * @filtre
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
