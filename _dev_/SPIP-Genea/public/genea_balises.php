<?php

/*******************************************************************
 *
 * Copyright (c) 2007-2008
 * Xavier BUROT
 * fichier : public/genea_balises
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL
 *
 * *******************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/genea_base');
include_spip('base/create');
include_spip('inc/filtres');
include_spip('inc/genea_date');
include_spip('public/interfaces');

global $table_des_traitements;

// -- Recupere informations de la rubrique contenant l'arbre genealogique
function titre_genea($id_genea, $champs='') {
    // Utiliser la bonne fonction de recherche sql (fetch) selon la version de SPIP
    $fetch = function_exists('sql_fetch') ? 'sql_fetch' : 'spip_fetch_array';
    // par précaution, on vérifié que le paramètre est une valeur numérique entière
    if((!($id_genea = intval($id_genea))) AND (!($champs==''))) return '';
    // on rédige puis on exécute la requête pour la base de données
    if($r = spip_query('SELECT rubriques.'.$champs.' FROM spip_genea AS genea, spip_rubriques AS rubriques WHERE (genea.id_rubrique=rubriques.id_rubrique) AND (genea.id_genea='.$id_genea.')'))
        // si cette requête renvoie un résultat pour le champ demandé on le retourne
        if($row = $fetch($r))
            return $row[$champs];
    // sinon, on renvoie une chaine vide
    return '';
}

// -- Balise affichant le titre de la rubrique contenant l'arbre --------
function balise_TITRE_GENEA_dist($p) {
    /* explorer la pile memoire pour atteindre le 'vrai' champ */
    $id_genea = champ_sql('id_genea', $p);
    /* le code php qui sera execute */
    $p->code = "titre_genea(".$id_genea.", 'titre')";
    return $p;
}

// -- Balise affichant le numero de la rubrique contenant l'arbre -------
function balise_ID_RUBRIQUE_GENEA_dist($p) {
    /* explorer la pile memoire pour atteindre le 'vrai' champ */
    $id_genea = champ_sql('id_genea', $p);
    /* le code php qui sera execute */
    $p->code = "intval(titre_genea(".$id_genea.", 'id_rubrique'))";
    return $p;
}

/* un TITRE_PARENT est un TITRE, sauf si on y tient absolument */
if (!isset($table_des_traitements['TITRE_GENEA'])) {
    $table_des_traitements['TITRE_GENEA'] = $table_des_traitements['TITRE'];
}

// -- Recherche le numero SOSA d'un invidu ------------------------------
function trouve_sosa($id_individu){
	$sosa='';
	if($id_individu){
		$q = "SELECT id_sosa FROM spip_genea_sosa WHERE id_individu=$id_individu";
		$res = spip_query($q);
		if ($row = spip_fetch_array($res)) $sosa = $row['id_sosa'];
	}
	return $sosa;
}

// -- Affiche le numero SOSA d'un individu ------------------------------
function balise_SOSA($p){
	$p->code = "trouve_sosa(".champ_sql('id_individu', $p).")";
	$p->interdire_scripts = true;
	return $p;
}
?>