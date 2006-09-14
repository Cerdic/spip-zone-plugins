<?php
// MES_OPTIONS pour ACCESGROUPE : toutes les fonctions utilisées pour le controle d'accès espaces public / privé

include_spip('base/accesgroupes_tables');

//include_spip('inc/accesgroupes_fonctions');


// SURCHARGE des fonctions de l'espace privé
//   inclure les fichiers originaux de /ecrire/exec pour que toutes les fonctions natives du core soient disponibles
//   mais ne le faire que si on est sur une page de l'espace privé le nécessitant
//	 !!! EXCEPTION : breves_voir est surchargé par le fichier /exec/breves_voir.php puisque le bridage d'accès se fait dans 
//	 la fonction afficher_breves_voir() et non pas la fonction exec_breves_voir() !!!
    	$exec = _request('exec');
    	if (in_array($exec,array('naviguer','rubriques_edit','articles','articles_edit','breves_edit'))) {  // ,'breves_voir'
    	 // inclure uniquement le fichier exec dont a besoin ET utiliser un include() php et non pas include_spip() pour ne pas se faire couillonner par find_in_path()
    		 include('exec/'.$exec.'.php');
       // appel du fichier contenant les fonctions exec_xxx() modifiées pour accesgroupes
    	   include_spip('inc/accesgroupes_prive');
    	}



// création de la boucle ACCESGROUPES
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
/* pour info : syntaxe possible dans $code = <<<CODE
				\$toto = '$txt';
				return \$toto;
*/


// le filtre qui permet d'ajouter une img aux #TITRE des rubriques/articles/breves à accès restreint
function accesgroupes_visualise($texte, $id_rub = 0, $image = 'cadenas-24.gif') {
				 if (accesgroupes_verif_acces($id_rub, 'public') == 1 OR accesgroupes_verif_acces($id_rub, 'public') == 2) {
				 		return "<img src=\"ecrire/img_pack/".$image."\" alt=\""._T('accesgroupes:bloque_rubrique')."\" style=\"border: none; vertical-align: baseline;\"> ".$texte;
				 }
				 else {
				 			return $texte;
				 }
}

// le critère qui permet de ne pas afficher les rubriques à accès restreint
function critere_accesgroupes_invisible($id_boucle, &$boucles, $crit) {
				 global $Tjpk_groupes_acces;
				 $boucle = &$boucles[$id_boucle];
      // construit le tableau de toutes les rubriques à acces interdit (dans la partie publique) pour l'auteur en cours
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


// détermine si une rubrique $rub est restreinte ou non (en fct de la provenance $prive_public : prive | public)
// retourne 0 : accès libre | 1 : accès restreint non-connecté | 2 : accès restreint non-autorisé | 3 accès retreint autorisé
function accesgroupes_verif_acces($rub, $prive_public){
//   		global $Tspip_rubriques, $Tspip_auteurs, $Tspip_auteurs_rubriques, $Tjpk_groupes_acces, $Tjpk_groupes_auteurs, $Tjpk_groupes;
      
      $aut = $GLOBALS['auteur_session']['login'];
      // vérifie si la rubrique courante est privée
      do{ // boucle tant que la rubrique n'est pas la racine du site et que le retour est vide
         if (accesgroupes_RubPrive($rub, $prive_public)) {
//echo '<br>$aut = '.$aut;      
            if ($aut != "") {
               $aut = accesgroupes_IdAut($aut); // cherche l'id_auteur
               if (accesgroupes_GrpAcces($aut, $rub)){
                 $retour = 3; // accès restreint : autorisé
               }
							 else {
                 $retour = 2; // accès restreint : non autorisé
               }
            }
						else {
               $retour = 1; // accès restreint : non connecté
            }
         }
				 else {
            $retour = 0; // accès libre - vérifier la rubrique parente
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


// retourne l'id_auteur à partir du login $aut
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

// vérifie si la rubrique $rub est restreinte, en fct de la provenance $prive_public (prive | public)
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

// vérifie si l'auteur $aut est autorisé à accéder à la rubrique restreinte $rub
function accesgroupes_GrpAcces($aut,$rub){
//      global $Tspip_rubriques, $Tspip_auteurs, $Tspip_auteurs_rubriques, $Tjpk_groupes_acces, $Tjpk_groupes_auteurs, $Tjpk_groupes;
      
      $acces = 0;
		// les admins restreints ont accès sans limitation à leur rubrique
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
         return TRUE; // accès autorisé
      }
			else {
         // si pas d'accès direct pour l'auteur => test si les groupes auxquels il appartient ont un droit d'acces
  				 $sql201 = "SELECT id_grpacces FROM spip_accesgroupes_auteurs WHERE id_auteur = $aut AND dde_acces = 0";
  				 $result201 = spip_query($sql201);
  				 while ($row = spip_fetch_array($result201)) {
  							 if (accesgroupes_ssGrpAcces($row['id_grpacces'], $rub) == TRUE) {
								 		return TRUE;
								 }
  				 }
  			// si pas d'accès direct ou par groupes => test des groupes dans lesquels l'utilisateur est inclu par son statut
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

// test des accès par ss-groupe, récursivement dans toute l'ascendance du groupe testé
function accesgroupes_ssGrpAcces($id_grpe, $rub) {
//      	 global $Tspip_rubriques, $Tspip_auteurs, $Tspip_auteurs_rubriques, $Tjpk_groupes_acces, $Tjpk_groupes_auteurs, $Tjpk_groupes;

         // si pas d'accès direct pour l'auteur => test si les groupes auxquels il appartient sont exonérés par prive_public
				 $sql202 = "SELECT COUNT(*) AS NbAcces FROM spip_accesgroupes_acces WHERE id_grpacces = $id_grpe AND id_rubrique = $rub";
				 $result202 = spip_query($sql202);
				 if ($row = spip_fetch_array($result202)) {
				 		if ($row['NbAcces'] > 0) {
							 return TRUE;
						}
  				  else {
  				 		// test des groupes de l'ascendance du groupe testé
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

// détermine si une rubrique à accès restreint est contrôlée par (au moins) un groupe autorisant les demandes d'accès
function accesgroupes_existe_demande_acces($rub) {
//				 global $Tspip_rubriques, $Tspip_auteurs, $Tspip_auteurs_rubriques, $Tjpk_groupes_acces, $Tjpk_groupes_auteurs, $Tjpk_groupes;
		// déterminer si c'est la rubrique en cours qui est restreinte
		 		 $sql333 = "SELECT COUNT(*) as nb_rub FROM $Tjpk_groupes_acces WHERE id_rubrique = $rub";
				 $result333 = spip_query($sql333);
				 $row333 = spip_fetch_array($result333);
				 $existe_rub = $row333['nb_rub'];
		// si c'est la rubrique en cours qui est contrôlée, tester si le groupe autorise les demandes d'accès
				 if ($existe_rub > 0) {
    				 $sql303 = "SELECT COUNT(*) AS nb_demande_acces
    				 				 	  FROM $Tjpk_groupes_acces, $Tjpk_groupes
    				 				 	  WHERE $Tjpk_groupes_acces.id_grpacces = $Tjpk_groupes.id_grpacces
    										AND $Tjpk_groupes_acces.id_rubrique = $rub
    										AND $Tjpk_groupes.demande_acces = 1";
    				 $result303 = spip_query($sql303);
    				 $rows303 = spip_fetch_array($result303);
    				 if ($rows303['nb_demande_acces'] > 0) {
    				 		return TRUE;
    				 }
    				 else {
									return FALSE;
    				 }
    		 }
		// sinon tester si c'est son parent qui est la rubrique restreinte (récursivement)
				 else {
				 			$sql374 = "SELECT id_parent FROM $Tspip_rubriques WHERE id_rubrique = $rub LIMIT 1";
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
      				 			$sql374 = "SELECT id_parent FROM $Tspip_rubriques WHERE id_rubrique = $rub LIMIT 1";
      							$result374 = spip_query($sql374);
      							$row374 = spip_fetch_array($result374);
      							$id_parent = $row374['id_parent'];
							 			$rub = $id_parent;
							 }
				 }
				 return $retour;
}

// FIN - Acces groupes


?>