<?php
/**
* Plugin Association
*
* Copyright (c) 2007
* Bernard Blazin & FranÃ§ois de Montlivault
* http://www.plugandspip.com 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/
include_spip('inc/presentation');

function exec_abonnes_payants() {

	global $connect_statut, $connect_toutes_rubriques, $table_prefix;

	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	debut_page(_T('asso:titre_gestion_pour_association'), "", "");

	// definir une meta qui flag le fork du plugin asso pour gerer des abonnements
	
	if(!isset($GLOBALS['meta']['asso_devient_abo'])) {
	 //echo "coucou" ;
	}
	
	
	
	$url_adherents = generer_url_ecrire('adherents');
	$url_ajout_cotisation = generer_url_ecrire('ajout_cotisation');
	$url_edit_adherent = generer_url_ecrire('edit_adherent');
	$url_voir_adherent = generer_url_ecrire('voir_adherent');
	$url_action_adherents = generer_url_ecrire('action_adherents');
	$url_pdf_adherents = generer_url_ecrire('pdf_adherents');
	
debut_gauche();
     debut_boite_info();
     echo propre("Gestion des abonn&eacute;s");  	
     fin_boite_info();

	include_spip('inc/raccourcis_nav');

	debut_droite();	
	
	debut_cadre_formulaire();
	
	icone_horizontale(_T('asso:menu2_titre_ajouter_abonne'),generer_url_ecrire('ajout_adherent'), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','creer.gif');
	
	fin_cadre_formulaire();
	
	echo "<br /><br />";
	
	debut_cadre_relief(  "", false, "", $titre = "Tous les abonn&eacute;s");
	
	debut_boite_info();

	print association_date_du_jour();
	
	$query=spip_query( "SELECT * FROM spip_asso_profil WHERE id_profil=1");
	$data=spip_fetch_array($query);
	$indexation=$data['indexation'];

	if ( isset ($_REQUEST['filtre'] )) { $filtre = $_REQUEST['filtre']; }
	else { $filtre = 'defaut'; }
		
	switch($filtre) {
		case "defaut":
			$critere= "statut<>'sorti'";
			break;
		case "ok":
			$critere="statut='ok'";
			break;
		case "echu":
			$critere="statut='echu'";  
			break;
		case "relance":
			$critere="statut='relance'";
			break;
		case "sorti":
			$critere="statut='sorti'";
			break;	   
		case "prospect":
			$critere="statut='prospect'"; 
			break;
		case "erreur_bank":
			$critere="statut='erreur_bank'"; 
			break;
		case "tous":
			$critere="statut LIKE '%'";
			break;	
	}

	echo "<style>.erreur_bank{background-color:red}</style>";
	
	echo '<table width="100%">';
	echo '<tr>';

// PAGINATION ALPHABETIQUE
	echo '<td>';

	$lettre=$_GET['lettre'];
	if ( empty ( $lettre ) ) { $lettre = "%"; }

	$query = spip_query ( "SELECT upper( substring( nom, 1, 1 ) )  AS init FROM spip_asso_adherents WHERE $critere GROUP BY init ORDER by nom, id_adherent ");

	while ($data = spip_fetch_array($query)) {
		if($data['init']==$lettre) {
			echo ' <strong>'.$data['init'].'</strong>';
		}
		else {
			
			echo ' <a href="'.generer_url_ecrire("abonnes_payants","lettre=".$data['init']."&filtre=$filtre").'">'.$data['init'].'</a>';
		}
	}
	if ($lettre == "%") { echo ' <strong>'._T('asso:adherent_entete_tous').'</strong>'; }
	else { echo ' <a href="'.generer_url_ecrire("abonnes_payants","filtre=$filtre").'">'._T('asso:adherent_entete_tous').'</a>'; }

	echo '<td>';
	echo '<a href="'.$url_pdf_adherents.'&'.$critere.'">Imprimer</a>';
	echo '</td>';
	
// FILTRES
	echo '<td style="text-align:right;">';
	
// ID
	if ( isset ($_POST['id'])) {
		$id=$_POST['id'];
		if ($indexation=="ref") { $critere="id_asso='$id'"; }
		else { $critere="id_adherent='$id'"; }
	}

	echo '<form method="post" action="'.$url_adherent.'">';
	echo '<input type="text" name="id"  class="fondl" style="padding:0.5px" onfocus=\'this.value=""\' size="10" ';
	if ($indexation=='ref') { echo ' value="'._T('asso:adherent_libelle_reference_interne_abrev').'" '; }
	else { echo ' value="ID" ';}
	echo ' onchange="form.submit()">';
	echo '</form>';
	echo '</td>';
	echo '<td style="text-align:right;">';
//STATUT
	echo '<form method="post" action="'.$url_adherent.'">';
	echo '<input type="hidden" name="lettre" value="'.$lettre.'">';
	echo '<select name ="filtre" class="fondl" onchange="form.submit()">';
	foreach (array('defaut','ok','echu','relance','erreur_bank','sorti','prospect','tous') as $statut) {
		echo '<option value="'.$statut.'"';
		if ($filtre==$statut) {echo ' selected="selected"';}
		echo '> '._T('asso:adherent_entete_statut_'.$statut) .'</option>';
	}
	echo '</select>';
	echo '</form>';
	echo '</td>';
	echo '</tr>';
	echo '</table>';

//Affichage de la liste
	echo '<table width="100%">';
	echo '<form method="post" action="'.$url_action_adherents.'">';
	echo '<tr bgcolor="#D9D7AA">';
	echo '<td><strong>'._T('asso:adherent_libelle_id').'</strong></td>';
	//echo '<td><strong>'._T('asso:adherent_libelle_photo').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_libelle_nom').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_libelle_prenom').'</strong></td>';
	//echo '<td><strong>'._T('asso:adherent_libelle_fonction').'</strong></td>';
	//echo '<td><strong>'._T('asso:adherent_libelle_email').'</strong></td>';
	//echo '<td><strong>'._T('asso:adherent_libelle_num_rue').'</strong></td>';
	//echo '<td><strong>'._T('asso:adherent_libelle_rue').'</strong></td>';
	//echo '<td><strong>'._T('asso:adherent_libelle_code_postal').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_libelle_ville').'</strong></td>';
	//echo '<td><strong>'._T('asso:adherent_libelle_portable').'</strong></td>';
	//echo '<td><strong>'._T('asso:adherent_libelle_telephone').'</strong></td>';
	//echo '<td><strong>'._T('asso:adherent_libelle_reference_interne_abrev').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_libelle_categorie').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_libelle_validite').'</strong></td>';
	//echo '<td><strong>'._T('asso:adherent_entete_statut_ok').'</strong></td>';
	//echo '<td><strong>'._T('asso:adherent_entete_notes').'</strong></td>';
	echo '<td colspan="4" style="text-align:center;"><strong>'._T('asso:adherent_entete_action').'</strong></td>';
	echo '<td><strong>'._T('asso:adherent_entete_supprimer_abrev').'</strong></td>';
	echo '</tr>';

	$max_par_page=30;
	$debut=$_GET['debut'];

	if (empty($debut)) { $debut=0; }

	if (empty($lettre)) {
		$query = spip_query ( "SELECT spip_asso_adherents.*, spip_asso_categories.libelle AS libelle_categorie FROM spip_asso_adherents 
		LEFT JOIN spip_asso_categories ON (spip_asso_categories.valeur=spip_asso_adherents.categorie) 
		WHERE $critere ORDER BY nom LIMIT $debut,$max_par_page" );
	}
	else {
		$query = spip_query ( "SELECT spip_asso_adherents.*, spip_asso_categories.libelle AS libelle_categorie FROM spip_asso_adherents 
		LEFT JOIN spip_asso_categories ON (spip_asso_categories.valeur=spip_asso_adherents.categorie) 
		WHERE upper( substring( nom, 1, 1 ) ) like '$lettre' AND $critere ORDER BY nom LIMIT $debut,$max_par_page" );
	}

	$i=0;

	while ($data = spip_fetch_array($query)) {	
		$i++;
		$id_adherent=$data['id_adherent'];

		switch($data['statut'])	{
			case "echu":
				$class= "impair";
				break;
			case "ok":
				$class="valide";
				break;
			case "relance":
				$class="pair";	   
				break;
			case "sorti":
				$class="sortie";	   
				break;
			case "prospect":
				$class="prospect";	   
				break;	 
			case "erreur_bank":
				$class="erreur_bank";	   
				break;	   
		}

		echo '<tr> ';
		echo '<td class ='.$class.' style="text-align:right;">'.$data["id_adherent"].'</td>';
/*		echo '<td class ="'.$class.'">';
		if ( !empty ($data['id_auteur'])) {
			echo'<img src="/IMG/auton'.$data['id_auteur'].'.jpg" width="60" eight= "60" title="'.$data["nom"].' '.$data["prenom"].'">';
		}

if (empty ($data['vignette']))
{echo'';}
else {echo'<img src="/IMG/assologo'.$data['id_adherent'].'" width="60" eight= "60" title="'.$data["nom"].' '.$data["prenom"].'">';}

		echo '</td>';
*/		echo '<td class ='.$class.'>';
		if (empty($data["email"])) { echo $data["nom"].'</td>'; }
		else	{
			echo '<a href="mailto:'.$data["email"].'">'.$data["nom"].'</a></td>';
		}
		echo '<td class ='.$class.'>'.$data["prenom"].'</td>';
//echo '<td class ='.$class.'>'.$data["fonction"].'</td>';
//echo '<td class ='.$class.' style="text-align:right;">'.$data["numero_ad"].'</td>';
//echo '<td class ='.$class.'>'.$data["rue_ad"].'</td>';
//echo '<td class ='.$class.'>'.$data["cp_ad"].'</td>';
		echo '<td class ='.$class.'>'.$data["ville"].'</td>';
//echo '<td class ='.$class.'>'.$data["portable"].'</td>';
//echo '<td class ='.$class.'>'.$data["telephone"].'</td>';
		//echo '<td class ='.$class.' style="text-align:right;">'.$data["id_asso"].'</td>'; //référence interne
		echo '<td class ='.$class.'>'.$data["libelle_categorie"].'</td>';
		echo '<td class ='.$class.'>'.association_datefr($data['validite']).'</td>';
//echo '<td class ='.$class.' style="text-align:center;"><img src="/ecrire/img_pack/'.$puce.'" title="'.$title.'"></td>';
//echo '<td class ='.$class.'>'.$data["remarques"].'</td>';
		echo '<td class ='.$class.'>';
		
		if (isset($data["id_auteur"])) {
			$id_auteur= $data["id_auteur"];
			$sql = spip_query ( "SELECT * FROM spip_auteurs WHERE id_auteur='$id_auteur' ");
			while ($auteur = spip_fetch_array($sql))	{
				switch($auteur['statut'])	{
					case "0minirezo":
						$logo= "admin-12.gif";
						break;
					case "1comite":
						$logo="redac-12.gif";
						break;
					case "5poubelle":
						$logo="poubelle-12.gif";	 
					case "6forum":
						$logo="visit-12.gif";							
				}
				echo '<a href="'.generer_url_ecrire("auteurs_edit","id_auteur=".$data['id_auteur']).'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/'.$logo.'" title="'._T('asso:adherent_label_modifier_visiteur').'"></a></td>';
			}
		}
		else { echo '&nbsp;</td>'; }
//echo '<td class ='.$class.'>';
//if (empty($data["email"])) 
//{ echo '&nbsp;</td>'; }
//else
//echo '<a href="mailto:'.$data["email"].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/mail-12.png" title="'._T('asso:adherent_label_envoyer_courrier').'"></a>';
//echo '<td class ='.$class.'><input name="cotisation[]" type="checkbox" value='.$id_adherent.'></td>';
		echo '<td class ='.$class.'><a href="'.$url_ajout_cotisation.'&id='.$data['id_adherent'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/cotis-12.gif" title="'._T('asso:adherent_label_ajouter_cotisation').'"></a></td>';
		echo '<td class ='.$class.'><a href="'.$url_edit_adherent.'&id='.$data['id_adherent'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="'._T('asso:adherent_label_modifier_membre').'"></a></td>';
		echo '<td class ='.$class.'><a href="'.$url_voir_adherent.'&id='.$data['id_adherent'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/voir-12.gif" title="'._T('asso:adherent_label_voir_membre').'"></a></td>';
		echo '<td class ='.$class.'><input name="delete[]" type="checkbox" value='.$data['id_adherent'].'></td>';

		echo '</tr>';
	}

	echo '</table>';

	echo '<table width="70%">';
	echo '<tr>';

//SOUS-PAGINATION
	echo '<td>';
	if (empty($lettre)) {
		$query = spip_query( "SELECT * FROM spip_asso_adherents WHERE $critere" );
	}
	else {
		$query = spip_query( "SELECT * FROM spip_asso_adherents WHERE upper( substring( nom, 1, 1 ) ) like '$lettre'  AND $critere" );
	}
	$nombre_selection=spip_num_rows($query);
	$pages=intval($nombre_selection/$max_par_page) + 1;

	if ($pages != 1)	{
		for ($i=0;$i<$pages;$i++)	{ 
			$position= $i * $max_par_page;
			if ($position == $debut)	{
				echo '<strong>'.$position.' </strong>';
			}
			else {
				echo '<a href="'.generer_url_ecrire("abonnes_payants","lettre=$lettre&debut=$position&filtre=$filtre").'">'.$position.'</a> ';
			}
		}	
	}
	
	echo '<td  style="text-align:right;">';
	echo '<input type="submit" name="Submit" value="'._T('asso:bouton_supprimer').'" class="fondo">';
	echo '</td>';
	echo '</table>';
	echo '</form>';

	echo '<p>'._T('asso:adherent_liste_legende').'</p>'; 

// TOTAUX
	$query = spip_query ( "SELECT montant FROM spip_asso_adherents WHERE statut ='ok' " );
	$nombre_membres=spip_num_rows($query);
	$query = spip_query ( "SELECT sum(montant) AS somme FROM spip_asso_adherents WHERE statut ='ok' " );
	$caisse = spip_fetch_array($query);

	echo '<p><font color="#9F1C30"><strong>'._T('asso:adherent_liste_total_cotisations',array('total' => $caisse['somme'])).'<br /> </strong></font><br/>';
	echo '<font color="blue"><strong>'._T('asso:adherent_liste_nombre_adherents',array('total' => $nombre_membres)).'</strong></font></p>';

	fin_boite_info();  
	fin_cadre_relief();  
	fin_page();
	
	//Tout ce qui suit est a passer en spip_cron a l'occasion
	
	//Petite routine pour mettre Ã  jour les statuts de cotisation "Ã©chu"
	spip_query("UPDATE spip_asso_adherents SET statut='echu' WHERE statut = 'ok' AND validite < CURRENT_DATE() ");

	//ROUTINE ID_AUTEUR
	//Enregistrement de l'id_auteur d'emails correspondants
	$query=spip_query("SELECT spip_auteurs.email, spip_auteurs.id_auteur 
	FROM spip_auteurs , spip_asso_adherents 
	WHERE spip_auteurs.email = spip_asso_adherents.email AND spip_auteurs.email <> '' " );
	
	while ($data=spip_fetch_array($query)) {
	$id_auteur=$data['id_auteur'];
	$email=$data['email'];
	spip_query("UPDATE spip_asso_adherents SET id_auteur=$id_auteur  WHERE email='$email' AND email <>'' ");
	}
	
}
?>
