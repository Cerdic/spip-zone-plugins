<?php
// MES_OPTIONS pour ACCESGROUPE : toutes les fonctions utilis�es pour le controle d'acc�s espaces public / priv�

include_spip('base/accesgroupes_tables');


// SURCHARGE des fonctions de l'espace priv�
//   inclure les fichiers originaux de /ecrire/exec pour que toutes les fonctions natives du core soient disponibles
//   mais ne le faire que si on est sur une page de l'espace priv� le n�cessitant
//	 !!! EXCEPTION : breves_voir est surcharg� par le fichier /exec/breves_voir.php puisque le bridage d'acc�s se fait dans 
//	 la fonction afficher_breves_voir() et non pas la fonction exec_breves_voir() !!!
//	 merci ESJ pour la subtilit� du include() php � la place du inclure_spip()
			$exec = _request('exec'); // si on est dans l'espace priv� : int�grer le fichier concern� par la surcharge
    	if (in_array($exec, array('naviguer','rubriques_edit','articles','articles_edit','articles_versions','breves_edit'))) {  // ,'breves_voir'
    	 // inclure uniquement le fichier exec dont a besoin ET utiliser un include() php et non pas include_spip() pour ne pas se faire couillonner par find_in_path()
    		 include('exec/'.$exec.'.php');
       // appel du fichier contenant les fonctions exec_xxx() modifi�es pour accesgroupes
    	   include_spip('inc/accesgroupes_prive');
    	}

// CACHE : n�cessit� d'un cache diff�renci� selon les rubriques autoris�es/restreintes 
//   ajouter un marqueur de cache pour permettre de differencier le cache en fonction des rubriques autorisees
// 	 potentiellement une version de cache differente par combinaison de rubriques autoris�es pour un utilisateur + le cache de base sans autorisation
//   merci Cedric pour la m�thode (plugin acces_restreint) 
			if ($exec == '') {  // si on on est dans l'espace public g�rer le marqueur de cache
          if (isset($auteur_session['id_auteur'])) {
//echo '<br>d�but cache';
	           $combins = accesgroupes_combin();
          	 $combins = join("-",$combins);
          	 if (!isset($GLOBALS['marqueur'])) {
							  $GLOBALS['marqueur'] = "";
						 }
          	 $GLOBALS['marqueur'] .= ":accesgroupes_combins $combins";
	        }
			}
		
// fct pour construire et renvoyer le tableau des rubriques � acc�s restreint dans la partie PUBLIQUE
// 		 clone de la fct accesgroupes_liste_rubriques_restreintes() de inc/accesgroupes_fonctions.php 
	function accesgroupes_combin($id_parent = 0) {
	  			 $id_parent = intval($id_parent); // securite					 
  				 static $Trub_restreintes; // n�cessaire pour que la suite ne soit �x�cut�e qu'une fois par hit (m�me si on � n BOUCLES)
      		 if (!is_array($Trub_restreintes)) {
      			  $Trub_restreintes = array();
    			// attaquer � la racine pour mettre tout de suite les �ventuels secteurs restreints dans le tableau ce qui acc�l�rera la suite
        			$sql1 = "SELECT id_rubrique, id_parent, id_secteur FROM spip_rubriques";	
        			$result1 = spip_query($sql1);
        			while ($row1 = spip_fetch_array($result1)) {
        			 			 $rub_ec = $row1['id_rubrique'];
    								 $parent_ec = $row1['id_parent'];
    								 $sect_ec = $row1['id_secteur'];
        					// si le parent ou le secteur est d�ja dans le tableau : vu le principe d'h�ritage pas la peine d'aller plus loin :)
/*        						 if (in_array($parent_ec, $Trub_restreintes) OR in_array($sect_ec, $Trub_restreintes)) {
        						 		$Trub_restreintes[] = $rub_ec;								
        						 }
        					// sinon c'est plus couteux : il faut faire le test complet de la restriction de la rubrique pour espace public
        						 else {*/
        									if (accesgroupes_verif_acces($rub_ec, 'public') == 1 OR accesgroupes_verif_acces($rub_ec, 'public') == 2) {
        										 $Trub_restreintes[] = $rub_ec;
       									  }
//        						 }
        			}
					 }
//echo '<br>tableau des rubriques = ';
//print_r($Trub_restreintes);
					 return $Trub_restreintes;
	}

			
/********************  OLD : la boucle de la v 0.7
// cr�ation de la boucle ACCESGROUPES
$GLOBALS['tables_principales']['spip_accesgroupes'] = array('field' => array(), 'key' => array());
$GLOBALS['table_des_tables']['accesgroupes'] = 'accesgroupes';

function boucle_ACCESGROUPES($id_boucle, &$boucles) {
				 global $Tspip_rubriques, $Tspip_breves, $Tspip_articles;
				 $boucle = &$boucles[$id_boucle];
			// si on est pas dans un squelette rubrique, trouver le id_rubrique
				 if (!$GLOBALS['id_rubrique']) {
				 	// cas du squelette article
						if ($GLOBALS['id_article']) {
							 $crit_champ = 'id_article';
							 $crit_table = $Tspip_articles;
							 $crit_id = $GLOBALS['id_article'];
						}
				 // cas du squelette breve
						else {
								 $crit_champ = 'id_breve';
								 $crit_table = $Tspip_breves;
								 $crit_id = $GLOBALS['id_breve'];
						}
					  $sql222 = "SELECT id_rubrique FROM $crit_table WHERE $crit_champ = $crit_id LIMIT 1";
					  $result222 = spip_query($sql222);
						$row222 = spip_fetch_array($result222);
						$id_rub = $row222['id_rubrique'];
				 }
				 else {
				 			$id_rub = $GLOBALS['id_rubrique'];
				 }
	$code = <<<CODE
						 if (accesgroupes_verif_acces($id_rub, 'public') == 1 OR accesgroupes_verif_acces($id_rub, 'public') == 2) {
						 		return '&nbsp;';
						 }
CODE;
	return $code;
}
// pour info : syntaxe possible dans $code = <<<CODE
//				\$toto = '$txt';
//				return \$toto;


// le filtre qui permet d'ajouter une img aux #TITRE des rubriques/articles/breves � acc�s restreint
function accesgroupes_visualise($texte, $id_rub = 0, $image = 'cadenas-24.gif') {
				 if (accesgroupes_verif_acces($id_rub, 'public') == 1 OR accesgroupes_verif_acces($id_rub, 'public') == 2) {
				 		return "<img src=\"ecrire/img_pack/".$image."\" alt=\""._T('accesgroupes:bloque_rubrique')."\" style=\"border: none; vertical-align: baseline;\"> ".$texte;
				 }
				 else {
				 			return $texte;
				 }
}

// le crit�re qui permet de ne pas afficher les rubriques � acc�s restreint
function critere_accesgroupes_invisible($id_boucle, &$boucles, $crit) {
				 global $Tjpk_groupes_acces;
				 $boucle = &$boucles[$id_boucle];
      // construit le tableau de toutes les rubriques � acces interdit (dans la partie publique) pour l'auteur en cours
				 $sql1 = "SELECT id_rubrique FROM $Tjpk_groupes_acces WHERE prive_public != 1";
         $result1 = spip_query($sql1);
         $Trub_interdites = array();
         while ($rows = spip_fetch_array($result1)) {
        			 if (accesgroupes_verif_acces($rows['id_rubrique'], 'public') == 1 OR accesgroupes_verif_acces($rows['id_rubrique'], 'public') == 2) {
        				  if (!in_array($rows['id_rubrique'], $Trub_interdites)) {
        				 		 $Trub_interdites[] = $rows['id_rubrique'];
        				  }
        			 }
         }
				 foreach ($Trub_interdites as $id_rub_ec) {
				 				 $boucle->where[] = ' id_rubrique != '.$id_rub_ec;
				 }
}

*************************** FIN OLD */


// d�termine si une rubrique $rub est restreinte ou non (en fct de la provenance $prive_public : prive | public)
// retourne 0 : acc�s libre | 1 : acc�s restreint non-connect� | 2 : acc�s restreint non-autoris� | 3 acc�s retreint autoris�
function accesgroupes_verif_acces($rub, $prive_public){
//   		global $Tspip_rubriques, $Tspip_auteurs, $Tspip_auteurs_rubriques, $Tjpk_groupes_acces, $Tjpk_groupes_auteurs, $Tjpk_groupes;
      
      $aut = $GLOBALS['auteur_session']['login'];
      // v�rifie si la rubrique courante est priv�e
      do{ // boucle tant que la rubrique n'est pas la racine du site et que le retour est vide
         if (accesgroupes_RubPrive($rub, $prive_public)) {
//echo '<br>$aut = '.$aut;      
            if ($aut != "") {
               $aut = accesgroupes_IdAut($aut); // cherche l'id_auteur
               if (accesgroupes_GrpAcces($aut, $rub)){
                 $retour = 3; // acc�s restreint : autoris�
               }
							 else {
                 $retour = 2; // acc�s restreint : non autoris�
               }
            }
						else {
               $retour = 1; // acc�s restreint : non connect�
            }
         }
				 else {
            $retour = 0; // acc�s libre - v�rifier la rubrique parente
         }
         $sql = "SELECT id_parent FROM spip_rubriques WHERE id_rubrique = $rub LIMIT 1"; // recherche la rubrique parente
         $result = spip_query($sql);
         if ($row = spip_fetch_array($result)) {
            $rub = $row['id_parent'];
         }
      }
			while ($rub > 0 && $retour == 0 );
      return $retour;
}


// retourne l'id_auteur � partir du login $aut
function accesgroupes_IdAut($aut){
//   		global $Tspip_rubriques, $Tspip_auteurs, $Tspip_auteurs_rubriques, $Tjpk_groupes_acces, $Tjpk_groupes_auteurs, $Tjpk_groupes;
      
      $sql = "SELECT id_auteur, statut FROM spip_auteurs WHERE login='" .addslashes($aut) ."' LIMIT 1";
      $result = spip_query($sql);
      if ($result){
         if ($row = spip_fetch_array($result)){
            $aut = $row['id_auteur'];
         }
      }
      return $aut;
}

// v�rifie si la rubrique $rub est restreinte, en fct de la provenance $prive_public (prive | public)
function accesgroupes_RubPrive($rub, $prive_public){
//   		global $Tspip_rubriques, $Tspip_auteurs, $Tspip_auteurs_rubriques, $Tjpk_groupes_acces, $Tjpk_groupes_auteurs, $Tjpk_groupes;
      $sql = "SELECT count(*) AS nb_acces
					 	  FROM spip_accesgroupes_acces
							LEFT JOIN spip_accesgroupes_groupes 
							ON spip_accesgroupes_acces.id_grpacces = spip_accesgroupes_groupes.id_grpacces
					 	  WHERE id_rubrique = $rub 
							AND actif = 1";
			$prive_public == 'prive' ? $sql .= ' AND prive_public < 2' : $sql .= ' AND prive_public != 1';
      $result = spip_query($sql);
//print '<br>$sql = '.$sql.'<br>mysql_error() = '.mysql_error().'<br>';			
      if ($row = spip_fetch_array($result)) {
      	 $prive = $row['nb_acces'];
      }
//echo '<br>$prive = '.$prive;			
      if ($prive > 0) {
         return true;
      }
			else {
         return false;
      }
}

// v�rifie si l'auteur $aut est autoris� � acc�der � la rubrique restreinte $rub
function accesgroupes_GrpAcces($aut,$rub){
//      global $Tspip_rubriques, $Tspip_auteurs, $Tspip_auteurs_rubriques, $Tjpk_groupes_acces, $Tjpk_groupes_auteurs, $Tjpk_groupes;
      
      $acces = 0;
		// les admins restreints ont acc�s sans limitation � leur rubrique
		  $sql507 = "SELECT COUNT(*) AS est_admin 
								 FROM spip_auteurs_rubriques 
								 WHERE id_auteur = $aut 
								 AND id_rubrique = $rub 
								 LIMIT 1";
			$result507 = spip_query($sql507);
			$row507 = spip_fetch_array($result507);
			if ($row507['est_admin'] > 0) {
				 return TRUE;
			}
			$sql517 = "SELECT count(*) AS NbAcces 
					 	  FROM spip_accesgroupes_acces
							LEFT JOIN spip_accesgroupes_auteurs
							ON spip_accesgroupes_acces.id_grpacces = spip_accesgroupes_auteurs.id_grpacces
							LEFT JOIN spip_accesgroupes_groupes
							ON spip_accesgroupes_auteurs.id_grpacces = spip_accesgroupes_groupes.id_grpacces
        			WHERE id_auteur = $aut  
              AND dde_acces = 0 
              AND id_rubrique = $rub 
              AND actif = 1";
      $result = spip_query($sql517);
//echo 'mysql_error $sql517 = '.mysql_error();			
      if ($row = spip_fetch_array($result)){
         $acces = $row['NbAcces'];
      }
      if ($acces > 0){
         return TRUE; // acc�s autoris�
      }
			else {
         // si pas d'acc�s direct pour l'auteur => test si les groupes auxquels il appartient ont un droit d'acces
  				 $sql201 = "SELECT id_grpacces FROM spip_accesgroupes_auteurs WHERE id_auteur = $aut AND dde_acces = 0";
  				 $result201 = spip_query($sql201);
  				 while ($row = spip_fetch_array($result201)) {
  							 if (accesgroupes_ssGrpAcces($row['id_grpacces'], $rub) == TRUE) {
								 		return TRUE;
								 }
  				 }
  			// si pas d'acc�s direct ou par groupes => test des groupes dans lesquels l'utilisateur est inclu par son statut
					 $sql202 = "SELECT statut FROM spip_auteurs WHERE id_auteur = $aut LIMIT 1";
           $result202 = spip_query($sql202);
           if ($result202){
              if ($row = spip_fetch_array($result202)){
    					   $sp_statut = $row['statut'];
              }
    			 }
					 $sql204 = "SELECT id_grpacces FROM spip_accesgroupes_auteurs WHERE sp_statut = '$sp_statut'";					 
  				 $result204 = spip_query($sql204);
  				 while ($row = spip_fetch_array($result204)) {
  							 if (accesgroupes_ssGrpAcces($row['id_grpacces'], $rub) == TRUE) {
  									  return TRUE;
  								 }
  				 }
 					 return FALSE;
      }
}

// test des acc�s par ss-groupe, r�cursivement dans toute l'ascendance du groupe test�
function accesgroupes_ssGrpAcces($id_grpe, $rub) {
//      	 global $Tspip_rubriques, $Tspip_auteurs, $Tspip_auteurs_rubriques, $Tjpk_groupes_acces, $Tjpk_groupes_auteurs, $Tjpk_groupes;

         // si pas d'acc�s direct pour l'auteur => test si les groupes auxquels il appartient sont exon�r�s par prive_public
				 $sql202 = "SELECT COUNT(*) AS NbAcces FROM spip_accesgroupes_acces WHERE id_grpacces = $id_grpe AND id_rubrique = $rub";
				 $result202 = spip_query($sql202);
				 if ($row = spip_fetch_array($result202)) {
				 		if ($row['NbAcces'] > 0) {
							 return TRUE;
						}
  				  else {
  				 		// test des groupes de l'ascendance du groupe test�
								 $sql203 = "SELECT id_grpacces FROM spip_accesgroupes_auteurs WHERE id_ss_groupe = $id_grpe";
  							 $result203 = spip_query($sql203);
  							 while ($row = spip_fetch_array($result203)) {
  										 $id_at = $row['id_grpacces'];
  										 if (accesgroupes_ssGrpAcces($id_at, $rub) == TRUE) {
  											  return TRUE;
  										 }
  							 }
  				  }
				 }
				 return FALSE;
}

// d�termine si une rubrique � acc�s restreint est contr�l�e par (au moins) un groupe autorisant les demandes d'acc�s
function accesgroupes_existe_demande_acces($rub) {
//				 global $Tspip_rubriques, $Tspip_auteurs, $Tspip_auteurs_rubriques, $Tjpk_groupes_acces, $Tjpk_groupes_auteurs, $Tjpk_groupes;
		// d�terminer si c'est la rubrique en cours qui est restreinte
		 		 $sql333 = "SELECT COUNT(*) as nb_rub FROM spip_accesgroupes_acces WHERE id_rubrique = $rub";
				 $result333 = spip_query($sql333);
				 $row333 = spip_fetch_array($result333);
				 $existe_rub = $row333['nb_rub'];
		// si c'est la rubrique en cours qui est contr�l�e, tester si le groupe autorise les demandes d'acc�s
				 if ($existe_rub > 0) {
    				 $sql303 = "SELECT COUNT(*) AS nb_demande_acces
    				 				 	  FROM spip_accesgroupes_acces
												LEFT JOIN spip_accesgroupes_groupes
												ON spip_accesgroupes_acces.id_grpacces = spip_accesgroupes_groupes.id_grpacces
    				 				 	  WHERE id_rubrique = $rub
    										AND demande_acces = 1";
    				 $result303 = spip_query($sql303);
    				 $rows303 = spip_fetch_array($result303);
    				 if ($rows303['nb_demande_acces'] > 0) {
    				 		return TRUE;
    				 }
    				 else {
									return FALSE;
    				 }
    		 }
		// sinon tester si c'est son parent qui est la rubrique restreinte (r�cursivement)
				 else {
				 			$sql374 = "SELECT id_parent FROM spip_rubriques WHERE id_rubrique = $rub LIMIT 1";
							$result374 = spip_query($sql374);
							$row374 = spip_fetch_array($result374);
							$id_parent = $row374['id_parent'];
							if (accesgroupes_existe_demande_acces($id_parent) == TRUE) {
								 return TRUE;
							}
				 }
}

// trouve la rubrique restreinte dans l'ascendance d'une rubrique
function accesgroupes_trouve_parent_restreint($rub, $prive_public, $retour = '') {
//				 global $Tspip_rubriques, $Tspip_auteurs, $Tspip_auteurs_rubriques, $Tjpk_groupes_acces, $Tjpk_groupes_auteurs, $Tjpk_groupes;
				 while ($rub != 0 AND $retour == '') {
							 if (accesgroupes_RubPrive($rub, $prive_public)) {
							 		$retour = $rub;
							 }
							 else {
      				 			$sql374 = "SELECT id_parent FROM spip_rubriques WHERE id_rubrique = $rub LIMIT 1";
      							$result374 = spip_query($sql374);
      							$row374 = spip_fetch_array($result374);
      							$id_parent = $row374['id_parent'];
							 			$rub = $id_parent;
							 }
				 }
				 return $retour;
}

// fct pour retourner un tableau avec l'id_auteur et le nom du proprio d'un groupe
function accesgroupes_trouve_proprio_groupe($id_grpe) {
				 $sql = "SELECT spip_accesgroupes_groupes.proprio, spip_auteurs.nom
				 			   FROM  spip_accesgroupes_groupes
								 LEFT JOIN spip_auteurs
								 ON spip_accesgroupes_groupes.proprio = spip_auteurs.id_auteur
								 WHERE id_grpacces = $id_grpe
								 LIMIT 1";
				$result = spip_query($sql);
				if ($row = spip_fetch_array($result)) {   // si le proprio est un admin restreint $row['nom'] est vide
					 return array('id_proprio' => $row['proprio'], 'nom_proprio' => ($row['nom'] != '' ? $row['nom'] : _T('accesgroupes:tous_les_admins')) );
				}
}

// fct pour g�rer l'affichage en cas de rubrique/article/breve restreints 
// dans l'espace priv� g�re info restreint + formulaire d'inscription / dans l'espace public ne g�re que le formulaire d'inscription
function accesgroupes_affichage_acces_restreint() {
				 $exec = _request('exec');
			// trouver l'id_rubrique dans laquelle se trouve l'�l�ment restreint en cours
				 if ($exec != '') {  // si on est dans l'espace priv�
						if ($exec == 'articles' OR $exec == 'articles_edit' OR $exec == 'articles_versions') {
									 global $id_article;
									 $sql2 = "SELECT id_rubrique FROM spip_articles WHERE id_article = $id_article LIMIT 1";
									 $result2 = spip_query($sql2);
									 if ($row2 = spip_fetch_array($result2)) {
									 		$id_rubrique = $row2['id_rubrique'];
									 }
									 else {
									 	  // si cr�ation d'un nouvel article en �tant positionn� dans une rubrique � acc�s interdit pour l'auteur
												if (isset($_GET['id_rubrique']) AND $_GET['id_rubrique']) {
													 $id_rubrique = $_GET['id_rubrique'];
												}
												else {
														 $id_rubrique = 0;
												}
									 }
//echo '<br>$id_rubrique='.$id_rubrique;			 
				 		}
						elseif ($exec == 'breves_voir' OR $exec == 'breves_edit') {
									 global $id_breve;
									 $sql2 = "SELECT id_rubrique FROM spip_breves WHERE id_breve = $id_breve LIMIT 1";
									 $result2 = spip_query($sql2);
									 if ($row2 = spip_fetch_array($result2)) {
									 		$id_rubrique = $row2['id_rubrique'];
									 }
									 else {
									 	  // si cr�ation d'une nouvelle br�ve en �tant positionn� dans une rubrique � acc�s interdit pour l'auteur
												if (isset($_GET['id_rubrique']) AND $_GET['id_rubrique']) {
													 $id_rubrique = $_GET['id_rubrique'];
												}
												else {
														 $id_rubrique = 0;
												}
									 }
						}
						else {
								 global $id_rubrique;
						}
						$url_img_pack = 'img_pack';
						$url_ecrire = '';
						$url_retour = 'ecrire/';					
				 }
				 else {   // on est dans l'espace public
				 			global $id_rubrique;
							$url_img_pack = 'ecrire/img_pack';
							$url_ecrire = 'ecrire/';
							$url_retour = 'index.php';
				 }

			// traitement des donn�es envoy�es par le formulaire
    	   $msg_retour_form = '';
				 if (isset($_POST['add_auteur']) AND isset($_POST['auteur']) AND $_POST['auteur'] != '' AND isset($_POST['groupe_demande_acces']) AND $_POST['groupe_demande_acces'] != '') {
    				 $auteur = $_POST['auteur'];
    				 $groupe_demande_acces = $_POST['groupe_demande_acces'];
    				 $sql224 = "SELECT nom FROM spip_accesgroupes_groupes WHERE id_grpacces = $groupe_demande_acces LIMIT 1";
    				 $result224 = spip_query($sql224);
    				 $row224 = spip_fetch_array($result224);
    				 $nom_groupe = $row224['nom'];
    				 $sql225 = "SELECT titre FROM spip_rubriques WHERE id_rubrique = $id_rubrique LIMIT 1";
    				 $result225 = spip_query($sql225);
    				 $row225 = spip_fetch_array($result225);
    				 $nom_rubrique = $row225['titre'];
    				 $message = _T('accesgroupes:msg_demande_acces1').'<strong>'.$GLOBALS['auteur_session']['nom'].'</strong> (#'.$auteur.') '
    				 						._T('accesgroupes:msg_demande_acces2').'<strong>'.$nom_groupe.'</strong> (#'.$groupe_demande_acces.')'
    										._T('accesgroupes:msg_demande_acces3').'<strong>'.$nom_rubrique.'</strong> (#'.$id_rubrique.')'
    										._T('accesgroupes:msg_demande_acces4')
												.'<a href="?exec=accesgroupes_admin&groupe='.$groupe_demande_acces.'">'
    										._T('accesgroupes:msg_demande_acces5').'</a><br />'
												.'<span style="font-size: 75%;">'._T('accesgroupes:msg_demande_acces6').'</span>';
    				 if (isset($_POST['message']) AND $message != '') {
    				 		$message .= '<br /><br /><strong>'._T('accesgroupes:msg_demande_acces7').'</strong><br />'.$_POST['message'];
    				 }
    				 $message = addslashes($message);
    				 $sql24 = "SELECT proprio FROM spip_accesgroupes_groupes WHERE id_grpacces = $groupe_demande_acces LIMIT 1";
    				 $result24 = spip_query($sql24);
    				 $row24 = spip_fetch_array($result24);
    				 $proprio = $row24['proprio'];				 
    				 $sql23 = "INSERT INTO spip_accesgroupes_auteurs (id_grpacces, id_auteur, dde_acces, proprio) 
						 					 VALUES ($groupe_demande_acces, $auteur, 1, $proprio)";
    				 spip_query($sql23);
    				 if (mysql_errno() == 1062) {
    				 	  $msg_retour_form = "<br /><img src=\"".$url_img_pack."/warning-24.gif\" style=\"vertical-align: middle;\"> "._T('accesgroupes:duplicata_demande_acces');
    				 }
    				 elseif (mysql_error() == '') {
    						 $sql25 = "SELECT MAX(id_message) AS maxId FROM spip_messages";
    						 $result25 = spip_query($sql25);
    						 $row25 = spip_fetch_array($result25);
        		 		 $id_forum = $row25['maxId'] + 1;
    						 $date_pub = date("y-m-d H:i:s");
    						 $titre_mess = addslashes(_T('accesgroupes:titre_demande_acces'));
    						 $sql26 = "INSERT INTO spip_messages (id_message, titre, texte, type, date_heure, rv, statut, id_auteur, maj)
    						 					 VALUES ($id_forum, '$titre_mess', '$message', 'normal', '$date_pub', 'non', 'publie', $auteur, '$date_pub')";
    						 spip_query($sql26);
    						 if (mysql_error() == '') {
    								if ($proprio != 0) {   // si le proprio n'est pas un admin total
    									 $sql28 = "INSERT INTO spip_auteurs_messages (id_auteur, id_message, vu) VALUES ($proprio, $id_forum, 'non')";
    									 spip_query($sql28);
    								}
    								else {  // si le proprio est un admin total ($proprio == 0), envoyer le message � tous les admins
    										 $sql29 = "SELECT id_auteur FROM spip_auteurs WHERE statut = '0minirezo'";
    										 $result29 = spip_query($sql29);
    										 while ($rows29 = spip_fetch_array($result29)) {
    										 			 $id_admin_ec = $rows29['id_auteur'];
    													 $sql30 = "SELECT COUNT(*) AS nb_rub_admin FROM spip_auteurs_rubriques WHERE id_auteur = $id_admin_ec";
    													 $result30 = spip_query($sql30);
    													 $rows30 = spip_fetch_array($result30);
    													 if ($rows30['nb_rub_admin'] < 1) {
    													 		$sql31 = "INSERT INTO spip_auteurs_messages (id_auteur, id_message, vu) 
																				 	  VALUES ($id_admin_ec, $id_forum, 'non')";
    									 						spip_query($sql31);
    													 }
    										 }
    								}
    								if (mysql_error() == '') {
    									// si tout s'est bien pass�, stocker la valeur de $id_forum dans dde_acces de l'auteur pour effa�age automatique du message de demande d'acc�s par la suite 
											 $sql32 = "UPDATE spip_accesgroupes_auteurs 
									 	   				 	 SET dde_acces = $id_forum
																 WHERE id_auteur = $auteur
																 AND id_grpacces = $groupe_demande_acces
																 AND proprio = $proprio
																 LIMIT 1";
											 spip_query($sql32);
//echo '<br>$id_forum = '.$id_forum.'<br>$sql32 = '.$sql32.'<br>mysql_error $sql32 = '.mysql_error();											 
											 $msg_retour_form .= "<br /><img src=\"$url_img_pack/m_envoi.gif\" style=\"vertical-align: middle;\"> <img src=\"$url_img_pack/message.gif\" style=\"vertical-align: bottom;\"> ";
    									 $msg_retour_form .= _T('accesgroupes:demande_ok');
    								}
    								else {
    										 $msg_retour_form .= "<br /><img src=\"$url_img_pack/warning-24.gif\" style=\"vertical-align: middle;\"> "._T('accesgroupes:erreur_creation_demande_acces');
    								}
    						 }
    						 else {
    						 			$msg_retour_form .= "<br /><img src=\"$url_img_pack/warning-24.gif\" style=\"vertical-align: middle;\"> "._T('accesgroupes:erreur_creation_demande_acces');
    						 }
    				 }
    				 else {
    				 		 $msg_retour_form .= "<br /><img src=\"$url_img_pack/warning-24.gif\" style=\"vertical-align: middle;\"> "._T('accesgroupes:erreur_creation_demande_acces');
    				 }
    		 }
				 if ($msg_retour_form != '') {
				 		$msg_retour_form .= '<br /><br /><a href="'.$url_retour.'">['._T('accesgroupes:retour_site').']</a><br />';
				 }

			// envoyer l'affichage de la restriction avec le formulaire si n�cessaire
				 if ($exec != '') {  // si on est dans l'espace priv�
						$provenance_prive_public = 'prive';
						if ($exec == 'articles' OR $exec == 'articles_edit') {
									 $info = 'info_numero_article';
									 $id_elem = $id_article;
									 $info_bloque = 'bloque_article';
						}
						elseif ($exec == 'breves_voir' OR $exec == 'breves_edit') {
									 $info = 'info_gauche_numero_breve';
									 $id_elem = $id_breve;
									 $info_bloque = 'bloque_breve';
						}
						else {
								 $info = 'titre_numero_rubrique';
								 $id_elem = $id_rubrique;
								 $info_bloque = 'bloque_rubrique';
						}
						debut_gauche(); 
            debut_boite_info(); 
            echo "<div align='center'>\n"; 
            echo "<font face='Verdana,Arial,Sans,sans-serif' size='1'><b>"._T($info)."</b></font>\n"; 
            echo "<br><font face='Verdana,Arial,Sans,sans-serif' size='6'><b>$id_elem</b></font>\n"; 
            echo "</div>\n"; 
            fin_boite_info(); 
            debut_droite(); 
            debut_cadre_relief($ze_logo); 
            echo "\n<table cellpadding=2 cellspacing=0 border=0 width='100%'>"; 
            echo "<tr width='100%'> <td width='100%' valign='top' colspan='2'>"; 
            gros_titre($titre); 
            echo "</td> </tr> <td>".http_img_pack("warning.gif",'', "width='48' height='48'", _T('info_administrer_rubrique')); 
            echo "</td><td>"._T('accesgroupes:'.$info_bloque)."</td></tr>"; 
            echo "</table>\r\n<br /><br />\r\n"; 
            fin_cadre_relief(); 
				 }
				 else {		// on est dans l'espace public 
				 			$provenance_prive_public = 'public';
				 }

      // affichage du formulaire de demande d'acc�s si au moins un groupe contr�lant la rubrique l'autorise
			// !!! et si $id_rubrique est != 0 => plantage de la page sinon !!!
    		 if ($id_rubrique != 0 AND accesgroupes_existe_demande_acces($id_rubrique) == TRUE AND $msg_retour_form == '') {			
						 echo "<form style=\"background: #eee; border: solid 1px #aaa; padding: 10px;\" name=\"accesgroupe\" method=\"post\" action=\"".basename($_SERVER['SCRIPT_FILENAME'])."?".$_SERVER['QUERY_STRING']."\">";
             echo _T('accesgroupes:demande_acces'); 
  					 echo "<br />"._T('accesgroupes:choix_groupe');
        	// trouver si c'est la rubrique en cours qui est restreinte ou un de ses ascendants
			       $id_rub_restreinte = accesgroupes_trouve_parent_restreint($id_rubrique, $provenance_prive_public);
			 		 	 $sql22 = "SELECT spip_accesgroupes_acces.id_grpacces, 
        		 									spip_accesgroupes_groupes.nom
        		 					 FROM spip_accesgroupes_acces
											 LEFT JOIN spip_accesgroupes_groupes
											 ON spip_accesgroupes_acces.id_grpacces = spip_accesgroupes_groupes.id_grpacces
        							 WHERE demande_acces = 1
        							 AND id_rubrique = $id_rub_restreinte
        							 AND actif = 1
        		  ";
        		  $result22 = spip_query($sql22);
//echo '<br>mysql_error $sql22 = '.mysql_error();									 
        		 	echo " <select name=\"groupe_demande_acces\" size=\"1\">";
          		while ($row22 = spip_fetch_array($result22)) {
											$id_groupe_ec = $row22['id_grpacces'];
                  	  $nom_groupe_ec = $row22['nom'];
											$Tproprio_grpe = accesgroupes_trouve_proprio_groupe($id_groupe_ec);
											$nom_proprio_ec = $Tproprio_grpe['nom_proprio'];
        
                   	  echo "<option value=\"$id_groupe_ec\">$nom_groupe_ec ("._T('accesgroupes:proprio')." = $nom_proprio_ec)</option>";
              }  
              echo " </select><br /><br />";

  						echo _T('accesgroupes:help_demande_acces')."<br /> <textarea name=\"message\" rows=\"4\" cols=\"55\"></textarea>";
              echo "<input type=\"hidden\" name=\"auteur\" value=\"".$GLOBALS['auteur_session']['id_auteur']."\" /><br>";
              echo "<input type=\"submit\" name=\"add_auteur\" value=\""._T('accesgroupes:envoyer')."\"/>";
              echo "</form>";
    		 }		// fin formulaire demande acces
				 else {  // affichage du message de retour du formulaire
				 			echo $msg_retour_form;
				 }
					
}




?>