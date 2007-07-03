<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function massimport_convertspip_uri($str) { 	// convertit les chaines http:// en syntaxe spip [ ]	 
	 return eregi_replace('[^(->)\'\"]http://([^[:space:]<]*)', ' [http://\\1->http://\\1]', $str); 
}

function exec_mass_import(){
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $couleur_claire;
	global $spip_lang_right;
	
	
	//-----------------------------------------
  //  Parametres  (FIXME: creer un fichier options)
  //-----------------------------------------
  // La rubrique par defaut dans lequel on importe les données si celle ci n'est pas spécifiée par l'utilisateur
  define ("ID_RUBRIQUE_PAR_DEFAUT","1");
  
  // ID de l'auteur responsable des imports sinon on prendra celui de l'utilisateur identifié
  define ("ID_AUTEUR_PAR_DEFAUT","1");
  define ("UTILISER_AUTEUR_PAR_DEFAUT","0"); // 0 ou 1
  
  // -----------------------------------------

	  
	debut_page(_T('massimport:page_mass_import'));
	
	echo "<br /><br /><br />";
	gros_titre(_T('massimport:titre_mass_import'));
	debut_gauche();
	
	debut_boite_info();
	echo propre(_T('massimport:info_page'));	
	fin_boite_info();
	
	debut_droite();
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	// données recus ?
	if (_request('mass_import')) {
	  // traitement des données du formulaire
	  debut_cadre_relief();
    if (UTILISER_AUTEUR_PAR_DEFAUT) $auteur_import_id = ID_AUTEUR_PAR_DEFAUT;
					                     else $auteur_import_id = $auteur_session['id_auteur'];
					                     
		$txt = _request('txt');
		$sep_art  = _request('sep_art');
		$sep_title = _request('sep_title');
		$conv_url = _request('conv_url');		
		$statut = _request('statut');
		$rub = (int) _request('rub');
		
		// rubrique 'import' valide ?
		$resq =spip_query("SELECT * FROM spip_rubriques WHERE id_rubrique=$rub");
		$res = spip_fetch_array($resq);
		if (!$res) die (_T('massimport:error_norub'));
		$lang = $res['lang'];
		$rub_titre = stripslashes($res['titre']);
		echo "<h3>"._T('massimport:rub_target')."<a href='?exec=naviguer&id_rubrique=$rub'>$rub_titre</a> ($lang)</h3>\n";
		
		// lecture configuration forums
		$forums_publics = substr(lire_meta('forums_publics'),0,3);
		
		// on  traite le resultat
		$txt .= $sep_art; // ajout d'un separateur pour ne pas perdre le dernier item
		$articles = explode($sep_art, $txt);
		foreach($articles as $k=>$val) {
			$current_article = trim($val);
			if ($conv_url == 1) $current_article = massimport_convertspip_uri($current_article);
			if ($conv_url == 2) $current_article = nl2br($current_article);
			$current_article_content = explode($sep_title,$current_article);
			if (count($current_article_content)==2){		// pas d'erreur, on recupere exactement deux elements
				$temp_titre = addslashes(trim($current_article_content[0]));
				if ($temp_titre=="") $temp_titre="????";
				$temp_text = addslashes(trim($current_article_content[1]));
				spip_query("INSERT INTO spip_articles (id_rubrique, titre, texte, statut, date, accepter_forum, lang) VALUES ($rub, '$temp_titre', '$temp_text' , '$statut', NOW(), '$forums_publics', '$lang')");
				$id_article = spip_insert_id();
				spip_query("INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES ($auteur_import_id, $id_article)");	
				echo "<br />"._T('massimport:article_sucess')." $id_article: <a href='?exec=articles&id_article=$id_article'>".stripslashes($temp_titre)."</a>\n";
			}
			
		}
					                     
		// lien pour importer de nouveau
		echo "<p><a href='?exec=mass_import'>"._T('massimport:new_import')."</a></p>\n";
	  fin_cadre_relief();
	
  } else {
  
	  // pas de donnees, on affiche le formulaire d'import
	  debut_cadre_relief();
	  echo "<form method='post'><input type='hidden' name='mass_import' value='1' />\n";
		echo "<br /><input type='text' size='8' name='rub' value='".ID_RUBRIQUE_PAR_DEFAUT."' /> "._T('massimport:rub_num');
		echo "<br /><input type='text' size='8' name='sep_title' value='$$$' /> "._T('massimport:sep_art');
		echo "<br /><input type='text' size='8' name='sep_art' value='***' /> "._T('massimport:sep_interart');		
		echo "<br /><strong>"._T('massimport:statut')."</strong>\n";
    $puce = 'puce-orange.gif';	$titre_etat = _T('texte_statut_propose_evaluation');
    $str_puce = " <img src='"._DIR_IMG_PACK."$puce' name='puce_temp1' width='9' height='9' style='border:0;' alt=\"$titre_etat\" title=\"$titre_etat\" />&nbsp;";	
		echo "<br /><input type='radio' name='statut' value='prop' checked='checked' />$str_puce "._T('texte_statut_propose_evaluation');
		$puce = 'puce-verte.gif';	$titre_etat = _T('texte_statut_publie');
    $str_puce = " <img src='"._DIR_IMG_PACK."$puce' name='puce_temp2' width='9' height='9' style='border:0;' alt=\"$titre_etat\" title=\"$titre_etat\" />&nbsp;";	
		echo "<br /><input type='radio' name='statut' value='publie' /> $str_puce"._T('texte_statut_publie');
		echo "<br /><br /><input type='checkbox' name='conv_url' value='1' /> "._T('massimport:convert_url');    	
		echo "<br /><br />"._T('massimport:text_import')."<br />\n";
		echo "<textarea name='txt' rows='30' cols='52'></textarea><br />\n";
		echo "<input type='submit' value='"._T('massimport:import')."' />\n";
		echo "</form>\n";
		fin_cadre_relief();
  }
	

	fin_page();
}

?>
