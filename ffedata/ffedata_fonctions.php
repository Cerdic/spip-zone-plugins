<?php
/**
 * Fonctions utiles au plugin Data FFE
 *
 * @plugin     Data FFE
 * @copyright  2015
 * @author     Jacques
 * @licence    GNU/GPL
 * @package    SPIP\Ffedata\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function inc_echecs_to_array_dist($u) {
    $obj = simplexml_load_string($u);
    // gestion du namespace spécifique au wsdl microsoft :
    // <diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">
    $data = $obj->children('urn:schemas-microsoft-com:xml-diffgram-v1')->children('')->NewDataSet;
    // transformation de l'objet en array
    $array = json_decode(json_encode((array)$data),1);
    // suppression récursive des arrays vides (plus propre)
    $array = echecs_array_remove_empty($array);
    return $array;
}

function echecs_array_remove_empty($haystack) {
    foreach ($haystack as $key => $value) {
        if (is_array($value)) {
            $haystack[$key] = echecs_array_remove_empty($haystack[$key]);
        }
        if (empty($haystack[$key])) {
            unset($haystack[$key]);
        }
    }
    return $haystack;
}

//pour afficher le tableau des joueurs dans le modèle des équipes PV 

function echec_ligne_tableau($vals, $nb) {
    static $liste = array('Blanc', 'Noir', 'Resultat');
 
    $ligne = '';
 
    if (isset($vals['Blanc' . $nb])) {
        $ligne .= '<tr>';
        foreach ($liste as $cellule) {
            $v = isset($vals[$cellule . $nb]) ? $vals[$cellule . $nb] : '';
            $ligne .= '<td>' . $v . '</td>';
        }
        $ligne .= "<td colspan='3'></td>";
        $ligne .= "</tr>";
    }
 
    return $ligne;
}