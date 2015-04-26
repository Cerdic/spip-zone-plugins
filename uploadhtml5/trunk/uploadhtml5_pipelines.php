<?php
/**
 * Utilisations de pipelines par Formulaire upload html5
 *
 * @plugin     Formulaire upload html5
 * @copyright  2014
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Uploadhtml5\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function uploadhtml5_jquery_plugins($scripts) {

    $scripts[] = 'lib/dropzone/dropzone.js';

    return $scripts;
}

function uploadhtml5_insert_head_css($flux) {

    $flux .= '<link rel="stylesheet" href="'.find_in_path('lib/dropzone/dropzone.css').'" type="text/css" media="screen" />';

    return $flux;
}

function uploadhtml5_header_prive($flux) {
    $flux .= '<link rel="stylesheet" href="'.find_in_path('lib/dropzone/dropzone.css').'" type="text/css" media="screen" />';

    $flux .= '<link rel="stylesheet" href="'.find_in_path('prive/css/dropzone_prive.css').'" type="text/css" media="screen" />';


    return $flux;
}

function uploadhtml5_afficher_complement_objet($flux) {

    if ($type=$flux['args']['type']
        and $id=intval($flux['args']['id'])
        and (autoriser('joindredocument',$type,$id))) {

        $flux['data'] .= recuperer_fond('prive/squelettes/inclure/uploadhtml5', $flux['args']);
    }

    return $flux;
}

function uploadhtml5_affiche_gauche($flux) {

    if ($en_cours = trouver_objet_exec($flux['args']['exec'])
        AND $en_cours['edition']!==false // page edition uniquement
        AND $type = $en_cours['type']
        AND $id_table_objet = $en_cours['id_table_objet']
        // id non defini sur les formulaires de nouveaux objets
        AND (isset($flux['args'][$id_table_objet]) and $id = intval($flux['args'][$id_table_objet])
            // et justement dans ce cas, on met un identifiant negatif
            OR $id = 0-$GLOBALS['visiteur_session']['id_auteur'])
      AND autoriser('joindredocument',$type,$id)) {

            $flux['data'] .= recuperer_fond('prive/squelettes/inclure/uploadhtml5', array('type' => $type, 'id' => $id));

        }

    return $flux;
}