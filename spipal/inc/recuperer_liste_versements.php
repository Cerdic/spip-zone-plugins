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

include_spip('inc/presentation');
include_spip('inc/securiser_action');
include_spip('base/abstract_sql');

function inc_recuperer_liste_versements_dist() {
    $lister_versements_servis = $_REQUEST['lister_versements_servis'];
    $where = array();
    $checked = '';
    if ( !$lister_versements_servis ) {
        $where[] = '(servi = 0)';
    }
    else {
        $checked = " checked='checked' ";
    }
    
    $rows = sql_select(
        array(
            'V.id_versement', 
            'V.id_auteur', 
            'V.item_number', 
            '(V.versement_ht + V.versement_taxes) AS versement', 
            'V.versement_charges', 
            'V.date_versement', 
            'V.servi', 
            'V.devise', 
            'A.nom'),
        " spip_spipal_versements AS V LEFT JOIN spip_auteurs AS A ON (V.id_auteur = A.id_auteur) ",
        $where
        );
    $inputs = '';
    while ($row = sql_fetch($rows) ) {
        $checked = '';
        if ( $row['servi'] ) {
            $checked = " checked='checked' disabled='disabled' ";
        }
        $inputs .= "<tr><td><input type='checkbox' $checked name='versements[]' value='{$row['id_versement']}' /></td>"
            ."\n<td>".$row['item_number']."</td>"
            ."\n<td>".$row['id_auteur'].":".$row['nom']."</td>"
            ."\n<td>".round($row['versement'],2)."</td>"
            ."\n<td>".$row['versement_charges']."</td>"
            ."\n<td>".$row['devise']."</td>"
            ."\n<td>".$row['date_versement']."</td>"
            ."</tr>";
    }

    $res = "<input name='lister_versements_servis' type='checkbox' $checked /> <label for='lister_versements_servis'>"._T('spipal:inclure_servis')."</label><hr />" . ($inputs ? "<table><tr><th></th><th>item</th><th>par</th><th>ttc</th><th>charges</th><th>en</th><th>le</th></tr>$inputs</table>" : '');
	
    $id = 'versements';
    $res = debut_cadre_trait_couleur('', true, '', _T('spipal:admin_params_liste_versements'))
        . $res . '<hr />'
        . "<input type='submit' />"
        . fin_cadre_trait_couleur(true);
    
    return redirige_action_post('spipal_recuperer_versements', $id, 'spipal', '', $res);
}

?>
