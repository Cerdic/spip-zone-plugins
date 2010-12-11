<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/


	function genespip_header_prive($flux){
		$flux .= '<link rel="stylesheet" type="text/css" href="'.direction_css(find_in_path('genespip.css')).'" />';
		return $flux;
	}

	function genespip_rediriger_javascript($url) {
		echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
		exit();
	}

	//Conversion de date français
	function genespip_datefr($date) {
		$split = split('-',$date); 
		$annee = $split[0];
		$mois = $split[1];
		$jour = $split[2];
		if ($annee==NULL){$annee='0000';}
		if ($mois==NULL){$mois='00';}
		if ($jour==NULL){$jour='00';}
		return $jour.'/'.$mois.'/'.$annee;
	} 
	//Conversion de date US
	function genespip_dateus($date) {
		$split = split('/',$date);
		$jour = $split[0];
		$mois = $split[1]; 
		$annee = $split[2];
		if ($annee==NULL){$annee='0000';}
		if ($mois==NULL){$mois='00';}
		if ($jour==NULL){$jour='00';}
		return $annee.'-'.$mois.'-'.$jour;
	}

	//*******************THEME du SITE*******************************
	function genespip_modif_theme($theme, $pub, $multilingue, $acces, $centans) {
		$update_theme = sql_update("spip_genespip_parametres", array("theme = '".$theme."', pub = ".$pub.", multilingue = ".$multilingue.", acces = ".$acces.", centans = ".$centans;
		$update_theme = spip_query($update_theme);
	}
	//***************************************************************


	//********************MAJ table spip_genespip_liste**************
	function genespip_maj_liste() {
		set_time_limit(0);
		echo "<br /><u>"._T('genespip:mise_a_jour_liste_eclair')."</u>";
		$date_update=date("Y-m-d");
		$result_individu = sql_select("id_individu, nom, count(id_individu) as comptenom", "spip_genespip_individu", "poubelle<>1 group by nom");
		while ($indi = spip_fetch_array($result_individu)) {
			$result_date_min = sql_select("date_evenement", "spip_genespip_individu,spip_genespip_evenements", "spip_genespip_individu.id_individu=spip_genespip_evenements.id_individu and nom = '".$indi['nom']."' and id_type_evenement='1' and date_evenement <> 0000-00-00 ORDER BY date_evenement ASC limit 0,1" );
			if (mysql_num_rows($result_date_min)!=0){
				while ($min = spip_fetch_array($result_date_min)) {
					$split = split('-',$min['date_evenement']);
					$date_min=$split[0];
				}
			}else{$date_min="?";}
			$result_date_max = sql_select("date_evenement", "spip_genespip_individu,spip_genespip_evenements", "spip_genespip_individu.id_individu=spip_genespip_evenements.id_individu and nom = '".$indi['nom']."' and id_type_evenement='1' and date_evenement <> 0000-00-00 ORDER BY date_evenement DESC limit 0,1" );
			if (mysql_num_rows($result_date_max)!=0){
				while ($max = spip_fetch_array($result_date_max)) {
					$split = split('-',$max['date_evenement']);
					$date_couverte=$date_min."-".$split[0];
				}
			}else{$date_couverte=$date_min."-?";}
			$result_liste = sql_select("*", "spip_genespip_liste", "nom = '".$indi['nom']."'";
			$result_liste = spip_query($result_liste);
			/*echo mysql_num_rows($result_liste);*/
			if (mysql_num_rows($result_liste)==0){
				$insert_liste = 'INSERT INTO spip_genespip_liste (nom, nombre, date_couverte, date_update)", "("'.$indi["nom"].'", '.$indi["comptenom"].', "'.$date_couverte.'", "'.$date_update.'")';
				/*echo $insert_liste."<br />";*/
				$insert_liste = spip_query($insert_liste) or die (_T('genespip:requete')." insert_liste"._T('genespip:invalide')."<br />");
			}else{
				while ($liste = spip_fetch_array($result_liste)) {
					if ($liste['nombre']!=$indi['comptenom']){
						$update_liste = sql_update("spip_genespip_liste", array("nombre = ".$indi['comptenom'].", date_couverte= '".$date_couverte."', date_update= '".$date_update."'", "nom = '".$indi['nom']."'";
						/*echo $update_liste."<br />";*/
						$update_liste = spip_query($update_liste) or die (_T('genespip:requete')." update_liste "._T('genespip:invalide')."<br />");
					}
				}
			}
		}
			$result_liste_inverse = sql_select("nom", "spip_spip_genespip_liste");
			while ($liste_inv = spip_fetch_array($result_liste_inverse)) {
				$result_individu_inverse = sql_select("nom", "spip_genespip_individu", "poubelle<>1 and nom = '".$liste_inv['nom']."'");
				if (mysql_num_rows($result_individu_inverse)==0){
					$delete_liste=mysql_query("DELETE", "spip_spip_genespip_liste", "('nom = '.$liste_inv["nom"]) or die (_T('genespip:requete')." delete_liste "._T('genespip:invalide')."")");
				}
			}
		echo "&nbsp;<font color='red'>OK</font><br />";
	}
	
	// ##Définition du journal##
	// 1 -> Nouvel individu
	// 2 -> Modification information individu
	// 3 -> Modification évènement
	// 4 -> Suppression évènement
	// 5 -> Ajout évènement
	// 6 -> Ajout portrait
	// 7 -> Ajout signature
	// 8 -> suppression individu

	//********************Gestion des fiches**************
	//Création fiche

function genespip_ajout_fiche() {
	$date_update=date("Y-m-d H:i:s");
	$date_update2=date("Y-m-d");
	$sexe=$_POST['sexe'].$_GET['sexe'];
	$nom=$_POST['nom'].$_GET['nom'];
	$prenom=$_POST['prenom'].$_GET['prenom'];
	$insert_fiche=sql_insert("spip_genespip_individu (nom ,prenom, sexe, id_auteur, date_update)", "('".addslashes($nom)."', '".addslashes($prenom)."', '".$sexe."', ".$GLOBALS['connect_id_auteur'].",'".$date_update."')";
	$insert_fiche = spip_query($insert_fiche);
	$id_individu=mysql_insert_id();
	// ### Journal ###
	$insert_journal=sql_insert("spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update)", "('creation fiche', '1', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')";
	$sqlJOURNAL =spip_query($insert_journal) or die (_T('genespip:requete')." JOURNAL "._T('genespip:invalide')."");
	genespip_maj_liste();
	return $id_individu;
}

//Modification d'une fiche - modif (22-03-2008)
  //Données générales
function genespip_modif_fiche($id_individu) {
$date_update=date("Y-m-d H:i:s");
$naissance=genespip_dateus($_POST['naissance']);
$deces=genespip_dateus($_POST['deces']);
$action_sql = sql_update("spip_genespip_individu", array("nom = '".addslashes($_POST['nom'])."', prenom = '".addslashes($_POST['prenom'])."', sexe ='".$_POST['sexe']."', metier = '".addslashes($_POST['metier'])."', enfant = '".$_POST['enfant']."', note = '".addslashes($_POST['note'])."', portrait='".$_POST['portrait']."', source= '".addslashes($_POST['source'])."', adresse= '".addslashes($_POST['adresse'])."', date_update= '".$date_update."', limitation= '".$_POST['limitation']."'", "id_individu = ".$id_individu;
$sqlmodif =spip_query($action_sql) or die (_T('genespip:requete')." update_fiche "._T('genespip:invalide')."");
// ### Journal ###
$insert_journal=sql_insert("spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update)", "('modification fiche', '2', '".$id_individu."', '".$GLOBALS['connect_id_auteur']."', '".$date_update."')";
$sqlJOURNAL =spip_query($insert_journal) or die (_T('genespip:requete')." JOURNAL "._T('genespip:invalide')."");

}
  //Modif Evenement  - create (22-03-2008)
function genespip_up_evt($id_individu,$id_type_evenement) {
$date_update=date("Y-m-d H:i:s");
$date_evenement=genespip_dateus($_POST['date_evenement']);
$action_sql = sql_update("spip_genespip_evenements", array("date_evenement ='".$date_evenement."', precision_date = '".$_POST['precision_date']."', id_lieu = '".$_POST['id_lieu']."', id_epoux = '".$_POST['id_epoux']."', date_update= '".$date_update."'", "id_type_evenement = ".$id_type_evenement." and id_epoux = '".$_POST['id_epoux']."' and id_individu = ".$id_individu;
$sqlup =spip_query($action_sql) or die (_T('genespip:requete')." update_evenement "._T('genespip:invalide')."");
if ($_POST['id_epoux']<>NULL){
$action_sql = sql_update("spip_genespip_evenements", array("date_evenement ='".$date_evenement."', precision_date = '".$_POST['precision_date']."', id_lieu = '".$_POST['id_lieu']."', id_epoux = '".$id_individu."', date_update= '".$date_update."'", "id_type_evenement = ".$id_type_evenement." and id_epoux = ".$id_individu." and id_individu = ".$_POST['id_epoux'];
$sqlup =spip_query($action_sql) or die (_T('genespip:requete')." update_evenement_epoux "._T('genespip:invalide')."");
}
// ### Journal ###
$insert_journal=sql_insert("spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update)", "('modification evenement', '3', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')";
$sqlJOURNAL =spip_query($insert_journal) or die (_T('genespip:requete')." JOURNAL "._T('genespip:invalide')."");
}
  //Supp Evenement  - create (22-03-2008)
function genespip_del_evt($id_evenement) {
$date_update=date("Y-m-d H:i:s");
$action_sql = "DELETE", "spip_genespip_evenements", "id_evenement = ".$id_evenement;
$sqldel =spip_query($action_sql) or die (_T('genespip:requete')." delete_evenement "._T('genespip:invalide')."");
// ### Journal ###
$insert_journal=sql_insert("spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update)", "('suppression evenement', '4', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')";
$sqlJOURNAL =spip_query($insert_journal) or die (_T('genespip:requete')." JOURNAL "._T('genespip:invalide')."");
}
  //Ajout Evenement  - create (22-03-2008)
function genespip_add_evt($id_individu) {
	$date_update=date("Y-m-d H:i:s");
	$date_evenement=genespip_dateus($_POST['date_evenement']);
	$action_sql=sql_insert("spip_genespip_evenements (id_individu, id_type_evenement, date_evenement ,precision_date, id_lieu, id_epoux, date_update)", "(".$id_individu.", ".$_POST['id_type_evenement'].", '".$date_evenement."', '".$_POST['precision_date']."', '".$_POST['id_lieu']."','".$_POST['id_epoux']."', '".$date_update."')";
	$sqladd =spip_query($action_sql) or die (_T('genespip:requete')." ajout_evenement "._T('genespip:invalide')."");
	if ($_POST['id_epoux']<>NULL){
		$action_sql=sql_insert("spip_genespip_evenements (id_individu, id_type_evenement, date_evenement ,precision_date, id_lieu, id_epoux, date_update)", "(".$_POST['id_epoux'].", ".$_POST['id_type_evenement'].", '".$date_evenement."', '".$_POST['precision_date']."', '".$_POST['id_lieu']."','".$id_individu."', '".$date_update."')";
		$sqladd =spip_query($action_sql) or die (_T('genespip:requete')." ajout_evenement_epoux "._T('genespip:invalide')."");
	}
	// ### Journal ###
	$insert_journal=sql_insert("spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update)", "('creation evenement', '5', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')";
	$sqlJOURNAL =spip_query($insert_journal) or die (_T('genespip:requete')." JOURNAL "._T('genespip:invalide')."");
}

//Modification d'une fiche - Ajout indicateur portrait
function genespip_modif_fiche_portrait($portrait,$id_individu,$format_portrait) {
$date_update=date("Y-m-d H:i:s");
$action_sql=sql_update("spip_genespip_individu", array("portrait = ".$portrait.", format_portrait = '".$format_portrait."', date_update= '".$date_update."'", "id_individu = ".$id_individu;
$sqlmodif =spip_query($action_sql) or die (_T('genespip:requete')." update_portrait "._T('genespip:invalide')."");
// ### Journal ###
if ($portrait==1){
$insert_journal=sql_insert("spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update)", "('ajout portrait', '6', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')";
$sqlJOURNAL =spip_query($insert_journal) or die (_T('genespip:requete')." JOURNAL "._T('genespip:invalide')."");
}
}
//Modification d'une fiche - Ajout indicateur signature
function genespip_modif_fiche_signature($signature,$id_individu,$format_signature) {
$date_update=date("Y-m-d H:i:s");
$action_sql=sql_update("spip_genespip_individu", array("signature = ".$signature.", format_signature = '".$format_signature."', date_update= '".$date_update."'", "id_individu = ".$id_individu;
$sqlmodif =spip_query($action_sql) or die (_T('genespip:requete')." update_signature "._T('genespip:invalide')."");
// ### Journal ###
if ($signature==1){
$insert_journal=sql_insert("spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update)", "('ajout signature', '7', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')";
$sqlJOURNAL =spip_query($insert_journal) or die (_T('genespip:requete')." JOURNAL "._T('genespip:invalide')."");
}
}

//Modification des parents
function genespip_modif_parent($id_individu) {
$date_update=date("Y-m-d H:i:s");
$action_sql=sql_update("spip_genespip_individu", array("pere = '".$_POST['pere']."', mere = '".$_POST['mere']."', date_update= '".$date_update."'", "id_individu = ".$id_individu;
$sqlmodif =spip_query($action_sql) or die (_T('genespip:requete')." update_parent "._T('genespip:invalide')."");
}
	//***************Lieu***********************
	//Modif Lieu  - create (25-03-2008)
	function genespip_up_lieu($id_lieu) {
		$action_sql = sql_update("spip_genespip_lieux", array("ville ='".addslashes($_POST['ville'])."', departement = '".addslashes($_POST['departement'])."', code_departement = '".$_POST['code_departement']."', region = '".addslashes($_POST['region'])."', pays= '".addslashes($_POST['pays'])."'", "id_lieu = ".$id_lieu;
		$sqlup =spip_query($action_sql) or die (_T('genespip:requete')." update_lieu "._T('genespip:invalide')."");
	}
	
	//Delete Lieu  - create (25-03-2008)
	function genespip_del_lieu($id_lieu) {
		$action_sql = "DELETE", "spip_genespip_lieux", "id_lieu = ".$id_lieu;
		$sqldel =spip_query($action_sql) or die (_T('genespip:requete')." delete_lieu "._T('genespip:invalide')."");
	}
	
	//Add Lieu  - create (25-03-2008)
	function genespip_add_lieu() {
		$action_sql=sql_insert("spip_genespip_lieux (ville, departement, code_departement , region, pays)", "('".addslashes($_POST['ville'])."', '".addslashes($_POST['departement'])."', '".$_POST['code_departement']."', '".addslashes($_POST['region'])."', '".addslashes($_POST['pays'])."')";
		$sqladd =spip_query($action_sql) or die (_T('genespip:requete')." ajout_lieu "._T('genespip:invalide')."");
	}

	//***************Corbeille***********************
	//Mise à la corbeille (en attente) d'une fiche
	function genespip_poubelle_fiche($id_individu) {
		$date_update=date("Y-m-d H:i:s");
		$action_sql=sql_update("spip_genespip_individu", array("poubelle = '".$_POST['poubelle']."'", "id_individu = ".$id_individu);
		$sqlmodif =spip_query($action_sql) or die (_T('genespip:requete')." update_poubelle "._T('genespip:invalide')."");
		echo "<font color='red'>".$date_update." : La fiche N&ordm;".$id_individu." a &eacute;t&eacute; mise &agrave; la poubelle.</font>";
		genespip_maj_liste();
		// ### Journal ###
		$insert_journal=sql_insert("spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update)", "('suppression fiche', '8', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')");
		$sqlJOURNAL =spip_query($insert_journal) or die (_T('genespip:requete')." JOURNAL "._T('genespip:invalide')."");
	}
	
	function genespip_supp_fiche($action) {
		if ($action=="Supprimer"){
			//****Suppression definitive
			$valeur=$_POST['action_fiche'];
			$nmax=count($valeur);
			for($i=0;$i!=$nmax;$i++)
			   {
			$sqldel=mysql_query("DELETE", "spip_genespip_individu", "id_individu = ".$valeur[$i]) or die (_T('genespip:requete')." delete_fiche "._T('genespip:invalide')."");
					echo "<font color='red'>"._T('genespip:fiche_no')." ".$valeur[$i]."</font><br />";
			$sqldel=mysql_query("DELETE", "spip_genespip_evenements", "id_individu = ".$valeur[$i]) or die (_T('genespip:requete')." delete_union2 "._T('genespip:invalide')."");
			$sqldel=mysql_query("DELETE", "spip_genespip_evenements", "id_epoux = ".$valeur[$i]) or die (_T('genespip:requete')." delete_union3 "._T('genespip:invalide')."");
					echo "<font color='red'>"._T('genespip:union_fiche_no')." ".$valeur[$i]."</font><br />";
			   }
		}elseif ($action=="Restaurer"){
			//****Restauration fiche
			$valeur=$_POST['action_fiche'];
			$nmax=count($valeur);
			for($i=0;$i!=$nmax;$i++)
			   {
			$date_update=date("Y-m-d H:i:s");
			$action_sql=sql_update("spip_genespip_individu", array("poubelle = 0", "id_individu = ".$valeur[$i]));
			$sqlmodif =spip_query($action_sql) or die (_T('genespip:requete')." update "._T('genespip:invalide')."");
					echo "<font color='red'>"._T('genespip:fiche_no')." ".$valeur[$i]." "._T('genespip:restaure')."</font><br />";
			   }
			genespip_maj_liste();
		}
	}

	//************************************************
	function genespip_nom_prenom($id_individu,$choix){
		if ($choix==1 or $choix==3){
			$result = sql_select("id_individu, nom, prenom", "spip_genespip_individu", "id_individu = ".$id_individu);
		}
		elseif ($choix==2){
			$result = sql_select("id_individu, nom, prenom", "spip_genespip_individu", "pere = ".$id_individu." or mere =".$id_individu);
		}
		$n=0;
		while ($fiche = spip_fetch_array($result)) {
			if ($n!=0){$detail .="<br />";}
			if ($choix==3){$detail=$fiche['nom']." ".$fiche['prenom'];}
			else {$detail .= "<a href=".generer_url_ecrire('fiche_detail')."&id_individu=".$fiche['id_individu'].">&raquo;&nbsp;".$fiche['nom']." ".$fiche['prenom']."</a>";}
			$n=$n+1;
		}
		return $detail;
	}

	//**************************DOCUMENTS********************
	//Ajout document
	function genespip_ajout_document($id_individu, $id_article) {
		$date_update = date("Y-m-d H:i:s");
		$requete_insert = sql_insert("spip_genespip_documents", "(id_individu, id_article)", "("$id_individu", "$id_article")");
		$insert_document = spip_query($requete_insert);
		echo "<br /><font color='red'>".$date_update." : "._T('genespip:nouvelle_liaison_document_realise')."</font>";
		$result = sql_select("*", "spip_articles", "id_article=".$id_article);
		while ($fiche = spip_fetch_array($result)) {
			if ($fiche['chapo']<>""){
				if (get_magic_quotes_gpc()==0){
					$chapo=addslashes($fiche['chapo'])."<br />";
				}else{
					$chapo=$fiche['chapo']."<br />";
				}
			}
			$chapo = $chapo."["._T('genespip:fiche_de')." ".genespip_nom_prenom($id_individu,3)."->spip.php?page=individu&id_individu=".$id_individu."]";
			$requete = sql_update("spip_articles", array("chapo = '".$chapo."'", "id_article=".$id_article);
			$update_article = spip_query($requete) or die (_T('genespip:requete')." ajout_lien "._T('genespip:invalide')."");
		}
	}

	//Suppression lien document
	function genespip_supp_document($id_individu, $id_article) {
		$sqldel=spip_query("DELETE", "spip_genespip_documents", "id_individu = ".$id_individu." and id_article = ".$id_article) or die (_T('genespip:requete')." delete_documents "._T('genespip:invalide')."");
	}
	
	//Selection article
	function genespip_choix_article(){
		$result = sql_select("id_article, titre", "spip_articles");
		  $art .= "<select size='1' name='id_article' size='3'>";
		  $art .= "<option value='0'>---</option>";
		while ($fiche = spip_fetch_array($result)) {
			$art .= "<option value='".$fiche['id_article']."'>".$fiche['titre']."</option>";
		}
		$art .= "</select>";
		return $art;
	}

	//détail article sélectionner
	function genespip_liste_document($id_individu){
		$url_action_document=generer_url_ecrire('fiche_document');
		$url_detail_document=generer_url_ecrire('articles');
		$result = sql_select("spip_genespip_documents.id_individu, spip_articles.id_article, spip_articles.titre", "spip_genespip_documents,spip_articles", "spip_genespip_documents.id_individu = ".$id_individu." and spip_genespip_documents.id_article=spip_articles.id_article");
		$art .= "<table width='100%'>";
		while ($fiche = spip_fetch_array($result)) {
			$art .= "<tr><td><a href='".$url_detail_document."&id_individu=".$fiche['id_individu']."&id_article=".$fiche['id_article']."'>".$fiche['id_article']."/ ".$fiche['titre']."</a></td>";
			$art .= "<td><a href='".$url_action_document."&action=delete&id_individu=".$fiche['id_individu']."&id_article=".$fiche['id_article']."'><img border='0' noborder src='"._DIR_PLUGIN_GENESPIP."img_pack/del.gif' alt='"._T('genespip:supprimer')."' /></a></td>";
		}
		$art .= "</table>";
		return $art;
	}

	//Tester présence lien article avant dans exec/articles et exec/article_edit
	function genespip_tester_document($id_individu,$id_article,$page){
		$url_action_document=generer_url_ecrire('fiche_document');
		$affiche .= "toto";
		if (isset($id_article)==NULL){
			$affiche .= icone_horizontale(_T('&rsaquo;&rsaquo;&nbsp;<:genespip:retour_sur_fiche_sans_enregistrer:>&nbsp;&rsaquo;&rsaquo;'), $url_action_document."&id_individu=".$id_individu, 'rien.gif', '');
		}else{
			$result = sql_select("*", "spip_genespip_documents", "id_individu = ".$id_individu." and id_article = ".$id_article);
			$compte = mysql_num_rows($result);
			if ($compte==0){
				$affiche .= icone_horizontale(_T('&rsaquo;&rsaquo;&nbsp;<:genespip:cliquer_ici_pour_lier_article_avec_fiche:>&nbsp;&rsaquo;&rsaquo;'), $url_action_document."&id_individu=".$id_individu."&id_article=".$id_article."&action=Valider", 'rien.gif', 'creer.gif');
			}else{
				if ($page=="articles"){
					$affiche .= icone_horizontale(_T('&rsaquo;&rsaquo;&nbsp;<:genespip:retour_sur_fiche:>&nbsp;&rsaquo;&rsaquo;'), $url_action_document."&id_individu=".$id_individu, 'rien.gif', '');
				}
				if ($page=="articles_edit"){
					$affiche .= icone_horizontale(_T('&rsaquo;&rsaquo;&nbsp;<:genespip:retour_sur_fiche_sans_enregistrer:>&nbsp;&rsaquo;&rsaquo;'), $url_action_document."&id_individu=".$id_individu, 'rien.gif', '');
				}
			}
		}
		return $affiche;
	}
	
	//Verif rubrique documents
	function genespip_creer_rubrique(){
		$date_update=date("Y-m-d H:i:s");
		$result = sql_select("spip_genespip_parametres.rubrique, spip_rubriques.id_rubrique, spip_rubriques.titre", "spip_genespip_parametres, spip_rubriques", "spip_rubriques.id_rubrique = spip_genespip_parametres.rubrique");
		$compte = mysql_num_rows($result);
		if ($compte==0){
			$insert_rubrique = sql_insert("spip_rubriques", "(titre, statut, date, idx, statut_tmp, date_tmp)", "('Documents, actes', 'publie', '".$date_update."', 'oui', 'publie', '".$date_update."')");
			$id_rubrique .=spip_insert_id();
			$insert_rubrique = sql_update("spip_genespip_parametres", array("rubrique" => sql_quote($id_rubrique)));
		}else{
		while ($fiche = spip_fetch_array($result)) {
				$id_rubrique .=$fiche['id_rubrique'];
			}
		}
		return $id_rubrique;
	}

	//Nouvelle fiche formulaire - modif 23/05/2008 (ajout sexe)
	function genespip_nouvelle_fiche($url_action_accueil){
		$ret .= "<a name='images'></a>";
		$ret .= debut_cadre_relief("petition-24.gif", true, "creer.gif", _T('genespip:nouvelle_fiche'));
		$ret .= "<form action='".$url_action_accueil."' method='post'>";
		$ret .= "<table><tr><td>";
		$ret .= _T('genespip:nom').":</td><td><input type='text' name='nom' size='12' /></td></tr><tr><td>";
		$ret .= _T('genespip:prenom').":</td><td><input type='text' name='prenom' size='12' /></td></tr>";
		$ret .= "<tr><td colspan='2'>M&nbsp;<input type='radio' name='sexe' value='0' id='1' checked />";
		$ret .= "&nbsp;F&nbsp;<input type='radio' name='sexe' value='1' id='2' /></td></tr>";
		$ret .= "<tr><td colspan='2'><input type='submit' name='submit' value='"._T('genespip:valider')."' size='8' /></td></tr></table>";
		$ret .= "<input type='hidden' name='edit' value='nouvellefiche' size='8' />";
		$ret .= "</form>";
		$ret .= fin_cadre_relief(true);
		return $ret;
	}

	// menu_lang plat sans URL sur la langue sélectionnée
	function url_lang ($langues) {
		include_spip('inc/charsets');    
		$texte = '';
		$tab_langues = explode(',', $GLOBALS['meta']['langues_multilingue']);
		while ( list($clef, $valeur) = each($tab_langues) )
		if ($valeur == $GLOBALS['spip_lang']) {
			if ($valeur=="en"){$flag="gb";}else{$flag=$valeur;}
			$drapeau="<img src='"._DIR_PLUGIN_GENESPIP."img_pack/pays/".$flag.".png'>";
			$texte .= '<span style="border:1px solid #626262;background-color:#EAEAEA;padding:1px">'.$drapeau.'</span>';
		}
		else {
			if ($valeur=="en"){$flag="gb";}else{$flag=$valeur;}
			$drapeau="<img src='"._DIR_PLUGIN_GENESPIP."img_pack/pays/".$flag.".png'>";
			$texte .= '<span style="padding:1px">';
			$texte .= '<a href="'.parametre_url(generer_url_action('cookie'), 'url', parametre_url(self(true), '&'), '&').'&amp;var_lang='.$valeur.'" alt="'.traduire_nom_langue($valeur).'">'.$drapeau.'</a>';
			$texte .= '</span>';
		}
		return $texte;
	}
//fin

include_spip('inc/genespip_balise');
?>
<script language="javascript" type="text/javascript">
function update_flag1(objet){
 if (objet.value)
  document.getElementById("img_flags1").src = '/genespip/plugins/genespip/img_pack/pays/'+objet.value+'.png';
}
function update_flag2(objet){
 if (objet.value)
  document.getElementById("img_flags2").src = '/genespip/plugins/genespip/img_pack/pays/'+objet.value+'.png';
}
</script>
