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
	include_spip('inc/gestion_base');
	include_spip ('inc/navigation_modules');
	
	function exec_association() {
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip ('inc/acces_page');
		
		association_verifier_base();		
		
		debut_page(_T('asso:association'), "naviguer", "association");
		
		$url_edit_adherent = generer_url_ecrire('edit_adherent');
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo propre(_T('asso:info_doc'));  	
		fin_boite_info();
		
		include_spip('inc/raccourcis');
		
		debut_droite();	
		
		debut_cadre_formulaire();
		gros_titre(_T('asso:votre_asso'));
		echo '<br>';		
		echo '<strong>'.lire_config('association/nom').'</strong><br>';
		echo lire_config('association/rue').'<br>';
		echo lire_config('association/cp').'&nbsp;';
		echo lire_config('association/ville').'<br>';
		echo lire_config('association/telephone').'<br>';
		echo lire_config('association/email').'<br>';
		echo lire_config('association/siret').'<br>';
		echo lire_config('association/declaration').'<br>';
		echo lire_config('association/prefet').'<br>';
		fin_cadre_formulaire();
		
		/* Provisoirement supprimé en attendant 1.9.3
		
		echo '<br />';
		gros_titre(_T('asso:votre_equipe'));		
		echo '<br />';	
		
		debut_cadre_relief();
		
		echo '<table border=0 cellpadding=2 cellspacing=0 width="100%" class="arial2" style="border: 1px solid #aaaaaa;">';
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td><strong>Nom</strong></td>';
		echo '<td><strong>Email</strong></td>';
		echo '<td><strong>Fonction</strong></td>';
		echo '<td><strong>Portable</strong></td>';
		echo '<td><strong>T&eacute;l&eacute;phone</strong></td>';
		echo '</tr>';
		$query = spip_query("SELECT * FROM spip_asso_adherents INNER JOIN spip_auteurs ON spip_auteurs_elargis.id_auteur=spip_auteurs.id_auteur WHERE fonction != '' AND statut_relance != 'sorti' ORDER BY nom_famille ");
		while ($data = mysql_fetch_assoc($query)) {	
			$id_auteur=$data['id_auteur'];
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;"><a href="'.generer_url_ecrire('auteur_infos',"id_auteur=$id_auteur").'" title="Modifier l\'administrateur">'.$data['nom'].' '.$data['prenom'].'</a></td>';
			echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;"><a href="mailto:'.$data['email'].'"title="Envoyer un email">email</a></td>';
			echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;">'.$data['fonction'].'</td>';
			echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;">'.$data['mobile'].'</td>';
			echo '<td class="arial1" style="border-top: 1px solid #CCCCCC;">'.$data['telephone'].'</td>';
			echo '</tr>';
		}				
		echo '</table>';
		
		fin_cadre_relief();	
		
		*/
		
		fin_page();
		
		//Petite routine pour mettre à jour les statuts de cotisation "échu"
		spip_query("UPDATE spip_asso_adherents SET statut_relance='echu' WHERE statut_relance = 'ok' AND validite < CURRENT_DATE() ");
		
	}
?>
