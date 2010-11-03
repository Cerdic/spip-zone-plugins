<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function voir_adherent_paiements($data, $lien)
{
	foreach($data as $k => $row) {
		$j = $lien ? $row['justification']
		  : nettoyer_raccourcis_typo($row['justification']);

		$data[$k] = '<tr style="background-color: #EEEEEE;">'
		  . '<td class="arial11 border1" style="text-align:right;">'.$row['id']."</td>\n"
		  . '<td class="arial11 border1">'.$row['journal']."</td>\n"
		  . '<td class="arial11 border1">'.association_datefr($row['date'])."</td>\n"
		  . '<td class="arial11 border1">'.propre($j)."</td>\n"
		  . '<td class="arial11 border1" style="text-align:right;">'.$row['montant'].' &euro;</td>'
		  . '</tr>';
	}
	return $data;
}

function voir_adherent_cotisations($id_auteur, $full=false)
{
	$row = sql_allfetsel('id_compte AS id, recette AS montant, date, justification, journal', "spip_asso_comptes", "id_journal=$id_auteur AND imputation=" . sql_quote($GLOBALS['association_metas']['pc_cotisations']), '', "date DESC" );

	return "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n"
	. "<tr style='background-color: #DBE1C5;'>\n"
	. '<th style="text-align:right;">'._T('asso:adherent_entete_id').'</th>'
	. '<th>'._T('asso:adherent_entete_journal').'</th>'
	. '<th>'._T('asso:adherent_entete_date').'</th>'
	. '<th>'._T('asso:adherent_entete_justification').'</th>'
	. '<th style="text-align:right;">'._T('asso:montant').'</th>'
	. '</tr>'
	. join("\n", voir_adherent_paiements($row, $full))
	. '</table>';
}

function voir_adherent_dons($id_auteur, $full=false)
{
	$row = sql_allfetsel('D.id_don AS id, D.argent AS montant, D.date_don AS date, justification, journal',
			     "spip_asso_dons AS D LEFT JOIN spip_asso_comptes AS C ON C.id_journal=D.id_don",
			     'C.imputation=' . sql_quote($GLOBALS['association_metas']['pc_dons']) . ' AND '. 'id_adherent='.$id_auteur, 
			     '',
			     "D.date_don DESC" );			

	return "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n"
	.  "<tr style='background-color: #DBE1C5;'>\n"
	.  '<th style="text-align:right;">'._T('asso:adherent_entete_id').'</th>'
	.  '<th>'._T('asso:adherent_entete_journal').'</th>'
	.  '<th>'._T('asso:adherent_entete_date').'</th>'
	.  '<th>'._T('asso:adherent_entete_justification').'</th>'
	.  '<th style="text-align:right;">'._T('asso:montant').'</th>'
	.  join("\n", voir_adherent_paiements($row, $full))
	  .  '</table>';
}

function voir_adherent_ventes($critere)
{
	$row = sql_allfetsel('id_vente AS id ,article, quantite, date_vente, date_envoi', "spip_asso_ventes", $critere, '', "date_vente DESC" );			
	foreach($row as $k => $v) { 
		$row[$k] = '<tr style="background-color: #EEEEEE;">'
		. '<td class="arial11 border1" style="text-align:right;">'.$v['id']."</td>\n"
		. '<td class="arial11 border1" style="text-align:right;">'.association_datefr($v['date_vente'])."</td>\n"
		. '<td class="arial11 border1">'.$v['article']."</td>\n"
		. '<td class="arial11 border1" style="text-align:right;">'.$v['quantite']."</td>\n"
		. '<td class="arial11 border1" style="text-align:right;">'.association_datefr($v['date_envoi'])."</td>\n"
		. '<td class="arial11 border1" style="text-align:center;">'
		. association_bouton(_T('asso:adherent_bouton_maj_vente'), 'edit-12.gif', 'edit_vente','id='.$v['id'])
		. "</td>\n"
		. '</tr>';
	}

	return "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n"
		. "<tr style='background-color: #DBE1C5;'>\n"
		. '<th style="text-align:right;">'._T('asso:vente_entete_id')."</th>\n"
		. '<th>'._T('asso:vente_entete_date')."</th>\n"
		. '<th>'._T('asso:vente_entete_article')."</th>\n"
		. '<th style="text-align:right;">'._T('asso:vente_entete_quantites')."</th>\n"
		. '<th>'._T('asso:vente_entete_date_envoi')."</th>\n"
		. "<td><strong>&nbsp;</strong></td>\n"
		. '</tr>'
	  . join("", $row)
	  . '</table>';
}
?>
