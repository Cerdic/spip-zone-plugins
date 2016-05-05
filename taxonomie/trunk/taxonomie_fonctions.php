<?php
/**
 * Ce fichier contient l'ensemble des fonctions implémentant l'API du plugin Taxonomie.
 *
 * @package SPIP\TAXONOMIE\API
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Charge tous les taxons d'un règne donné, du règne lui-même aux taxons de genre au maximum.
 * La fonction permet aussi de choisir un rang taxonomique feuille différent du genre.
 * Les nom communs anglais, français ou espagnols peuvent aussi être chargés en complément mais
 * ne couvrent pas l'ensemble des taxons.
 *
 * @api
 * @filtre
 * @uses taxonomie_regne_existe()
 * @uses taxon_preserver_editions()
 * @uses taxonomie_vider_regne()
 * @uses itis_read_hierarchy()
 * @uses itis_spipcode2language()
 * @uses itis_read_vernaculars()
 *
 * @param string	$regne
 * 		Nom scientifique du règne en lettres minuscules : `animalia`, `plantae`, `fungi`.
 * @param string	$rang
 * 		Rang taxonomique minimal jusqu'où charger le règne. Ce rang est fourni en anglais, en minuscules et
 * 		correspond à : `phylum`, `class`, `order`, `family`, `genus`.
 * @param array		$codes_langue
 * 		Tableau des codes (au sens SPIP) des langues à charger pour les noms communs des taxons.
 *
 * @return bool
 * 		`true` si le chargement a réussi, `false` sinon
 */
function taxonomie_charger_regne($regne, $rang, $codes_langue=array()) {
	$retour = false;
	$taxons_edites = array();

	// Vérifie si le règne existe bien dans la table spip_taxons
	$regne_existe = taxonomie_regne_existe($regne, $meta_regne);
	if ($regne_existe) {
		// Sauvegarde des taxons ayant été modifiés manuellement suite à leur création automatique.
		include_spip('inc/taxonomer');
		$taxons_edites = taxon_preserver_editions($regne);

		// Vider le règne avant de le recharger
		taxonomie_vider_regne($regne);
	}

	// Lecture de la hiérarchie des taxons à partir du fichier texte extrait de la base ITIS
	$meta_regne = array();
	include_spip('services/itis/itis_api');
	$taxons = itis_read_hierarchy($regne, $rang, $meta_regne['sha']);

	// Ajout des noms communs extraits de la base ITIS dans la langue demandée
	if ($taxons) {
		$meta_regne['compteur'] = count($taxons);
		$traductions = array();
		foreach ($codes_langue as $_code_langue) {
			$langue = itis_spipcode2language($_code_langue);
			if ($langue) {
				$noms = itis_read_vernaculars($langue, $sha_langue);
				if ($noms) {
					$meta_regne['traductions']['itis'][$_code_langue]['sha'] = $sha_langue;
					$nb_traductions = 0;
					foreach ($noms as $_tsn => $_nom) {
						if (array_key_exists($_tsn, $taxons)) {
							// On ajoute les traductions qui sont de la forme [xx]texte
							// On sauvegarde le tsn concerné afin de clore les traductions
							// avec les balises multi et d'optimiser ainsi les traitements
							// sachant qu'il y a très peu de traductions comparées aux taxons
							$taxons[$_tsn]['nom_commun'] .= $_nom;
							$nb_traductions += 1;
							$traductions[$_tsn] = $_tsn;
						}
					}
					$meta_regne['traductions']['itis'][$_code_langue]['compteur'] = $nb_traductions;
				}
			}
		}

		// Clore les traductions avec les balises multi
		if ($traductions) {
			foreach ($traductions as $_tsn) {
				$taxons[$_tsn]['nom_commun'] =  '<multi>' . $taxons[$_tsn]['nom_commun'] . '</multi>';
			}
		}

		// Réinjection des taxons modifiés manuellement
		// -- descriptif: remplacement
		// -- nom commun: merge en considérant que la mise à jour manuelle est prioritaire
		// -- edite: oui, on conserve bien sur l'indicateur d'édition
		if ($taxons_edites) {
			foreach ($taxons_edites as $_taxon_edite) {
				if (($tsn = $_taxon_edite['tsn'])
				AND (array_key_exists($tsn, $taxons))) {
					$taxons[$tsn]['descriptif'] = $_taxon_edite['descriptif'];
					$taxons[$tsn]['nom_commun'] = taxon_merger_traductions(
													$_taxon_edite['nom_commun'],
													$taxons[$tsn]['nom_commun']);
					$taxons[$tsn]['edite'] = 'oui';
				}
			}
		}

		// Insertion dans la base de données
		$retour = sql_insertq_multi('spip_taxons', array_values($taxons));
		if ($retour) {
			// Insérer les sha dans une meta propre au règne.
			// Ca permettra de tester l'utilité ou pas d'un rechargement du règne
			$meta_regne['rang'] = $rang;
			$meta_regne['maj'] = date('Y-m-d H:i:s');
			ecrire_meta("taxonomie_$regne", serialize($meta_regne));
		}
	}

	return $retour;
}


/**
 * Supprime tous les taxons d'un règne donné de la base de données.
 * La meta concernant les informations de chargement du règne est aussi effacée.
 * Les modifications manuelles effectuées sur les taxons du règne sont perdues, elles
 * doivent donc être préservées au préalable.
 *
 * @api
 * @filtre
 *
 * @param string	$regne
 * 		Nom scientifique du règne en lettres minuscules : `animalia`, `plantae`, `fungi`.
 *
 * @return bool
 * 		`true` si le vidage a réussi, `false` sinon
 */
function taxonomie_vider_regne($regne) {
	$retour = sql_delete('spip_taxons', 'regne=' . sql_quote($regne));
	if ($retour !== false) {
		// Supprimer la meta propre au règne.
		effacer_meta("taxonomie_$regne");
		$retour = true;
	}

	return $retour;
}


/**
 * Retourne l'existence ou pas d'un règne en base de données.
 * La fonction scrute la table `spip_taxons` et non la meta propre au règne.
 *
 * @api
 * @filtre
 *
 * @param string	$regne
 * 		Nom scientifique du règne en lettres minuscules : `animalia`, `plantae`, `fungi`.
 * @param array		$meta_regne
 * 		Meta propre au règne, créée lors du chargement de celui-ci et retournée si le règne
 * 		existe.
 *
 * @return bool
 * 		`true` si le règne existe, `false` sinon.
 */
function taxonomie_regne_existe($regne, &$meta_regne) {
	$meta_regne = array();
	$existe = false;

	$retour = sql_countsel('spip_taxons', 'regne=' . sql_quote($regne));
	if ($retour) {
		// Récupérer la meta propre au règne afin de la retourner.
		include_spip('inc/config');
		$meta_regne = lire_config("taxonomie_$regne");
		$existe = true;
	}

	return $existe;
}


/**
 * Liste dans un tableau les rangs taxonomiques supportés par le plugin, suivant certains critères.
 * Les règnes et les rangs sont exprimés en anglais et écrits en lettres minuscules.
 * La fonction permet d'exclure certains rangs de la liste.
 *
 * @api
 * @filtre
 *
 * @param string	$regne
 * 		Nom scientifique du règne pour lequel la liste des rangs est demandée.
 * 		Cet argument permet de remplacer le rang `phylum` par `division` qui est son synonyme
 * 		pour les règnes fongique et végétal.
 * @param array		$liste_base
 * 		Liste de base contenant les rangs par défaut à renvoyer. Il existe deux listes de base, à savoir :
 * 		- du règne au genre (`_TAXONOMIE_RANGS_PARENTS_ESPECE`)
 * 		- de l'espèce à la sous-forme (`_TAXONOMIE_RANGS_ESPECE_ET_FILS`)
 * @param array		$exclusions
 * 		Liste des rangs à exclure de la liste fournie dans l'argument `$liste_base`
 *
 * @return array
 * 		Liste des rangs demandée.
 */
function taxonomie_lister_rangs($regne=_TAXONOMIE_REGNE_ANIMAL, $liste_base, $exclusions=array()) {
	include_spip('inc/taxonomer');

	$rangs = explode(':', $liste_base);
	$rangs = array_diff($rangs, $exclusions);

	if (($regne == _TAXONOMIE_REGNE_FONGIQUE)
	OR  ($regne == _TAXONOMIE_REGNE_VEGETAL)) {
		if ($index_cherche = array_search(_TAXONOMIE_RANG_PHYLUM, $rangs))
			$rangs[$index_cherche] = _TAXONOMIE_RANG_DIVISION;
	}

	return $rangs;
}


/**
 * Fournit l'ascendance taxonomique d'un taxon donné par consultation en base de données.
 *
 * @api
 * @filtre
 *
 * @param int		$id_taxon
 * 		Id du taxon pour lequel il faut fournir l'ascendance.
 * @param int		$tsn_parent
 * 		TSN du parent correspondant au taxon id_taxon. Ce paramètre permet d'optimiser le traitement
 * 		mais n'est pas obligatoire. Si il n'est pas connu lors de l'appel il faut passer `null`.
 * @param string	$ordre
 * 		Classement de la liste des taxons : `descendant`(défaut) ou `ascendant`.
 *
 * @return array
 * 		Liste des taxons ascendants. Chaque taxon est un tableau associatif contenant les informations
 * 		suivantes : `id_taxon`, `tsn_parent`, `nom_scientifique`, `nom_commun`, `rang`.
 */
function taxonomie_informer_ascendance($id_taxon, $tsn_parent=null, $ordre='descendant') {
	$ascendance = array();

	// Si on ne passe pas le tsn du parent correspondant au taxon pour lequel on cherche l'ascendance
	// alors on le cherche en base de données.
	// Le fait de passer ce tsn parent est uniquement une optimisation.
	if (is_null($tsn_parent)) {
		$tsn_parent = sql_getfetsel('tsn_parent', 'spip_taxons', 'id_taxon=' . intval($id_taxon));
	}

	while ($tsn_parent > 0) {
		$select = array('id_taxon', 'tsn_parent', 'nom_scientifique', 'nom_commun', 'rang');
		$where = array('tsn=' . intval($tsn_parent));
		$taxon = sql_fetsel($select, 'spip_taxons', $where);
		if ($taxon) {
			$ascendance[] = $taxon;
			$tsn_parent = $taxon['tsn_parent'];
		}
	}

	if ($ascendance
	AND ($ordre == 'descendant'))
		$ascendance = array_reverse($ascendance);

	return $ascendance;
}


/**
 * Fournit les phrases de crédits des sources d'information ayant permis de compléter le taxon.
 * La référence ITIS n'est pas répétée dans le champ `sources` de chaque taxon car elle est
 * à la base de chaque règne. Elle est donc insérée par la fonction.
 *
 * @api
 * @filtre
 * @uses ${service}_credit fonction de formatage des crédits propre à chaque service
 *
 * @param int		$id_taxon
 * 		Id du taxon pour lequel il faut fournir les crédits
 * @param string	$sources_specifiques
 * 		Tableau sérialisé des sources possibles autres qu'ITIS (CINFO, WIKIPEDIA...) telles qu'enregistrées
 * 		en base de données dans le champ `sources`.
 * 		Ce paramètre permet d'optimiser le traitement mais n'est pas obligatoire.
 *
 * @return array
 * 		Tableau des phrases de crédits indexées par source.
 */
function taxonomie_informer_credits($id_taxon, $sources_specifiques=null) {
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

?>