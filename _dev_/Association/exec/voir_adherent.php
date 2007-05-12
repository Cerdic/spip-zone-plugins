<?php
/**
* Plugin Association
*
* Copyright (c) 2007
* Bernard Blazin & François de Montlivault
* http://www.plugandspip.com 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

include_spip('inc/presentation');

function exec_voir_adherent(){
	global $connect_statut, $connect_toutes_rubriques;

	debut_page(_T('asso:titre_gestion_pour_association'), "", "");

// LES URL'S
	$url_asso = generer_url_ecrire('association');
	$url_edit_compte = generer_url_ecrire('edit_compte');
	$url_edit_activite = generer_url_ecrire('edit_activite');

	include_spip ('inc/navigation');

	debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_titre_fiche_signaletique'));
	debut_boite_info();
	
//LE MENU
	print association_date_du_jour();


// FICHE SIGNALETIQUE 	
	$id_adherent = $_GET['id'];
	$query=spip_query( "SELECT * FROM spip_asso_adherents where id_adherent='$id_adherent' ");
	
	echo '<fieldset><legend>'._T('asso:adherent_titre_fiche_signaletique_id',array('id' => $id_adherent)).'</legend>';
	echo '<table width="70%">';	
	while($data = spip_fetch_array($query)) {
		$id_asso=$data['id_asso'];
		echo '<tr> ';
		echo '<td>'._T('asso:adherent_libelle_reference_interne').' :</td>';
		echo '<td><strong>'.$id_asso.'</strong></td>';
		echo '<td rowspan=6><img src="/IMG/auton'.$data['id_auteur'].'.jpg"></td>';
		echo '<td rowspan=6>';
		$link=generer_url_ecrire('edit_adherent',"id=$id_adherent");
		icone(_T('asso:adherent_bouton_modifier_membre'), $link, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','edit.gif' );
		echo '</td></tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:adherent_libelle_fonction').' :</td>';
		echo '<td><strong>'.$data['fonction'].'</strong></td></tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:adherent_libelle_nom').' :</td>';
		echo '<td><strong>'.$data['nom'].'</strong></td>';
		echo '<tr> ';
		echo '<td>'._T('asso:adherent_libelle_prenom').' :</td>';
		echo '<td><strong>'.$data['prenom'].'</strong></td></tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:adherent_libelle_sexe').' :</td>';
		echo '<td><strong>';
		if($data['sexe']=="H") { echo _T('asso:adherent_libelle_masculin'); }
		else { echo _T('asso:adherent_libelle_feminin'); }
		echo '</strong></td>';
		echo '<tr> ';
		echo '<td>'._T('asso:adherent_libelle_date_naissance').' :</td>';
		echo '<td><strong>'.association_datefr($data['naissance']).'</strong></td></tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:adherent_libelle_categorie').' :</td>';
		echo '<td><strong>'.$data['categorie'].'</strong></td>';
		echo '<td>'._T('asso:adherent_libelle_statut').' :</td>';
		echo '<td><strong>';
		echo _T('asso:adherent_libelle_statut_'.$data['statut']);
		echo '</strong></td>';
		echo '<tr> ';	
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td></tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:adherent_libelle_email').' :</td>';
		echo '<td colspan="3"><strong>'.$data['email'].'</strong></td></tr>';
		echo '<tr> ';
		echo '<td style="vertical-align:top;">'._T('asso:adherent_libelle_adresse').' :</td>';
		echo '<td><strong>'.$data['rue'].'<br>'.$data['cp'].' '.$data['ville'].'</strong></td>';
		echo '<td>'._T('asso:adherent_libelle_portable').' :<br>'._T('asso:adherent_libelle_telephone').' :</td>';
		echo '<td><strong>'.$data["portable"].'<br>'.$data["telephone"].'</strong></td></tr>';
		echo '<tr> ';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td></tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:adherent_libelle_societe').' :</td>';
		echo '<td><strong>'.$data["societe"].'</strong></td>';
		echo '<td>'._T('asso:adherent_libelle_profession').' :</td>';
		echo '<td><strong>'.$data["profession"].'</strong></td></tr>';
		echo '<tr> ';	
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td></tr>';
		echo '<tr> ';	
		echo '<td>'._T('asso:adherent_libelle_secteur').' :</td>';
		echo '<td><strong>'.$data["secteur"].'</strong></td>';
		echo '<td>'._T('asso:adherent_libelle_accord').' :</td>';
		echo '<td><strong>'.$data['publication'].'</strong></td></tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:adherent_libelle_utilisateur1').' :</td>';
		echo '<td><strong>'.$data["utilisateur1"].'</strong></td>';
		echo '<td>'._T('asso:adherent_libelle_utilisateur2').' :</td>';
		echo '<td><strong>'.$data["utilisateur2"].'</strong></td></tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:adherent_libelle_utilisateur3').' :</td>';
		echo '<td><strong>'.$data["utilisateur3"].'</strong></td>';
		echo '<td>'._T('asso:adherent_libelle_utilisateur4').' :</td>';
		echo '<td><strong>'.$data["utilisateur4"].'</strong></td></tr>';
		echo '<tr> ';	
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td></tr>';
		$id_auteur = $data['id_auteur'];
		$sql = spip_query("SELECT * FROM spip_auteurs where id_auteur='$id_auteur'");
		if ($auteur = spip_fetch_array($sql)) 
		echo '<tr> ';	
		echo '<td>'._T('asso:adherent_libelle_visiteur_spip').' :</td>';
		echo '<td><strong>'.$auteur['nom'].'</strong></td>';
		echo '<td>'._T('asso:adherent_libelle_identifiant').' :</td>';
		echo '<td><strong>'.$auteur['login'].'</strong></td></tr>';
		echo '<tr> ';	
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td></tr>';
		echo '<tr> ';      
		echo '<td>'._T('asso:adherent_libelle_remarques').' :</td>';
		echo '<td colspan="3"><strong>'.$data["remarques"].'</strong></td></tr>';
	}
	echo '</table>';
	echo '</fieldset>';

// FICHE HISTORIQUE 	
	echo '<fieldset><legend>'._T('asso:adherent_titre_historique_cotisations').'</legend>';
	echo '<table width="70%" border="0">';
	echo '<tr bgcolor="silver">';
	echo '<td style="text-align:right;"><strong>'._T('asso:adherent_entete_id').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_entete_date').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_entete_livre').'</strong></td>';
	echo '<td style="text-align:right;"><strong>'._T('asso:adherent_entete_paiement').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_entete_justification').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_entete_journal').'</strong></td>';
	echo '<td><strong>&nbsp;</strong></td>';
	echo '</tr>';
	
	$query = spip_query ("SELECT * FROM spip_asso_comptes WHERE id_journal=$id_adherent ORDER BY date DESC" );
//$query = "SELECT * FROM spip_asso_comptes WHERE date_format( date, '%Y' ) = '$annee' AND imputation like '$imputation'  ORDER BY date DESC LIMIT $debut,$max_par_page";

	while ($data = spip_fetch_array($query)) {

		$class= "pair";

		echo '<tr> ';
		echo '<td class ='.$class.' style="text-align:right;">'.$data['id_compte'].'</td>';
//echo '<td class ='.$class.'>'.$jour.'-'.$mois. '-'.$annee.'</td>';
		echo '<td class ='.$class.'>'.association_datefr($data['date']).'</td>';
		echo '<td class ='.$class.'>'.$data['imputation'].'</td>';
		echo '<td class ='.$class.' style="text-align:right;">'.$data['recette'].' &euro;</td>';
		echo '<td class ='.$class.'>'.$data['justification'].'</td>';
		echo '<td class ='.$class.'>'.$data['journal'].'</td>';
		echo '<td class ='.$class.' style="text-align:center"><a href="'.$url_edit_compte.'&id='.$data['id_compte'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="'._T('asso:adherent_bouton_maj_operation').'"></a></td>';
//echo '<td class ='.$class.'><input name="delete[]" type="checkbox" value='.$data['id_compte'].'></td>';
		echo '</tr>';
	}
	echo '</table>';
	echo '</fieldset>';

// FICHE ACTIVITES	
	echo '<fieldset><legend>'._T('asso:adherent_titre_historique_activites').'</legend>';
	echo '<table width="70%" border="0">';
	echo '<tr bgcolor="silver">';
	echo '<td style="text-align:right;"><strong>'._T('asso:adherent_entete_id').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_entete_date').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_entete_activite').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_entete_lieu').'</strong></td>';
	echo '<td style="text-align:right;"><strong>'._T('asso:adherent_entete_inscrits').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_entete_statut').'</strong></td>';
	echo '<td><strong>&nbsp;</strong></td>';
	echo '</tr>';
	echo '<tr>';
	$query = spip_query ("SELECT * FROM spip_asso_activites WHERE id_adherent=$id_asso ORDER BY date DESC" );
	//$query = spip_query ("SELECT * FROM spip_asso_activites WHERE id_adherent=$id_adherent ORDER BY date DESC" );
	//$query = "SELECT * FROM spip_asso_comptes WHERE date_format( date, '%Y' ) = '$annee' AND imputation like '$imputation'  ORDER BY date DESC LIMIT $debut,$max_par_page";

	while ($data = spip_fetch_array($query)) {
		$class= "pair";
		$id_evenement=$data['id_evenement'];

		echo '<td class ='.$class.' style="text-align:right;">'.$data['id_activite'].'</td>';

		$sql = spip_query ("SELECT * FROM spip_evenements WHERE id_evenement=$id_evenement" );
		while ($evenement = spip_fetch_array($sql)) {
			$date = substr($evenement['date_debut'],0,10);
			echo '<td class ='.$class.'>'.association_datefr($date).'</td>';
			echo '<td class ='.$class.'>'.$evenement['titre'].'</td>';
			echo '<td class ='.$class.'>'.$evenement['lieu'].'</td>';
		}
		echo '<td class ='.$class.' style="text-align:right;">'.$data['inscrits'].'</td>';
		echo '<td class ='.$class.'>'.$data['statut'].'</td>';
		echo '<td class ='.$class.' style="text-align:center"><a href="'.$url_edit_activite.'&id='.$data['id_activite'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="'._T('asso:adherent_bouton_maj_inscription').'"></a></td>';
		echo '</tr>';
	}
	echo '</table>';
	echo '</fieldset>';
// ON FERME TOUT
	fin_boite_info();
	  
	fin_cadre_relief();

	fin_page();
} 
?>

