<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/texte'); // pour nettoyer_raccourci_typo

function voir_adherent_paiements($data, $lien, $type)
{
	foreach($data as $k => $row) {
		$j = $lien ? $row['justification']
		  : nettoyer_raccourcis_typo($row['justification']);
		$id = $row['id'];
		$id_compte = ($row['id_compte'])?$row['id_compte']:$id; // l'id_compte est soit explicitement present dans la ligne(pour les dons), sinon c'est qu'il est le meme qu'id (pour les cotisations)
		$data[$k] = "<tr id='$type$id' style='background-color: #EEEEEE;'>"
		. "<td class='arial11 border1' style='text-align:right;'>$id</td>\n"
		. '<td class="arial11 border1">'.$row['journal']."</td>\n"
		. '<td class="arial11 border1">'.association_datefr($row['date'])."</td>\n"
		. '<td class="arial11 border1">'.propre($j)."</td>\n"
		. '<td class="arial11 border1" style="text-align:right;">'.$row['montant'].' &euro;</td>'
		. '<td style="text-align:right;">'
		. association_bouton(_T('asso:adherent_label_voir_operation'), 'voir-12.png', 'comptes','id_compte='.$id_compte)
		. "</td>"
		. '</tr>';
	}
	return $data;
}

function voir_adherent_cotisations($id_auteur, $full=false)
{
	$row = sql_allfetsel('id_compte AS id, recette AS montant, date, justification, journal', "spip_asso_comptes", "id_journal=$id_auteur AND imputation=" . sql_quote($GLOBALS['association_metas']['pc_cotisations']), '', "date DESC, id_compte DESC" );

	if (!$row) return '';

	return "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n"
	. "<tr style='background-color: #DBE1C5;'>\n"
	. '<th style="text-align:right;">'._T('asso:adherent_entete_id').'</th>'
	. '<th>'._T('asso:adherent_entete_journal').'</th>'
	. '<th>'._T('asso:adherent_entete_date').'</th>'
	. '<th>'._T('asso:adherent_entete_justification').'</th>'
	. '<th style="text-align:right;">'._T('asso:montant').'</th>'
	. '<th style="text-align:right;">'._T('asso:action').'</th>'
	. '</tr>'
	  . join("\n", voir_adherent_paiements($row, $full, 'cotisation'))
	. '</table>';
}

function voir_adherent_dons($id_auteur, $full=false)
{
	$row = sql_allfetsel('D.id_don AS id, D.argent AS montant, D.date_don AS date, justification, journal, id_compte',
			     "spip_asso_dons AS D LEFT JOIN spip_asso_comptes AS C ON C.id_journal=D.id_don",
			     'C.imputation=' . sql_quote($GLOBALS['association_metas']['pc_dons']) . ' AND '. 'id_adherent='.$id_auteur, 
			     '',
			     "D.date_don DESC" );			

	if (!$row) return '';

	return "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n"
	.  "<tr style='background-color: #DBE1C5;'>\n"
	.  '<th style="text-align:right;">'._T('asso:adherent_entete_id').'</th>'
	.  '<th>'._T('asso:adherent_entete_journal').'</th>'
	.  '<th>'._T('asso:adherent_entete_date').'</th>'
	.  '<th>'._T('asso:adherent_entete_justification').'</th>'
	.  '<th style="text-align:right;">'._T('asso:montant').'</th>'
	. '<th style="text-align:right;">'._T('asso:action').'</th>'
	. '</tr>'
	  .  join("\n", voir_adherent_paiements($row, $full, 'don'))
	  .  '</table>';
}

function voir_adherent_ventes($critere)
{
	$row = sql_allfetsel('id_vente AS id ,article, quantite, date_vente, date_envoi', "spip_asso_ventes", $critere, '', "date_vente DESC" );			
	if (!$row) return '';

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

function voir_adherent_recus($id_auteur)
{
	$row = array_map('array_shift', sql_allfetsel("date_format( date, '%Y' )  AS annee", "spip_asso_comptes", "id_journal=$id_auteur", 'annee', "annee ASC" ));
	foreach($row as $k => $annee) {
		$h = generer_url_ecrire('pdf_fiscal', "id=$id_auteur&annee=$annee");
		$row[$k] = "<a href='$h'>$annee</a>";
	}
	return join("\n", $row);
}

/* Cette fonction permet entre autres de recuperer tous les membres qui ont un email dans la table spip_auteurs, a reprendre lors de l'interfacage avec Coordonnees car les emails peuvent alors etre uniquement dans spip_emails
 et ils peuvent etre plusieurs, il faudrait peut etre laisser la possibilite de choisir ou prendre la/les adresses email qui sont de toute facon recuperes dans action/modifier_relances.php, le JOIN sur la
table spip_auteurs permet d'afficher uniquement les membres qui ont un email dans cette table */
function voir_adherent_infos($sel='*', $from='', $where='', $group='', $order='', $limit='')
{
  return sql_select($sel, "spip_asso_membres AS A  LEFT JOIN spip_auteurs AS B ON A.id_auteur=B.id_auteur $from", $where, $group, $order, $limit);
}

?>
