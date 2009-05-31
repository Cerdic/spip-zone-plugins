<?php
/*  LES FONCTIONS utilisées par accesgroupes_admin uniquement  */

// affichage de la liste des rubriques disponibles pour l'utilisateur 
//      rubriques privées en rouge + vert + jaune + bleu
function accesgroupes_acces_rubrique($id_rubrique) {
	global $connect_toutes_rubriques;
	global $connect_id_rubrique;

	return ($connect_toutes_rubriques OR isset($connect_id_rubrique[$id_rubrique]));
}

function accesgroupes_enfant($leparent){
	global $Trub_grpe_ec_parent;  // tableau des rubriques restreintes par le groupe en cours
	global $groupe;
	global $connect_toutes_rubriques;
	global $i;
	global $couleur_claire, $spip_lang_left;
	global $browser_name, $browser_version;
	global $dernier_option;
	//	global $prive_public_ec, $prive_public_parent_ec;
	$i++;
	//       $prive_public_ec = $prive_public_parent;
	
	$query = "SELECT * FROM spip_rubriques WHERE id_parent = $leparent ORDER BY 0+titre,titre";
	$result = spip_query($query);
	$jpknb = 1;
	//echo '<br>mysql_error accesgroupes_enfant = '.mysql_error();
	while($row = spip_fetch_array($result)){
		$my_rubrique = $row['id_rubrique'];
		$titre = typo($row['titre']);
		$statut_rubrique = $row['statut'];
		$lang_rub = $row['lang'];
		$langue_choisie_rub = $row['langue_choisie'];
		$style = "";
		$espace = "";
		//              $prive = $row['prive'];
		echo "\n<!-- BOUCLE ENFANT N°".$jpknb++."-->\n";
		
		if (eregi("mozilla", $browser_name)) {
			$style .= "padding-$spip_lang_left: ".($i*16)."px;";
		}else{
			for ($count = 0; $count <= $i; $count ++) {
				$espace .= "&nbsp;&nbsp;&nbsp;&nbsp;";
			}
		}
		if ($i == 1) {
			$style .= "background: $couleur_claire url("._DIR_IMG_PACK."secteur-12.gif) no-repeat ".(($i - 1) * 16)."px 0px; ";
		}else{
			$style .= "background: $couleur_claire url("._DIR_IMG_PACK."rubrique-12.gif) no-repeat ".(($i * 16) - 12)."px 2px; ";
		}
		// affiche en rouge/vert/jaune/bleu les rubriques privées : prive_public = 0 => prive+public | 1 => prive | 2 => public 
		echo "\n<!-- accesgroupes_trouve_prive_public($my_rubrique, $groupe) -->";
		$prive_public_ec = accesgroupes_trouve_prive_public($my_rubrique, $groupe);
		echo "\n<!-- accesgroupes_trouve_prive_public($leparent, $groupe) -->";
		$prive_public_parent = accesgroupes_trouve_prive_public($leparent, $groupe);
		if ($prive_public_parent >= 3 AND $prive_public_parent <= 5) {
			$prive_public_parent -= 3;
		} 
		// remplissage du tableau des rubriques restreintes par le groupe en cours : permet d'avoir le prive_public_parent de chaque rubrique restreinte par le groupe en cours
		$Trub_grpe_ec_parent[$my_rubrique] = $prive_public_parent; 
		echo "\n<!-- switch ($prive_public_ec) -->";
		switch ($prive_public_ec) {
		case 0 :		// privé + public
			$style .= "color: #f00;";
			break;
		case 1 :		// privé seul
			$style .= "color: #093;";
			break;
		case 2 :		// public seul
			$style .= "color: #f90;";
			break;
		case 3 :		// autres groupes : privé+public 
			$style .= "color: #00f;";
			break;
		case 4 :		// autres groupes :  privé 
			$style .= "color: #00f;";
			break;
		case 5 :		// autres groupes : public 
			$style .= "color: #00f;";
			break;
		default :	// rubrique sans restriction
			$style .= "color: #000;";
			break;
		}
		if ($prive_public_ec <= 5) {
			$style .= "font-weight:bold; ";
		}
		if ($statut_rubrique != 'publie') {
			$titre = "($titre , non publi&eacute;e)";
		}
		if ($GLOBALS['meta']['multi_rubriques'] == 'oui' AND $langue_choisie_rub == "oui") {
			$titre = $titre." [".traduire_nom_langue($lang_rub)."]";
		}
		$selec_rub = "selec_rub";
		if ($browser_name == "MSIE" AND floor($browser_version) == "5") {
			$selec_rub = ""; // Bug de MSIE MacOs 9.0
		}
		echo "\n<!-- acces_rubrique($my_rubrique) -->";
		if (accesgroupes_acces_rubrique($my_rubrique)) {
			echo "\r\n <option".mySel($my_rubrique,$id_parent)." class=\"$selec_rub\" style=\"$style\">$espace".supprimer_tags($titre);
			//echo " i = $i my_rubrique = $my_rubrique prive_public_ec = $prive_public_ec prive_public_parent = $prive_public_parent";
			if ($prive_public_ec >= 3 AND $prive_public_ec <= 5) {
				echo " (";
				echo ($prive_public_ec == 3 ? _T('accesgroupes:prive_public') : ($prive_public_ec == 4 ? _T('accesgroupes:prive_seul') : _T('accesgroupes:public_seul') ));
				echo ")";
			}
			echo "</option>\n";
			//	$prive_public_parent >= 3 ? $prive_public_parent_ec = $prive_public_parent - 3 : $prive_public_parent_ec = $prive_public_parent;
			if ($prive_public_ec < 3) {
				echo "<script language=\"JavaScript\" type=\"text/javascript\">
						Tacces_rub.push([\"$my_rubrique\", \"$prive_public_ec\", \"$prive_public_parent\"]);</script>";
			}
			elseif ($prive_public_ec <=5 AND $prive_public_ec > 2) {
				echo "<script language=\"JavaScript\" type=\"text/javascript\">
						Tacces_rub.push([\"$my_rubrique\", \"".($prive_public_ec - 3)."\", \"$prive_public_parent\"]);</script>";
			}
		}
		echo "\n<!-- acces_rubrique($my_rubrique) -->";
		accesgroupes_enfant($my_rubrique);
	}
	$i = $i - 1;
	//	$i == 1 ? $prive_public_ec = 10 : $prive_public_ec = $prive_public_parent_ec;
}

//  fct pour renvoyer le code numérique du type d'accès à une rubrique restreinte
//	si la rubrique est contrôlée par le groupe $id_grpacces retourne : 0 => prive + public, 1 => privé, 2 => public
//	si la  rubrique est contrôlée par un autre groupe retourne : 3 =>  prive + public, 4 => privé, 5 => public
function accesgroupes_trouve_prive_public($id_rub, $id_grpacces) {
	$min = 10;
	do {
		$prive_ec = accesgroupes_valeur_prive($id_rub, $id_grpacces);
		if ($prive_ec < $min) {
			$min = $prive_ec;
		}
		$sql = "SELECT id_parent FROM spip_rubriques WHERE id_rubrique = $id_rub LIMIT 1"; 
		$result = spip_query($sql);
		if ($row = spip_fetch_array($result)) {
			$id_rub = $row['id_parent'];
		}
	}
	while ($id_rub > 0); // vérification de l'ascendance : on boucle jusqu'à être remonté à la racine du site
	return $min;
}	 
//print '<br>trouve_prive_public(42, 1) = '.accesgroupes_trouve_prive_public(42, 1);

// fct pour retourner la valeur privé mini d'une rubrique 
// (on envisage le cas ou une rubrique peut être restreinte par plusieurs groupes, avec des valeurts de privé différentes)
function accesgroupes_valeur_prive($id_rub, $id_grpacces) {
	$pp_ec = 10;
	$sql2 = "SELECT prive_public, id_grpacces FROM spip_accesgroupes_acces WHERE id_rubrique = $id_rub";
	$result2 = spip_query($sql2);				
	while ($row2 = spip_fetch_array($result2)){
		$id_groupe_ec = $row2['id_grpacces'];
		$prive_public_ec = $row2['prive_public'];
		if ($id_groupe_ec != $id_grpacces) {
			$prive_public_ec += 3;
		}
		if ($prive_public_ec < $pp_ec) {
			$pp_ec = $prive_public_ec;
		}
	}
	return $pp_ec;
}


// les fonctions nécessaires pour la gestions des ss-groupes et statuts inclus dans les groupes

// fct de vérification que le groupe_pere n'est pas inclu dans le groupe_fils ou l'un de ses ascendants (récursivement)
// renvoie FALSE si groupe_pere ou l'un de ses ascendants contient groupe_fils 
function accesgroupes_verifie_inclusions_groupe($id_groupe_fils, $id_groupe_pere) {				 
	$sql111 = "SELECT COUNT(*) AS inclusions FROM spip_accesgroupes_auteurs WHERE id_grpacces = $id_groupe_fils AND id_ss_groupe = $id_groupe_pere LIMIT 1";
	$result111 = spip_query($sql111);
	$row = spip_fetch_array($result111);
	if ($row['inclusions'] > 0){
		return FALSE;
	}
	// le père n'est pas inclu dans le fils => on teste toute l'ascendance du fils par récurrence avant d'être OK
	else {
		$sql110 = "SELECT id_grpacces FROM spip_accesgroupes_auteurs WHERE id_ss_groupe = $id_groupe_pere";
		$result110 = spip_query($sql110);
		while ($row = spip_fetch_array($result110)) {
			$id_groupe_ec = $row["id_groupe"];
			if (accesgroupes_verifie_inclusions_groupe($id_groupe_fils, $id_groupe_ec) == FALSE) {
				return FALSE;
			}
		}
	}				 
	// pas d'inclusion d'un parent donc OK
	return TRUE; 				 
}

// fct pour construire le tableau de la descendance d'un groupe (récursivement)
function accesgroupes_descendance_groupe($groupe_pere, $Tdescendance = array()) {
	$sql121 = "SELECT spip_accesgroupes_auteurs.id_ss_groupe, spip_accesgroupes_groupes.nom, spip_accesgroupes_auteurs.id_grpacces
										FROM spip_accesgroupes_auteurs
										LEFT JOIN spip_accesgroupes_groupes
										ON spip_accesgroupes_auteurs.id_ss_groupe = spip_accesgroupes_groupes.id_grpacces
										WHERE actif = 1 
										AND id_ss_groupe != 0
										ORDER BY nom";
	//										WHERE spip_accesgroupes_auteurs.id_grpacces = $groupe_pere 
	$result121 = spip_query($sql121);
	while ($row = spip_fetch_array($result121)) {
		if ($row['id_grpacces'] != $groupe_pere) {
			continue;
		}
		$id_descendant_ec = $row["id_ss_groupe"];
		$nom_descendant_ec = $row["nom"];
		$Tdescendance[$groupe_pere][] = array('id' => $id_descendant_ec, 'nom' => $nom_descendant_ec);
		$Tdescendance = accesgroupes_descendance_groupe($id_descendant_ec, $Tdescendance);
	}
	return $Tdescendance;
}

// fct pour afficher le tableau $Tdesc de la descendance du groupe $id_grpe (récursivement)
function accesgroupes_affiche_descendance($id_grpe, $Tdesc, $a_afficher = "") {
	if (is_array($Tdesc[$id_grpe]) AND count($Tdesc[$id_grpe]) > 0) {
		$a_afficher .= "<div style='margin-left: 10px; font-size: 10px; padding: 2px;'>";
		foreach ($Tdesc[$id_grpe] as $a => $Td) {
			$a_afficher .= "<img src='"._DIR_PLUGIN_ACCESGROUPES."/img_pack/sous-groupe.png' alt='|_' style='vertical-align:top;'>";
			$h =generer_url_ecrire("accesgroupes_admin","groupe=".$Td['id']);
			$a_afficher .= "<img src='"._DIR_PLUGIN_ACCESGROUPES."/img_pack/groupe-12.png' alt='|_' style='vertical-align:top;'> <a href=\"$h\">".$Td['nom']."</a><br />";
			$a_afficher = accesgroupes_affiche_descendance($Td['id'], $Tdesc, $a_afficher);
		}
		$a_afficher .= "</div>";
	}
	return $a_afficher;
}

// fct pour créer le tableau des rubriques gérées par les groupes
function accesgroupes_affiche_groupes_rubriques() {
	$a_afficher =  "<table CELLPADDING=2 CELLSPACING=0 WIDTH='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
	$a_afficher .= "<tr><th colspan=\"2\">"._T('accesgroupes:groupes_rubriques')."</th></tr>";
	$sql301 = "SELECT nom, id_grpacces, actif FROM spip_accesgroupes_groupes GROUP BY id_grpacces";
	$result301 = spip_query($sql301);
	while ($row301 = spip_fetch_array($result301)) {
		$id_grpe_ec = $row301['id_grpacces'];
		$nom_grpe_ec = $row301['nom'];
		$a_afficher .= "<tr style='background-color: #eeeeee;'>";
		$a_afficher .= "<td class='verdana11' style='border-top: 1px solid #cccccc; width: 14px; vertical-align:top;'>";
		if (accesgroupes_est_admin_restreint() == TRUE AND accesgroupes_est_proprio($id_grpe_ec) == TRUE) {
			$a_afficher .= "<img src='img_pack/admin-12.gif' alt='|_' style='vertical-align:top;'>";
		}
		$a_afficher .= "<img src='"._DIR_PLUGIN_ACCESGROUPES."/img_pack/groupe-12.png' alt='|_' style='vertical-align:top;'></td>";
		$h = generer_url_ecrire("accesgroupes_admin","groupe=$id_grpe_ec");
		$a_afficher .= "<td style='border-top: 1px solid #cccccc;'><a href=\"$h\">";
		if ($row301['actif'] != 1) {
			$a_afficher .= '('.$nom_grpe_ec.' : <span style="color: #6c3;">'._T('accesgroupes:inactif').'</span>)';
		}
		else {
			$a_afficher .= $nom_grpe_ec;
		}
		$a_afficher .= "</a><br />";
		$a_afficher .= "<div style='margin-left: 20px; font-size: 10px; padding: 2px;'>";
		$sql302 = "SELECT spip_rubriques.titre, spip_rubriques.id_rubrique
											FROM spip_rubriques
													LEFT JOIN spip_accesgroupes_acces
													ON spip_accesgroupes_acces.id_rubrique = spip_rubriques.id_rubrique
													WHERE id_grpacces = $id_grpe_ec";
		
		if ($result302 = spip_query($sql302)) {
			while ($row302 = spip_fetch_array($result302)) {
				$id_rub_ec = $row302['id_rubrique'];
				$nom_rub_ec = $row302['titre'];
				$a_afficher .= "<img src='"._DIR_PLUGIN_ACCESGROUPES."/img_pack/sous-groupe.png' alt='|_' style='vertical-align:top;'>";
				$a_afficher .= " <a href=\"?exec=naviguer&id_rubrique=".$id_rub_ec."\"><img src='img_pack/rubrique-12.gif' alt='|_' style='vertical-align:top; border: 0px;'>".typo($nom_rub_ec);
				
				if (accesgroupes_est_admin_rubrique($id_rub_ec) == TRUE) {
					$a_afficher .= " <img src='img_pack/admin-12.gif'>";
				}
				
				$a_afficher .= "</a><br />";
			}
		}
		//echo '<br>mysql_error $sql302 = '.mysql_error();				 
		$a_afficher .= "</div>\r\n</td>";
		$a_afficher .= "</tr>";
	}
	$a_afficher .= "</table>";
	return $a_afficher;
}

// fct pour créer et retourner l'array contenant les rubriques d'un admin restreint
function accesgroupes_cree_Trub_admin () {          
	$id_utilisateur = accesgroupes_trouve_id_utilisateur();
	$sql501= "SELECT  id_rubrique
										FROM spip_auteurs_rubriques
										WHERE id_auteur = '$id_utilisateur'";
	$result501 = spip_query($sql501);
	$Trubriques_autorises = array();
	while ($row501 = spip_fetch_array($result501)) {
		$Trubriques_autorises[] = $row501['id_rubrique'];
	}
	return $Trubriques_autorises;
}

// fct pour trouver l'id de l'utilisateur en cours
function accesgroupes_trouve_id_utilisateur() {					
	$login_utilisateur = $GLOBALS['auteur_session']['login'];

	$sql502 = "SELECT id_auteur FROM spip_auteurs WHERE login = '$login_utilisateur' LIMIT 1";
	$result502 = spip_query($sql502);
	$row502 = spip_fetch_array($result502);
	$id_utilisateur = $row502['id_auteur'];
	return $id_utilisateur;				 
}

// fct pour déterminer si l'utilisateur en cours est admin restreint
function accesgroupes_est_admin_restreint() {
	$Trub_restreint = accesgroupes_cree_Trub_admin();
	if (count($Trub_restreint) > 0) {
		return TRUE;
	}
	else {
		return $Trub_restreint;
	}
}

// fct pour déterminer si l'utilisateur est proprio du groupe en cours (donc peut le modifier)
function accesgroupes_est_proprio($id_groupe) {
	$sql505 = "SELECT proprio FROM spip_accesgroupes_groupes WHERE id_grpacces = $id_groupe LIMIT 1";
	$result505 = spip_query($sql505);
	$row505 = spip_fetch_array($result505);
	$id_proprio = $row505['proprio'];
	if ($id_proprio == accesgroupes_trouve_id_utilisateur()) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

// fct pour déterminer si l'utilisateur est proprio de l'accès sur une rubrique
function accesgroupes_est_proprio_acces($id_rubrique) {
	$proprio = 0;
	$sql506 = "SELECT proprio FROM spip_accesgroupes_acces WHERE id_rubrique = $id_rubrique";
	$result506 = spip_query($sql506);
	while ($row506 = spip_fetch_array($result506)) {
		$id_proprio_acces = $row506['proprio'];
		if ($id_proprio_acces == accesgroupes_trouve_id_utilisateur()) {
			$proprio = 1;
		}
	}
	if ($proprio == 1) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

// fct pour déterminer le statut d'un utilisateur
function accesgroupes_trouve_statut($id_util) {
	$sql506 = "SELECT statut FROM spip_auteurs WHERE id_auteur = $id_util LIMIT 1";
	$result506 = spip_query($sql506);
	$row506 = spip_fetch_array($result506);
	return $row506['statut'];
}

// fct pour traiter les noms de groupes en doublons retourne FALSE si le nom existe déja
function accesgroupes_verifie_duplicata_groupes($nom_at) {
	$sql507 = "SELECT nom FROM spip_accesgroupes_groupes";
	if ($result507 = spip_query($sql507)) {
		while ($row507 = spip_fetch_array($result507)) {
			if ($row507['nom'] == $nom_at) {
				return FALSE;
			}
		}
	}
	return TRUE;
}

function accesgroupes_debug(){
	if(mysql_errno() > 0){
		echo "<br />".mysql_errno().": ".mysql_error();
	}
}

// libère les rubriques qui n'ont plus de restriction, ok avec mysql 3.23
function accesgroupes_rub_reinit(){
	$sql = "SELECT spip_rubriques.* 
							FROM spip_rubriques
						LEFT JOIN spip_accesgroupes_acces
						ON spip_rubriques.id_rubrique = spip_accesgroupes_acces.id_rubrique 
								AND spip_rubriques.prive = 1
						WHERE spip_accesgroupes_acces.id_rubrique IS NULL";
	$result=spip_query($sql);
	accesgroupes_debug($result);
	while ($row = spip_fetch_array($result)){
		$sql1 = "UPDATE spip_rubriques SET prive=0 WHERE id_rubrique = \"".$row['id_rubrique']."\"";
		$result1=spip_query($sql1);
		accesgroupes_debug($result1);
	}
}

//  fct pour déterminer si une sous-rubrique est inclue dans une rubrique gérée par un admin restreint
function accesgroupes_est_admin_rubrique($id_rub) {
	$id_auteur_ec = accesgroupes_trouve_id_utilisateur();
	// remonter dans l'ascendance de la rubrique jusqu'à trouver une rubrique parent dont l'admin en cours est l'admin restreint
	do {
		$sql563 = "SELECT COUNT(*) AS nb_rub 
											FROM spip_auteurs_rubriques
											WHERE id_rubrique = $id_rub
											AND id_auteur = $id_auteur_ec";
		$result563 = spip_query($sql563);
		if ($row563 = spip_fetch_array($result563) AND $row563['nb_rub'] > 0) {
			return TRUE;
		}
		else {
			$sql564 = "SELECT id_parent
														FROM spip_rubriques
														WHERE id_rubrique = $id_rub
														LIMIT 1";
			$result564 = spip_query($sql564);
			$row564 = spip_fetch_array($result564);
			$id_rub = $row564['id_parent'];
		}
	}
	while ($id_rub != 0);
	return FALSE;
}


/* versions avant modifs
function accesgroupes_enfant($leparent, $prive_public_parent = 10){
				global $Trub_grpe_ec_parent;
				global $groupe;
		global $connect_toutes_rubriques;
		global $i;
		global $couleur_claire, $spip_lang_left;
		global $browser_name, $browser_version;
				global $prive_public_ec, $prive_public_parent_ec;

		$i++;
		$prive_public_ec = $prive_public_parent;
				
				$query = "SELECT * FROM spip_rubriques WHERE id_parent = $leparent ORDER BY 0+titre,titre";
		$result = spip_query($query);
		while($row = spip_fetch_array($result)){
			$my_rubrique = $row['id_rubrique'];
			$titre = typo($row['titre']);
			$statut_rubrique = $row['statut'];
			$lang_rub = $row['lang'];
			$langue_choisie_rub = $row['langue_choisie'];
			$style = "";
			$espace = "";
			$prive = $row['prive'];
	
			if (eregi("mozilla", $browser_name)) {
				$style .= "padding-$spip_lang_left: ".($i*16)."px;";
			} 
						else {
					for ($count = 0; $count <= $i; $count ++) {
											$espace .= "&nbsp;&nbsp;&nbsp;&nbsp;";
									}
					}
			if ($i == 1) {
				$style .= "background: $couleur_claire url("._DIR_IMG_PACK."secteur-12.gif) no-repeat ".(($i - 1) * 16)."px 0px; ";
			}
							else {
									$style .= "background: $couleur_claire url("._DIR_IMG_PACK."rubrique-12.gif) no-repeat ".(($i * 16) - 12)."px 2px; ";
							}
		// affiche en rouge/vert/jaune/bleu les rubriques privées : prive_public = 0 => prive+public | 1 => prive | 2 => public 
$ppe_old = $prive_public_ec;
							$prive_public_ec = accesgroupes_trouve_prive_public($my_rubrique, $groupe, $prive_public_ec);
						switch ($prive_public_ec) {
									case 0 :		// privé + public
												$style .= "color: #f00;";
									break;
									case 1 :		// privé seul
												$style .= "color: #093;";
									break;
									case 2 :		// public seul
												$style .= "color: #f90;";
									break;
									case 3 :		// autres groupes : privé+public 
												$style .= "color: #00f;";
									break;
										case 4 :		// autres groupes :  privé 
												$style .= "color: #00f;";
									break;
										case 5 :		// autres groupes : public 
												$style .= "color: #00f;";
									break;
									default :	// rubrique sans restriction
												$style .= "color: #000;";
									break;
						}
			if ($prive_public_ec <= 5) {
								$style .= "font-weight:bold; ";
						}
			if ($statut_rubrique != 'publie') {
							$titre = "($titre , non publi&eacute;e)";
						}
			if (lire_meta('multi_rubriques') == 'oui' AND $langue_choisie_rub == "oui") {
							$titre = $titre." [".traduire_nom_langue($lang_rub)."]";
						}
			$selec_rub = "selec_rub";
			if ($browser_name == "MSIE" AND floor($browser_version) == "5") {
							$selec_rub = ""; // Bug de MSIE MacOs 9.0
						}
			if (acces_rubrique($my_rubrique)) {
				echo "\r\n <option".mySel($my_rubrique,$id_parent)." class='$selec_rub' style=\"$style\">$espace".supprimer_tags($titre);
echo " i = $i my_rubrique = $my_rubrique prive_public_ec = $prive_public_ec prive_public_parent = $prive_public_parent ppe_old = $ppe_old";
									if ($prive_public_ec >= 3 AND $prive_public_ec <= 5) {
										echo " (";
										echo ($prive_public_ec == 3 ? _T('accesgroupes:prive_public') : ($prive_public_ec == 4 ? _T('accesgroupes:prive_seul') : _T('accesgroupes:public_seul') ));
										echo ")";
									}
									echo "</option>\n";  
									$prive_public_parent >= 3 ? $prive_public_parent_ec = $prive_public_parent - 3 : $prive_public_parent_ec = $prive_public_parent;
									if ($prive_public_ec < 3) {
									echo "<script language=\"JavaScript\" type=\"text/javascript\">
													Tacces_rub.push([\"$my_rubrique\", \"$prive_public_ec\", \"$prive_public_parent\"]);</script>";
							// remplissage du tableau des rubriques restreintes par le groupe en cours : permet d'avoir le prive_public_parent de chaque rubrique restreinte par le groupe en cours
									$Trub_grpe_ec_parent[$my_rubrique] = $prive_public_parent; 
								}
									elseif ($prive_public_ec <=5 AND $prive_public_ec > 2) {
												echo "<script language=\"JavaScript\" type=\"text/javascript\">
													Tacces_rub.push([\"$my_rubrique\", \"".($prive_public_ec - 3)."\", \"$prive_public_parent_ec\"]);</script>";
									}

			}
			accesgroupes_enfant($my_rubrique, $prive_public_ec);
		}
		$i = $i - 1;
				$i == 1 ? $prive_public_ec = 10 : $prive_public_ec = $prive_public_parent_ec;
}

//  fct pour renvoyer le code numérique du type d'accès à une rubrique restreinte
//	si la rubrique est contrôlée par le groupe $id_grpacces retourne : 0 => prive + public, 1 => privé, 2 => public
//	si la  rubrique est contrôlée par un autre groupe retourne : 3 =>  prive + public, 4 => privé, 5 => public
function accesgroupes_trouve_prive_public($id_rub, $id_grpacces, $prive_public_parent = 10) {
				$prive_public_rub = $prive_public_parent;  // prive_public = 10 => indéterminé
		$sql2 = "SELECT prive_public, id_rubrique, id_grpacces, proprio FROM spip_accesgroupes_acces WHERE id_rubrique = $id_rub";
		$result2 = spip_query($sql2);
			while ($row2 = spip_fetch_array($result2)){
						$id_rubrique_ec = $row2['id_rubrique'];
						$id_groupe_ec = $row2['id_grpacces'];
						$prive_public_ec = $row2['prive_public'];
							if ($id_groupe_ec != $id_grpacces) {
								$prive_public_ec += 3;
							}
							if ($prive_public_ec < $prive_public_rub) {
								$prive_public_rub = $prive_public_ec;
							}
		}
			return $prive_public_rub;
}	 
*/

?>