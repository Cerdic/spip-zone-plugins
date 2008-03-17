<?php
session_start();
include_spip('inc/presentation');
include_spip('inc/charsets');
include_spip('inc/acces');
define('DIR_ODB_COMMUN',_DIR_PLUGINS."odb/odb_commun/");
include_once(DIR_ODB_COMMUN.'inc-html.php');
include_once(DIR_ODB_COMMUN."inc-referentiel.php");
include_once(DIR_ODB_COMMUN.'inc-odb.php');

//include_once(DIR_ODB_COMMUN."inc-regles_gestion.php");

define('MIN_REFERENTIEL',2002); // ann&eacute;e minimale dans le r&eacute;f&eacute;rentiel
define('OK',"<SPAN style='color:#3C3;font-weight:bold;'>[OK]</SPAN>");
define('KO',"<SPAN style='color:#C33;font-weight:bold;'>[KO]</SPAN>");
define('ODB_PREFIXE','odb_ref_'); // prefixe tables referentiel odb
define('LIGNES_TITRE',20); // nb lignes avant rappel titre
define('INPUT_MAX_SIZE',40);
define('ODB_BIO_OPERATEUR','Operateur de saisie');

global $debug;
$debug=false;

/**
 * exécuté automatiquement par le plugin au chargement de la page ?exec=odb_ref
 * 
 * @author Cedric PROTIERE
 */
function exec_odb_ref() {
   global $connect_statut, $connect_toutes_rubriques,$debug;
   debut_page(_T('R&eacute;f&eacute;rentiel ODB'), "", "");
   echo "<br /><br />";
   gros_titre(_T('Office Du Baccalaur&eacute;at'));

   debut_cadre_relief(  "", false, "", $titre = _T('Gestion du r&eacute;f&eacute;rentiel ODB'));
   debut_boite_info();
   echo '<br>';
   if($debug) {
      echo "_POST<pre>";print_r($_POST);echo "</pre>\n";
   }
   $REFERER=$_SERVER['HTTP_REFERER'];
   $REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
   /* envoi de mail (pour rappel)
      $texte="connexion $REMOTE_ADDR - Site appelant - $REFERER \n";
      $headers= "From: cedric.protiere@auf.org";
      $to="cedric.protiere@auf.org";
      $envoi = @mail($to, "Connexion à votre site - Page priv&eacute;e, "$texte", $headers,"-f cedric.protiere@auf.org");
      echo "Curieux! Un mail vient d'être envoy&eacute; à l'administrateur";
   */

   echo "<IMG SRC='"._DIR_PLUGIN_ODB_REF."/img_pack/logo_odb.png' alt='Office du bac' ALIGN='absmiddle'><br><br>\n";
   
   isAutorise(array('Admin'));

   $annuler=$_REQUEST["annuler"];
   $annee=$_REQUEST["annee"];
   if($annee=='') $annee=date("Y");
   $table=$_REQUEST["table"];
   $step2=$_REQUEST["step2"];
   $step3=$_REQUEST["step3"];
   if(strlen($step3)>0) $table=$step3;

   //$libelle=$_POST["libelle"];
   if(substr_count($table,'ETA|')>0) {
      $isETA=true;
      $tab_table=explode('|',$table);
      $id_departement=$tab_table[1];
      $lib_departement=$tab_table[2];
      $table_step2=$table;
      $table=$tab_table[3];
      $libelle="&Eacute;tablissements de $lib_departement";
   } else {
      $isETA=false;
      $libelle=ucwords(substr($table,strlen(ODB_PREFIXE)));
      switch($libelle) {
         case 'Ef':
            $libelle='&Eacute;preuves facultatives';
            break;
         case 'Lv':
            $libelle='Langues vivantes';
            break;
      }
      $table_step2=$table;
   }
   $champ = substr($table,strlen(ODB_PREFIXE));

   
   if(!$isETA && !in_array($champ,array('salle','examen','operateur','ecole'))) 
      $afficherRaccourcis=true;
   else $afficherRaccourcis=false;
   if($afficherRaccourcis) {
   	debut_gauche();
   		odb_raccourcis('odb_ref');
		debut_droite();
   }
   if(strlen($step2)>0) {
      ///////////////////////////////////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////// step2 : modif du referentiel choisi
		$colspan_textarea=1;
      if($afficherRaccourcis)
         debut_cadre_relief("", false, "", $titre = _T("Modification du r&eacute;f&eacute;rentiel [$libelle]"));
      else gros_titre("Modification du r&eacute;f&eacute;rentiel [$libelle]");
      // Après choix : affichage du referentiel pour consultation / modification / ajout
      echo "<FORM NAME='form_odb_ref' id='form_odb_ref' ACTION='".generer_url_ecrire('odb_ref') ."' class='forml spip_xx-small' METHOD='POST'>\n";
      echo "Videz un champ pour supprimer une valeur<br/>\n";
      echo "<TABLE class='spip' style='background-color:#eee;'>\n";
      echo "<INPUT type='hidden' name='exec' value='odb_ref'/>\n";
      echo "<INPUT type='hidden' name='step3' value='$table_step2'/>\n";
      echo "<INPUT type='hidden' name='table' value='$table'/>\n";
      echo "<INPUT type='hidden' name='annee' value='$annee'/>\n";
      echo "<INPUT type='hidden' name='libelle' value='$libelle'/>\n";

      if($isETA) {
         $colspan="><small>Action</small></TD><TD colspan=2 ";
         $sql="SELECT id, etablissement etablissement, id_ville, id_centre, annee_centre, id_departement from odb_ref_etablissement WHERE id_departement=$id_departement ORDER BY etablissement";
         $result=odb_query($sql,__FILE__,__LINE__);
         $cpt=0;
         $maxlength=mysql_field_len($result, 1);
         $size=min($maxlength,35);
         while($row=mysql_fetch_array($result)) {
            unset($selectVille,$selectCentre,$selectDepartement,$id_centre);
            if($cpt % LIGNES_TITRE == 0)
               echo "<tr><th>$lib_departement</th><th>&Eacute;tablissement</th><th>Ville</th><th>D&eacute;partement</th><th>Centre le + proche</th><th>Si centre : depuis quand ?</th></tr>\n";
            $id=$row["id"];
            $id_ville=trim($row['id_ville']);
            $selectVille=formOptionsRefInSelect('ville',$id_ville,'Ville',$id_departement);
            $selectVille="<SELECT class='fondo' NAME='set|id_ville|$id'>$selectVille</SELECT>\n";
            $id_centre=trim($row['id_centre']);
            $selectCentre=formOptionsRefInSelect('centre',$id_centre,'Centre',$id_departement);
            $selectCentre="<SELECT class='fondo' NAME='set|id_centre|$id'>$selectCentre</SELECT>\n";
            $id_departement=trim($row['id_departement']);
            $selectDepartement=formOptionsRefInSelect('departement',$id_departement);
            $selectDepartement="<SELECT class='fondo' NAME='set|id_departement|$id'>$selectDepartement</SELECT>\n";
            $selectAnneeCentre="<OPTION NAME='' VALUE='0'>-=[Ann&eacute;e]=-</OPTION>\n".formSelectAnnee($row['annee_centre']);
            $selectAnneeCentre="<SELECT class='fondo' NAME='set|annee_centre|$id'>$selectAnneeCentre</SELECT>\n";
            if($id_ville==0 || $id_centre==0 || $id_departement==0) $couleur='red';
            else $couleur='gray';
            echo formInputTextTR("<font color='$couleur'>[<b>$id</b>]</font>",$table."|".$id,trim($row[$champ]),"class='fondo' size='$size' maxlength='$maxlength' onfocus='this.select();' ","<td>$selectVille</td><td>$selectDepartement</td><td>$selectCentre</td><td>$selectAnneeCentre</td>");
            $cpt++;
         }
      } elseif ($champ=='serie') {
         $colspan="><small>Action</small></TD><TD colspan=1 ";
         $colspan_textarea=2;
         $sql="SELECT id, serie, libelle from odb_ref_serie ORDER BY serie";
         $result=odb_query($sql,__FILE__,__LINE__);
         $cpt=0;
         $maxlength=mysql_field_len($result, 1);
         $size=min($maxlength,35);
         while($row=mysql_fetch_array($result)) {
            if($cpt % LIGNES_TITRE == 0)
               echo "<tr><th>#".ucwords($champ)."</th><th>".ucwords($champ)."</th><th>Libell&eacute;</th></tr>\n";
            $id=$row["id"];
            $serie=trim($row['serie']);
            $lib=trim($row['libelle']);
            if($lib=='') $couleur='red';
            else $couleur='gray';
            echo formInputTextTR("<font color='$couleur'>[<b>$id</b>]</font>",$table."|".$id,trim($row[$champ]),"class='fondo' size='$size' maxlength='$maxlength' onfocus='this.select();' ",
            "<td><input class='fondo' type='text' onfocus='this.select();' maxlength='36' size='30' value='$lib' name='set|libelle|$id'/></td>");
            $cpt++;
         }
      } elseif ($champ=='ville') {
         $colspan="><small>Action</small></TD><TD colspan=1 ";
         $sql="SELECT id, ville, id_departement from odb_ref_ville ORDER BY ville";
         $result=odb_query($sql,__FILE__,__LINE__);
         $cpt=0;
         $maxlength=mysql_field_len($result, 1);
         $size=min($maxlength,35);
         while($row=mysql_fetch_array($result)) {
            if($cpt % LIGNES_TITRE == 0)
               echo "<tr><th>#".ucwords($champ)."</th><th>".ucwords($champ)."</th><th>D&eacute;partement</th></tr>\n";
            $id=$row["id"];
            $id_ville=trim($row['id_ville']);
            $id_departement=trim($row['id_departement']);
            $selectDepartement=formOptionsRefInSelect('departement',$id_departement);
            $selectDepartement="<SELECT class='fondo' NAME='set|id_departement|$id'>$selectDepartement</SELECT>\n";
            if($id_departement==0) $couleur='red';
            else $couleur='gray';
            echo formInputTextTR("<font color='$couleur'>[<b>$id</b>]</font>",$table."|".$id,trim($row[$champ]),"class='fondo' size='$size' maxlength='$maxlength' onfocus='this.select();' ","<td>$selectDepartement</td>");
            $cpt++;
         }
      } elseif ($champ=='ecole') {
         $colspan="><small>Action</small></TD><TD colspan=3 ";
         $sql="SELECT ecole.id, ecole, commentaire, id_serie, id_matiere1, id_matiere2, id_matiere3, id_matiere4, coeff1, coeff2, coeff3, coeff4, serie\n".
         " from odb_ref_ecole ecole\n".
         " left join odb_ref_serie serie on ecole.id_serie=serie.id\n".
         " ORDER BY ecole, serie";
         //echo $sql;
         $result=odb_query($sql,__FILE__,__LINE__);
         $cpt=0;
         $maxlength=mysql_field_len($result, 1);
         $size=min($maxlength,35);
         $trTitre="<tr><th>#".ucwords($champ)."</th><th>".ucwords($champ)."</th><th>S&eacute;rie</th><th>Mati&egrave;re</th><th>Coeff</th></tr>\n";
         //echo $trTitre;
         while($row=mysql_fetch_array($result)) {
            foreach(array('id','ecole','commentaire','id_serie','serie','id_matiere1','id_matiere2','id_matiere3','id_matiere4','coeff1','coeff2','coeff3','coeff4') as $col)
            	$$col=$row[$col];
            $textCommentaire="<TEXTAREA COLS=80 ROWS=2 class='fondo' name='set|commentaire|$id'>$commentaire</TEXTAREA>\n";
            echo "<tr><td colspan=7><hr size=1/></td></tr>\n";
            if($ecole!=$old_ecole) {
            	echo "<tr><th colspan=2 style='color:#f00;'>$ecole</th><td colspan=3>$textCommentaire</td></tr>\n";
            	echo $trTitre;
            }
            $selectSerie="<SELECT class='fondo' NAME='set|id_serie|$id'>".formOptionsRefInSelect('serie',$id_serie)."</SELECT>\n";
            $selectMatieres='';
            $inputCoeffs='';
            for ($i=1;$i<5;$i++) {
            	$inputCoeffs.="<INPUT CLASS='fondo' NAME='set|coeff$i|$id' VALUE='".${"coeff$i"}."' SIZE=2 MAXLENGTH=2/><br/>\n";
            	if($id_serie==0) 
            		$selectMatieres.="<input size=40 value='Veuillez commencer par choisir la s&eacute;rie' disabled class='fondo'><br/>\n";
            	else $selectMatieres.=formSelectQuery("Mati&egrave;re $i","set|id_matiere$i|$id",
            			"SELECT DISTINCT id_matiere, matiere\n from odb_ref_examen exa, odb_ref_matiere mat\n".
            			" WHERE exa.id_serie=$id_serie and exa.id_matiere=mat.id and exa.annee=$annee order by matiere",
            			'matiere', ${"id_matiere$i"}, "class='fondo'", 'id_matiere'
            		)."<br/>\n";
            }
            if($id_serie==0) $couleur='red';
            else $couleur='gray';
            echo formInputTextTR("<font color='$couleur'>[<b>$id</b>]</font>",$table."|".$id,trim($row[$champ]),"class='fondo' size='$size' maxlength='$maxlength' onfocus='this.select();' ","<td>$selectSerie</td><td>$selectMatieres</td><td>$inputCoeffs</td>");
            $cpt++;
            $old_ecole=$ecole;
         }
      } elseif ($champ=='operateur') {
         $colspan="><small>Action</small></TD><TD colspan=3 ";
         $sql = 'SELECT ope . id , operateur , mot_passe, id_deliberation , del . deliberation , jury1 , jury2 , jury3 , jury4 '
        . ' from odb_ref_operateur ope '
        . ' left join odb_ref_deliberation del on ( ope . id_deliberation = del . id ) '
        . " where ope.annee=$annee"
        . ' ORDER BY deliberation , operateur';
         $result=odb_query($sql,__FILE__,__LINE__);
         $cpt=0;
         //echo $sql;
         $maxlength=mysql_field_len($result, 1);
         $maxlengthMotPasse=mysql_field_len($result,2);
         $size=min($maxlength,35);
         $colonnes=array('id','operateur','id_deliberation','mot_passe','deliberation','jury1','jury2','jury3','jury4');
         $nb_jury=mysql_num_rows($result);
         while($row=mysql_fetch_array($result)) {
            foreach($colonnes as $col)
            $$col=trim($row[$col]);
            if($deliberation!=$oldDeliberation)
            echo "<tr><th>#".ucwords($champ)."</th><th>Op&eacute;rateur<br/>($deliberation)</th><th>Mot de<br/>passe</th><th>Centre de<br/>d&eacute;lib&eacute;ration</th><th>Jury 1</th><th>Jury 2</th><th>Jury 3</th><th>Jury 4</th></tr>\n";
            $selectDeliberation=formOptionsRefInSelect('deliberation',$id_deliberation);
            if($id_deliberation>0) {
        			// lorsqu'un centre de deliberation est defini,
        			// on ne propose que les jurys qui ne sont pas d'un autre centre de deliberation
        			$sql = 'SELECT DISTINCT jury FROM odb_repartition rep '
				        . " WHERE annee=$annee AND jury >= IFNULL( "
				        . " (SELECT min( jury1 ) FROM odb_ref_operateur WHERE annee=$annee AND id_deliberation = $id_deliberation and jury1 is not null )"
			         	. ' ,0) '
				        . ' ORDER BY jury';
            } else
            	$sql="SELECT distinct jury from odb_repartition where annee=$annee order by jury";
            //echo "<br/>$sql";
            for ($i=1;$i<=4;$i++) {
            	${"selectJury$i"}=formSelectQuery("Jury $i", "set|jury$i|$id", $sql, 'jury', ${"jury$i"});
            }
            $selectDeliberation="<SELECT class='fondo' NAME='set|id_deliberation|$id'>$selectDeliberation</SELECT>\n";
            $inputMotPasse="<INPUT NAME='set|mot_passe|$id' value='$mot_passe' size=$maxlengthMotPasse maxlength=$maxlengthMotPasse onClick=\"if(this.value=='') alert('Veuillez noter que SIOU generera automatiquement\\nun mot de passe a la creation des auteurs SPIP\\n\\n(Voir lien ci-dessous)');\"/>\n";
            if((int)$id_deliberation==0) $couleur='red';
            else $couleur='gray';
            echo formInputTextTR("<font color='$couleur'>[<b>$id</b>]</font>",$table."|".$id,$operateur,"class='fondo' size='$size' maxlength='$maxlength' onfocus='this.select();' "
            ,"<td>$inputMotPasse</td><td>$selectDeliberation</td><td>$selectJury1</td><td>$selectJury2</td><td>$selectJury3</td><td>$selectJury4</td>");
            $cpt++;
            $oldDeliberation=$deliberation;
         }
         $tmpStr="Cr&eacute;er les auteurs SPIP correspondants aux op&eacute;rateurs $annee ci-dessus";
         $imgCreerAuteurs='<img src="'._DIR_PLUGIN_ODB_REF.'img_pack/auteurs.png" alt="'.$tmpStr.'" align="absmiddle"/>';
         echo "<tr><td colspan=8 align='center'><center>"
			. "<A HREF='".generer_url_ecrire('odb_ref')."&actionSession=creer_operateurs' onClick=\"return confirm('Cette action va supprimer les auteurs actuels et generer un nouveau mot de passe pour les operateurs ayant un mot de passe vide\\n\\nEtes-vous sur(e) de vouloir continuer ?')\">$imgCreerAuteurs <b>$tmpStr</b></A>"
			. "</center></td></tr>\n"
			;
			//generation des 'alea_actuel' et 'alea_futur' pour les mots de passe ($pass = md5($alea_actuel.$new_pass); : editer_auteur[162])
			for ($i=0;$i<$nb_jury;$i++) {
				$tAlea[$i]['actuel']=creer_uniqid();
				$tAlea[$i]['futur']=creer_uniqid();
			}
			$tAlphabet=range('a','z');
			$tVoyelles=array('a','e','i','o','u','y');
			$tConsonnes=array_diff($tAlphabet,$tVoyelles);
			unset($_SESSION['creer_operateurs']);
			$_SESSION['creer_operateurs']['sql'][]='DELETE FROM spip_auteurs WHERE bio = \''.ODB_BIO_OPERATEUR.'\'';
			$sqlPass="UPDATE odb_ref_operateur SET mot_passe=concat(ELT(1+floor(RAND()*".(count($tConsonnes)-1)."),'".implode("','",$tConsonnes)."'),ELT(1+floor(RAND()*".(count($tVoyelles)-1)."),'".implode("','",$tVoyelles)."'),ELT(1+floor(RAND()*".(count($tConsonnes)-1)."),'".implode("','",$tConsonnes)."'),ELT(1+floor(RAND()*".(count($tVoyelles)-1)."),'".implode("','",$tVoyelles)."')) WHERE mot_passe=''";
			//echo $sqlPass;
			$_SESSION['creer_operateurs']['sql'][]=$sqlPass;
			foreach (array('a','b') as $ope) {
				$_SESSION['creer_operateurs']['sql'][]='INSERT INTO spip_auteurs(id_auteur, nom, statut, bio, nom_site, email, maj, login, pass, alea_actuel, alea_futur, source, lang, idx ) ('
	        . ' select ope.id + (select max(id_auteur) from spip_auteurs), '
	        . ' CONCAT(operateur,\' '.strtoupper($ope).'\'), \'1comite\' statut, \'Operateur de saisie\' bio, concat( deliberation, \' - centre \', id_deliberation ) deliberation, '
	        . ' concat(\'notes@\', ifnull( jury1, \'\' ) , \'|\', ifnull( jury2, \'\' ) , \'|\', ifnull( jury3, \'\' ) , \'|\', ifnull( jury4, \'\' ) ) jurys, '
	        . ' now( ) , CONCAT(LCASE(operateur),\''.$ope.'\') login, md5(mot_passe) pass, \'\' alea_actuel, \'\'alea_futur, \'siou\' source, \'fr\' lang, \'oui\' idx '
	        . ' FROM odb_ref_operateur ope, odb_ref_deliberation delib '
	        . " WHERE delib.id = ope.id_deliberation and ope.annee=$annee ORDER BY operateur)";
			}
			$_SESSION['creer_operateurs']['msgSql'][]='anciens auteurs SPIP op&eacute;rateurs de saisie supprim&eacute;s avec succ&egrave;s';
			$_SESSION['creer_operateurs']['msgSql'][]='mots de passe des op&eacute;rateurs g&eacute;n&eacute;r&eacute;s avec succ&egrave;s';
			$_SESSION['creer_operateurs']['msgSql'][]='<b>auteurs SPIP [A] cr&eacute;&eacute;s avec succ&egrave;s</b>';
			$_SESSION['creer_operateurs']['msgSql'][]='<b>auteurs SPIP [B] cr&eacute;&eacute;s avec succ&egrave;s</b>';
			$_SESSION['creer_operateurs']['retour']['msg']='Retour au r&eacute;f&eacute;rentiel <b>[Op&eacute;rateur]</b>';
			$_SESSION['creer_operateurs']['retour']['url']=generer_url_ecrire('odb_ref').'&table=odb_ref_operateur&step2=manuel';
			//$pass = md5($alea_actuel.$new_pass);
      } elseif ($champ=='salle') {
         $colspan="><small>Action</small></TD><TD colspan=1 ";
         $sql="SELECT salle.id, salle.annee, salle, id_etablissement, nb_salles, capacite, departement, eta.id_departement\n"
         	. " FROM odb_ref_salle salle, odb_ref_etablissement eta, odb_ref_departement dept\n"
         	. " WHERE salle.id_etablissement = eta.id\n"
         	. " AND eta.id_departement = dept.id AND annee=$annee\n"
         	. " ORDER BY departement, id_etablissement, salle"
         	;
         $result=odb_query($sql,__FILE__,__LINE__);
         if(mysql_num_rows($result)==0) {
         	echo "<tr><td>"
         		. "Aucun r&eacute;sultat en $annee<br/>"
         		. "<A HREF='".generer_url_ecrire('odb_ref')."&actionSession=majCapaciteSalles&annee=$annee'>R&eacute;cup&eacute;rer les capacit&eacute;s de salles ".($annee-1)."</A>"
         		. "</td></tr>"
         		;
         	echo "</table>\n";
         	unset($_SESSION['majCapaciteSalles']);
				$_SESSION['majCapaciteSalles']['msgSql'][]="Capacit&eacute; d'accueil $annee mise &agrave; jour avec succ&egrave;s";
         	$_SESSION['majCapaciteSalles']['sql']['']="INSERT INTO odb_ref_salle(id, annee, salle, id_etablissement, nb_salles, capacite)\n (SELECT id, $annee,salle,id_etablissement,nb_salles,capacite\n\t FROM odb_ref_salle WHERE annee=".($annee-1).')';
				$_SESSION['majCapaciteSalles']['retour']['msg']='Retour au r&eacute;f&eacute;rentiel <b>[Salles]</b>';
				$_SESSION['majCapaciteSalles']['retour']['url']=generer_url_ecrire('odb_ref').'&table=odb_ref_salle&step2=manuel';
         	exit;
			}
         $cpt=0;
         $maxlength=mysql_field_len($result, 1);
         $size=min($maxlength,35);
         while($row=mysql_fetch_array($result)) {
            $departement=$row['departement'];
            if($departement!=$oldDepartement)
               echo "<tr><th>$departement</th><th>Nom salle</th><th>Centre de composition</th><th>Nb salles</th><th>Capacit&eacute;</th></tr>\n";
            $id=$row['id'];
            $id_etablissement=trim($row['id_etablissement']);
            $id_departement=$row['id_departement'];
            $selectCentre=formOptionsRefInSelect('centre',$id_etablissement,'Centre',$id_departement);
            $selectCentre="<SELECT class='fondo' NAME='set|id_etablissement|$id'>$selectCentre</SELECT>\n";
            $nb_salles=$row['nb_salles'];
            $inputNbSalles="<INPUT type=text size=3 maxlength=3 name='set|nb_salles|$id' VALUE='$nb_salles'/>";
            $capacite=$row['capacite'];
            $tab_capacite=array(0,20,25,30,35,40,45,50);
            $selectCapacite=array();
            foreach($tab_capacite as $cap)
               $selectCapacite.=formOptionsInSelect("$cap places",$cap,$capacite);
            $selectCapacite="<SELECT class='fondo' NAME='set|capacite|$id'>\n$selectCapacite</SELECT>\n";
            if($id_etablissement==0 || $nb_salles==0 || $capacite==0) $couleur='red';
            else $couleur='gray';
            echo formInputTextTR(
               "<font color='$couleur'>[<b>$id</b>]</font>",$table."|".$id,trim($row[$champ]),
               "class='fondo' size='$size' maxlength='$maxlength' onfocus='this.select();' ",
               "<td>$selectCentre</td><td>$inputNbSalles</td><td>$selectCapacite</td>"
             );
            $cpt++;
            $oldDepartement=$departement;
         }
         $sql="SELECT id, `salle` , `id_etablissement` , `nb_salles` , `capacite` FROM `odb_ref_salle` WHERE id_etablissement=0";
         $result=odb_query($sql,__FILE__,__LINE__);
         if(mysql_num_rows($result)>0) {
            echo "<tr><th>&Agrave; d&eacute;finir</th><th>Nom salle</th><th>Centre de composition</th><th>Nb salles</th><th>Capacit&eacute;</th></tr>\n";
            while($row=mysql_fetch_array($result)) {
            $id=$row['id'];
            $id_etablissement=trim($row['id_etablissement']);
            $selectCentre=formOptionsRefInSelect('centres',$id_etablissement,'Centre',$id_departement);
            $selectCentre="<SELECT class='fondo' NAME='set|id_etablissement|$id'>$selectCentre</SELECT>\n";
            $nb_salles=$row['nb_salles'];
            $inputNbSalles="<INPUT type=text size=3 maxlength=3 name='set|nb_salles|$id' VALUE='$nb_salles'/>";
            $capacite=$row['capacite'];
            $tab_capacite=array(0,20,25,30,35,40,45,50);
            $selectCapacite=array();
            foreach($tab_capacite as $cap)
               $selectCapacite.=formOptionsInSelect("$cap places",$cap,$capacite);
            $selectCapacite="<SELECT class='fondo' NAME='set|capacite|$id'>\n$selectCapacite</SELECT>\n";
            echo formInputTextTR(
               "<font color='red'>[<b>$id</b>]</font>",$table."|".$id,trim($row[$champ]),
               "class='fondo' size='$size' maxlength='$maxlength' onfocus='this.select();' ",
               "<td>$selectCentre</td><td>$inputNbSalles</td><td>$selectCapacite</td>"
             );
            }
         }
      } elseif ($champ=='examen') {
         $colspan="><small>Action</small></TD><TD colspan=2 ";
         $sql="SELECT id, examen, type, id_serie, id_matiere, duree, coeff\n from odb_ref_$champ\n WHERE annee=$annee\n ORDER BY id_serie, examen";
         $result=odb_query($sql,__FILE__,__LINE__);
         if(mysql_num_rows($result)==0) {
         	echo "<tr><td>"
         		. "Aucun examen enregistr&eacute; en $annee<br/>"
         		. "<A HREF='".generer_url_ecrire('odb_ref')."&actionSession=majExamen&annee=$annee'>R&eacute;cup&eacute;rer le calendrier des &eacute;preuves ".($annee-1)."</A>"
         		. "</td></tr>"
         		;
         	echo "</table>\n";
         	unset($_SESSION['majExamen']);
				$_SESSION['majExamen']['msgSql'][]="Calendrier <b>$annee</b> mis &agrave; jour avec succ&egrave;s";
         	$_SESSION['majExamen']['sql']['']="INSERT INTO odb_ref_examen(id, annee, id_serie, id_matiere, examen, type, duree, coeff)\n (SELECT id, $annee, id_serie, id_matiere, adddate(examen,interval 1 year), type, duree, coeff\n\t FROM odb_ref_examen\n WHERE annee=".($annee-1).')';
				$_SESSION['majExamen']['retour']['msg']='Retour au r&eacute;f&eacute;rentiel <b>[Examen]</b>';
				$_SESSION['majExamen']['retour']['url']=generer_url_ecrire('odb_ref').'&table=odb_ref_examen&step2=manuel';
         	exit;
			}
         $cpt=0;
         $maxlength=mysql_field_len($result, 1);
         $size=min($maxlength,35);
         while($row=mysql_fetch_array($result)) {
            if($cpt % LIGNES_TITRE == 0)
               echo "<tr><th>#Exm</th><th>Date/Heure</th><th>S&eacute;rie</th><th>Mati&egrave;re</th><th>Type</th><th>Dur&eacute;e</th><th>Coeff.</th></tr>\n";
            foreach(array('id','examen','type','id_serie','id_matiere','duree','coeff') as $col)
               $$col=$row[$col];
            //echo "examen $examen<br>";
            $selectSerie=formOptionsRefInSelect('serie',$id_serie);
            $selectSerie="<SELECT NAME='set|id_serie|$id' class='fondo'>$selectSerie</SELECT>\n";
            $selectMatiere=formOptionsRefInSelect('matiere',$id_matiere);
            $selectMatiere="<SELECT NAME='set|id_matiere|$id' class='fondo'>$selectMatiere</SELECT>\n";
            $selectType='';
            foreach(array('Pratique','Ecrit','Oral','Divers') as $tmpType) {
            	$selected=$type==$tmpType?'selected':'';
            	$selectType.="<OPTION $selected value='$tmpType'>$tmpType</OPTION>\n";
				}
            $selectType="<SELECT NAME='set|type|$id' class='fondo'>$selectType</SELECT>\n";
            if($examen=='0000-00-00 00:00:00' || $examen=='') {
               $isBadDate=true;
               $examen=$annee.'-06-00 00:00:00';
            } else {
               $isBadDate=false;
            }
            $inputDuree="<input name='set|duree|$id' value='$duree' size=1 maxlength=3 class='fondo'/>h";
            $inputCoeff="<input name='set|coeff|$id' value='$coeff' size=1 maxlength=2 class='fondo'/>";
            if($id_serie==0 || $id_matiere==0 || ($duree=='0' && !$isBadDate)) $couleur='red';
            elseif($isBadDate) $couleur='orange';
            else $couleur='gray';
            echo formInputTextTR(
               "<font color='$couleur'>[<b>$id</b>]</font>",$table."|".$id,trim($$champ),
               "class='fondo' size='$size' maxlength='$maxlength' onfocus='this.select();' ",
               "<td>$selectSerie</td><td>$selectMatiere</td><td>$selectType</td><td>$inputDuree</td><td>$inputCoeff</td>".
         		"<input type='hidden' name='set|annee|$id' value='$annee'/>"
             );
            $cpt++;
         }
      } else {
         $sql="SELECT id, $champ from $table ORDER BY $champ";
         $result=odb_query($sql,__FILE__,__LINE__);
         $cpt=0;
         $maxlength=mysql_field_len($result, 1);
         $size=min($maxlength,INPUT_MAX_SIZE);
         while($row=mysql_fetch_array($result)) {
            if($cpt % LIGNES_TITRE == 0)
               echo "<tr><th>#".ucwords($champ)."</th><th>$libelle</th></th></tr>\n";
            $id=$row["id"];
            if($champ=='matiere') $champ_aff=trim($row[$champ]);
            else $champ_aff=trim(ucwords(strtolower($row[$champ])));
            echo formInputTextTR("<font color='lightgray'>$table</font> [<b>$id</b>]",$table."|".$id,$champ_aff,"class='fondo' size='$size' maxlength='$maxlength' onfocus='this.select();' ");
            $cpt++;
         }
      }
      //echo formInputTextTR("$ref [+]",$ref."_ajout","","class='formo' size='11' onfocus='this.select();'");
      echo formTextAreaTR("Ajout en masse<br/>(une valeur par ligne)",$table."|ajoutMasse","","class='formo' cols='40' rows='7' onfocus='this.select();'","colspan='$colspan_textarea'");
      echo "<INPUT type='hidden' name='cpt' value='$cpt'/>\n";
      echo "<TR>"
         . "<TD $colspan><INPUT TYPE='SUBMIT' NAME='ok' VALUE='Mettre &agrave; jour\n$libelle' class='formo'/></TD>"
         . "<TD><INPUT TYPE='BUTTON' NAME='annuler' VALUE='Annuler' class='formo' onClick=\"window.location='".generer_url_ecrire('odb_ref')."&annuler=$table&step3=$table&table=$table';\"/></TD>"
         . "</TR>\n";
   } else {
      debut_cadre_relief(  "", false, "", $titre = _T('Choix du r&eacute;f&eacute;rentiel'));

      ///////////////////////////////////////////////////////////////////////////////////////////////////////
      //////////////////////// step1/step3 : choix du referentiel à modifier + session + step3 : modification effective (ou annulation)
		if(isset($_REQUEST['actionSession'])) {
			debut_boite_info();
			$actionSession=$_REQUEST['actionSession'];
			echo "<b>".ucfirst(str_replace('_',' ',$actionSession))."</b><br/>\n";
			foreach($_SESSION[$actionSession] as $key=>$val) {
				if($key=='sql') {
					foreach($val as $k=>$sql) {
						odb_query($sql,__FILE__,__LINE__);
						$nb_rows[$k]=mysql_affected_rows();
						//echo "<hr/>$nb_rows[$k] lignes :<br/>$sql ";
					}
				} elseif ($key=='msgSql') {
					foreach($val as $k=>$str)
						echo '<br/>'.OK.' - <b>'.$nb_rows[$k].'</b> '.$str;
				} elseif ($key=='msg') {
					echo implode('<br/>',$val); 
				} elseif ($key=='retour')
					echo "<BR/><A href='".$val['url']."'>".$val['msg']."</A>";
				else die(KO.' - Cl&eacute; <b>$key</b> non pr&eacute;vue');
			}
			unset($_SESSION[$actionSession]);
			fin_boite_info();echo "<br/>\n";
		} elseif(strlen($step3)>0) {
         $champ=substr($table,strlen(ODB_PREFIXE));
         if($champ=='etablissement') {
            $isETA=true;
         } else {
            $isETA=false;
         }

         foreach($_POST as $key => $value) {
               if(substr_count($key,'set|')>0) {
               $tmp=explode("|",$key);
               $identifiant_set=$tmp[1];
               $id_set=$tmp[2];
               if(trim($value)!='' || !is_numeric($value)) {
               	if(!is_numeric($value)) $value="'$value'";
                  $set[$id_set].=", $identifiant_set=$value";
                  if($debug) echo "set[$id_set] : $identifiant_set=$value<br/>\n";
               }
               unset($_POST[$key]);
            }
         }

         debut_boite_info();
         if($annuler!="")
            echo "Op&eacute;ration annul&eacute;e - aucun changement effectu&eacute;\n";
         else {
            $cpt=$_POST["cpt"];
            $cpt_ajoutMasse=0;
            $cpt_supprime=0;
            $sql="";
            foreach($_POST as $key => $value) {
               $sql_masse="";
               $sql="";
               $value=mysql_real_escape_string(unicode2charset(charset2unicode(trim($value))));
               $ref=explode("|",$key);
               if($debug) {
                  echo "<br/>[$key]=$value\t";
                  /* affichage du tableau
                  echo('<pre>');
                  print_r($ref);
                  echo('</pre>');
                  */
                  echo htmlentities($step3);
               }
               if($ref[0]==$table) {
                  $id=$ref[1];
                  if($debug)
                     echo "::$id";
                  if($id=="ajoutMasse") {
                     if($value!="") {
                        $val=explode("\\n",$value);
                        $sql_masse="";
                        foreach($val as $val_masse) {
                           $val_masse=trim(str_replace("\\r","",$val_masse));
                           if($val_masse!="") {
                              if($isETA)
                                 $sql_masse="INSERT INTO $table (id_departement,etablissement) VALUES ($id_departement,'$val_masse');";
                              else
                                 $sql_masse="INSERT INTO $table ($champ) VALUES ('$val_masse');";
                              $result=mysql_query($sql_masse) or $erreur=trim(mysql_errno());
                              if($erreur=="1062") { //Duplicata du champ pour la clef 2
                                 $txt_erreur.="<b><U style='color:#f81;'>/!\</U></b> Le champ '<b>$val_masse</b>' existait d&eacute;jà dans le r&eacute;f&eacute;rentiel <b>$step3</b> - ajout ignor&eacute;<br/>\n";
                              }
                              elseif ($erreur>0) { //autre erreur
                                 die(KO." - erreur ".mysql_errno()." : requête $sql_masse<br/>".__FILE__.' ('.__LINE__.')<br/>'.mysql_error());
                              }
                              else { // ajout OK
                                 $cpt_ajoutMasse++;
                                 $txt_ajoutMasse.="<li>$val_masse</li>\n";
                              }
                           }
                        }
                     } else
                        $sql_masse='';
                  } else {
                     if(trim($value=='')) {
                        $sql="DELETE from $table WHERE id=$id";
                        $cpt_supprime++;
                        $cpt--;// &eacute;tait compt&eacute; comme UPDATE alors que c'est DELETE
                     } else {
                        $sql="UPDATE $table SET $champ='$value' ".$set[$id]." WHERE id=$id ";
                        if(in_array($table,array('odb_ref_operateur','odb_ref_salle','odb_ref_examen')))
                        	$sql.=" and annee=$annee";
                     }
                     $result=odb_query($sql,__FILE__,__LINE__);
                  }
                  if($debug) {
                     echo "<PRE>$sql $sql_masse</PRE>\n";
                  }
               }
            }
            if($cpt_supprime>0) {
               //$js="confirm('Souhaitez-vous r&eacute;ellement supprimer $cpt_supprime valeurs\ndu r&eacute;f&eacute;rentiel $step3 ?')";
               //echo "<script>$js</script>\n";
               echo "Suppression de <b>$cpt_supprime</b> valeurs dans le r&eacute;f&eacute;rentiel <b>$libelle</b> ".OK."<br/>\n";
            }
            echo "Modification des <b>$cpt</b> valeurs du r&eacute;f&eacute;rentiel <b>$libelle</b> ".OK."<br/>\n";
            if(strlen($ajout)>0)
               echo "Ajout de la valeur <b>$ajout</b> dans le r&eacute;f&eacute;rentiel <b>$libelle</b> ".OK."<br/>\n";
            if($cpt_ajoutMasse>0) {
               echo "Ajout en masse de <b>$cpt_ajoutMasse</b> valeurs dans le r&eacute;f&eacute;rentiel <b>$libelle</b> ".OK."<br/>\n";
               echo "<ul>$txt_ajoutMasse</ul>\n";
            }
				switch($table) {
					case 'odb_ref_operateur':
					case 'odb_ref_salle':
					case 'odb_ref_examen':
						// pour les referentiels qui dependent de l'annee
						$sql="UPDATE $table SET annee=$annee WHERE annee=0";
						odb_query($sql,__FILE__,__LINE__);
						$nb=mysql_affected_rows();
						if($nb>0)
							echo "<b>$nb</b> valeurs du r&eacute;f&eacute;rentiel <b>$libelle</b> pass&eacute;es en <b>$annee</b> - ".OK."<br/>\n";
				}
            echo $txt_erreur;
         }
         echo "<br/>Retourner &agrave; la gestion du r&eacute;f&eacute;rentiel <A HREF='".generer_url_ecrire('odb_ref')."&table=$step3&step2=manuel&annee=$annee'>$libelle</b></A>\n";
         fin_boite_info();
         echo "<br/>\n";
      }

      echo "<FORM NAME='form_odb_ref' id='form_odb_ref' ACTION='".generer_url_ecrire('odb_ref')."'"
      	. " class='forml spip_xx-small' METHOD='POST'"
      	. " onSubmit=\"if(this.table.value=='') {alert('Veuillez choisir un referentiel dans la liste');this.table.focus();return false;}\">\n"
      	;
      echo "<TABLE>\n";
      echo "<INPUT type='hidden' name='step2' value='step2'/>\n";
      /* année
      echo formSelectTR1("<b>Ann&eacute;e du r&eacute;f&eacute;rentiel</b>","annee","onChange=\"document.forms['form_odb_ref'].ref.value='';\"");
      echo formSelectAnnee($annee);
      echo formSelectTR2();
      */
      echo formSelectTR1("Veuillez choisir un r&eacute;f&eacute;rentiel","table","class='fondo' onChange=\"document.forms['form_odb_ref'].submit();\"");
      $base=getBddConf('bdd');
      //echo "base $base";
      $sql = "SHOW TABLES FROM $base LIKE '".ODB_PREFIXE."%'";
      $result=odb_query($sql,__FILE__,__LINE__);
      ;
      while($row=mysql_fetch_array($result)) {
         $table=$row[0];
         $libelle=ucwords(substr($table,strlen(ODB_PREFIXE)));
         switch(strtolower($libelle)) {
            case 'etablissement':
               $tab_ref=getReferentiel('departement');
               //echo "tab_ref<pre>";print_r($tab_ref);echo "</pre>\n";
               $sql2="SELECT DISTINCT(id_departement) from $table";
               $result2=odb_query($sql2,__FILE__,__LINE__);
               ;
               while($row2=mysql_fetch_array($result2)) {
                  $id_departement = $row2['id_departement'];
                  if($id_departement==0) {
                     $cpt_pb_dept++;
                     $lib_departement="&lt;!&gt; Sans d&eacute;partement &lt;!&gt;";
                  } else {
                     $lib_departement=ucwords(strtolower(str_replace('-',' / ',$tab_ref[$id_departement])));
                  }
                  $tab_eta_lib[$id_departement]=$lib_departement;
                  $tab_eta_val[$id_departement]="ETA|$id_departement|$lib_departement|$table";
               }
               $lib_out='&Eacute;tablissement';
               unset($libelle); //pour ne pas l'afficher dans la premiere liste
               break;
            case 'lv':
               $libelle='Langues vivantes';
               break;
            case 'ef':
               $libelle='&Eacute;preuves facultatives';
               break;
            //default:
         }
         //echo "Libelle $libelle table $table<br>\n";
         if($libelle) $ref_optgroup.= formOptionsInSelect($libelle,$table,$selectedTable);
      }
      echo formOptionsInSelect("-=[R&eacute;f&eacute;rentiel]=-","",$selectedTable);
      echo "<optgroup label='R&eacute;f&eacute;rentiel''>$ref_optgroup</optgroup>\n";
      echo "<optgroup label='$lib_out'>\n";
      //echo "tab_eta_lib<pre>";print_r($tab_eta_lib);echo "</pre>\n";
      asort($tab_eta_lib);//trie sur les clés
      foreach($tab_eta_lib as $id_departement => $lib_departement)
         echo formOptionsInSelect($lib_departement,$tab_eta_val[$id_departement],$selectedTable);
      echo '</optgroup>';
      echo "</SELECT></TD><TD>"
      	. "<SELECT class='fondo' name='annee'>".formSelectAnnee($annee)."</SELECT>"
      	. "</TD></TR>\n";
      echo "<TR><TD>&nbsp;</TD><TD><INPUT TYPE='SUBMIT' NAME='ok' VALUE='Ok' class='formo'/></TD></TR>\n";
   }
   echo "</FORM>\n</TABLE>\n";
   if($cpt_pb_dept>0)
      echo boite_important("Aucune info sur le d&eacute;partement pour <b>$cpt_pb_dept &eacute;tablissement(s)</b><br/><i>Conseil</i> : Commencez par leur <a href='".generer_url_ecrire('odb_ref')."&table=".$tab_eta_val[0]."&step2=manuel'>affecter un d&eacute;partement</a>",'<!>');

   fin_cadre_relief();

   fin_boite_info();
   fin_cadre_relief();
	$jquery= <<<FINSCRIPT
$(document).ready(function() {
	$('#form_odb_ref').submit(function() {
		flag=true;
		$("input[@name*=odb_ref_]").each(function() {
			if(this.value=='') {
				this.focus();
				flag=flag&&confirm('Etes-vous sur(e) de vouloir supprimer \\n'+this.name+' ?\\n\\nReflechissez bien aux impacts !');
			}
		});
		return flag;
	});
});
FINSCRIPT;
	echo putJavascript($jquery);
   fin_page();
   exit;
}
?>
