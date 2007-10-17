<?php

    include_spip('base/compat193');
    include_spip('public/assembler');

/*! \brief charge le fond de selection des documents coté privé
 *
 *  lors d'un apple à la page ecrire/?exec=articles, un menu est chargé. Ce menu liste les documents affecté à l'article
 *  et offre la possibilité de les convertirs
 *    
 *  \param $id_article identifiant de l'article
 *  \return $flux un flux html contenant le menu de selection 
 */   
function affiche_liste_doc($id_article) {

    spip_log('genere liste doc','doc2img');
    $flux = "";
        //met un saut de ligne avec le bloc de données précédent
        $flux .= '<div style="height: 5px;"/>';
        $flux .= '</div>';

        //genere un bloc de données pour le dod2img
    	$flux .= debut_cadre('r');

            //definition du contexte
            $contexte = array("id_article" => $id_article);
            //chargement du fond demandé
            $flux .= recuperer_fond("squelette/doc2img",$contexte);

    	$flux .= fin_cadre('r');
    return $flux;
}

/*
ajax_action_post( $action, $arg, $retour, $gra, $corps, $clic="", $atts_i="", $atts_span = "", $args_ajax="" )
*/

?>
