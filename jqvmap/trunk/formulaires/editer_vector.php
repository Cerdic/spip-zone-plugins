<?php

/**
 * Gestion du formulaire de d'édition de vector.
 *
 * @plugin     jQuery Vector Maps
 *
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité.
 *
 * @param int|string $id_vector
 *                                Identifiant du vector. 'new' pour un nouveau vector.
 * @param string     $retour
 *                                URL de redirection après le traitement
 * @param int        $lier_trad
 *                                Identifiant éventuel d'un vector source d'une traduction
 * @param string     $config_fonc
 *                                Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *                                Valeurs de la ligne SQL du vector, si connu
 * @param string     $hidden
 *                                Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return string
 *                Hash du formulaire
 */
function formulaires_editer_vector_identifier_dist($id_vector = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '')
{
    return serialize(array(intval($id_vector)));
}

/**
 * Chargement du formulaire d'édition de vector.
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_vector
 *                                Identifiant du vector. 'new' pour un nouveau vector.
 * @param string     $retour
 *                                URL de redirection après le traitement
 * @param int        $lier_trad
 *                                Identifiant éventuel d'un vector source d'une traduction
 * @param string     $config_fonc
 *                                Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *                                Valeurs de la ligne SQL du vector, si connu
 * @param string     $hidden
 *                                Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *               Environnement du formulaire
 */
function formulaires_editer_vector_charger_dist($id_vector = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '')
{
    $valeurs = formulaires_editer_objet_charger('vector', $id_vector, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
    if (isset($valeurs['id_map']) and $valeurs['id_map'] == '') {
        $valeurs['id_map'] = (_request('id_map')) ? _request('id_map') : '';
    }

    return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de vector.
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_vector
 *                                Identifiant du vector. 'new' pour un nouveau vector.
 * @param string     $retour
 *                                URL de redirection après le traitement
 * @param int        $lier_trad
 *                                Identifiant éventuel d'un vector source d'une traduction
 * @param string     $config_fonc
 *                                Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *                                Valeurs de la ligne SQL du vector, si connu
 * @param string     $hidden
 *                                Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *               Tableau des erreurs
 */
function formulaires_editer_vector_verifier_dist($id_vector = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '')
{
    return formulaires_editer_objet_verifier('vector', $id_vector, array('id_map', 'titre', 'path'));
}

/**
 * Traitement du formulaire d'édition de vector.
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_vector
 *                                Identifiant du vector. 'new' pour un nouveau vector.
 * @param string     $retour
 *                                URL de redirection après le traitement
 * @param int        $lier_trad
 *                                Identifiant éventuel d'un vector source d'une traduction
 * @param string     $config_fonc
 *                                Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *                                Valeurs de la ligne SQL du vector, si connu
 * @param string     $hidden
 *                                Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *               Retours des traitements
 */
function formulaires_editer_vector_traiter_dist($id_vector = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '')
{
    return formulaires_editer_objet_traiter('vector', $id_vector, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
}
