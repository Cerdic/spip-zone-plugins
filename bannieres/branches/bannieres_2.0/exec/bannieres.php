<?php
	/**
	* Plugin Bannières
	*
	* Copyright (c) 2009
	* François de Montlivault
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
	
	include_spip('inc/gestion_base');
	
	function exec_bannieres() {
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip ('inc/acces_page');	
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('ban:gestion_bannieres'));
		
		echo debut_gauche('',true);
		
		echo debut_boite_info(true);
		echo propre(_T('ban:info_doc'));  	
		echo fin_boite_info(true);
		
		$rac=icone_horizontale(_T('ban:creer_campagne'), generer_url_ecrire("edit_banniere","edit=ajoute"), '../'._DIR_PLUGIN_BANNIERES.'/img_pack/bannieres.png', 'creer.gif',false);	
		echo bloc_des_raccourcis($rac);
		
		echo debut_droite('',true);	
		
		echo '<br />';
		gros_titre(_T('ban:gestion_bannieres'));		
		echo '<br />';	
		
		echo debut_cadre_relief('',true);
		
		echo '<table border=0 cellpadding=2 cellspacing=0 width="100%" class="arial2" style="border: 1px solid #aaaaaa;">';
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td colspan="2"><strong>&nbsp;</strong></td>';
		echo '<td><strong>Campagne</strong></td>';
		echo '<td><strong>D&eacute;but</strong></td>';
		echo '<td><strong>Fin</strong></td>';
		echo '<td><strong>Clics</strong></td>';
		echo '<td><strong>Commentaire</strong></td>';
		echo '<td colspan="2"><strong>Action</strong></td>';
		echo '</tr>';
		$query = spip_query("SELECT * FROM spip_bannieres ORDER BY id_banniere ");
		while ($data = mysql_fetch_assoc($query)) {	
			if($data['debut']<date('Y-m-d')) {$puce="verte";} else {$puce="blanche";}
			if($data['fin']<date('Y-m-d')) {$puce="rouge";}
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;">'.$data['id_banniere'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;"><img src="/prive/images/puce-'.$puce.'.gif"></td>';
			echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;"><a href="mailto:'.$data['email'].'"title="Envoyer un email">'.$data['nom'].'</a></td>';
			echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;">'.bannieres_datefr($data['debut']).'</td>';
			echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;">'.bannieres_datefr($data['fin']).'</td>';
			echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;">'.$data['clics'].'</td>';
			echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;">'.$data['commentaires'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;"><a href="'.generer_url_ecrire("action_bannieres","edit=supprime&id=".$data['id_banniere']).'" title="Supprimer la campagne"><img src="'._DIR_PLUGIN_BANNIERES.'/img_pack/poubelle-12.gif" title="Supprimer"></a></td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;"><a href="'.generer_url_ecrire("edit_banniere","edit=modifie&id=".$data['id_banniere']).'" title="Modifier la campagne"><img src="'._DIR_PLUGIN_BANNIERES.'/img_pack/edit-12.gif" title="Modifier"></a></td>';
			echo '</tr>';
		}				
		echo '</table>';
		
		echo fin_cadre_relief(true);	
		
		echo fin_page();
		
	}
?>
