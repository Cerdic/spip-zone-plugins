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


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_voir_activites()
{
	if (!autoriser('associer', 'activites')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_evenement = intval(_request('id'));
		association_onglets(_T('asso:titre_onglet_activite'));
		$evenement = sql_fetsel('*', 'spip_evenements', "id_evenement=$id_evenement") ;
		// INTRO : Rappel Infos Evenement
		$infos['evenement_date_du'] = association_datefr($evenement['date_debut'],'dtstart').' '.substr($data['date_debut'],10,6);
		$infos['evenement_date_au'] = association_datefr($evenement['date_fin'],'dtend').' '.substr($data['date_debut'],10,6);
		$infos['evenement_lieu'] = $evenement['lieu'];
		echo totauxinfos_intro($evenement['titre'], 'evenement', $id_evenement, $infos, 'agenda');
		// STATS sur les participations (nombre de personnes inscrites et montant paye)
		echo totauxinfos_stats('participations', 'activites', array('activite_entete_inscrits'=>'inscrits','entete_montant'=>'montant',), "id_evenement=$id_evenement");
		// TOTAUX : nombres d'inscrits par etat de paiement
		$liste_libelles = $liste_effectifs = array();
		$liste_libelles['valide'] = _T('asso:activite_entete_validees');
		$liste_libelles['pair'] = _T('asso:activite_entete_impayees');
		$liste_effectifs['valide'] = sql_getfetsel('COUNT(*)+SUM(inscrits) AS valide', 'spip_asso_activites', "id_evenement=$id_evenement AND date_paiement<>'0000-00-00' ");
		$liste_effectifs['impair'] = sql_getfetsel('COUNT(*)+SUM(inscrits) AS impair', 'spip_asso_activites', "id_evenement=$id_evenement AND NOT date_paiement='0000-00-00' ");
		echo totauxinfos_effectifs('activites', $liste_libelles, $liste_effectifs);
		// TOTAUX : montants des participations validees
		$montant = sql_fetsel('SUM(montant) AS encaisse', 'spip_asso_activites', "id_evenement=$id_evenement AND statut " );
		echo totauxinfos_montants(_T('asso:participations'), $montant['encaisse'], NULL);
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = association_icone('activite_bouton_ajouter_inscription',  generer_url_ecrire('ajout_inscription', 'id_evenement='.$id_evenement), 'panier_in.gif');
		if (test_plugin_actif('FPDF')) {
			$res .= association_icone('activite_bouton_voir_liste_inscriptions',  generer_url_ecrire('pdf_activite','id='.$id_evenement), 'print-24.png');
		}
		$res .= association_icone('bouton_retour', generer_url_ecrire('activites','annee='.substr($evenement['date_debut'],0,4)), 'retour-24.png');
		echo bloc_des_raccourcis($res);
		debut_cadre_association('activites.gif', 'activite_titre_inscriptions_activites');
	// PAGINATION ET FILTRES
		echo "<table class='asso_tablo_filtres'><tr>\n<td width='70%'></td><td width='30%' class='formulaire'>";
		echo '<form method="post" action="'. generer_url_ecrire('voir_activites') .'"><div>';
		echo '<input type="hidden" name="id" value="'.$id_evenement.'" />';
		echo '<select name="statut" onchange="form.submit()">';
		echo '<option value="0"'. ((!$statut)?' selected="selected"':'') .'>'._T('asso:activite_entete_toutes').'</option>';
		echo '<option value="+1"'. (($statut>0)?' selected="selected"':'') .'>'._T('asso:activite_entete_validees').'</option>';
		echo '<option value="-1"'. (($statut<0)?' selected="selected"':'') .'>'._T('asso:activite_entete_impayees').'</option>';
		echo "</select></div></form></td></tr></table>\n";
	//TABLEAU
		echo '<form action="'. generer_url_ecrire('action_activites') .'" method="post">';
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_activite'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'. _T('asso:entete_id') .'</th>';
		echo '<th>'. _T('asso:entete_date') .'</th>';
		echo '<th>'. _T('asso:entete_nom') .'</th>';
		echo '<th>'. _T('asso:activite_entete_inscrits') .'</th>';
		echo '<th>'. _T('asso:entete_montant') .'</th>';
		echo '<th colspan="3" class="actions">'. _T('asso:entete_action') .'</th>';
		echo "</tr>\n</thead><tbody>";
		if ($statut) { // restriction de la selection
			$critereSupplementaire = ' AND '. ($statut>0?"date_paiement='0000-00-00'":"date_paiement<>'0000-00-00'");
		}
		$query = sql_select('*', 'spip_asso_activites', "id_evenement=$id_evenement $critereSupplementaire ", '', 'id_activite') ;
		while ($data = sql_fetch($query)) {
			echo '<tr class="'.(($data['date_paiement']=='0000-00-00')?'pair':'valide').'">';
			echo '<td class="integer">'.$data['id_activite'].'</td>';
			echo '<td class="date">'.association_datefr($data['date']).'</td>';
			echo '<td class="text">'.  association_calculer_lien_nomid($data['nom'],$data['id_adherent']) .'</td>';
			echo '<td class="integer">'.$data['inscrits'].'</td>';
			echo '<td class="decimal">'. association_prixfr($data['montant']) .'</td>';
			echo '<td class="action">', association_bouton('activite_bouton_maj_inscription', 'edit-12.gif', 'ajout_inscription','id='.$data['id_activite']), '</td>';
			echo '<td class="action">'. association_bouton('activite_bouton_ajouter_inscription', 'cotis-12.gif', 'ajout_participation', 'id='.$data['id_activite']) .'</td>';
			echo '<td class="action"><input name="delete[]" type="checkbox" value="'.$data['id_activite'].'" /></td>';
			if ($data['commentaire']) {
				echo '</tr><tr class="'.(($data['date_paiement']=='0000-00-00')?'pair':'valide').'"><td colspan="8" class="text">&nbsp;'.$data['commentaire'].'</td>';
			}
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		echo "<table class='asso_tablo_filtres'><tr>\n<td width='90%'></td><td width='10%' class='formulaire'>";
		echo '<input type="submit" value="'._T('asso:bouton_supprimer').'" />';
		echo "</td></tr></table>\n";
		echo '</form>';
		fin_page_association();
	}
}

?>