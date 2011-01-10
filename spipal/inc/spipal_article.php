<?php

/***************************************************************************\
 *  SPIPAL, Utilitaire de paiement en ligne pour SPIP                      *
 *                                                                         *
 *  Copyright (c) 2007 Thierry Schmit                                      *
 *  Copyright (c) 2011 Emmanuel Saint-James                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

require_once("spipal_tabledata.php");

function inc_spipal_article($id_article) {

    $mod = "<div style='text-align:center'>"._T('spipal:exec_articles_vendre_ou_non');
    $mod .= '<select onchange="$(\'#truc\').show()" name="action_vente"><option value="0">'._T('spipal:exec_article_pas_vendre')."</option>";
    
    $selected = '';
    $pour = '';
    $prix = 0;
    if ( $row = est_a_vendre($id_article) ) {
        $prix = $row['prix_unitaire_ht'];
        $selected__1 = '';
        $selected__2 = '';
        switch ( $row['don'] ) {
            case 1:
                $selected_2 = " selected='selected' ";
                $pour       = '<a href="javascript:$(\'#truc\').show()">details</a>';
                break;
            default:
                $selected_1 = " selected='selected' ";
                $pour       = '<a href="javascript:$(\'#truc\').show()">'.$prix.'</a> '._T('spipal:euros');
        }
        
    }
    if ( $GLOBALS['spipal_metas']['vendre'] )
        $mod .= "<option value='1' $selected_1>"._T('spipal:exec_article_a_vendre')."</option>";
    if ( $GLOBALS['spipal_metas']['donner'] )
        $mod .= "<option value='2' $selected_2>"._T('spipal:exec_article_a_votre_bon_coeur')."</option>";
    $mod .= '</select>'.$pour;
    $mod .= '</div>';
    
    $mod .= "<div id='truc' style='display:none; margin-top:10px'>";
    $mod .= mbt_echo_form_table(
        'spip_spipal_produits', 
        '', 
        $id_article,
        array(
            'ref_produit' => array(
                'size' => 30,
            ),
            'nom_com' => array(
                'size' => 30,
            ),
            'prix_unitaire_ht' => array(
                'size' => 10,
            ),
            'tva' => array(
                'size' => 10
            )
        ),
	'spipal',
        false
    );

    $mod .= "<button type='submit'>maj</button>";
    $mod .="</div>";
    
    return debut_cadre_relief(_DIR_PLUGIN_SPIPAL_ICONES.'avendre.png', true, '', '')
      . redirige_action_auteur("spipal_proposer", $id_article, 'articles', "id_article=$id_article", $mod)
      . fin_cadre_relief(true)
      . '<br />';
}

?>
