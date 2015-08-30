<?php

/**
 * Gestion du formulaire de d'édition de map.
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
 * @param int|string $id_map
 *                                Identifiant du map. 'new' pour un nouveau map.
 * @param string     $retour
 *                                URL de redirection après le traitement
 * @param int        $lier_trad
 *                                Identifiant éventuel d'un map source d'une traduction
 * @param string     $config_fonc
 *                                Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *                                Valeurs de la ligne SQL du map, si connu
 * @param string     $hidden
 *                                Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return string
 *                Hash du formulaire
 */
function formulaires_editer_map_identifier_dist($id_map = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '')
{
    return serialize(array(intval($id_map)));
}

/**
 * Chargement du formulaire d'édition de map.
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_map
 *                                Identifiant du map. 'new' pour un nouveau map.
 * @param string     $retour
 *                                URL de redirection après le traitement
 * @param int        $lier_trad
 *                                Identifiant éventuel d'un map source d'une traduction
 * @param string     $config_fonc
 *                                Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *                                Valeurs de la ligne SQL du map, si connu
 * @param string     $hidden
 *                                Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *               Environnement du formulaire
 */
function formulaires_editer_map_charger_dist($id_map = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '')
{
    $valeurs = formulaires_editer_objet_charger('map', $id_map, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

    return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de map.
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_map
 *                                Identifiant du map. 'new' pour un nouveau map.
 * @param string     $retour
 *                                URL de redirection après le traitement
 * @param int        $lier_trad
 *                                Identifiant éventuel d'un map source d'une traduction
 * @param string     $config_fonc
 *                                Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *                                Valeurs de la ligne SQL du map, si connu
 * @param string     $hidden
 *                                Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *               Tableau des erreurs
 */
function formulaires_editer_map_verifier_dist($id_map = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '')
{
    include_spip('base/abstract_sql');
    $erreurs = array();
    $erreurs = formulaires_editer_objet_verifier('map', $id_map, array('titre', 'width', 'height', 'code_map'));

    // Code repris du plugin Menus.
    $code_map = _request('code_map');
    // Si l'api editer_objet ne retourne pas d'erreur, on regarde si le code_map est au bon format
    if (empty($erreurs['code_map']) and !preg_match('/^[\w-]+$/', $code_map)) {
        $erreurs['code_map'] = _T('map:erreur_code_map_forme');
    }
    if (empty($erreurs['code_map'])) {
        // Maintenant, on vérifie que le code unique (code_map) ne soit pas déjà utilisé
        // par une autre carte.
        $deja = sql_getfetsel(
            'id_map',
            'spip_maps',
            array(
                'code_map='.sql_quote($code_map),
                'id_map > 0',
                'id_map!='.intval(_request('id_map')),
            )
        );
        if ($deja) {
            $erreurs['code_map'] = _T('map:erreur_code_map_deja');
        }
    }

    // Code repris du plugin Menus.
    $data_name = _request('data_name');
    // Si l'api editer_objet ne retourne pas d'erreur, on regarde
    if (!is_null($data_name) and !empty($data_name) and empty($erreurs['data_name']) and !preg_match('/^[\w-]+$/', $data_name)) {
        $erreurs['data_name'] = _T('map:erreur_data_name_forme');
    }

    return $erreurs;
}

/**
 * Traitement du formulaire d'édition de map.
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_map
 *                                Identifiant du map. 'new' pour un nouveau map.
 * @param string     $retour
 *                                URL de redirection après le traitement
 * @param int        $lier_trad
 *                                Identifiant éventuel d'un map source d'une traduction
 * @param string     $config_fonc
 *                                Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *                                Valeurs de la ligne SQL du map, si connu
 * @param string     $hidden
 *                                Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *               Retours des traitements
 */
function formulaires_editer_map_traiter_dist($id_map = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '')
{
    return formulaires_editer_objet_traiter('map', $id_map, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
}
