<?php

/** l'utilisateur est il admin ?
 * @return boolean : true si utilisateur est admin
 */
function isAdmin() {
	$tab_auteur=$GLOBALS["auteur_session"];

	if($tab_auteur['statut']!="0minirezo")
	return false;
	else return true;
}

// regles de gestion sur les tables
// entrees : $valeur,$type = la valeur à tester et le type de champ correspondant (ind&eacute;pendant de la table)
//           $departement : le d&eacute;partement (pour le test de l'&eacute;tablissement)
// sorties : ["retour"] = valeur telle qu'elle sera importée dans la base (identifiant par exemple)
//           ["fatal"] = texte expliquant pourquoi on ne peut importer (et valeurs possibles)
//           ["txt_debug"] = texte pour debug
function reglesGestion($table,$valeur,$champ,$dept,$num_candidat) {
   global $odb_referentiel;
   $type=$odb_referentiel[$table][$champ];
   //print_r($odb_referentiel[$table]);
   static $aErreurs = array();
   $annee=date('Y');
   //print_r($aErreurs);
   if($dept=="") die(KO." - dept NULL : valeur $valeur - type $type - candidat $num_candidat");
   switch($type) {
      ////////////////////////////// R&egrave;gles de gestion en fonction du type ///////////////////////
      case "row_titre":
         //ne rien faire
         break;
      case "INTEGER":
      case "YEAR":
         //doit être num&eacute;rique
         if($valeur=="")
            $valeur="0";
         elseif(!is_numeric($valeur))
            $fatal.=KO." - Candidat $num_candidat, le champ <b>$champ</b> ($valeur) n'est pas entier<br/>\n";
         break;
      case 'TIMESTAMP':
         if($valeur=='0000-00-00 00:00:00') $valeur=date("Y-m-d H:i:s");
         $valeur="@_quote_@".mysql_real_escape_string ($valeur)."@_quote_@";
         break;
      case "VARCHAR":
         //on &eacute;chappe et on met des apostrophes
         $valeur="@_quote_@".mysql_real_escape_string ($valeur)."@_quote_@";
         break;
      case "DATE":
         //on teste date, on la formate et on met apostrophes
         if(strlen($valeur)>0 && $valeur!="0000-00-00") {
            $tab=explode("/",$valeur);
            if(count($tab)>1) {
               $jour_=$tab[0];
               $mois_=$tab[1];
               $annee_=$tab[2];
            } else {
               $tab=explode("-",$valeur);
               $annee_=$tab[0];
               $mois_=$tab[1];
               $jour_=$tab[2];
            }
            if(strlen($annee_)==2) $annee_='19'.$annee_;
            if(!checkdate((int)$mois_,(int)$jour_,(int)$annee_))
               $fatal.=KO." - Candidat $num_candidat, le champ <b>$champ</b> ($valeur) n'est pas une date valide<br/>\n";
            else
               $valeur="$annee_-$mois_-$jour_";
            $valeur="@_quote_@$valeur@_quote_@";
         } else
            $valeur="@_quote_@0000-00-00@_quote_@";
         break;
      case "Et.":
         //Etablissement : r&eacute;cup&eacute;rer le bon r&eacute;f&eacute;rentiel en fonction du d&eacute;partement
         if(trim($dept)!="") {
            $tmp=explode("-",$dept);
            //TODO quand on aura les bons departements
            //$referentiel="Et-".ucfirst(strtolower($tmp[0]))."/".ucfirst(strtolower($tmp[1]));
            $referentiel='etablissement';
            if(!$aErreurs[$referentiel][$valeur]) {
               $isInRef=isInReferentiel($valeur,$referentiel,true);
               $isFound=$isInRef["isFound"];
               $valeurs_possibles=$isInRef["valeursPossibles"];
               if(!$isFound) {
                  if(trim($valeurs_possibles)!="") {
                     $fatal .= KO." - Candidat $num_candidat, [<b>$valeur</b>] n'est pas un &eacute;tablissement possible du d&eacute;partement <b>$dept</b><br/>\n";
                     $fatal.= "Note : valeurs possibles dans <b>$referentiel</b> : <ul>$valeurs_possibles</ul>";
                     $id_dept=$tab_referentiel['departement'][$dept];
                     if(isAdmin()) $fatal.= "<A HREF='".generer_url_ecrire("odb_ref")."&table=ETA|$id_dept|$dept|odb_ref_".$referentiel."'>>> Modifiez le r&eacute;f&eacute;rentiel <b>$referentiel</b></A><br/>\n";
                  } else {
                     $fatal.= KO." - Candidat $num_candidat ($referentiel), impossible de v&eacute;rifier un &eacute;tablissement si le d&eacute;partement $dept est inconnu<br/>\n";
                  }
               } else {
                  $txt_debug.=OK." <b>$valeur</b> dans $referentiel<br/>";
                  $valeur=$isFound;
               }
            }
            else {
               $fatal.=KO." - Candidat $num_candidat<br/>\n"
                     ."Erreur d&eacute;j&agrave; rencontr&eacute;e : ".$referentiel
                     ."[<A HREF='#".$referentiel.'_'.$valeur
                     ."'>$valeur</A>]\n"
                     ;
            }
         } else {
            if(trim($valeur)=="")
               //$txt=KO. " - Impossible d'identifier le d&eacute;partement pour le <b>candidat $num_candidat</b>, veuillez retoucher le fichier d'import";
               $fatal.=KO. " - Impossible d'identifier le d&eacute;partement pour le <b>candidat $num_candidat</b>, veuillez retoucher le fichier d'import";
            else
               $fatal .= KO. " - Impossible de trouver dans quel r&eacute;f&eacute;rentiel se trouve l'etablissement <b>$valeur</b> pour le d&eacute;partement $dept - <b>candidat $num_candidat</b>";
         }
         break;
      case "VraiFaux":
         //vraiFaux : On remplace par boolean 0/1
         if($valeur=="VRAI" || $valeur==1) $valeur="1"; //compatibilit&eacute;
         else $valeur="0"; // 0 par d&eacute;faut
         break;
      default:
         if(substr_count($type,"ref")>0) {
            if($valeur!="") {
               //c'est un r&eacute;f&eacute;rentiel : verifier qu'il est bien en table
               $referentiel=strtolower(substr($type,3)); // on recupere le nom du referentiel
               $isInRef=isInReferentiel($valeur,$referentiel,true);
               //echo "<hr>$type=$valeur<pre>";print_r($isInRef);echo "</pre>\n<br>";
               $isFound=$isInRef["isFound"];
               $valeurs_possibles=$isInRef["valeursPossibles"];
               if(!$isFound) {
                  $fatal .= KO." - Candidat $num_candidat, [<b>$valeur</b>] n'est pas une valeur possible du r&eacute;f&eacute;rentiel <b>$referentiel</b><br/>\n";
                  $fatal.= "Note : valeurs possibles dans <b>$referentiel</b> : <ul>$valeurs_possibles</ul>";
                  $fatal.= "<A HREF='".generer_url_ecrire("odb_ref")."&table=odb_ref_".$referentiel."&step2=manuel'>>> Modifiez le r&eacute;f&eacute;rentiel <b>$referentiel</b></A><br/>\n";
               } else {
                  $txt_debug.=OK." <b>$valeur</b> dans $referentiel<br/>";
                  $valeur=$isFound;
               }
            } else $valeur="@_quote_@$valeur@_quote_@";
         } else {
         // on ne sait pas : ne rien faire et avertir
            $txt_debug .= KO." - Colonne [$champ] inconnue dans <b>$table</b> : ".$row1[$c]." (type $type / valeur $valeur)<br/>\n";
         }
         break;
   } switch ($champ) {
      ////////////////////////////// Règles de gestion en fonction du champ ///////////////////////
      case "Num_Saisie":
      case "Serie":
      case "Nom":
      case "Prenoms":
      case "Sexe":
      case "Lieu_Naiss":
      case "Nationalite":
      case "Ville":
      //case "Departement":
      case "Etablissement":
         //Champs obligatoires
         if(strlen($valeur)==0)
            $fatal.=KO." - Le champ <b>$champ</b> est obligatoire (Candidat $num_candidat)<br/>\n";
         break;
      case "Departement"://TODO BOUCHON A SUPPRIMER
         if(!is_numeric($valeur))
            $valeur='0';
      case "Annee":
         //Si l'ann&eacute;e est vide on met l'ann&eacute;e pass&eacute;e en param&egrave;tre
         if(strlen($valeur)==0)
            $valeur=$annee;
         break;
      case "Ne_le":
      /* TODO à sortir
         //Un seul des 3 champs rempli
         //echo KO.KO.KO."$c ".$data[$c]." $champ $type $valeur";
         $isNeLe=false;$isNeEn=false;$isNeVers=false;
         if(strlen($data[$c])>0) $isNeLe=true;
         if(strlen($data[$c+1])>0) $isNeEn=true;
         if(strlen($data[$c+2])>0) $isNeVers=true;
         if(($isNeLe && $isNeEn) || ($isNeLe && $isNeVers) || ($isNeEn && $isNeVers))
            $fatal.=KO." - Un seul des champs 'N&eacute; le', 'N&eacute; en', 'N&eacute; vers' doit être rempli (Candidat $num_candidat)<br/>\n";
         break;
      case "LV2":
      case "EF2":
      /* TODO à sortir
         //Si ce champ est rempli alors celui d'avant aussi
         if(strlen($valeur)>0)
            if (strlen($data[$c-1])==0) $fatal.=KO." - Si $champ est rempli, alors le champ pr&eacute;c&eacute;dent doit l'être aussi<br/>\n";
         break;
      */
   }
   if(strlen($fatal)>0)
      $fatal="<hr height=1/>\n$fatal";
   $retour["valeur"]=$valeur;
   $retour["fatal"]=$fatal;
   $retour["txt_debug"]=$txt_debug;
   return $retour;
}
?>
