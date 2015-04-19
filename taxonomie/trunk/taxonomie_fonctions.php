<?php
/**
 * Ce fichier contient l'ensemble fonctions implémentant l'API du plugin Taxonomie.
 *
 * @package SPIP\TAXONOMIE\API
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement de tous les taxons d'un règne donné, du règne lui-même aux taxons de genre au maximum.
 * La fonction permet aussi de choisir un rang taxonomique feuille différent du genre.
 * Les nom communs anglais et français peuvent aussi être chargés en complément mais
 * ne couvrent pas l'ensemble des taxons.
 *
 * @api
 * @filtre
 *
 * @param string	$regne
 * 		Nom scientifique du règne en lettres minuscules (animalia, plantae, fungi)
 * @param string	$rang
 * 		Rang taxonomique minimal jusqu'où charger le règne. Ce rang est fourni en anglais et
 * 		correspond à : phylum, class, order, family, genus.
 * @param array		$langues
 * 		Tableau des codes (au sens SPIP) des langues à charger pour les noms communs des taxons
 *
 * @return bool|string
 * 		Retour true/false
 */
function taxonomie_charger_regne($regne, $rang, $langues=array('fr')) {
	$retour = false;
	$taxons_edites = array();

	// todo : ne faut-il pas tester la valeur du regne ?

	$regne_existe = taxonomie_regne_existe($regne, $meta_regne);
	if ($regne_existe) {
		// Sauvegarde des taxons ayant été modifiés manuellement suite à leur création automatique.
		include_spip('inc/taxonomer');
		$taxons_edites = preserver_taxons_edites($regne);

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
		foreach ($langues as $_cle => $_langue) {
			$noms = itis_read_vernaculars($_langue, $sha_langue);
			if ($noms) {
				$meta_regne['langues'][$_langue]['sha'] = $sha_langue;
				$nb_traductions = 0;
				foreach ($noms as $_tsn => $_nom) {
					if (array_key_exists($_tsn, $taxons)) {
                        // On ajoute les traductions qui ont de la forme [xx]texte
                        // On sauvegarde le tsn concerné afin de clore les traductions
                        // avec les balises multi et d'optimiser ainsi les traitements
                        // sachant qu'il y a très peu de traductions comparées aux taxons
						$taxons[$_tsn]['nom_commun'] .= $_nom;
						$nb_traductions += 1;
                        $traductions[$_tsn] = $_tsn;
					}
				}
				$meta_regne['langues'][$_langue]['compteur'] = $nb_traductions;
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
					$taxons[$tsn]['nom_commun'] = merger_multi(
													$taxons[$tsn]['nom_commun'],
													$_taxon_edite['nom_commun'],
													true);
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
 * Suppression de tous les taxons d'un règne donné de la base de données.
 * La meta concernant les informations de chargement du règne est aussi effacée.
 * Les modifications manuelles effectuées sur les taxons du règne sont perdues!
 *
 * @api
 * @filtre
 *
 * @param string	$regne
 * 		Nom scientifique du règne en lettres minuscules (animalia, plantae, fungi)
 *
 * @return bool
 * 		Retour true/false
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
 * Interrogation sur l'existence ou pas d'un règne en base de données.
 * La fonction scrute la table spip_taxons et non la meta propre au règne.
 *
 * @api
 * @filtre
 *
 * @param string	$regne
 * 		Nom scientifique du règne en lettres minuscules (animalia, plantae, fungi)
 * @param array		$meta_regne
 * 		Meta propre au règne, créée lors du chargement de celui-ci et retournée si le règne
 * 		existe
 *
 * @return bool
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
 * Liste dans un tableau les rangs taxonomiques supportés par le plugin, à savoir:
 * kingdom, phylum, class, order, family, genus et species.
 * Les règnes sont exprimés en anglais et écrits en lettres minuscules.
 * La fonction permet d'exclure de la liste les rangs extrêmes kingdom et specie et de choisir
 * entre le rang phylum et son synonyme division.
 *
 * @param bool $exclure_regne
 * 		Demande d'exclusion du règne de la liste des rangs
 * @param bool $exclure_espece
 * 		Demande d'exclusion de l'espèce de la liste des rangs
 * @param string	$regne
 * 		Nom scientifque du règne pour lequel la liste des rangs est demandée.
 * 		Cet argument permet de remplacer le rang phylum par division qui est son synonyme
 * 		pour les règnes fongique et végétal
 *
 * @return array
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
 * Fourniture de l'ascendance taxonomique d'un taxon donné.
 *
 * @api
 * @filtre
 *
 * @param int	$id_taxon
 * 		Id du taxon pour lequel il faut fournir l'ascendance
 * @param int	$tsn_parent
 *      TSN du parent correspondant au taxon id_taxon. Ce paramètre permet d'optimiser le traitement
 * 		mais n'est pas obligatoire.
 *
 * @return array
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
 * Fourniture des sources d'information ayant permis de compléter le taxon.
 * La référence ITIS n'est pas répétée dans le champ sources de chaque taxon car elle est
 * à la base de chaque règne. Elle est donc insérée par la fonction elle-même.
 *
 * @api
 * @filtre
 *
 * @param int		$id_taxon
 * 		Id du taxon pour lequel il faut fournir l'ascendance
 * @param string	$sources_specifiques
 * 		Tableau sérialisé des identifiants des sources possibles autres qu'ITIS (CINFO, WIKIPEDIA...).
 * 		Ce paramètre permet d'optimiser le traitement mais n'est pas obligatoire.
 *
 * @return array
 */
function taxonomie_informer_sources($id_taxon, $sources_specifiques=null) {
	$sources = array();

	// Si on ne passe pas les sources du taxon concerné alors on le cherche en base de données.
	// Le fait de passer ce champ sources est uniquement une optimisation.
	if (is_null($sources_specifiques)) {
		$sources_specifiques = sql_getfetsel('sources', 'spip_taxons', 'id_taxon=' . intval($id_taxon));
	}

	// On merge ITIS et les autres sources
	$liste_sources = array('itis' => '');
	if ($sources_specifiques) {
		$liste_sources = array_merge($liste_sources, unserialize($sources_specifiques));
	}

	// Puis on construit le fichier
	foreach ($liste_sources as $_source => $_champs) {
		include_spip("services/${_source}/${_source}_api");
		if (function_exists($citer = "${_source}_citation")) {
			$sources[$_source] = array(
				'texte' => $citer(),
				'champs' => $_champs
			);
		}
	}

	return $sources;
}

function taxonomie_informer($recherche, $section='') {
	include_spip('services/wikipedia/wikipedia_api');
	return wikipedia_get($recherche, $section);
}

?>