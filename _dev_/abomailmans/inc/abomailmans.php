<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
 * Inspire de Spip-Listes
*/


	function _abomailmans_install(){
		if ($GLOBALS['spip_version_code']<1.9204){
			include_spip('base/abomailmans_upgrade');
			abomailmans_install('install');
		}
	}
	
	// Fonction utilitaires
	function abomailmans_abomailman_editable($id_abomailman = 0) {
		global $connect_statut;
		return $connect_statut == '0minirezo';
	}
	
	function abomailmans_abomailman_administrable($id_abomailman = 0) {
		global $connect_statut;
		return $connect_statut == '0minirezo';
	}

	
	//
	// Afficher une liste de mailmans
	//
	
	function abomailmans_afficher_abomailmans($titre_table, $requete, $icone = '') {
		global $couleur_claire, $couleur_foncee;
		global $connect_id_auteur;

		$tous_id = array();
		
		$select = $requete['SELECT'] ? $requete['SELECT'] : '*';
		$from = $requete['FROM'] ? $requete['FROM'] : 'spip_articles AS articles';
		$join = $requete['JOIN'] ? (' LEFT JOIN ' . $requete['JOIN']) : '';
		$where = $requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '';
		$order = $requete['ORDER BY'] ? (' ORDER BY ' . $requete['ORDER BY']) : '';
		$group = $requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '';
		$limit = $requete['LIMIT'] ? (' LIMIT ' . $requete['LIMIT']) : '';
	
		$cpt = "$from$join$where$group";
		$tmp_var = substr(md5($cpt), 0, 4);

		if (!$group){
			$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM $cpt"));
			if (! ($cpt = $cpt['n'])) return $tous_id ;
		}
		else
			$cpt = spip_num_rows(spip_query("SELECT $select FROM $cpt"));
		if ($requete['LIMIT']) $cpt = min($requete['LIMIT'], $cpt);
	
		$nb_aff = 1.5 * _TRANCHES;
		$deb_aff = intval(_request('t_' .$tmp_var));
	
		if ($cpt > $nb_aff) {
			$nb_aff = (_TRANCHES); 
			$tranches = afficher_tranches_requete($cpt, 3, $tmp_var, '', $nb_aff);
		}
		
		if (!$icone) $icone = "../"._DIR_PLUGIN_ABOMAILMANS."/img_pack/mailman.gif";
		
		if ($cpt) {
			if ($titre_table) echo "<div style='height: 12px;'></div>";
			echo "<div class='liste'>";
			bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black");
			echo "<table width='100%' cellpadding='4' cellspacing='0' border='0'>";
	
			echo $tranches;
	
			$result = spip_query("SELECT $select FROM $from$join$where$group$order LIMIT $deb_aff, $nb_aff");
			$num_rows = spip_num_rows($result);
	
			$ifond = 0;
			$premier = true;
			
			$compteur_liste = 0;
			while ($row = spip_fetch_array($result)) {
				$vals = '';
				$id_abomailman = $row['id_abomailman'];
				$reponses = $row['reponses'];
				$titre = $row['titre'];

				$tous_id[] = $id_abomailman;

				$retour = parametre_url(self(),'duplique_chart','');
				$link = generer_url_ecrire('abomailmans_edit',"id_abomailman=$id_abomailman&retour=".urlencode($retour));
				if ($reponses) {
					$puce = 'puce-verte-breve.gif';
				}
				else {
					$puce = 'puce-orange-breve.gif';
				}
	
				$s = "<img src='"._DIR_IMG_PACK."$puce' width='7' height='7' border='0'>&nbsp;&nbsp;";
				$vals[] = $s;
				
				//$s .= typo($titre);
				$s = icone_horizontale(typo($titre), $link,"../"._DIR_PLUGIN_ABOMAILMANS."/img_pack/mailman.gif", "",false);
				$vals[] = $s;
				
				$s = "";
				$vals[] = $s;
	
				$s = "";
				
				$s = "";
				if(abomailmans_abomailman_administrable($id_abomailman)){
					$link = parametre_url('','exec=abomailmans_edit&supp_abomailman', $id_abomailman);
					$link = parametre_url($link,'id_abomailman', $id_abomailman);
					$vals[] = "<a href='$link'>"._T("abomailmans:supprimer")."</a>";
				}
				$vals[] = $s;

				$table[] = $vals;
			}
			spip_free_result($result);
			
			$largeurs = array('','','','','');
			$styles = array('arial11', 'arial11', 'arial1', 'arial1','arial1');
			echo afficher_liste($largeurs, $table, $styles);
			echo "</table>";
			echo "</div>\n";
		}
		return $tous_id;
	}


//* Envoi de mail via Mailman
	function abomailman_mail ($from_nom, $from_email, $to_nom, $to_email, $subject="", $body="", $html="", $charset="") {	
		include_spip ("class/phpmailer/class.phpmailer");
		$mail = new PHPMailer();
		$mail ->CharSet  =  $charset;
		if ($html==true) $mail->IsHTML(true);
		$mail->FromName = $from_nom;
		$mail->From = $from_email;
	 	$mail->AddAddress($to_email);
		$mail->Subject = $subject;
		$mail->Body = $body;

		if(!$mail->Send())	{
	  		return false; }
		else {
			return true;
		}
	}


// Afficher l'arbo
function  abomailman_arbo_rubriques($id_rubrique,  $rslt_id_rubrique="") {
	global $ran;
	$ran ++;
	
	$marge="&nbsp;&nbsp;&nbsp;|";
	for ($g=0;$g<$ran;$g++) {
		if (($ran-1)==0) {
			$marge="&bull;";
		}
		else {
			$marge .="-"; 
		}
	}
	$marge .="&nbsp;";

	$rqt_rubriques = spip_query ("SELECT id_rubrique, id_parent, titre FROM spip_rubriques WHERE id_parent='".$id_rubrique."'");
	while ($row = spip_fetch_array($rqt_rubriques)) {
		$id_rubrique = $row['id_rubrique'];
		$id_parent = $row['id_parent'];
		$titre = $row['titre'];
		$arbo .="<option value='".$id_rubrique."'>" . $marge  . supprimer_numero (typo($titre)) . "</option>";
		$arbo .= abomailman_arbo_rubriques($id_rubrique,   $rslt_id_parent);
	}
	
	return $arbo;
	
}

?>
