<?php
/**
 * Utilisations de pipelines par signature
 *
 * @plugin     signature
 * @copyright  2014
 * @author     erational
 * @licence    GNU/GPL
 * @package    SPIP\signature\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	

function signature_affiche_gauche($flux) {
    $exec = $flux["args"]["exec"];
     
    if (($exec == "auteur") || ($exec == "infos_perso")) {
        if ($flux['args']['id_auteur'])
                   $id_auteur = $flux['args']['id_auteur'];
              else  {
                    // cas infos_perso ou l'id_auteur n'est pas url
                    $id_auteur = $GLOBALS['visiteur_session']['id_auteur']; 
              }
    
        $flux["data"] .= recuperer_fond('prive/signature_telecharger', array('id_auteur'=>$id_auteur));
    }
     
    return $flux;
}



?>