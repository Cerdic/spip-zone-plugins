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
	

	include_spip('inc/navigation_modules');
	
	function exec_adherents() {
		
		global  $table_prefix;
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		$url_association = generer_url_ecrire('association');
		$url_adherents = generer_url_ecrire('adherents');
		$url_ajout_cotisation = generer_url_ecrire('ajout_cotisation');
		$url_editer_auteur = generer_url_ecrire('auteur_infos');
		$url_edit_adherent = generer_url_ecrire('edit_adherent');
		$url_voir_adherent = generer_url_ecrire('voir_adherent');
		$url_action_adherents = generer_url_ecrire('action_adherents');
		$url_edit_relances=generer_url_ecrire('edit_relances');
		$url_pdf_adherents = generer_url_ecrire('pdf_adherents');
		$indexation = lire_config('association/indexation');
		
		//debut_page(_T('asso:titre_gestion_pour_association'), "", "");
		 $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:association')) ;
		association_onglets();
		
		echo debut_gauche("",true);
		
		$critere = request_statut_interne(); // peut appeler set_request
		$statut_interne = _request('statut_interne');

		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo '<p>'._T('asso:adherent_liste_legende').'</p>'; 
		
		// TOTAUX	
		
		echo '<div><strong>'._T('asso:adherent_liste_nombre').'</strong></div>';
		$nombre=0;
		$membres = $GLOBALS['association_liste_des_statuts'];
		array_shift($membres); // ancien membre
		foreach ($membres as $statut) {
			$query = association_auteurs_elargis_select("*",'', "statut_interne='$statut'");
			$nombre=sql_count($query);
			echo '<div style="float:right;text_align:right">'.$nombre.'</div>';
			echo '<div>'._T('asso:adherent_liste_nombre_'.$statut).'</div>';
			$nombre_total += $nombre;
		}		
		echo '<div style="float:right;text_align:right">'.$nombre_total.'</div>';
		echo '<div>'._T('asso:adherent_liste_nombre_total').'</div>';
		echo fin_boite_info(true);	
		
		
		$res=icone_horizontale(_T('asso:menu2_titre_relances_cotisations'), $url_edit_relances,  _DIR_PLUGIN_ASSOCIATION_ICONES.'ico_panier.png','rien.gif',false );
		$res.=icone_horizontale(_T('asso:bouton_impression'), $url_pdf_adherents.'&statut_interne='.$statut_interne,  _DIR_PLUGIN_ASSOCIATION_ICONES.'print-24.png','rien.gif',false ); 
		$res.=icone_horizontale(_T('Param&egrave;tres'), $url_association,  _DIR_PLUGIN_ASSOCIATION_ICONES.'annonce.gif','rien.gif',false ); 
			echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", true, "", $titre = _T('asso:adherent_titre_liste_actifs'));		
		
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2'>\n";
		echo "<tr>";
		
		// PAGINATION ALPHABETIQUE
		echo '<td>';
		
		$lettre=$_GET['lettre'];
		if ( empty ( $lettre ) ) { $lettre = "%"; }
		
		$query = association_auteurs_elargis_select("upper( substring( nom_famille, 1, 1 ) )  AS init", '', '',  'init', 'nom_famille, id_auteur');
		
		while ($data = sql_fetch($query)) {
			if($data['init']==$lettre) {
				echo ' <strong>'.$data['init'].'</strong>';
			}
			else {
				echo ' <a href="'.$url_adherents.'&lettre='.$data['init'].'&statut_interne='.$statut_interne.'">'.$data['init'].'</a>';
			}
		}
		if ($lettre == "%") { echo ' <strong>'._T('asso:adherent_entete_tous').'</strong>'; }
		else { echo ' <a href="'.$url_adherents.'&statut_interne='.$statut_interne.'">'._T('asso:adherent_entete_tous').'</a>'; }
		
		// FILTRES
		echo '<td style="text-align:right;">';
		
		//Filtre ID
		if ( isset ($_POST['id'])) {
			$id=_q($_POST['id']);
			$critere="id_auteur=$id";
			if ($indexation=="id_asso") { $critere="id_asso=$id"; }
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
		echo '<select name ="statut_interne" class="fondl" onchange="form.submit()">';
		foreach ($GLOBALS['association_liste_des_statuts'] as $statut) {
			echo '<option value="'.$statut.'"';
			if ($statut_interne==$statut) {echo ' selected="selected"';}
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
		echo '<td colspan="4" style="text-align:center;"><strong>'._T('asso:adherent_entete_action').'</strong></td>';
		echo '<td><strong>'._T('asso:adherent_entete_supprimer_abrev').'</strong></td>';
		echo '</tr>';
		
		$max_par_page=30;
		$debut=$_GET['debut'];
		
		if (empty($debut)) { $debut=0; }
		if (empty($lettre)) 
			$critere .= " AND upper( substring( nom_famille, 1, 1 ) ) like '$lettre' ";
		$chercher_logo = charger_fonction('chercher_logo', 'inc');
		$query = association_auteurs_elargis_select("*", " a LEFT JOIN spip_auteurs b ON a.id_auteur=b.id_auteur", $critere, '', "nom_famille ", "$debut,$max_par_page" );
		while ($data = spip_fetch_array($query)) {	
			$id_auteur=$data['id_auteur'];			
			switch($data['statut_interne'])	{
				case "echu": $class= "impair"; break;
				case "ok": $class="valide";	break;
				case "relance": $class="pair"; break;
				case "sorti": $class="sortie"; break;
				default : $class="prospect"; break;	   
			}
			
			echo '<tr> ';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;" class ='.$class.'>';
			echo ($indexation=="id_asso") ? $data["id_asso"] : $id_auteur;
			echo '</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ="'.$class.'">';

			$logo = $chercher_logo($id_auteur, 'id_auteur');
			if ($logo) {
			  echo '<img src="', $logo[0],  '" alt="&nbsp;" width="60"  title="'.$data["nom_famille"].' '.$data["prenom"].'">';
			}else{
			  echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'ajout.gif" alt="&nbsp;" width="10"  title="'.$data["nom_famille"].' '.$data["prenom"].'">';
			}
			echo '</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>';
			if (empty($data["email"])) { 
				echo $data["nom_famille"].'</td>'; 
			} else {
			echo '<a href="mailto:'.$data["email"].'">'.$data["nom_famille"].'</a></td>';
			}
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>'.$data["prenom"].'</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>';
			$sql=sql_select("valeur", "spip_asso_categories", "id_categorie=".intval($data["categorie"]));
			$categorie=spip_fetch_array($sql);
			echo $categorie['valeur'];			
			echo '</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>';
			if ($data['validite']==""){echo '&nbsp;';}else{echo association_datefr($data['validite']);}
			echo '</td>';
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
			echo '<a href="'.$url_editer_auteur.'&id_auteur='.$data['id_auteur'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.$logo.'" title="'._T('asso:adherent_label_modifier_visiteur').'"></a></td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'><a href="'.$url_ajout_cotisation.'&id='.$data['id_auteur'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'cotis-12.gif" title="'._T('asso:adherent_label_ajouter_cotisation').'"></a></td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'><a href="'.$url_edit_adherent.'&id='.$data['id_auteur'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="'._T('asso:adherent_label_modifier_membre').'"></a></td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'><a href="'.$url_voir_adherent.'&id='.$data['id_auteur'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'voir-12.png" title="'._T('asso:adherent_label_voir_membre').'"></a></td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'><input name="delete[]" type="checkbox" value='.$data['id_auteur'].'></td>';
			echo '</tr>';
		}
		
		echo '</table>';
		
		//SOUS-PAGINATION
		echo '<table width=100%>';
		echo '<tr>';	
		echo '<td>';

		$query = association_auteurs_elargis_select("*",'', $critere);
		$nombre_selection=sql_count($query);
		$pages=intval($nombre_selection/$max_par_page) + 1;
		
		if ($pages != 1)	{
			for ($i=0;$i<$pages;$i++)	{ 
				$position= $i * $max_par_page;
				if ($position == $debut)	{
					echo '<strong>'.$position.' </strong>';
				}
				else {
					echo '<a href="'.$url_adherents.'&lettre='.$lettre.'&debut='.$position.'&statut_interne='.$statut_interne.'">'.$position.'</a> ';
				}
			}	
		}
		
		echo '<td  style="text-align:right;">';
		echo '<input type="submit" name="Submit" value="'._T('asso:bouton_supprimer').'" class="fondo">';
		echo '</td>';
		echo '</table>';
		echo '</form>';
		
		echo fin_cadre_relief(true);  
		 echo fin_gauche(), fin_page();
	}
?>
