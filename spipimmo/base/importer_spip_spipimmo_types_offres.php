<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V4
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/
/**
 * Fonction d'import de la table spip_spipimmo_types_offres
 * a utiliser dans le fichier d'administration du plugin
 *
 * include_spip('base/importer_spip_spipimmo_types_offres');
 * $maj['create'][] = array('importer_spip_spipimmo_types_offres');
 *
**/
function importer_spip_spipimmo_types_offres() {

	######## VERIFIEZ LE NOM DE LA TABLE D'INSERTION ###########
	$table = 'spip_spipimmo_types_offres';

	// nom_du_champ_source => nom_du_champ_destination
	// mettre vide la destination ou supprimer la ligne permet de ne pas importer la colonne.
	$correspondances = array(
		'id_type_offre' => 'id_type_offre',
		'libelle_offre' => 'libelle_offre',
	);

	// transposer les donnees dans la nouvelle structure
	$inserts = array();
	list($cles, $valeurs) = donnees_spip_spipimmo_types_offres();
	// on remet les noms des cles dans le tableau de valeur
	// en s'assurant de leur correspondance au passage
	if (is_array($valeurs)) {
		foreach ($valeurs as $v) {
			$i = array();
			foreach ($v as $k => $valeur) {
				$cle = $cles[$k];
				if (isset($correspondances[$cle]) and $correspondances[$cle]) {
					$i[ $correspondances[$cle] ] = $valeur;
				}
			}
			$inserts[] = $i;
		}
		unset($valeurs);

		// inserer les donnees en base.
		$nb_inseres = 0;
		// ne pas reimporter ceux deja la (en cas de timeout)
		$nb_deja_la = sql_countsel($table);
		$inserts = array_slice($inserts, $nb_deja_la);
		$nb_a_inserer = count($inserts);
		// on decoupe en petit bout (pour reprise sur timeout)
		$inserts = array_chunk($inserts, 100);
		foreach ($inserts as $i) {
			sql_insertq_multi($table, $i);
			$nb_inseres += count($i);
			// serie_alter() relancera la fonction jusqu'a ce que l'on sorte sans timeout.
			if (time() >= _TIME_OUT) {
				// on ecrit un gentil message pour suivre l'avancement.
				echo "<br />Insertion dans $table relanc&eacute;e : ";
				echo "<br />- $nb_deja_la &eacute;taient d&eacute;j&agrave; l&agrave;";
				echo "<br />- $nb_inseres ont &eacute;t&eacute; ins&eacute;r&eacute;s.";
				$a_faire = $nb_a_inserer - $nb_inseres;
				echo "<br />- $a_faire &agrave; faire.";
				return;
			}
		}
	}
}


/**
 * Donnees de la table spip_spipimmo_types_offres
**/
function donnees_spip_spipimmo_types_offres() {

	$cles = array('id_type_offre', 'libelle_offre');

	$valeurs = array(
		array('1', '<multi>[fr]Appartement</multi>'),
		array('2', '<multi>[fr]Boutique</multi>'),
		array('3', '<multi>[fr]Bureaux</multi>'),
		array('4', '<multi>[fr]Bureau / Local commercial</multi>'),
		array('5', '<multi>[fr]Commerce</multi>'),
		array('6', '<multi>[fr]Divers</multi>'),
		array('7', '<multi>[fr]Hangar</multi>'),
		array('8', '<multi>[fr]Hôtel particulier</multi>'),
		array('9', '<multi>[fr]Immeuble</multi>'),
		array('10', '<multi>[fr]Local</multi>'),
		array('11', '<multi>[fr]Maison / Villa</multi>'),
		array('12', '<multi>[fr]Parking</multi>'),
		array('13', '<multi>[fr]Terrain</multi>'),
	);

	return array($cles, $valeurs);
}
