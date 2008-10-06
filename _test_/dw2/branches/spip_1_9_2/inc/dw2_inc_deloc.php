<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Fonctions communes dw2_deloc
+--------------------------------------------+
*/


//
// Logos de serveurs
function aff_logo_serv($id_serv)
	{
	$logoserv = ""._DIR_IMG_DW2."logoserv_".$id_serv.".".$GLOBALS['ext_logo_serv'];
		if (!file_exists($logoserv))
			{ $logoserv = ""._DIR_IMG_DW2."list_serv.gif"; }
	return $logoserv;
	}

//
// bouton annuler la Destination... 
function bouton_annule_dest($id_document,$id_serv) {
	global $connect_id_auteur, $couleur_claire;
	echo "<form action='".generer_url_action("dw2actions", "arg=annuledocserveur-".$id_document)."' method='post'>";
	echo "<div align='center' style='background:$couleur_claire; padding:2px;'>";
	echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_import", "id_serv=".$id_serv)."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-annuledocserveur-".$id_document)."' />";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />";
	echo "<input type=submit value='"._T('dw:annuler')."' class='fondo'>";
	echo "</div></form>";
}




//
// Connexion au serveur $ftp_server
//
function connexion_serv($ftp_server, $port, $ftp_user_name, $ftp_user_pass, $repertoire_dest) {
	$flux = ftp_connect($ftp_server,$port);
	if(!$flux) { 
		$message="ftpechec";
	}
	else {
		$login_conex = ftp_login($flux, $ftp_user_name, $ftp_user_pass);
		if(!$login_conex) {
			$message="loginechec";
		}
		else {
			// mode passif initie client !//h.20/8/06 ??
			#ftp_pasv($flux, true);
		
			ftp_chdir($flux, "/");
			$chdir_dest = ftp_chdir($flux, "$repertoire_dest");
			if(!$chdir_dest) {
				$message="repertechec";
			}
			else {
				$message="connect";
			}
		}
	}
	return $retour_conex=array($flux,$message);
}



// test format des scheme et host des $site_distant
function test_caract($a) //oemweb.com
	{
	if (trim($a) !== $a) return false;
	$pdr = "^[-[:alnum:]]*(-|_)*[-[:alnum:]]*$";
	return eregi($pdr, $a);
	}

function verif_scheme_host($site_distant) //oemweb.com
	{$p = parse_url($site_distant);
		switch ($p['scheme'])
			{
			case "http":
			case "ftp": break;
			default : return false;
			}
		$t = explode (".", $p['host']);
		$n = count($t);
		if ($n < 2) return false;
		while (list(, $v) = each($t))
			{
			if (strlen($v)==0 || !test_caract($v)) return false;
			}
		return true;
	}


// Génère le bouton de selection/info du fichier dans import
// avec controle doublons fichier dans catalog dw2
// controle d'extension de fichier ..: dans spip ?
function bout_select_fichier($fich, $idmf, $iddoc, $id_serv, $site_distant, $repert_distant) {
	if ($idtype = extens_spipon($fich)) {
		$form_imp = "<input type='hidden' name='id_type' value='".$idtype."'>\n";

		if ($idmf==0) {
			$form_imp.="<input type='image' src='"._DIR_IMG_DW2."puce-verte.gif' align='absmiddle'>\n";
		}
		else {
			$form_imp = doublons_localiser($iddoc, $id_serv, $site_distant, $repert_distant);
		} 
	}
	else {
		$form_imp = "<img src='"._DIR_IMG_DW2."puce-poubelle-breve.gif' align='absmiddle'>\n";
	}
	
	return $form_imp;
}



// controle extension dans spip et fixe $id_type
function extens_spipon($fichier) {
	if (ereg("\.([^.]+)$", $fichier, $match)) {
		$ext = strtolower($match[1]);
		
		if ($ext == 'htm') { $ext = 'html'; }
		$req = "SELECT extension, id_type FROM spip_types_documents WHERE extension='$ext'";
		$result = spip_query($req);
		if ($row = @spip_fetch_array($result)) {
			$idtype = $row['id_type'];
			return $idtype;
		}
		return false;
		
	}
	return false;
}



// detecte les fichiers en "doublons" :
// $id -> id_document passer dans 'function bout_select_fichier'
// génère l'affichage adéquate
function doublons_localiser($iddoc, $id_serv, $site_distant, $repert_distant)
{
	$result=spip_query("SELECT url, heberge FROM spip_dw2_doc WHERE id_document = $iddoc");
	$row=spip_fetch_array($result);
	$url=$row['url'];
	$heberge = $row['heberge'];
	$nomfichier = substr(strrchr($url,'/'), 1); // extrait nomfichier d'url
	$chfi = str_replace($nomfichier, '', $url); // extrait repertoires d'url
	$chemfichier = substr(strchr($chfi,'/'), 1); // vire le premier '/'
 	if ($heberge==$site_distant) {
		if ($repert_distant !== $chemfichier) {
			$aff_dbl = "<a href='".generer_url_ecrire("dw2_change_serv", "id_serv=".$id_serv."&id_doc=".$iddoc)."' title='$chemfichier'>".
						"<img src='"._DIR_IMG_DW2."dot_serveur.gif' align='absmiddle' border='0'></a>\n";
		}
		else {
			$aff_dbl = "<img src='"._DIR_IMG_DW2."puce-blanche-breve.gif' align='absmiddle'>";
		}
	}
	else if ($heberge == "local") {
		$aff_dbl = "<a href='".generer_url_ecrire("dw2_change_serv", "id_serv=".$id_serv."&id_doc=".$iddoc)."' title='$heberge'>".
					"<img src='"._DIR_IMG_DW2."dot_serveur3.gif' align='absmiddle' border='0'></a>\n";
	}
	else if ($heberge == "distant") { // h.13/03 prend en charge 'distant'
		$aff_dbl = "<img src='"._DIR_IMG_PACK."attachment.gif' align='absmiddle' border='0'></a>\n";
	}
	else {
		$aff_dbl = "<a href='".generer_url_ecrire("dw2_change_serv", "id_serv=".$id_serv."&id_doc=".$iddoc)."' title='$heberge'>".
					"<img src='"._DIR_IMG_DW2."dot_serveur2.gif' align='absmiddle' border='0'></a>\n";
	}
	return $aff_dbl;
}



// Selecteur de rubrique ...
// .. la roue ? alors on pompe -> spip : ecrire/article_edit.php3
function rub_parent($leparent) {
	//global $id_parent;
	//global $id_rubrique;
	static $i = 0, $premier = 1;
	//global $statut;
	global $connect_toutes_rubriques;
	global $connect_id_rubriques;
	global $couleur_claire;

	$i++;
 	$query="SELECT * FROM spip_rubriques WHERE id_parent='$leparent' ORDER BY titre";
 	$result=spip_query($query);
	while($row=spip_fetch_array($result))
	{
		$my_rubrique=$row['id_rubrique'];
		$titre=typo($row['titre']);
		$statut_rubrique=$row['statut'];
		$lang_rub = $row['lang'];
		$langue_choisie_rub = $row['langue_choisie'];
		$style = "";

		// si l'article est publie il faut etre admin pour avoir le menu
		// sinon le menu est present en entier (proposer un article)
		//
		/*if ($statut != "publie" OR acces_rubrique($my_rubrique))
			{ $rubrique_acceptable = true; }
		else
			{ $rubrique_acceptable = false; }*/

		$espace="";
		for ($count=1;$count<$i;$count++)
			{ $espace.="&nbsp;&nbsp;&nbsp; "; }

		switch ($i)
			{
		case 1:
			$espace= "";
			$style .= "font-weight: bold;";
			break;
		case 2:
			$style .= "color: #202020;";
			break;
		case 3:
			$style .= "color: #404040;";
			break;
		case 4:
			$style .= "color: #606060;";
			break;
		case 5:
			$style .= "color: #808080;";
			break;
		default;
			$style .= "color: #A0A0A0;";
			break;
			}
		/*if ($rubrique_acceptable) {*/
			if ($i == 1 && !$premier) echo "<option value='".$my_rubrique."'>\n"; // sert a separer les secteurs
			$titre = couper($titre." ", 50); // largeur maxi
			if (lire_meta('multi_rubriques') == 'oui' AND ($langue_choisie_rub == "oui" OR $leparent == 0)) $titre = $titre." [".traduire_nom_langue($lang_rub)."]";
			echo "<OPTION value='".$my_rubrique."' style=\"$style\">$espace".supprimer_tags($titre)."\n";
			echo "($my_rubrique)</option>"; //h.12/3
		/*}*/
		$premier = 0;
		rub_parent($my_rubrique);
	}
	$i=$i-1;
}
//



?>
