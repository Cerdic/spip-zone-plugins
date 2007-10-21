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
	
	function exec_adherents() {
		
		global $connect_statut, $connect_toutes_rubriques, $table_prefix;
		
		include_spip ('inc/acces_page');
		
		$url_adherents = generer_url_ecrire('adherents');
		$url_ajout_cotisation = generer_url_ecrire('ajout_cotisation');
		$url_edit_adherent = generer_url_ecrire('editer_adherent','act=val');
		$url_voir_adherent = generer_url_ecrire('voir_adherent');
		$url_action_adherents = generer_url_ecrire('action_adherents');
		$url_edit_relances=generer_url_ecrire('edit_relances');
		$url_pdf_adherents = generer_url_ecrire('pdf_adherents');
		$indexation = lire_config('association/indexation');
		
		debut_page(_T('asso:titre_gestion_pour_association'), "", "");
		
		association_onglets();
		
		debut_gauche();
		
		if ( isset ($_REQUEST['filtre'] )) { $filtre = $_REQUEST['filtre']; }
		else { $filtre = 'ok'; }
		
		switch($filtre) {
			case "ok": $critere="statut_interne='ok'";break;
			case "echu": $critere="statut_interne='echu'";break;
			case "relance": $critere="statut_interne='relance'";break;
			case "sorti": $critere="statut_interne='sorti'";break;	   
			case "prospect": 
			$var=lire_config('inscription2/statut_interne');
			$critere="statut_interne='$var'";break;
			case "tous": $critere="statut_interne LIKE '%'";break;	
		}			
		
		// TOTAUX
		$query = spip_query ( "SELECT * FROM spip_auteurs_elargis WHERE statut_interne ='ok' " );
		$nombre_membres=spip_num_rows($query);		
		
		debut_boite_info();
		//echo association_date_du_jour();	
		echo '<p>'._T('asso:adherent_liste_legende').'</p>'; 
		echo '<p>';
		echo '<font color="blue"><strong>'._T('asso:adherent_liste_nombre_adherents',array('total' => $nombre_membres)).'</strong></font>';
		echo '</p>';
		fin_boite_info();	
		
		debut_raccourcis();
		icone_horizontale(_T('asso:menu2_titre_relances_cotisations'), $url_edit_relances,  '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ico_panier.png','rien.gif' ); 
		icone_horizontale(_T('asso:bouton_impression'), $url_pdf_adherents.'&statut_interne='.$filtre,  '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/print-24.png','rien.gif' ); 
		fin_raccourcis();
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_titre_liste_actifs'));		
		
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2'>\n";
		echo "<tr>";
		
		// PAGINATION ALPHABETIQUE
		echo '<td>';
		
		$lettre=$_GET['lettre'];
		if ( empty ( $lettre ) ) { $lettre = "%"; }
		
		$query = spip_query ( "SELECT upper( substring( nom_famille, 1, 1 ) )  AS init FROM spip_auteurs_elargis GROUP BY init ORDER by nom_famille, id ");
		
		while ($data = spip_fetch_array($query)) {
			if($data['init']==$lettre) {
				echo ' <strong>'.$data['init'].'</strong>';
			}
			else {
				echo ' <a href="'.$url_adherents.'&lettre='.$data['init'].'&filtre='.$filtre.'">'.$data['init'].'</a>';
			}
		}
		if ($lettre == "%") { echo ' <strong>'._T('asso:adherent_entete_tous').'</strong>'; }
		else { echo ' <a href="'.$url_adherents.'&filtre='.$filtre.'">'._T('asso:adherent_entete_tous').'</a>'; }
		
		// FILTRES
		echo '<td style="text-align:right;">';
		
		//Filtre ID
		if ( isset ($_POST['id'])) {
			$id=$_POST['id'];
			if ($indexation=="id_asso") { $critere="id_asso='$id'"; }
			else { $critere="id='$id'"; }
		}
		
		echo '<form method="post" action="'.$url_adherent.'">';
		echo '<input type="text" name="id"  class="fondl" style="padding:0.5px" onfocus=\'this.value=""\' size="10" ';
		if ($indexation=='id_asso') { echo ' value="'._T('asso:adherent_libelle_id_asso').'" '; }
		else { echo ' value="'._T('asso:adherent_libelle_id_adherent').'" ';}
		echo ' onchange="form.submit()">';
		echo '</form>';
		echo '</td>';
		echo '<td style="text-align:right;">';
		
		//Filtre statut
		echo '<form method="post" action="'.$url_adherent.'">';
		echo '<input type="hidden" name="lettre" value="'.$lettre.'">';
		echo '<select name ="filtre" class="fondl" onchange="form.submit()">';
		foreach (array(ok,echu,relance,sorti,lire_config('inscription2/statut_interne'),tous) as $statut) {
			echo '<option value="'.$statut.'"';
			if ($filtre==$statut) {echo ' selected="selected"';}
			echo '> '._T('asso:adherent_entete_statut_'.$statut).'</option>';
		}
		echo '</select>';
		echo '</form>';
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		
		//Affichage de la liste
			
		echo '<form method="post" action="'.$url_action_adherents.'">';
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td><strong>';
		if ($indexation=="id_asso") { echo _T('asso:adherent_libelle_id_asso');}
		else { echo _T('asso:adherent_libelle_id_adherent');} 
		echo '</strong></td>';
		echo '<td><strong>'._T('asso:adherent_libelle_photo').'</strong></td>';
		echo '<td><strong>'._T('asso:adherent_libelle_nom').'</strong></td>';
		echo '<td><strong>'._T('asso:adherent_libelle_prenom').'</strong></td>';
		echo '<td><strong>'._T('asso:adherent_libelle_categorie').'</strong></td>';
		echo '<td><strong>'._T('asso:adherent_libelle_validite').'</strong></td>';
		echo '<td colspan="3" style="text-align:center;"><strong>'._T('asso:adherent_entete_action').'</strong></td>';
		echo '<td><strong>'._T('asso:adherent_entete_supprimer_abrev').'</strong></td>';
		echo '</tr>';
		
		$max_par_page=30;
		$debut=$_GET['debut'];
		
		if (empty($debut)) { $debut=0; }
		if (!empty($lettre)) {$critere2="AND upper( substring( nom_famille, 1, 1 ) ) like '$lettre' ";}
		$query = spip_query ( "SELECT * FROM spip_auteurs_elargis LEFT JOIN spip_asso_adherents ON spip_auteurs_elargis.id_auteur=spip_asso_adherents.id_auteur LEFT JOIN spip_auteurs ON spip_auteurs.id_auteur=spip_auteurs_elargis.id_auteur WHERE $critere ".$critere2." ORDER BY nom_famille LIMIT $debut,$max_par_page" );
		while ($data = spip_fetch_array($query)) {	
			$id_adherent=$data['id'];
			switch($data['statut_interne'])	{
				case "echu": $class= "impair"; break;
				case "ok": $class="valide";	break;
				case "relance": $class="pair"; break;
				case "sorti": $class="sortie"; break;
				default : $class="prospect"; break;	   
			}
			
			echo '<tr> ';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;" class ='.$class.'>';
			if ($indexation=="id_asso") { echo $data["id_asso"];}
			else { echo $data["id"];}
			echo '</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ="'.$class.'">';
			
			if ( !empty ($data['spip_auteurs.id_auteur'])) {
				echo'<img src="/IMG/auton'.$data['id_auteur'].'.jpg" alt="&nbsp;" width="60" height= "60" title="'.$data["nom_famille"].' '.$data["prenom"].'">';
			}
			echo '</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>';
			if (empty($data["email"])) { 
				echo $data["nom_famille"].'</td>'; 
			} else {
			echo '<a href="mailto:'.$data["email"].'">'.$data["nom_famille"].'</a></td>';
			}
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>'.$data["prenom"].'</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>'.$data["categorie"].'</td>';
			//echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>'.association_datefr($data['validite']).'</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>'.$data['validite'].'</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>';
			
			switch($data['statut'])	{
				case "0minirezo":
					$logo= "admin-12.gif"; break;
				case "1comite":
					$logo="redac-12.gif"; break;
				case "5poubelle":
					$logo="poubelle-12.gif"; break; 
				case "6forum":
					$logo="visit-12.gif"; break;	
				default :
					$logo="adher-12.gif"; break;
			}
			echo '<a href="'.$url_edit_adherent.'&id='.$data['id_auteur'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/'.$logo.'" title="'._T('asso:adherent_label_modifier_visiteur').'"></a></td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'><a href="'.$url_ajout_cotisation.'&id='.$data['id_inscription'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/cotis-12.gif" title="'._T('asso:adherent_label_ajouter_cotisation').'"></a></td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'><a href="'.$url_voir_adherent.'&id='.$data['id_inscription'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/voir-12.gif" title="'._T('asso:adherent_label_voir_membre').'"></a></td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'><input name="delete[]" type="checkbox" value='.$data['id_inscription'].'></td>';
			echo '</tr>';
		}
		
		echo '</table>';
		
		//SOUS-PAGINATION
		echo '<table width=100%>';
		echo '<tr>';	
		echo '<td>';
		if (!empty($lettre)) {"AND upper( substring( nom_famille, 1, 1 ) ) like '$lettre' ";}
		$query = spip_query( "SELECT * FROM spip_auteurs_elargis WHERE $critere ".$critere2);
		$nombre_selection=spip_num_rows($query);
		$pages=intval($nombre_selection/$max_par_page) + 1;
		
		if ($pages != 1)	{
			for ($i=0;$i<$pages;$i++)	{ 
				$position= $i * $max_par_page;
				if ($position == $debut)	{
					echo '<strong>'.$position.' </strong>';
				}
				else {
					echo '<a href="'.$url_adherents.'&lettre='.$lettre.'&debut='.$position.'&filtre='.$filtre.'">'.$position.'</a> ';
				}
			}	
		}
		
		echo '<td  style="text-align:right;">';
		echo '<input type="submit" name="Submit" value="'._T('asso:bouton_supprimer').'" class="fondo">';
		echo '</td>';
		echo '</table>';
		echo '</form>';
		
		fin_cadre_relief();  
		fin_page();
	}
?>
