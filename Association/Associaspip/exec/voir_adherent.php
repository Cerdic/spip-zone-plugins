<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/navigation_modules');
include_spip('inc/association_comptabilite');

function exec_voir_adherent(){
	$id_auteur = intval(_request('id'));
	$full = autoriser('associer', 'adherents');
	$data = sql_fetsel('m.sexe, m.nom_famille, m.prenom, m.validite, m.id_asso, c.libelle','spip_asso_membres as m LEFT JOIN spip_asso_categories as c ON m.categorie=c.id_categorie', "m.id_auteur=$id_auteur");
	if ((!$full AND ($id_auteur!==intval($GLOBALS['visiteur_session']['id_auteur']))) OR !$data) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('inc/association_coordonnees');
		$nom_membre = association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']);
		$validite = $data['validite'];
		$adresses = association_formater_adresses(array($id_auteur));
		$emails = association_formater_emails(array($id_auteur));
		$telephones = association_formater_telephones(array($id_auteur));
		$categorie = $data['libelle']?$data['libelle']:_T('asso:pas_de_categorie_attribuee');
		$statut = sql_getfetsel('statut', 'spip_auteurs', 'id_auteur='.$id_auteur);
		switch($statut)	{
			case '0minirezo':
				$statut='auteur'; break;
			case '1comite':
				$statut='auteur'; break;
			default :
				$statut='visiteur'; break;
		}
		association_onglets(_T('asso:titre_onglet_membres'));
		// INFOs
		if ($adresses[$id_auteur])
			$infos['adresses'] = $adresses[$id_auteur];
		if ($emails[$id_auteur])
			$infos['emails'] = $emails[$id_auteur];
		if ($telephones[$id_auteur])
			$infos['numeros'] =  $telephones[$id_auteur];
		echo '<div class="vcard">'. totauxinfos_intro('<span class="fn">'.htmlspecialchars($nom_membre).'</span>', $statut, $id_auteur, $infos, 'coordonnees') .'</div>';
		$coord = '';
		if ($full) {
			$coord .= '<p>'.$categorie.'</p>';
			$infos['adherent_libelle_categorie'] = $categorie;
		}
		$coord .= '<p>'._T('asso:adherent_libelle_date_validite').'<br/>'.association_datefr($data['validite']).'</p>';
		$infos['adherent_libelle_validite'] = association_datefr($data['validite']);
		if ($GLOBALS['association_metas']['id_asso']=='on') {
			$coord .= '<p>'. ($data['id_asso']?_T('asso:adherent_libelle_reference_interne').'<br/>'.$data['id_asso']:_T('asso:pas_de_reference_interne_attribuee')) .'</p>';
			$infos['adherent_libelle_reference_interne'] = $data['id_asso'];
		}
		echo '<div class="vcard" style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'.$coord.'</div>';
//		echo '<div class="vcard">'. totauxinfos_intro('<span class="fn">'.htmlspecialchars($nom_membre).'</span>', $statut, $id_auteur, $infos ) .'</div>';
		// Afficher les champs extras
		echo '<div style="text-align: center" class="verdana1 spip_xx-small">'. pipeline('afficher_contenu_objet', array ('args'=>array('type'=>'asso_membre', 'id_objet'=>$id_auteur, 'contexte'=>array()), 'data'=>'')) .'</div>';
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);

		$res = $full ? association_icone('adherent_label_modifier_membre',  generer_url_ecrire('edit_adherent', 'id='.$id_auteur), 'edit.gif') : '';
		$res .= association_icone('adherent_label_modifier_'.$statut,  generer_url_ecrire('auteur_infos', 'id_auteur='.$id_auteur), 'edit.gif' ); // pas modifier mais voir page...
		$res .= association_icone('bouton_retour', str_replace('&', '&amp;', $_SERVER['HTTP_REFERER']), 'retour-24.png');
		echo bloc_des_raccourcis($res);
		debut_cadre_association('annonce.gif', 'membre', $nom_membre);
		// Liste des groupes
		$query = sql_select('g.id_groupe as id_groupe, g.nom as nom', 'spip_asso_groupes g LEFT JOIN spip_asso_groupes_liaisons l ON g.id_groupe=l.id_groupe', 'l.id_auteur='.$id_auteur, '', 'g.nom');
		if (sql_count($query)) {
			echo '<div class="cadre_padding">'._T('asso:groupes_dp');
			if ($row=sql_fetch($query)) {
				echo ' <a href="'.generer_url_ecrire('voir_groupe', 'id='.$row['id_groupe']).'">'.$row['nom'].'</a>';
			}
			while ($row=sql_fetch($query)) {
				echo ', <a href="'.generer_url_ecrire('voir_groupe', 'id='.$row['id_groupe']).'">'.$row['nom'].'</a>';
			}
			echo '.</div>';
		}
		// JUSTIFICATIFS
		if (test_plugin_actif('fpdf') AND $GLOBALS['association_metas']['recufiscal']) {
		/* afficher le lien vers les justificatifs seulemeunt si active en configuration et si FPDF est actif */
			echo '<fieldset><legend>'. _T('asso:liens_vers_les_justificatifs'), '</legend><div id="tableliste_recusfiscaux">';
			$data = array_map('array_shift', sql_allfetsel("DATE_FORMAT(date, '%Y')  AS annee", 'spip_asso_comptes', "id_journal=$id_auteur", 'annee', 'annee ASC') );
			foreach($data as $k => $annee) {
				echo '<a href="'. generer_url_ecrire('pdf_fiscal', "id=$id_auteur&annee=$annee") .'">'.$annee.'</a> ';
			}
			echo '</div></fieldset>';
		}
		// FICHE HISTORIQUE COTISATIONS
		echo '<fieldset><legend>'._T('asso:adherent_titre_historique_cotisations').'</legend>';
		/* si on a l'autorisation admin, on ajoute un bouton pour ajouter une cotisation */
		if ($full) {
			echo '<a href="'.generer_url_ecrire('ajout_cotisation', 'id='.$id_auteur).'">'._T('asso:adherent_label_ajouter_cotisation').'</a>';
		}
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_historique_dons'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'._T('asso:entete_id').'</th>';
		echo '<th>'._T('asso:adherent_entete_journal').'</th>';
		echo '<th>'._T('asso:entete_date').'</th>';
		echo '<th>'._T('asso:adherent_entete_justification').'</th>';
		echo '<th>'._T('asso:montant').'</th>';
		echo '<th>'._T('asso:action').'</th>';
		echo "</tr>\n</thead><tbody>";
		$association_imputation = charger_fonction('association_imputation', 'inc');
		$critere = $association_imputation('pc_cotisations');
		if ($critere)
			$critere .= ' AND ';
		$query = sql_allfetsel('id_compte AS id, recette AS montant, date, justification, journal', 'spip_asso_comptes', "$critere id_journal=$id_auteur", '', 'date DESC, id_compte DESC' );
		echo join("\n", voir_adherent_paiements($query, $full, 'cotisation'));
		echo "</tbody>\n</table>\n";
		echo '</fieldset>';
		// FICHE HISTORIQUE ACTIVITES
		if ($GLOBALS['association_metas']['activites']=='on'){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_activites').'</legend>';
			echo "<table width='100%' class='asso_tablo' id='asso_tablo_historique_activites'>\n";
			echo "<thead>\n<tr>";
			echo '<th>'._T('asso:entete_id').'</th>';
			echo '<th>'._T('asso:entete_date').'</th>';
			echo '<th>'._T('asso:adherent_entete_activite').'</th>';
			echo '<th>'._T('asso:adherent_entete_inscrits').'</th>';
			echo '<th>'._T('asso:adherent_entete_statut').'</th>';
			echo '<th>&nbsp;</th>';
			echo "</tr>\n</thead><tbody>";
			$query = sql_select('*', 'spip_asso_activites', "id_adherent=$id_auteur", '', 'date DESC');
			while ($data = sql_fetch($query)) {
				$id_evenement = $data['id_evenement'];
				echo '<tr>';
				echo '<td class="integer">'.$data['id_activite'].'</td>';
				$sql = sql_select('*', 'spip_evenements', "id_evenement=$id_evenement" );
				while ($evenement = sql_fetch($sql)) {
					$date = substr($evenement['date_debut'],0,10);
					echo '<td class="date">'.association_datefr($date).'</td>';
					echo '<td class="text">'.$evenement['titre'].'</td>';
				}
				echo '<td class="integer">'.$data['inscrits'].'</td>';
				echo '<td class="text">'.$data['statut'].'</td>';
				echo '<td class="action">', association_bouton('adherent_bouton_maj_inscription', 'edit-12.gif', 'edit_activite', 'id='.$data['id_activite']), '</td>';
				echo "</tr>\n";
			}
			echo "</tbody>\n</table>\n";
			echo '</fieldset>';
		}
		// FICHE HISTORIQUE VENTES
		if ($GLOBALS['association_metas']['ventes']=='on'){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_ventes').'</legend>';
			echo "<table width='100%' class='asso_tablo' id='asso_tablo_historique_ventes'>\n";
			echo "<thead>\n<tr>";
			echo '<th>'._T('asso:entete_id').'</th>';
			echo '<th>'._T('asso:entete_date').'</th>';
			echo '<th>'._T('asso:vente_entete_article').'</th>';
			echo '<th>'._T('asso:vente_entete_quantites').'</th>';
			echo '<th>'._T('asso:vente_entete_date_envoi').'</th>';
			echo '<th>&nbsp;</th>';
			echo "</tr>\n</thead><tbody>";
			$query = sql_select('id_vente, article, quantite, date_vente, date_envoi', 'spip_asso_ventes', 'id_acheteur='. $id_auteur, '', 'date_vente DESC' );
			while ($data = sql_fetch($query)) {
				echo '<tr>';
				echo '<td class="integer">'.$data['id_vente'].'</td>';
				echo '<td class="date">'.association_datefr($data['date_vente']).'</td>';
				echo '<td class="text">'.$data['article'].'</td>';
				echo '<td class="decimal">'.$data['quantite'].'</td>';
				echo '<td class="date">'.association_datefr($data['date_envoi']).'</td>';
				echo '<td class="action">'. association_bouton('adherent_bouton_maj_vente', 'edit-12.gif', 'edit_vente','id='.$data['id_vente']) .'</td>';
				echo "</tr>\n";
			}
			echo "</tbody>\n</table>\n";
			echo '</fieldset>';
		}
		// FICHE HISTORIQUE DONS
		if ($GLOBALS['association_metas']['dons']=='on'){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_dons').'</legend>';
			echo "<table width='100%' class='asso_tablo' id='asso_tablo_historique_dons'>\n";
			echo "<thead>\n<tr>";
			echo '<th>'._T('asso:entete_id').'</th>';
			echo '<th>'._T('asso:adherent_entete_journal').'</th>';
			echo '<th>'._T('asso:entete_date').'</th>';
			echo '<th>'._T('asso:adherent_entete_justification').'</th>';
			echo '<th>'._T('asso:montant').'</th>';
			echo '<th>'._T('asso:action').'</th>';
			echo "</tr>\n</thead><tbody>";
			$association_imputation = charger_fonction('association_imputation', 'inc');
			$critere = $association_imputation('pc_dons');
			if ($critere)
				$critere .= ' AND ';
			$query = sql_allfetsel('D.id_don AS id, D.argent AS montant, D.date_don AS date, justification, journal, id_compte', 'spip_asso_dons AS D LEFT JOIN spip_asso_comptes AS C ON C.id_journal=D.id_don', "$critere id_adherent=$id_auteur",'D.date_don DESC');
			echo join("\n", voir_adherent_paiements($query, $full, 'don'));
			echo "</tbody>\n</table>\n";
			echo '</fieldset>';
		}
		// FICHE HISTORIQUE PRETS
		if ($GLOBALS['association_metas']['prets']=='on'){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_prets').'</legend>';
			echo "<table width='100%' class='asso_tablo' id='asso_tablo_historiques_prets'>\n";
			echo "<thead>\n<tr>";
			echo '<th>&nbsp;</th>';
			echo '<th>'._T('asso:entete_id').'</th>';
			echo '<th>'._T('asso:vente_entete_article').'</th>';
			echo '<th>'._T('asso:prets_entete_date_sortie').'</th>';
			echo '<th>'._T('asso:prets_entete_date_retour').'</th>';
			echo '<th>&nbsp;</th>';
			echo "</tr>\n</thead><tbody>";
			$query = sql_select('*', 'spip_asso_prets AS P LEFT JOIN spip_asso_ressources AS R ON P.id_ressource=R.id_ressource', 'id_emprunteur='._q($id_auteur), '', 'id_pret DESC' );
			while ($data = sql_fetch($query)) {
				if (is_numeric($data['statut'])) { // nouveuaux statuts entiers
					if (($data['statut'])>0)
						$puce = 'verte';
					elseif (($data['statut'])<0)
						$puce = 'rouge';
					else
						$puce = 'orange';
				} else switch($data['statut']){ // anciens statuts texte
					case 'ok':
						$puce = 'verte'; break;
					case 'reserve':
						$puce = 'rouge'; break;
					case 'suspendu':
						$puce = 'orange'; break;
					case 'sorti':
						$puce = 'poubelle'; break;
				}
				echo '<tr>';
				echo '<td class="action">';
				echo '<img src="' . _DIR_PLUGIN_ASSOCIATION_ICONES . 'puce-'.$puce. '.gif" /></td>';
				echo '<td class="integer">'.$data['id_pret'].'</td>';
				echo '<td class="text">'.$data['intitule'].'</td>';
				echo '<td class="date">'.association_datefr($data['date_sortie'],'dtstart').'</td>';
				echo '<td class="date">';
				if($data['date_retour']<=$data['date_sortie']){
					echo '&nbsp;';
				} else {
					echo association_datefr($data['date_retour'], 'dtend');
				}
				echo '</td>';
				echo '<td class="action">' . association_bouton('adherent_bouton_maj_operation', 'edit-12.gif', 'edit_pret', 'agir=modifie&id_pret='.$data['id_pret']) . '</td>';
				echo "</tr>\n";
			}
			echo "</tbody>\n</table>\n";
			echo '</fieldset>';
		}
		fin_page_association();
	}
}

function voir_adherent_paiements($data, $lien, $type)
{
	include_spip('inc/texte'); // pour nettoyer_raccourci_typo
	foreach($data as $k => $row) {
		$j = $lien ? $row['justification']
		  : nettoyer_raccourcis_typo($row['justification']);
		$id = $row['id'];
		$id_compte = ($row['id_compte'])?$row['id_compte']:$id; // l'id_compte est soit explicitement present dans la ligne(pour les dons), sinon c'est qu'il est le meme qu'id (pour les cotisations)
		$data[$k] = "<tr id='$type$id'>"
		. '<td class="integer">'.$id.'</td>'
		. '<td class="text">'.$row['journal'].'</td>'
		. '<td class="date">'. association_datefr($row['date']). '</td>'
		. '<td class="text">'. propre($j) .'</td>'
		. '<td class="decimal">'. association_prixfr($row['montant']) .'</td>'
		. '<td class="action">'. association_bouton('adherent_label_voir_operation', 'voir-12.png', 'comptes','id_compte='.$id_compte) .'</td>' // pas plutot edit_compte ? (a propos, il faudrait carrement un voir_compte pour ne pas risquer de modifier ainsi une operation marquee "vu" et donc archivee/verouillee)
		. '</tr>';
	}
	return $data;
}

?>