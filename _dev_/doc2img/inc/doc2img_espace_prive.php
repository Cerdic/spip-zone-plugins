<?php

    include_spip('base/compat193');
    include_spip('public/assembler');

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
