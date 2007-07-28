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
	include_spip ('inc/navigation_modules');

	function exec_plan(){
		global $connect_statut, $connect_toutes_rubriques;
		
		if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}
		
		debut_page(_T('plan comptable'), "", "");
		
		$url_plan = generer_url_ecrire('plan');
		$url_edit_plan=generer_url_ecrire('edit_plan');
		$url_action_plan=generer_url_ecrire('action_plan');
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		
		echo association_date_du_jour();	
		
		fin_boite_info();
		
		debut_raccourcis();
		icone_horizontale(_T('asso:ajout_plan'), generer_url_ecrire('edit_plan','action=ajoute'), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/EuroOff.gif', 'creer.gif');
		echo 'retour';
		fin_raccourcis();
				
		debut_droite();
		
		debut_cadre_relief(  "../"._DIR_PLUGIN_ASSOCIATION."/img_pack/EuroOff.gif", false, "", $titre = _T('asso:plan_comptable'));
		
		$classe = '%';
		$actif = 'oui'; 
		if ( isset ($_REQUEST['classe'] )) { $classe = $_REQUEST['classe']; }
		if ( isset ($_REQUEST['actif'] )) { $actif = $_REQUEST['actif']; }
		
		echo '<table width="100%">';
		
		// Filtre classes
		echo '<tr>';
		echo '<td>';
		
		$query = spip_query ("SELECT DISTINCT classe, actif  FROM spip_asso_plan WHERE actif='$actif' ORDER BY classe");
		
		while ($data = spip_fetch_array($query)) {
			if ($data['classe']==$class)	{echo ' <strong>'.$data['classe'].' </strong>';}
			else {echo '<a href="'.$url_plan.'&classe='.$data['classe'].'">'.$data['classe'].'</a> ';}
		}
		if ($classe == "%") { echo ' <strong>'._T('asso:plan_entete_tous').'</strong>'; }
		else { echo ' <a href="'.$url_plan.'">'._T('asso:plan_entete_tous').'</a>'; }
		echo '</td>';
		
		echo '<td style="text-align:right;">';
		
		//Filtre actif
		echo '<form method="post" action="'.$url_plan.'">';
		echo '<input type="hidden" name="classe" value="'.$classe.'">';
		echo '<select name ="actif" class="fondl" onchange="form.submit()">';
		echo '<option value="oui" ';
		if ($actif=='oui') {echo ' selected="selected"';}
		echo '> '._T('asso:plan_comptes_actifs').'</option>';
		echo '<option value="non" ';
		if ($actif=='non') {echo ' selected="selected"';}
			echo '> '._T('asso:plan_comptes_desactives').'</option>';
		echo '</select>';
		echo '</form>';
		echo '</td>';
		
		echo '</tr></table>';
		
		//Affichage de la table
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td><strong>Classe</strong></td>';
		echo '<td><strong>Code</strong></td>';
		echo '<td><strong>Intitul&eacute;</strong></td>';
		echo '<td><strong>R&eacute;f&eacute;rence</strong></td>';
		echo '<td style="text-align:right;"><strong>Solde initial</strong></td>';
		echo '<td><strong>Date</strong></td>';
		echo '<td colspan=2 style="text-align:center;"><strong>Action</strong></td>';
		echo'  </tr>';
		$query = spip_query ( "SELECT * FROM spip_asso_plan WHERE classe LIKE '$classe' AND actif='$actif' ORDER by classe, code" );
		while ($data = spip_fetch_array($query)) {
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.$data['classe'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['code'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['intitule'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['reference'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.number_format($data['solde_anterieur'], 2, ',', ' ').' &euro;</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.association_datefr($data['date_anterieure']).'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_action_plan.'&action=supprime&id='.$data['id_plan'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/poubelle-12.gif" title="Supprimer"></a></td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_edit_plan.'&action=modifie&id='.$data['id_plan'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Modifier"></a></td>';
			echo'  </tr>';
		}     
		echo'</table>';
		
		fin_cadre_relief();  
		
		fin_page();
	}
?>
