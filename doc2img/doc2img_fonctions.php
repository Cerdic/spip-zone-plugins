<?php
/**
 * doc2img_fonctions.php
 * Liste des fonctions chargées à chaque appel d'une page publique ou privée
 *
 * A chaque chargement d'une page, les fonctions suivantes sont accesibles.
 * Ce sont entre autres les filtres, balises, ... utiles au plugin
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * d2c_convertir
 *
 * Filtre pour lancer la conversion d'un document depuis un squelette
 *
 * @param $id_document le document à convertir
 * @return un statut vrai/faux exploitable par un tableau
 */
function d2c_convertir($id_document) {
    if (can_doc2img($id_document)) {
        if (is_doc2img($id_document)) {
            return "deja converti";
        } else {
        	$convertir = charger_fonction('doc2img_convertir','inc');
            return $convertir($id_document);
        }
    } else {
        return "non convertible";
    }
}

function d2c_is_convert($id_document) {
    include_spip('inc/doc2img_convertir');
    return is_doc2img($id_document) ? ' ' : '';
}

function d2c_can_convert($id_document) {
    include_spip('inc/doc2img_convertir');
    return can_doc2img($id_document) ? ' ' : '';
}

/**
 * Génère l'url absolue d'un doc2img en fonction de son id
 * @return string l'url absolue du fichier
 */
function generer_url_doc2img($id) {
    $fichier = sql_getfetsel('fichier','spip_doc2img','id_doc2img='.$id);
    $doc2img = get_spip_doc($fichier);
    return $GLOBALS['meta']['adresse_site'].'/'.$doc2img;
}

/**
 * Fournir un #URL_DOC2IMG adapté au contexte ou bien #URL_DOC2IMG{unid} pour un id_doc2img donné
 */
function balise_URL_DOC2IMG_dist($p) {

    $type = 'doc2img';

    $_id = interprete_argument_balise(1,$p);
    if (!$_id) $_id = champ_sql('id_' . $type, $p);

    $p->code = "generer_url_doc2img($_id)";
    $p->interdire_scripts = false;

    return $p;
}

?>
