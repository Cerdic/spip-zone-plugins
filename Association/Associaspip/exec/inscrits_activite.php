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

function exec_inscrits_activite()
{
	if (!autoriser('associer', 'activites')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_evenement = intval(_request('id'));
		onglets_association('titre_onglet_activite', 'activites');
		$evenement = sql_fetsel('*', 'spip_evenements', "id_evenement=$id_evenement") ;
		// INTRO : Rappel Infos Evenement
		$format = 'association_formater_'. (($evenement['horaire']=='oui')?'heure':'date');
		$infos['evenement_date_du'] = $format($evenement['date_debut'],'dtstart');
		$infos['evenement_date_au'] = $format($evenement['date_fin'],'dtend');
		$infos['evenement_lieu'] = '<span class="location">'.$evenement['lieu'].'</span>';
		echo '<div class="vevent">'. association_totauxinfos_intro('<span class="summary">'.$evenement['titre'].'</span>', 'evenement', $id_evenement, $infos, 'agenda', 'evenement') .'</div>';
		// STATS sur les participations (nombre de personnes inscrites et montant paye)
		echo association_totauxinfos_stats('participations', 'activites', array('activite_entete_inscrits'=>'inscrits','entete_montant'=>'montant',), "id_evenement=$id_evenement");
		// TOTAUX : nombres d'inscrits par etat de paiement
		echo association_totauxinfos_effectifs('activites', array(
			'valide' => array( 'asso:activite_entete_validees', sql_getfetsel('COUNT(*)+SUM(inscrits) AS valide', 'spip_asso_activites', "id_evenement=$id_evenement AND date_paiement<date_inscription "), ),
			'pair' => array( 'asso:activite_entete_impayees', sql_getfetsel('COUNT(*)+SUM(inscrits) AS pair', 'spip_asso_activites', "id_evenement=$id_evenement AND NOT date_paiement<date_inscription "), ),
		));
		// TOTAUX : montants des participations
		$montant = sql_fetsel('SUM(montant) AS encaisse', 'spip_asso_activites', "id_evenement=$id_evenement " );
		echo association_totauxinfos_montants('participations', $montant['encaisse'], NULL);
		// datation et raccourcis
		$res['activite_bouton_modifier_article'] = array('edit-12.gif', array('articles', 'id_article='.$evenement['id_article']));
		$res['activite_bouton_ajouter_inscription'] = array('panier_in.gif', array('edit_activite', "id_evenement=$id_evenement"));
		if (test_plugin_actif('FPDF')) { // PDF des inscrits
			$res['activite_bouton_imprimer_inscriptions'] = array('print-24.png', array('pdf_activite', "id=$id_evenement"));
		}
		if (test_plugin_actif('AGENDA')) { // inscrits via le formulaire d'Agenda2
			$res['activite_bouton_synchroniser_inscriptions'] = array('reload-32.png', array('synchronis_activites', "id=$id_evenement"));
		}
		raccourcis_association(array('activites','annee='.substr($evenement['date_debut'],0,4)), $res);
		debut_cadre_association('activites.gif', 'activite_titre_inscriptions_activites');
	// FILTRES
		echo '<form method="get" action="'. generer_url_ecrire('inscrits_activite') .'">';
		echo "\n<input type='hidden' name='exec' value='inscrits_activite' />\n";
		echo '<input type="hidden" name="id" value="'.$id_evenement.'" />';
		echo "\n<table width='100%' class='asso_tablo_filtres'><tr>";
		echo '<td id="filtre_statut">';
		echo '<select name="statut" onchange="form.submit()">';
		echo '<option value="0"'. ((!$statut)?' selected="selected"':'') .'>'._T('asso:activite_entete_toutes').'</option>';
		echo '<option value="+1"'. (($statut>0)?' selected="selected"':'') .'>'._T('asso:activite_entete_validees').'</option>';
		echo '<option value="-1"'. (($statut<0)?' selected="selected"':'') .'>'._T('asso:activite_entete_impayees').'</option>';
		echo '</select></td>';
		echo '<noscript><td><input type="submit" value="'._T('asso:bouton_filtrer').'" /></td></noscript>';
		echo '</tr></table></form>';
	//TABLEAU
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_activite'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'. _T('asso:entete_id') .'</th>';
		echo '<th>'. _T('asso:entete_date') .'</th>';
		echo '<th>'. _T('asso:entete_nom') .'</th>';
		echo '<th>'. _T('asso:activite_entete_inscrits') .'</th>';
		echo '<th>'. _T('asso:entete_montant') .'</th>';
		echo '<th colspan="2" class="actions">'. _T('asso:entete_actions') .'</th>';
		echo "</tr>\n</thead><tbody>";
		if ($statut) { // restriction de la selection
			$critereSupplementaire = ' AND '. ($statut>0?"date_paiement<date_inscription ":"date_paiement>=date_inscription ");
		}
		$query = sql_select('*', 'spip_asso_activites', "id_evenement=$id_evenement $critereSupplementaire ", '', 'id_activite') ;
		while ($data = sql_fetch($query)) {
			echo '<tr class="'.(($data['date_paiement']=='0000-00-00')?'pair':'valide').'">';
			echo '<td class="integer">'.$data['id_activite'].'</td>';
			echo '<td class="date">'. association_formater_date($data['date']). '</td>';
			echo '<td class="text">'.  association_calculer_lien_nomid($data['nom'],$data['id_adherent']) .'</td>';
			echo '<td class="integer">'.$data['inscrits'].'</td>';
			echo '<td class="decimal">'. association_formater_prix($data['montant']) .'</td>';
			echo association_bouton_suppr('activite', $data['id_activite']);
			echo association_bouton_act('activite_bouton_maj_inscription', 'cotis-12.gif', 'edit_activite','id='.$data['id_activite']);
			if ($data['commentaire']) {
				echo '</tr><tr class="'.(($data['date_paiement']<$data['date_inscription'])?'pair':'valide').'"><td colspan="7" class="text">&nbsp;'.$data['commentaire'].'</td>';
			}
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		fin_page_association();
	}
}

?>