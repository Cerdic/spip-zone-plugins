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
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$url_association = generer_url_ecrire('association');
		$url_adherents = generer_url_ecrire('adherents');
		$url_edit_relances=generer_url_ecrire('edit_relances');
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
		$nombre= $nombre_total=0;
		$membres = $GLOBALS['association_liste_des_statuts'];
		array_shift($membres); // ancien membre
		foreach ($membres as $statut) {
			$query = sql_select("*",_ASSOCIATION_AUTEURS_ELARGIS, "statut_interne='$statut'");
			$nombre=sql_count($query);
			echo '<div style="float:right;text_align:right">'.$nombre.'</div>';
			echo '<div>'._T('asso:adherent_liste_nombre_'.$statut).'</div>';
			$nombre_total += $nombre;
		}		
		echo '<div style="float:right;text_align:right">'.$nombre_total.'</div>';
		echo '<div>'._T('asso:adherent_liste_nombre_total').'</div>';
		echo fin_boite_info(true);	
		
		
		$res=association_icone(_T('asso:menu2_titre_relances_cotisations'),  $url_edit_relances, 'ico_panier.png');
		$res.=association_icone(_T('asso:bouton_impression'), 
					generer_url_ecrire('pdf_adherents', 'statut_interne='.$statut_interne), 
					'print-24.png'); 
		$res.=association_icone(_T('asso:parametres'),  $url_association, 'annonce.gif'); 
			echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", true, "", $titre = _T('asso:adherent_titre_liste_actifs'));		
		
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2'>\n";
		echo "<tr>";
		
		// PAGINATION ALPHABETIQUE
		echo '<td>';
		
		$lettre= _request('lettre');
		if (!$lettre) { $lettre = "%"; }
		
		$query = sql_select("upper( substring( nom_famille, 1, 1 ) )  AS init", _ASSOCIATION_AUTEURS_ELARGIS, '',  'init', 'nom_famille, id_auteur');
		
		while ($data = sql_fetch($query)) {
			$i = $data['init'];
			if($i==$lettre) {
				echo ' <strong>'.$i.'</strong>';
			}
			else {
				$h = generer_url_ecrire('adherents', "statut_interne=$statut_interne&lettre=$i");
				echo " <a href='$h'>$i</a>\n";
			}
		}
		if ($lettre == "%") { echo ' <strong>'._T('asso:adherent_entete_tous').'</strong>'; }
		else {
		$h = generer_url_ecrire('adherents', "statut_interne=$statut_interne");
		echo "\n<a href='$h'>"._T('asso:adherent_entete_tous').'</a>'; }
		
		// FILTRES
		echo '</td><td style="text-align:right;">';
		
		//Filtre ID
		if ( isset ($_POST['id'])) {
			$id=_q($_POST['id']);
			$critere="id_auteur=$id";
			if ($indexation=="id_asso") { $critere="id_asso=$id"; }
		}
		
		echo "\n<form method='post' action='".$url_adherents."'><div>";
		echo '<input type="text" name="id"  class="fondl" style="padding:0.5px" onfocus=\'this.value=""\' size="10" ';
		if ($indexation=='id_asso') { echo ' value="'._T('asso:adherent_libelle_id_asso').'" '; }
		else { echo ' value="'._T('asso:adherent_libelle_id_adherent').'" ';}
		echo ' onchange="form.submit()" />';
		echo '</div></form>';
		echo '</td>';
		echo '<td style="text-align:right;">';
		
		//Filtre statut
		echo "\n<form method='post' action='".$url_adherents."'><div>\n";
		echo '<input type="hidden" name="lettre" value="'.$lettre.'" />';
		echo "\n<select name='statut_interne' class='fondl' onchange='form.submit()'>\n";
		foreach ($GLOBALS['association_liste_des_statuts'] as $statut) {
			echo '<option value="'.$statut.'"';
			if ($statut_interne==$statut) {echo ' selected="selected"';}
			echo '> '._T('asso:adherent_entete_statut_'.$statut).'</option>';
		}
		echo '</select>';
		echo '</div></form>';
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		
		//Affichage de la liste
		echo adherents_liste(intval(_request('debut')), $lettre, $critere, $statut_interne, $indexation);
		echo fin_cadre_relief(true);  
		echo fin_gauche(), fin_page();
	}
}

function adherents_liste($debut, $lettre, $critere, $statut_interne, $indexation)
{
	$url_adherents = generer_url_ecrire('adherents');

	$res = "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
	$res .= "<tr style='background-color: #DBE1C5'>\n";
	$res .= '<td><strong>';
	if ($indexation=="id_asso") { $res .= _T('asso:adherent_libelle_id_asso');}
	else { $res .= _T('asso:adherent_libelle_id_adherent');} 
	$res .= '</strong></td>'
	.  "<td><strong>\n"._T('asso:adherent_libelle_photo').'</strong></td>'
	.  "<td><strong>\n"._T('asso:adherent_libelle_nom').'</strong></td>'
	.  "<td><strong>\n"._T('asso:adherent_libelle_prenom').'</strong></td>'
	.  "<td><strong>\n"._T('asso:adherent_libelle_categorie').'</strong></td>'
	.  "<td><strong>\n"._T('asso:adherent_libelle_validite').'</strong></td>'
	.  '<td colspan="4" style="text-align:center;"><strong>'._T('asso:adherent_entete_action').'</strong></td>'
	.  "<td><strong>\n"._T('asso:adherent_entete_supprimer_abrev').'</strong></td>'
	.  '</tr>';
	
	$max_par_page=30;


	if (empty($lettre)) 
		$critere .= " AND upper( substring( nom_famille, 1, 1 ) ) like '$lettre' ";
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$query = sql_select("*",_ASSOCIATION_AUTEURS_ELARGIS .  " a LEFT JOIN spip_auteurs b ON a.id_auteur=b.id_auteur", $critere, '', "nom_famille ", "$debut,$max_par_page" );
	$auteurs = '';
	while ($data = sql_fetch($query)) {	
		$id_auteur=$data['id_auteur'];		
		switch($data['statut_interne'])	{
		case "echu": $class= "impair"; break;
		case "ok": $class="valide";	break;
		case "relance": $class="pair"; break;
		case "sorti": $class="sortie"; break;
		default : $class="prospect"; break;	   
		}
		
		$auteurs .= "\n<tr>";
		$auteurs .= '<td style="text-align:right;" class="'.$class. ' border1">';
		$auteurs .= ($indexation=="id_asso") ? $data["id_asso"] : $id_auteur;
		$auteurs .= '</td>';
		$auteurs .= '<td class="'.$class. ' border1">';

		$logo = $chercher_logo($id_auteur, 'id_auteur');
		if ($logo) {
		  $auteurs .= '<img src="'. $logo[0] .  '" alt="&nbsp;" width="60"  title="'.$data["nom_famille"].' '.$data["prenom"].'" />';
		}else{
		  $auteurs .= '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'ajout.gif" alt="&nbsp;" width="10"  title="'.$data["nom_famille"].' '.$data["prenom"].'" />';
		}
		$auteurs .= "</td>\n";
		$auteurs .= '<td class="'.$class. ' border1">';
		if (empty($data["email"])) { 
		$auteurs .= $data["nom_famille"].'</td>'; 
		} else {
		$auteurs .= '<a href="mailto:'.$data["email"].'">'.$data["nom_famille"].'</a></td>';
		}
		$auteurs .= '<td class="'.$class. ' border1">'.$data["prenom"]."</td>\n";
		$auteurs .= '<td class="'.$class. ' border1">';
		$auteurs .=sql_getfetsel("valeur", "spip_asso_categories", "id_categorie=".intval($data["categorie"]));
		$auteurs .= '</td>';
		$auteurs .= '<td class="'.$class. ' border1">';
		if ($data['validite']==""){$auteurs .= '&nbsp;';}else{$auteurs .= association_datefr($data['validite']);}
		$auteurs .= '</td>';
		$auteurs .= '<td class="'.$class. ' border1">';
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
		$auteurs .= '<a href="'
		.generer_url_ecrire('auteur_infos','id_auteur='	.$id_auteur)
		.'">'
		. http_img_pack($logo,'','', _T('asso:adherent_label_modifier_visiteur'))
		."</a></td>\n"
		. '<td class="'.$class. ' border1"><a href="'
		.generer_url_ecrire('ajout_cotisation','id='.$id_auteur)
		.'">'
		. http_img_pack('cotis-12.gif','','', _T('asso:adherent_label_ajouter_cotisation'))
		."</a></td>\n"
		. '<td class="'.$class. ' border1"><a href="'
		.generer_url_ecrire('edit_adherent','id='.$id_auteur)
		.'">'
		. http_img_pack('edit-12.gif','','', _T('asso:adherent_label_modifier_membre'))
		."</a></td>\n"
		. '<td class="'.$class. ' border1"><a href="'
		.generer_url_ecrire('voir_adherent','id='.$id_auteur)
		.'">'
		. http_img_pack('voir-12.png', '','', _T('asso:adherent_label_voir_membre'))
		."</a></td>\n"
		. '<td class="'.$class. ' border1"><input name="delete[]" type="checkbox" value="'.$id_auteur.'" /></td>'
		. '</tr>';
	}
	
	$res .= $auteurs . '</table>';
	
	//SOUS-PAGINATION
	$res .= '<table width=100%><tr><td>';

	$query = sql_select("*",_ASSOCIATION_AUTEURS_ELARGIS, $critere);
	$nombre_selection=sql_count($query);
	$pages=intval($nombre_selection/$max_par_page) + 1;
	
	if ($pages != 1)	{
		for ($i=0;$i<$pages;$i++)	{ 
		$position= $i * $max_par_page;
		if ($position == $debut)	{
			$res .= '<strong>'.$position."</strong>\n";
		}
		else {
			$h = generer_url_ecrire('adherents', 'lettre='.$lettre.'&debut='.$position.'&statut_interne='.$statut_interne);
			$res .= "<a href='$h'>$position</a>\n";
			}
		}	
	}
	
	$res .= "\n<td style='text-align:right;'>\n"
	.  !$auteurs ? '' : ('<input type="submit" value="'._T('asso:bouton_supprimer').'" class="fondo" />')
	.  '</td>'
	.  '</table>';

	return 	generer_form_ecrire('action_adherents', $res);

}
?>
