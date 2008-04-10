<?php
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/charsets');
define('DIR_ODB_COMMUN',_DIR_PLUGINS."odb/odb_commun/");
include_once(DIR_ODB_COMMUN.'inc-html.php');
include_once(DIR_ODB_COMMUN.'inc-referentiel.php');
include_once(DIR_ODB_COMMUN.'inc-odb.php');
include_once('inc-bdd.php');

define('MAX_LIGNES',100); //nb de lignes affichées max

setlocale(LC_TIME, "fr_FR");

global $debug, $txt_gauche, $txt_debug;
$debug=false;//isset($_REQUEST['debug']);

$txt_gauche="";// texte boite de gauche
$txt_="";// texte boite de droite
$txt_debug=""; // texte debug
define(OK,"<SPAN style='color:#3C3;font-weight:bold;'>[OK]</SPAN>");
define(KO,"<SPAN style='color:#C33;font-weight:bold;'>[KO]</SPAN>");

/**
 * affiche formulaire de saisie
 * 
 * @param $table : table MySQL concernee
 * @param $titre : titre du formulaire
 * @param $annee : annee concernee
 * @return string : formulaire de saisie
 */
function odb_form_saisie($table,$titre,$annee) {
   $str = "<!-- FORMULAIRE $table -->\n";
   $str .= "<form id='form_$table' action='".generer_url_ecrire('odb_saisie')."#form_$table' method='post' class='forml spip_xx-small'>\n";
   $str .= "\t<label for='identifiant'>Veuillez choisir un id et une ann&eacute;e</label>";
   //bouton_radio($nom, $valeur, $titre, $actif = false, $onClick="")
   /*$liste = array("id_saisie"=>"N° de saisie","id_table"=>"N° de table");
   foreach($liste as $key=>$val) {
      $str.=bouton_radio("identifiant",$key,$val,$key=="id_saisie","document.forms['form_$table'].submit_$table.value='Retrouver ce $val\\n dans $titre'");
   }*/
   //$str .= afficher_choix("identifiant","id_saisie",$liste," ");
   $str .= "<SELECT name='annee' onchange=\"window.location='".generer_url_ecrire('odb_saisie')."&annee='+this.value;\">\n";
   $str .= formSelectAnnee($annee);
   $str .= "</SELECT>\n";
   $str .= "<BR/>\n";
   $str .= "\t<input id='id' name='id' class='fondo' type='text' />\n";
   $str .= "\t<input type='hidden' name='step2' value='$table' />\n";
   $str .= "\t<input id='submit_$table' class='fondo' type='submit' value='[oo]\nRechercher' onclick=\"if(document.forms['form_$table'].id.value=='') {alert('Veuillez entrer l\'identifiant dans le champ de saisie correspondant\\npour $titre');document.forms['form_$table'].id.focus();return false;} else if(isNaN(parseInt(document.forms['form_$table'].id.value))) {alert('Vous devez saisir un numero de saisie ou un numero de table\\npour $titre');document.forms['form_$table'].id.select();return false;} else return true;\" />\n";
   //$str .= "\t<input type='submit' class='fondo' id='ajout_candidat' name='ajout_candidat' value='[+]\nSaisir' />\n";
   //$str .= "\t<input type='submit' class='fondo' id='introspecter' name='introspecter' value='[?]\nIntrospection' />\n";
   $str .= "</form>\n";
   return ($str);
}

/**
 * exécuté automatiquement par le plugin au chargement de la page ?exec=odb_saisie
 * 
 * @author Cedric PROTIERE
 */
function exec_odb_saisie() {
global $connect_statut, $connect_toutes_rubriques, $debug, $txt_gauche, $txt_droite, $txt_debug, $tab_referentiel, $odb_referentiel,$odb_mapping;

include_once(DIR_ODB_COMMUN."inc-referentiel.php");
include_once(DIR_ODB_COMMUN."inc-regles_gestion.php");
$annee=$_REQUEST['annee'];

$array_ref=array('departement','etablissement','ef','lv','eps','prefixe','serie','sexe','ville','pays');
foreach($array_ref as $ref)
   $tab_referentiel[$ref]=getReferentiel($ref,'tout');

debut_page(_T('Saisie candidats ODB'), "", "");
//echo "<br /><br />";
gros_titre(_T('Office Du Baccalaur&eacute;at'));
$tab_auteur=$GLOBALS["auteur_session"];

if(!isAdmin()) {
   $isAdmin=false;
   $statut=getStatutUtilisateur();
   
   switch ($statut) {
               // ci dessous inutile pour le moment (chefs d'etablissement)
		case 'Etablissement':
			$isOperateur=false;
			$isChefEtablissement=true;
			$etab=$tab_auteur['nom_site'];

			foreach($tab_referentiel['etablissement'] as $key => $val)
				if($val==$etab) {
					$tab_auteur['id_etablissement']=$key;
				}
			if ($debug) echo "etablissement $etab (".$tab_auteur['id_etablissement'].")<br/>\n";
			break;
		case 'Encadrant':
			$isEncadrant=true;
		case 'Operateur':
			$isOperateur=true;
			$isChefEtablissement=false;
			break;
		default:
			die(KO." - Statut <b>$statut</b> invalide");
		}
} else
   $isAdmin=true;

if ($debug) {
   echo "<A HREF='#fin_debug'>Sauter les infos de debug</A>\n";
   echo "<hr/>Auteur<pre style='text-align:left;'>";
   print_r($tab_auteur);
   echo "</pre><hr/>";
   echo "_REQUEST<pre style='text-align:left;'>";
   print_r($_REQUEST);
   echo "</pre><hr/>";
/*   echo "tab_referentiel<pre style='text-align:left;'>";
   print_r($tab_referentiel);
   echo "</pre><hr/>";
   echo "<A NAME='fin_debug'></A>\n";*/
}

debut_cadre_relief( "", false, "", $titre = _T('Saisie des candidats ODB'));
//debut_boite_info();

$REFERER=$_SERVER['HTTP_REFERER'];
$REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
$URL_SCRIPT=_DIR_PLUGIN_ODB_SAISIE;

echo "\n\n<!-- ============ Code SIOU - Module de saisie ============== -->\n\n";
isAutorise(array('Admin','Operateur','Encadrant','Etablissement'));

if(isset($_REQUEST['historique_suppressions']) && ($isAdmin || $isEncadrant)) {
	$tCol=array('id_saisie','maj', 'login', 'nom', 'prenoms', 'ne_le', 'ne_en', 'ne_vers', 'ldn');
	$sql = "SELECT ".join(',',$tCol)." \n"
			." FROM odb_suppr_candidats \n"
			." ORDER BY maj"
			;
	$result = odb_query($sql,__FILE__,__LINE__);
	while ($row = mysql_fetch_array($result)) {
		$id_saisie=$row['id_saisie'];
		foreach ($tCol as $col) {
			$$col=$row[$col];
			if($col=='login') $$col=ucwords($$col);
			$tSuivi[$id_saisie][$col] = $$col;
		}
	}
	$thead='';
	foreach($tCol as $col) $thead.="<th>".ucfirst(str_replace('_',' ',$col))."</th>";
	//echo '<pre>';print_r($tSuivi);echo'</pre>';
	$cpt=0;$tbody=array();
	if(is_array($tSuivi)) {
		foreach($tSuivi as $id_saisie => $t1) {
			$cpt++;
			foreach($t1 as $val) {
				$tbody[$cpt].="<td>$val</td>";
			}
		}
		$historique_suppressions=odb_html_table("Historique des suppressions $annee",$tbody,$thead,'historique-24.gif');
	} else $historique_suppressions="Aucun dossier n'a &eacute;t&eacute; supprim&eacute; en $annee.";
}

if ((isset($_REQUEST['supprimer'])) && ($isAdmin || $isEncadrant)) {
	// suppression d'une fiche
	$identifiant=$_REQUEST['supprimer'];
	$id=$_REQUEST['id'];
	$annee=$_REQUEST['annee'];
	debut_boite_info();
	$sql="INSERT INTO odb_suppr_candidats (`id_saisie`, `id_table`, `id_table_old`, `annee`, `serie`, `prefixe`, `nom`, `prenoms`, `ne_le`, `ne_en`, `ne_vers`, `ldn`, `pdn`, `sexe`, `nationalite`, `quartier_res`, `lv1`, `lv2`, `eps`, `ef1`, `ef2`, `ville`, `departement`, `etablissement`, `ajourne`, `non_inscrit`, login, maj)\n (SELECT `id_saisie`, `id_table`, `id_table_old`, `annee`, `serie`, `prefixe`, `nom`, `prenoms`, `ne_le`, `ne_en`, `ne_vers`, `ldn`, `pdn`, `sexe`, `nationalite`, `quartier_res`, `lv1`, `lv2`, `eps`, `ef1`, `ef2`, `ville`, `departement`, `etablissement`, `ajourne`, `non_inscrit`,'".$tab_auteur['login']."',NOW() from odb_candidats where annee=$annee and $identifiant='$id')";
	odb_query($sql,__FILE__,__LINE__);
	$nb=mysql_affected_rows();
	if($nb==1) {
		echo OK." - Suppression enregistr&eacute;e dans l'historique des suppressions de candidats<br/>";
		$sql="DELETE FROM odb_candidats WHERE annee=$annee and $identifiant='$id'";
		odb_query($sql,__FILE__,__LINE__);
		echo OK." - La fiche $identifiant=$id a bien &eacute;t&eacute; supprim&eacute;e<br/>";
	}
	else echo KO." - aucun ($nb) candidat ne correspond &agrave; $identifiant=$id";
	fin_boite_info();
}


if(isset($_POST["step3"])) {
//////////////////////////////////////////////// step 3 : mise à jour de la table
   $table=$_POST["step3"];
   $annee=$_POST["annee"];
   $id=$_POST["id"];
   $identifiant=$_POST["identifiant"];
   $id_dept=$_POST["sql__departement"];
   $dept=$tab_referentiel['departement'][$id_dept];
   $isNouveau=isset($_POST['isNouveau']);
   if(!is_numeric($id)) $id="'$id'"; 
   gros_titre($table);
   /*debut_gauche();
      odb_raccourcis('tous');
   creer_colonne_droite();
   debut_droite();*/
   debut_boite_info();
   //echo "<pre>";print_r($_POST);echo "</pre>";
   $sql="UPDATE $table SET\n";
   foreach($_POST as $key=>$val) {
      if(substr_count($key,"sql")>0) {
         $tab_tmp=explode("__",$key);
         $champ=$tab_tmp[1];
         $champ_map=$odb_mapping[$table][$champ];
         $type=$odb_referentiel[$table][$champ_map];
         if($isNouveau && $champ=='id_saisie') {
         	$id=0;
			}
         if(substr($type,0,3)=="ref") {
            $myRef=strtolower(substr($type,3));
            switch($myRef) {
               case 'ville':
                  $check=$tab_referentiel[$myRef][$id_dept][$val];
                  break;
               default:
                  $check=$tab_referentiel[$myRef][$val];
                  break;
            }
         } elseif(substr($type,0,3)=="Et.") {
            //echo "<hr>====== etab : $val [$id_dept] ==========";
            $check=$tab_referentiel['etablissement'][$id_dept][$val];
         } else $check=$val;
         //echo '$table,$check,$champ,$dept,$id '."$table,$check,$champ,$dept,$id<br>";
         $retour=reglesGestion($table,$check,$champ_map,$dept,$id);
         if($type!="") {
            $val=$retour["valeur"];
            $fatal.=$retour["fatal"];
            $txt_debug.=$retour["txt_debug"];
         } else {
            $txt_debug.="Type inconnu pour le champ $champ (valeur : $val)<br/>\n";
            if($val=="") $val="'$val'";
         }
         $val=str_replace("@_quote_@","'",$val);
         switch ($champ)
         {
         	case 'ne_le' : 
         		$tmp=array();
         		$tmp=explode('/',$val);
         		if(count($tmp)>1) $val=$tmp[2].'-'.$tmp[1].'-'.$tmp[0];
         		break;
         	default: 
         }
         
         $sql_tab[]="$champ=$val";
         $sql_insert_col[]=$champ;
         $sql_insert_val[]=$val;
         if(substr_count($champ,'ne_')>0) {
         	// modif d'un candidat : on force les champs ne_xxx non passes (car disabled) a 0
         	foreach(array('ne_le','ne_en','ne_vers') as $col) {
         		if($champ!=$col) {
         			$sql_tab[]="$col=''";
					}
				}
			}
         if($champ!='maj') $sql_histo_pre[]="$champ";
         else $maj=$val;
         $sql_histo_post[]="$val";
         if($champ=='id_saisie') $id_saisie=$val;
         if ($debug) echo "$champ=$val<br>\n";
      }
   }

   echo $fatal;
   if(strlen(trim($fatal))==0) {
      $sql_histo="INSERT into odb_histo_candidats (login,maj,".join(",",$sql_histo_pre).") (SELECT '".$tab_auteur['login']."',$maj, ".join(",",$sql_histo_pre)." from odb_candidats where id_saisie='$id_saisie' and annee=$annee)";
      if(!$isNouveau) odb_query($sql_histo,__FILE__,__LINE__);

      //$sql_delete="DELETE FROM $table WHERE annee=$annee AND $identifiant=$id";
      //$txt_debug.= "Requêtes<br/><pre>$sql_delete</pre><br/>\n";
      //mysql_query($sql_delete) or die (KO. " - requête $sql_delete<br/>\n".mysql_error());
      $sql .= join(",",$sql_tab)."\nWHERE annee=$annee AND $identifiant=$id";
      if($isNouveau) {
			$sql2="SELECT max(id_saisie)+1 id from odb_candidats where id_saisie<100000 and annee=$annee";
			$result=odb_query($sql2,__FILE__,__LINE__);
			$row=mysql_fetch_array($result);
			$id_saisie=$row['id'];
			if($id_saisie=='') die(KO." - Nouveau id_saisie introuvable");
			if($id_saisie==0) $id_saisie=1; // premier candidat de l'annee (ne dois pas avoir id_saisie=0)
			$id=$id_saisie;
         //echo "$champ $champ_map $key $val<br>$sql";

      	$sql="INSERT into odb_candidats (\nlogin,id_saisie,".join(',',$sql_insert_col)."\n) VALUES (\n'".$tab_auteur['login']."',$id,".join(',',$sql_insert_val)."\n)";
		}
      $txt_debug .= "<pre>$sql</pre><br/>\n";
      odb_query($sql,__FILE__,__LINE__);
      $sql="UPDATE odb_repartition rep, odb_candidats can set rep.id_table=can.id_table where rep.id_saisie=can.id_saisie and can.annee=$annee and rep.annee=$annee";
      odb_query($sql,__FILE__,__LINE__);
      $sql="delete from odb_repartition where (id_table='' or id_table='0') and annee=$annee";
      odb_query($sql,__FILE__,__LINE__);
      $sql="UPDATE odb_candidats set id_table='0' where id_table='' and annee=$annee";
      odb_query($sql,__FILE__,__LINE__);
      if($isNouveau) {
      	echo OK." Le candidat <b>$id</b> a bien &eacute;t&eacute; enregistr&eacute; <br/>\n";
		}
      else echo OK." Mise &agrave; jour de <b>$table</b> effectu&eacute;e pour <b>$identifiant=$id</b> en <b>$annee</b><br/>Les anciennes donn&eacute;es ont &eacute;t&eacute; enregist&eacute;es dans la table d'historique<br/>\n";
   }
   if($debug) echo "<hr/>$txt_debug";

   fin_boite_info();
   echo "<br />";
   //debut_cadre_relief("", false, "", $titre = _T("Acc&egrave;s direct &agrave; un candidat $annee"));
	if(isset($_REQUEST['ajouter_nouveau'])) {
		//echo"<pre>";print_r($_POST);echo"</pre>";
		// revenir au cadre d'ajout de nouveau candidat
		//unset($_POST);
		$_REQUEST['step2']='odb_candidats';
		$_REQUEST['ajout_candidat']='auto';
		$_REQUEST['annee']=$annee;
		$ajouterNouveau=true;
		$tPersist['etablissement']=$_REQUEST['sql__etablissement'];
		$tPersist['ville']=$_REQUEST['sql__ville'];
		$tPersist['serie']=$_REQUEST['sql__serie'];
		//print_r($tPersist);
	}
	else unset($_POST); // revenir au debut
   //fin_cadre_relief();
   
} 
if(isset($_REQUEST["step2"])) {
//////////////////////////////////////////////// step 2 : affichage table à modifier
   $table=$_REQUEST["step2"];
   $id=$_REQUEST["id"]; //numero
	$isNouveau=false; // modification d'un candidat existant
	if($_REQUEST['ajout_candidat']) {
		$isNouveau=true; // insertion d'un nouveau candidat
		$identifiant='id_saisie';
	} elseif(!is_numeric($id)) {//numero de table
   	//$id=getIdTableHumain($id);
   	$identifiant='id_table';
   } else {
   	$identifiant='id_saisie'; //champ
   }
   //print_r($_REQUEST);
   $annee=$_REQUEST["annee"];
   //debut_gauche();
   gros_titre($table);
   if($identifiant=='id_table') $id="'$id'";

   if($isNouveau) {
   	//xxx
   	$nbCan=getNbCandidats();
   	if($nbCan>0) {
   		$sql="SELECT * from odb_candidats LIMIT 0,1";
   		$result=odb_query($sql,__FILE__,__LINE__);
		} else { 
			$sql="INSERT INTO odb_candidats(id_saisie,annee) VALUES (0,$annee)";
			odb_query($sql,__FILE__,__LINE__);
   		$sql="SELECT * from odb_candidats LIMIT 0,1";
   		$result=odb_query($sql,__FILE__,__LINE__);			
			$sql="DELETE FROM odb_candidats WHERE id_saisie=0 and annee=$annee";
			odb_query($sql,__FILE__,__LINE__);
		}
	} else {
		$sql="SELECT * FROM $table WHERE $identifiant=$id AND annee=$annee";
		$result=odb_query($sql,__FILE__,__LINE__);
	}
	$nb_fields=mysql_num_fields($result);
	for ($i=0;$i<$nb_fields;$i++) {
		$label_bdd=mysql_field_name($result, $i);
		$label[$i]=str_replace('_',' ',$odb_mapping[$table][$label_bdd]);
		$label_sql[$i]=$label_bdd;
		$tCol[$label_bdd]=$i;
	}

   if(isset($_REQUEST['sql__departement'])) $id_departement=$_REQUEST['sql__departement'];
   $couleurOk='#AD2';
   $couleurKo='#C66';
   $couleurDisable='#aaa';
   $couleurEnable=$couleurOk;
   $couleurFocus='#fa0';

   //debut_droite();
/*print_r
   print_r($label);
   echo"<hr/>";
   print_r($label_sql);
   echo"<hr/>";
   print_r($odb_mapping[$table]);
*/

//   
   if(isset($_REQUEST['introspecter'])) {
		debut_gauche();
			debut_raccourcis();
				icone_horizontale (_L('Saisie d\'un nouveau candidat'), 
					generer_url_ecrire("odb_saisie")."&annee=$annee&id=&step2=odb_candidats&ajout_candidat=true#form_odb_candidats", 
					"../"._DIR_PLUGIN_ODB_REF."/img_pack/siou_carre.png");
				icone_horizontale (_L('Introspection'), 
					generer_url_ecrire("odb_saisie")."&annee=$annee&step2=odb_candidats&introspecter=true", 
					"../"._DIR_PLUGIN_ODB_REF."/img_pack/siou_carre.png");
				if($isEncadrant || $isAdmin) {
					icone_horizontale (_L('Historique des suppressions'), 
						generer_url_ecrire("odb_saisie")."&annee=$annee&historique_suppressions=true", 
						"../"._DIR_PLUGIN_ODB_REF."/img_pack/siou_carre.png");
				}
			fin_raccourcis();
		creer_colonne_droite();
		debut_droite();
			if(strlen($historique_suppressions)>0) {
				debut_boite_info();
					echo $historique_suppressions;
				fin_boite_info();
			}
		debut_boite_info();
   	$nbErreurs=odb_introspection($annee);
   	$s=$nbErreurs>1?'s':'';
   	if($nbErreurs==0) $nbErreurs='Aucune';
   	echo "<b>$nbErreurs</b> erreur$s d&eacute;tect&eacute;e$s";
   	fin_boite_info();
   	exit;
	}
   if ($isNouveau || mysql_num_rows($result) > 0) {
   	$flag=0;
   	$tColsSaisieObligatoire=array('annee','serie','nom','prenoms','ldn','pdn','sexe','nationalite','eps','ville','etablissement');
   	$tColsSaisieFacultative=array('prefixe','ne_le','ne_en','ne_vers','lv1','lv2','ef1','ef2','quartier_res');
      while($row=mysql_fetch_array($result)) {
         $id=$row[$identifiant];
         $login=ucwords($row['login']);

         $etablissement=stripslashes($row['etablissement']);
         if(!$id_departement>0) 
         	if($isNouveau) $id_departement=0;
         	else $id_departement=$row['departement'];         
         $etablissement=stripslashes($tab_referentiel[$etablissement][$id_departement]);
         if($isChefEtablissement)
            if($tab_auteur["nom_site"]!=$etablissement) {
               die(KO." - <b>".$tab_auteur["nom"]."</b>, vous ne pouvez modifier que les candidats de l'&eacute;tablissement <B>".$tab_auteur["nom_site"]."</B>,or le candidat <b>$identifiant=$id</b> est dans l'&eacute;tablissement <b>$etablissement</b>");
            } else $disabled="disabled";
         $tReference=$row;
         for ($i=0;$i<$nb_fields;$i++) {
            $type=$odb_referentiel[$table][$odb_mapping[$table][$label_sql[$i]]];
            if($isNouveau) {
            	$row[$i]=isset($tPersist[$label_sql[$i]])?$tPersist[$label_sql[$i]]:'';
            	//echo "$label_sql[$i] : $row[$i]<br/>";
				} else $row[$i]=stripslashes($row[$i]);
				$js=" onChange=\"this.style.backgroundColor='$couleurOk';\"";
            if(substr_count($type,"ref")>0) { //c'est un référentiel
               $referentiel=substr($type,3);
               //echo "$referentiel-".$row[$i]." :: ";
               switch ($label_sql[$i])
               {
               	case 'serie': 
               		$js.="onClick=\"if(this.options[selectedIndex].text=='A1' || this.options[selectedIndex].text=='A2' || this.options[selectedIndex].text=='B') {\n".
               			"\tflag=false;document.forms['form_$table'].sql__lv1.style.backgroundColor='$couleurEnable';document.forms['form_$table'].sql__lv2.style.backgroundColor='$couleurEnable';\n"
               			."} else {\n\tflag=true;document.forms['form_$table'].sql__lv1.value=0;document.forms['form_$table'].sql__lv1.style.backgroundColor='$couleurDisable';document.forms['form_$table'].sql__lv2.style.backgroundColor='$couleurDisable';document.forms['form_$table'].sql__lv2.value=0;\n}\n"
               			."document.forms['form_$table'].sql__lv1.disabled=flag;document.forms['form_$table'].sql__lv2.disabled=flag;\""
               			;
               		break;
               	case 'pdn':
               		$js.="onBlur=\"document.forms['form_$table'].sql__nationalite.value=this.value;\" ";
               		break;
               }
               if($id_departement>0)
               	$txt_ligne=formSelectRefInTR($label[$i],"sql__".$label_sql[$i],$referentiel,trim($row[$i]),"class='fondo' $js",$id_departement);
               else 
						$txt_ligne="<tr><td><label for='sql__".$label_sql[$i]."'>$label[$i]</label></td>\n".
							"<td><SELECT name='sql__".$label_sql[$i]."' id='sql__".$label_sql[$i]."' class='fondo'>\n".
							"<OPTION SELECTED VALUE='0'>$label[$i]</OPTION>".
							"</SELECT></td></tr>\n"
							;
            } elseif($type=="Et.") { // Etablissement : on concatene les ref d'etablissement
               if($id_departement>0)
	               $txt_ligne=formSelectRefInTR("&Eacute;tablissement","sql__".$label_sql[$i],"etablissement",$row[$i],"$disabled class='fondo' $js",$id_departement); //$label,$name,$referentiel,$annee,$valeur_defaut,$html=""
               else 
						$txt_ligne="<tr><td><label for='sql__".$label_sql[$i]."'>$label[$i]</label></td>\n".
							"<td><SELECT name='sql__".$label_sql[$i]."' id='sql__".$label_sql[$i]."' class='fondo'>\n".
							"<OPTION SELECTED VALUE='0'>$label[$i]</OPTION>".
							"</SELECT></td></tr>\n"
							;
            } elseif($type=="VraiFaux") { // booleen
            	if($isNouveau) {
            		$txt_ligne=formInputTextTR($label[$i],"sql__".$label_sql[$i],0,'readonly style="font-size:12px;border:none;font-family:Arial,sans-serif;background-color:transparent;"');
            	} else 
            		$txt_ligne=formSelectTR1($label[$i],"sql__".$label_sql[$i],"class='fondo' $js").formSelectVraiFaux($row[$i]).formSelectTR2();
               //echo $label[$i]." sql__".$label_sql[$i]." : ".$row[$i]."<br />";
            } elseif($type=="TIMESTAMP") { // timestamp
               $txt_ligne="<input type='hidden' name='sql__".$label_sql[$i]."' value='".date('Y-m-d H:i:s')."'/><td>".$label[$i]."</td><td>".$row[$i]." &rArr; ".date('Y-m-d H:i:s')." </td>\n";
               //echo $label[$i]." sql__".$label_sql[$i]." : ".$row[$i]."<br />";
            } elseif($type=="YEAR" && $label_sql[$i]=='annee') {
            	if($isOperateur) $option="<OPTION SELECTED value='$annee'>$annee</OPTION>";
            	else $option=formSelectAnnee($annee);
            	$txt_ligne="<tr><td><label for='sql__annee'>$label[$i]</label></td>\n".
            		"<td><SELECT name='sql__annee' id='sql__annee' class='fondo'>\n".
            		$option.
            		"</SELECT></td></tr>\n";
            } else {// Champ texte
            	$readonly='class="fondo"';
            	if($isNouveau || $isOperateur) {
            		if ($label_sql[$i]=='id_saisie' && $isNouveau) {
            			$row[$i]='&Agrave; venir';
            			$isTexte=true;
						} else $isTexte=false;
            		if (in_array($label_sql[$i],array('id_saisie','id_table','id_table_old')))
            			$readonly='readonly style="font-size:14px;color:#336699;font-weight:bold;border:none;font-family:Arial,sans-serif;background-color:transparent;"';
            	}
            	
            	$js="onFocus=\"this.style.backgroundColor='$couleurFocus';\" ";
            	switch ($label_sql[$i])
            	{
            		case 'nom': 
            			$js.="onKeyUp=\"javascript:this.value=this.value.toUpperCase();this.style.backgroundColor='$couleurOk';\""; 
            			break;
            		case 'ne_le':
            			$tmp=array();
            			$tmp=explode('-',$row[$i]);
            			//echo $row[$i];print_r($tmp);
            			if(count($tmp)>1)
            				$row[$i]=$tmp[2].'/'.$tmp[1].'/'.$tmp[0];
            			$js.="onBlur=\"if(this.value!='') {if(isDate(this.value)) {isBadDate=false;} else {isBadDate=true;this.style.backgroundColor='$couleurKo';}}\" ";
            		case 'ne_en':
            		case 'ne_vers':
            			$inter=array_diff(array('ne_en','ne_le','ne_vers'),array($label_sql[$i]));
            			$js.="onKeyUp=\"if(this.value!='') {flag=true;couleur='$couleurDisable';} else {flag=false;couleur='$couleurEnable';}";
            			foreach($inter as $col) {
            				$js.="document.forms['form_$table'].sql__$col.disabled=flag;".
            					"document.forms['form_$table'].sql__$col.style.backgroundColor=couleur;";
							}
							$js.="\" onChange=\"if(!isBadDate) this.style.backgroundColor='$couleurOk';\"";
							//print_r($inter);
            			break;
            		default: 
            			$js.='';
            	}
        			$js.=" onBlur=\"if(this.value=='') {this.style.backgroundColor='$couleurKo';} else {this.style.backgroundColor='$couleurOk';}\"";
            	
               $maxlength=mysql_field_len($result,$i);
               //echo $label[$i]." : $maxlength<br/>";
               if($isTexte) $txt_ligne=formInputTextTR($label[$i],"txt_".$label_sql[$i],$row[$i],"$readonly $js maxlength='$maxlength'");
               else $txt_ligne=formInputTextTR($label[$i],"sql__".$label_sql[$i],$row[$i],"$readonly $js maxlength='$maxlength'");
               //echo "$label[$i] : $type<br/>\n";
            }
            if($flag==1) $class='row_odd';else $class='row_even';
            $flag=1-$flag;
            //FIXME Beurk (gestion des TR)
            if(!in_array($label_sql[$i],array('login','departement'))) 
            	$texte_table.=str_replace('<tr>',"<tr class='$class'>",$txt_ligne)."\n";
            elseif($label_sql[$i]=='departement') {
            	if(!isset($id_departement)) $id_departement=$val;
            	$js=$isNouveau?"onChange=\"url2='".generer_url_ecrire('odb_saisie')."&sql__departement='+this.value+'&annee=$annee&step2=odb_candidats&ajout_candidat=auto#form_$table';window.location=url2;\""
            		:"onChange=\"document.form_$table.sql__etablissement.value=0;document.form_$table.sql__ville.value=0;\"";
					$selectDepartement=formSelectRefInTR('D&eacute;partement','sql__departement','departement',$id_departement,"class='forml' $js",$id_departement);
					$titre=$isNouveau?"\t\t<TH>Ajout d'un candidat en $annee</TH><TH align='right'><div align='right'>".$tab_auteur['nom']."</div></TH>\n"
										  :"\t\t<TH>Mise &agrave; jour de $table pour $identifiant=$id en $annee</TH><TH align='right'><div align='right'>$login</div></TH>\n";
					$jsForm=putJavascript('isDate');
					$jsForm.=putJavascript('changerStyle');
					$jsCheckForm = "function checkForm(formulaire,bAlerter) {\n"
							. "\tcheck=true;alerte='Verification formulaire de saisie\\n________________________\\n';\n\n"
							. "\tif(formulaire.sql__departement.value==0) {\n"
							. "\t\tcheck=false;alerte+='\\n- Veuillez choisir un departement';\n"
							;
   				foreach(array_merge($tColsSaisieObligatoire,$tColsSaisieFacultative) as $col) 
   					$jsCheckForm.="\t\tformulaire.sql__$col.disabled=true;formulaire.sql__$col.style.backgroundColor='$couleurDisable';\n";
					$jsCheckForm.="\t} else {\n";
					foreach($tColsSaisieObligatoire as $col)
						$jsCheckForm.="\t\tif(formulaire.sql__$col.value=='' || formulaire.sql__$col.value=='0')\n"
								. "\t\t\t{alerte+='\\n- Veuillez saisir [".ucwords($col)."]';check=false;formulaire.sql__$col.style.backgroundColor='$couleurKo';formulaire.sql__$col.focus();}\n"
								;
					foreach($tab_referentiel['serie'] as $idTmp=>$dptTmp)
						if(in_array($dptTmp,array('A1','A2','B'))) $tSeriesLitteraires[$dptTmp]=$idTmp;
					$jsCheckForm.="\t\tisLitteraire=false;if(true";foreach($tSeriesLitteraires as $idTmp)
						$jsCheckForm.="&&formulaire.sql__serie.value!='$idTmp'";
					$jsCheckForm.=")\n\t\t\t{isLitteraire=false;couleurFond='$couleurDisable';}\n\t\telse {isLitteraire=true;couleurFond='$couleurOk';}\n";
					$jsCheckForm.="\t\tformulaire.sql__lv1.disabled=!isLitteraire;formulaire.sql__lv2.disabled=!isLitteraire;formulaire.sql__lv1.style.backgroundColor=couleurFond;formulaire.sql__lv2.style.backgroundColor=couleurFond;\n";
					$jsCheckForm.="\t\tcptDdn=0;\n"
							. "\t\tif(formulaire.sql__ne_le.value=='00/00/0000' || formulaire.sql__ne_le.value=='0000-00-00' || formulaire.sql__ne_le.value=='') {formulaire.sql__ne_le.value='';isNeLeSaisi=false;} else {cptDdn++;isNeLeSaisi=true;changerStyle(formulaire.sql__ne_en,'$couleurDisable','disable');changerStyle(formulaire.sql__ne_vers,'$couleurDisable','disable');}\n"
							. "\t\tif(isNeLeSaisi) {if(!isDate(formulaire.sql__ne_le.value)) {alerte+='\\n- Veuillez taper une date de naissance valide au format JJ/MM/AAAA';check=false;formulaire.sql__ne_le.style.backgroundColor='$couleurKo';formulaire.sql__ne_le.focus();} }\n"
							. "\t\tif(formulaire.sql__ne_en.value=='0000' || formulaire.sql__ne_en.value=='') {formulaire.sql__ne_en.value='';} else {cptDdn++;changerStyle(formulaire.sql__ne_le,'$couleurDisable','disable');changerStyle(formulaire.sql__ne_vers,'$couleurDisable','disable');}\n"
							. "\t\tif(formulaire.sql__ne_vers.value=='0000' || formulaire.sql__ne_vers.value=='') {formulaire.sql__ne_vers.value='';} else {cptDdn++;changerStyle(formulaire.sql__ne_en,'$couleurDisable','disable');changerStyle(formulaire.sql__ne_le,'$couleurDisable','disable');}\n"
							. "\t\tif(cptDdn!=1) {alerte+='\\n- Veuillez corriger la date de naissance';check=false;formulaire.sql__ne_le.style.backgroundColor='$couleurKo';formulaire.sql__ne_en.style.backgroundColor='$couleurKo';formulaire.sql__ne_vers.style.backgroundColor='$couleurKo';formulaire.sql__ne_le.readonly=false;formulaire.sql__ne_le.disabled=false;formulaire.sql__ne_le.focus();}\n"
							. "\t\tif(((formulaire.sql__lv1.value==formulaire.sql__lv2.value) || formulaire.sql__lv1.value=='0' || formulaire.sql__lv2.value=='0') && isLitteraire) {alerte+='\\n- Langues vivantes invalides';check=false;formulaire.sql__lv2.style.backgroundColor='$couleurKo';formulaire.sql__lv2.focus();}\n"
							. "\t\tif((formulaire.sql__ef1.value==formulaire.sql__ef2.value || formulaire.sql__ef1.value=='0') && formulaire.sql__ef2.value!='0') {alerte+='\\n- Epreuves facultatives invalides';check=false;formulaire.sql__ef2.style.backgroundColor='$couleurKo';formulaire.sql__ef2.focus();}\n"
							. "\t}\n\tif(bAlerter && !check) alert(alerte);else formulaire.sql__nom.focus();\n\treturn(check);\n"
							. "}\n"
							;
					$jsForm.=putJavascript($jsCheckForm);
					$texte_table = "$jsForm\n<A NAME='form_$table'></A><form id='form_$table' action='".generer_url_ecrire('odb_saisie')."' method='post' class='forml spip_xx-small' onSubmit='return checkForm(this,true);'>\n"
						. "<TABLE class='spip' width='700'>\n"
						. "\t<TR class='row_first'>"
						. $titre
						. "\t</TR>\n"
						. $selectDepartement
						. $texte_table
						;            	
				}
         }
      }
   } else
      die("L'identifiant $identifiant=$id n'existe pas pour l'ann&eacute;e $annee<br/>Veuillez <A HREF='".generer_url_ecrire('odb_saisie')."&annee=$annee'>recommencer</A>\n");

	if($isEncadrant || $isAdmin) $boutonSupprimer="\t<input type='button' name='supprimer' value='[#]\nSupprimer' style='background-color:#000;' class='fondo' onClick=\"if(confirm('Souhaitez-vous vraiment supprimer le dossier\\ndu candidat $id ?')) window.location='".generer_url_ecrire('odb_saisie')."&annee=$annee&supprimer=$identifiant&id=$id';\"/>\n";
	else $boutonSupprimer='';
	if($isNouveau) {
		$identifiant='id_saisie';
		$id=$max;
		$texte_table.="<tr><td colspan=2><center>\n".
			"\t<input type='submit' name='ajouter_nouveau' value='[+ +]\nAjouter et nouveau' class='fondo'/>\n".
			"\t<input type='submit' name='ajouter_quitter' value='[+ x]\nAjouter et quitter' class='fondo'/>\n".
			"\t<input type='button' name='annuler' value='[x]\nAbandonner' style='background-color:$couleurKo;' class='fondo' onClick=\"window.location='".generer_url_ecrire('odb_saisie')."';\"/>\n".
			"\t<input type='hidden' name='isNouveau' value='true'/>\n".
			"</center></td></tr>\n";
	} else $texte_table.= "<TR><TD COLSPAN=2><CENTER><INPUT TYPE='submit' VALUE='[ok]\nMettre &agrave; jour' class='fondo' />\n"
		."\t<input type='button' name='annuler' value='[x]\nAbandonner' style='background-color:$couleurKo;' class='fondo' onClick=\"window.location='".generer_url_ecrire('odb_saisie')."';\"/>"
		."$boutonSupprimer</CENTER></TD></TR>\n";
	if($isOperateur) $texte_table.="\t<input type='hidden' name='isOperateur' value='true'/>\n";
   $texte_table .= "\t<input type='hidden' name='step3' value='$table' />\n";
   $texte_table .= "\t<input type='hidden' name='annee' value='$annee' />\n";
   $texte_table .= "\t<input type='hidden' name='identifiant' value='$identifiant' />\n";
   $texte_table .= "\t<input type='hidden' name='id' value='$id' />\n";
   $texte_table.= "</TABLE>\n";
   $texte_table.= "</FORM>\n";
   echo $texte_table;
   echo putJavascript("checkForm(document.forms['form_$table'],false);\n");

   fin_boite_info();

	if(!$isNouveau) {
		if($identifiant=='id_table') $id="'$id'";
		$sql="SELECT * from odb_histo_candidats where $identifiant=$id AND annee=$annee order by maj desc";
		$result=odb_query($sql,__FILE__,__LINE__);
		$nb_fields=mysql_num_fields($result);
		$nb_rows=mysql_num_rows($result);
		if($nb_rows>0) {
		   echo "<h3>Historique</h3>";
		   for ($i=0;$i<$nb_fields;$i++) {
		      $label_bdd=mysql_field_name($result, $i);
		      $label[$i]=str_replace('_',' ',$odb_mapping[$table][$label_bdd]);
		      if($label[$i]=='') $label[$i]=ucfirst(str_replace('_',' ',$label_bdd));
		      $label_sql[$i]=$label_bdd;
		   }
		   echo "<table class='spip'>\n<tr>\n";
		   foreach($label as $titre) echo "\t<th><small>$titre</small></th>\n";
		   while($row=mysql_fetch_array($result)) {
		      $id_departement=$row['departement'];
		      echo "<tr>\n";
		      for($i=0;$i<$nb_fields;$i++) {
		         $valeur=stripslashes($row[$i]);
		         if($valeur=='') $valeur="&nbsp;";
		         if($valeur!=$tReference[$i]) $isChanged=true; else $isChanged=false;
		         //echo "$valeur == ".$tReference[$i]." ? $isChanged<br/>";
		         $champ=$odb_referentiel[$table][$odb_mapping[$table][$label_sql[$i]]];
		         if(substr_count($champ,"ref")>0) {
		            $ref=strtolower(substr($champ,3));
		            //echo "$ref = $valeur";
		            $valeur=$tab_referentiel[$ref][$valeur];
		         } elseif($champ=='Et.') $valeur=$tab_referentiel['etablissement'][$id_departement][$valeur];
		         elseif($champ=='VraiFaux') $valeur=$valeur==0?'Faux':'Vrai';
		         if($isChanged) $style='color:#F00;font-weight:bold;background-color:#FF0;'; else $style='color:#000;';
		         echo "\t<td style='$style'><small>$valeur</small></td>\n";
		      }
		      echo "</tr>\n";
		   }
		   echo "</table>";
		}
	}
} else {
//////////////////////////////////////////////// step 1 : affichage interface saisie
$annee=isset($_REQUEST['annee'])?$_REQUEST['annee']:date("Y");
   debut_gauche();
      echo "<IMG SRC='"._DIR_PLUGIN_ODB_SAISIE."/img_pack/logo_odb.png' alt='Office du bac' ALIGN='absmiddle'><br><br>\n";
   	debut_raccourcis();
			icone_horizontale (_L('Saisie d\'un nouveau candidat'), 
				generer_url_ecrire("odb_saisie")."&annee=$annee&id=&step2=odb_candidats&ajout_candidat=true#form_odb_candidats", 
				"../"._DIR_PLUGIN_ODB_REF."/img_pack/siou_carre.png");
			icone_horizontale (_L('Introspection'), 
				generer_url_ecrire("odb_saisie")."&annee=$annee&step2=odb_candidats&introspecter=true", 
				"../"._DIR_PLUGIN_ODB_REF."/img_pack/siou_carre.png");
			if($isEncadrant || $isAdmin) {
				icone_horizontale (_L('Historique des suppressions'), 
					generer_url_ecrire("odb_saisie")."&annee=$annee&historique_suppressions=true", 
					"../"._DIR_PLUGIN_ODB_REF."/img_pack/siou_carre.png");
			}
		fin_raccourcis();
   creer_colonne_droite();
   debut_droite();
   	if(strlen($historique_suppressions)>0) {
			debut_boite_info();
				echo $historique_suppressions;
			fin_boite_info();
		}
      debut_cadre_relief("", false, "", $titre = _T("Acc&egrave;s direct &agrave; un candidat $annee"));
      echo odb_form_saisie("odb_candidats","la table des candidats",$annee);
      fin_cadre_relief();

      //Acces par clic
      $orderBy=isset($_GET['orderBy'])?$_GET['orderBy']:'nom';
      $limit=isset($_GET['limit'])?$_GET['limit']:0;
      $filtreSerie=trim($_GET['filtreSerie'])!=''?$_GET['filtreSerie']:false;
      $filtreDepartement=trim($_GET['filtreDepartement'])!=''?$_GET['filtreDepartement']:false;
      $filtreEtablissement=trim($_GET['filtreEtablissement'])!=''?$_GET['filtreEtablissement']:false;

      $where="WHERE annee=$annee";
      if($isChefEtablissement) $where .= ' AND etablissement='.$tab_auteur['id_etablissement'];
      elseif($isOperateur && !$isEncadrant) $where .= ' AND login="'.$tab_auteur['login'].'"';
      if($filtreSerie) {
         $where.=" AND serie=$filtreSerie";
         $andEtablissement.=" AND serie=$filtreSerie";
         $andDepartement.=" AND serie=$filtreSerie";
      }
      if($filtreDepartement) {
         $where.=" AND departement=$filtreDepartement";
         $andSerie.=" AND departement=$filtreDepartement";
         $andEtablissement.=" AND departement=$filtreDepartement";
      }
      if($filtreEtablissement) {
         $where.=" AND etablissement=$filtreEtablissement";
         $andSerie.=" AND etablissement=$filtreEtablissement";
         $andDepartement.=" AND etablissement=$filtreEtablissement";
      }

      $sql="SELECT id_saisie, id_table, sexe, prefixe, nom, prenoms, etablissement, serie, ville, departement, login, maj\n FROM odb_candidats\n $where";
      $result=odb_query($sql,__FILE__,__LINE__);
      $nb_lignes_tot=mysql_num_rows($result);// nb d'enregistrements total
      $sql.=" ORDER BY $orderBy LIMIT $limit,".MAX_LIGNES;
      $result2=odb_query($sql,__FILE__,__LINE__);
      $nb_lignes=mysql_num_rows($result2);
      if($nb_lignes>0) {

         $tab_titres=array('id_saisie'=>'N&deg; Saisie','id_table'=>'N&deg; Table','nom'=>'Nom','serie'=>'S&eacute;r.','etablissement'=>'&Eacute;tablissement','departement'=>'D&eacute;pt.');
         $thead='';
         foreach($tab_titres as $cle=>$titre) {
            if($orderBy==$cle) $titre="<u>$titre</u>";
            if(($isAdmin || $isOperateur || ($isChefEtablissement && $cle!='etablissement' || $cle!='departement')) && !${"filtre".ucwords($cle)} ) //ne pas afficher le filtre en cours
            	$thead.="<td align='center'><A class='cellule-h' HREF=\"javascript:document.forms['tri_filtre'].orderBy.value='$cle';document.forms['tri_filtre'].submit();\">$titre</A></td>";
         }

         while($row=mysql_fetch_array($result2)) {
            $id_saisie=$row['id_saisie'];
            $id_table=getIdTableHumain($row['id_table']);
            $id_departement=$row['departement'];
            $serie=$tab_referentiel['serie'][$row['serie']];
            $departement=$tab_referentiel['departement'][$id_departement];
            $ville=$tab_referentiel['ville'][$id_departement][$row['ville']];
            $id_etablissement=$row['etablissement'];
            $prefixe=$tab_referentiel['prefixe'][$row['prefixe']];
            $sexe=$tab_referentiel['sexe'][$row['sexe']]=='M'?'M.':'Mlle';
            $e=$sexe=='M.'?'':'e';
            $nom=stripslashes($row['nom']);
            $prenoms=stripslashes($row['prenoms']);
            $login=ucfirst($row['login']);
            list($tmpDate,$tmpHeure)=explode(' ',$row['maj']);
            list($yMaj,$mMaj,$jMaj)=explode('-',$tmpDate);
            $texteMaj="Saisi$e par $login le $jMaj/$mMaj/$yMaj &agrave; ".substr($tmpHeure,0,5);
            if(!$isChefEtablissement) $etablissement=$tab_referentiel['etablissement'][$id_departement][$id_etablissement];
            //else $etablissement=$tab_auteur['nom_site'];

            $ligne="<td><small><A title='$texteMaj' HREF='".generer_url_ecrire('odb_saisie')."&annee=$annee&step2=odb_candidats&identifiant=id_saisie&id=$id_saisie'>$id_saisie</a></small></td><td><small>$id_table</small></td><td><small>$sexe </small><A title='$texteMaj' HREF='".generer_url_ecrire('odb_saisie')."&annee=$annee&step2=odb_candidats&identifiant=id_saisie&id=$id_saisie'>$prefixe <b>$nom</b><small> $prenoms</small></A></td>";
            if(!$filtreSerie) $ligne.="<td><small>$serie</small></td>";
            if(!$isChefEtablissement) {
               if(!$filtreEtablissement) $ligne.="<td title='$ville'><small>$etablissement</small></td>";
               if(!$filtreDepartement) $ligne.="<td><small>$departement</small></td>";
            }
            $candidats[$id_saisie]=$ligne;
         }
      } else $thead="<td>Aucun candidat trouv&eacute; pour ces crit&egrave;res en $annee</td>";
      echo "Vous pouvez choisir un candidat dans la liste ci-dessous<br/><br/>\n";
      echo "<A NAME='acces_clic'></A>";
      if($nb_lignes==0 && $nb_lignes_tot>0)
         $thead="Vos filtres vous obligent &agrave; <b><A HREF=\"javascript:document.forms['tri_filtre'].limit.value=0;document.forms['tri_filtre'].submit();\">cliquer ici pour retourner au premier &eacute;cran</A></b>";
      if($nb_lignes_tot>MAX_LIGNES) {
         if($limit>=MAX_LIGNES) $nav= "<b><A HREF=\"javascript:document.forms['tri_filtre'].limit.value=".($limit-MAX_LIGNES).";document.forms['tri_filtre'].submit();\">&lt;&lt; Pr&eacute;c.</A></b> :: ";
         $nav.= "$nb_lignes_tot enregistrements au total <b>[$limit-".($limit+$nb_lignes)."]</b>";
         if($nb_lignes_tot-$limit>MAX_LIGNES) $nav.= " :: <b><A HREF=\"javascript:document.forms['tri_filtre'].limit.value=".($limit+MAX_LIGNES).";document.forms['tri_filtre'].submit();\">Suiv. &gt;&gt;</A></b>\n";
      } else $nav= "$nb_lignes_tot enregistrements";
      echo "<FORM NAME='tri_filtre' METHOD='GET' ACTION='".generer_url_ecrire('odb_saisie')."#acces_clic' class='forml spip_xx-small'>"
         . "<input type='hidden' name='limit' value='$limit'/>\n"
         . "<input type='hidden' name='orderBy' value='$orderBy'/>\n"
         . "<INPUT TYPE='hidden' name='exec' value='odb_saisie'>\n"
         . "<div align='center'>\n$nav</div>\n"
         ;
      $ligne="Acc&egrave;s par clic &agrave; un candidat $annee\n"
            ."<TABLE CELLPADDING=5 CELLSPACING=0 WIDTH='100%'>\n<TR>\n"
            ."\t<td><label for='filtreSerie'>Filtre s&eacute;rie</label><br/>".formSelectQueryRef('S&eacute;rie','filtreSerie',"SELECT DISTINCT serie from odb_candidats where annee=$annee $andSerie order by serie",'serie','serie',$filtreSerie,"class='forml' onChange=\"document.forms['tri_filtre'].submit();\"")
            ."</td>\n\t<td><label for='filtreDepartement'>Filtre d&eacute;partement</label><br/>".formSelectQueryRef('D&eacute;partement','filtreDepartement',"SELECT DISTINCT departement from odb_candidats where annee=$annee $andDepartement order by departement",'departement','departement',$filtreDepartement,"class='forml' onChange=\"document.forms['tri_filtre'].submit();\"")
            ."</td>\n\t<td><label for='filtreEtablissement'>Filtre &eacute;tablissement</label><br/>".formSelectQueryRef('&Eacute;tablissement','filtreEtablissement',"SELECT DISTINCT etablissement from odb_candidats where annee=$annee $andEtablissement order by etablissement",'etablissement','etablissement',$filtreEtablissement,"class='forml' onChange=\"document.forms['tri_filtre'].submit();\"")
            ."</td>\n</TR>\n</TABLE>\n"
            ;
      if($isChefEtablissement) {
         echo $tab_auteur['nom'].", vous avez acc&egrave;s aux candidats de l'&eacute;tablissement <b>".$tab_auteur['nom_site']."</b>.<br/>\n";
         $ligne.=' de l\'&eacute;tablissement '.$tab_auteur['nom_site'];
      }
      echo odb_html_table($ligne,$candidats,$thead,$icone='message.gif');
      echo "<div align='center'>\n$nav</div></form>\n";
}

//fin_boite_info();
fin_cadre_relief();
fin_page();
exit;
}
?>
