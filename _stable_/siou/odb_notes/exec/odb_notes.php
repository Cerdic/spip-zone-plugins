<?php
/*
    This file is part of SIOU.

    SIOU is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    SIOU is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with SIOU; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    
    Copyright 2007, 2008 Agence Universitaire de la Francophonie - http://auf.org
    Auteur : Cedric PROTIERE - Proprietaire : AUF
*/
session_start();
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/charsets');

define('DIR_ODB_COMMUN',_DIR_PLUGINS."odb/odb_commun/");
define('DIR_ODB_CONTRIB',_DIR_PLUGINS."odb/odb_contrib/");
include_once(DIR_ODB_COMMUN.'inc-odb.php');
include_once(DIR_ODB_COMMUN.'inc-html.php');
include_once(DIR_ODB_COMMUN.'inc-referentiel.php');
include_once('inc-bdd.php');
include_once('inc-traitements.php');
define('ODB_BIO_OPERATEUR','Operateur de saisie');
define('ID_MATIERE_EF1',-1);
define('ID_MATIERE_EF2',-2);
define('ID_MATIERE_EPS',-3);
define('LIB_MATIERE_EF1','&Eacute;preuve Faculative 1');
define('LIB_MATIERE_EF2','&Eacute;preuve Faculative 2');
define('LIB_MATIERE_EPS','&Eacute;ducation Physique et Sportive');

setlocale(LC_TIME, "fr_FR");
global $debug, $PDF_A3_PAYSAGE, $PDF_A3_PORTRAIT;
$debug=false;

define(OK,"<SPAN style='color:#3C3;font-weight:bold;'>[OK]</SPAN>");
define(KO,"<SPAN style='color:#C33;font-weight:bold;'>[KO]</SPAN>");

$PDF_A3_PAYSAGE=array(
    //'shadecol' => array(0.1,0.8,0.5),
	'rowgap' => 125, //taille de l'espace entre le texte et les traits du tableau
	'width'  => 1150,
	'maxWidth' => 1150
    );

$PDF_A3_PORTRAIT=array(
    //'shadecol' => array(0.1,0.8,0.5),
	'rowgap' => 125, //taille de l'espace entre le texte et les traits du tableau
	'width'  => 575,
	'maxWidth' => 575
    );

/**
* exécuté automatiquement par le plugin au chargement de la page ?exec=odb_notes
*
* @author Cedric PROTIERE
*/
function exec_odb_notes() {
    $pass=getParametresODB('code');
    global $connect_statut, $connect_toutes_rubriques, $debug, $txt_gauche, $txt_droite, $txt_debug, $tab_referentiel, $odb_referentiel,$odb_mapping, $gauche, $deliberationCentre;
    if(isset($_REQUEST['reset'])) session_destroy();
    
    include_once(DIR_ODB_COMMUN."inc-referentiel.php");
    include_once(DIR_ODB_COMMUN."inc-regles_gestion.php");
    
    $tParam=getParametresODB();
    
    $tab_auteur=$GLOBALS["auteur_session"];
    $annee=isset($_REQUEST['annee'])?$_REQUEST['annee']:date("Y");
    
    echo "<SCRIPT SRC='".DIR_ODB_CONTRIB."boxover/boxover.js'></SCRIPT>\n";
    $imgInfo="<img src='".DIR_ODB_CONTRIB."boxover/info.gif' style='vertical-align:middle'>";
    
    $r_jury=$_REQUEST['jury'];
    
    debut_page(_T('Saisie des notes'), "", "");
    echo "<br />\n";
    gros_titre(_T('Office Du Baccalaur&eacute;at'));
    $tab_auteur=$GLOBALS["auteur_session"];
    
    if ($debug) {
	echo "<A HREF='#fin_debug'>Sauter les infos de debug</A>\n";
	echo "_POST<pre style='text-align:left;'>";
	print_r($_POST);
	echo "</pre><hr/>";
	echo "<A NAME='fin_debug'></A>\n";
    }
    
    debut_cadre_relief( "", false, "", $titre = _T("Saisie des notes $annee"));
    //debut_boite_info();
    //echo '<br>';
    
    $REFERER=$_SERVER['HTTP_REFERER'];
    $REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
    $isPleinEcran=false;
    $isDessineCadrePrincipal=true;
    
    $gauche="<IMG SRC='"._DIR_PLUGIN_ODB_NOTES."/img_pack/logo_odb_150.png' alt='Office du bac' ALIGN='absmiddle'><br/><br/>\n";
    isAutorise(array('Admin','Notes'));
    
    $tJurys=getJurys($tab_auteur['login'],getStatutUtilisateur(),$annee);
    if(isAdmin()) {
	$isAdmin=true;
	$isOperateur=false;
    } else {
	$isAdmin=false;
	$isOperateur=false;
    }
    
    
    
    //////////////////////////////////// securite
    if($r_jury!='' && !in_array($r_jury,$tJurys))
	die(KO.' - Vous n\'&ecirc;tes pas autoris&eacute;(e) &agrave; saisir le jury '.$r_jury.', veuillez <A HREF="'.generer_url_ecrire('odb_notes').'">recommencer</A>');
    if(isset($_REQUEST['imprimer'])) {
	$tRefSerie=getReferentiel('serie');
	foreach(array('jury','serie','id_serie') as $col)
	    $$col=$_REQUEST[$col];
	if($id_serie=='') {
	    	$id_serie=$tRefSerie[$serie];
	} elseif ($serie=='') {
		$serie=$tRefSerie[$id_serie];
	}
	$isPleinEcran=false;
	$titrePrincipal="Impressions pour le jury $jury s&eacute;rie $serie $annee";
	$selectJury="<SELECT NAME='jury' class='fondo' onChange=\"document.forms['form_resultats'].submit();\">\n";
	foreach($tJurys as $sJury) {
	    if($jury==$sJury) $selected='selected';else $selected='';
	    $selectJury.="\t<OPTION VALUE='$sJury' $selected>$sJury</OPTION>\n";
	}
	$selectJury.="</SELECT>\n";
	$tSeries=getSeriesFromJury($jury,$annee);
	$selectSerie="<SELECT NAME='id_serie' class='fondo' onChange=\"document.forms['form_resultats'].submit();\">\n\t<OPTION VALUE=''>-=[S&eacute;rie]=-</OPTION>\n";
	foreach($tSeries as $iIdSerie=>$sSerie) {
	    if($id_serie==$iIdSerie) $selected='selected';else $selected='';
	    $selectSerie.="\t<OPTION VALUE='$iIdSerie' $selected>$sSerie</OPTION>\n";
	}
	$selectSerie.="</SELECT>\n";
	$msg="<table class='spip'>\n<form name='form_resultats' action='".generer_url_ecrire('odb_notes')."' method='get' class='spip_xx-small'/>\n".
	"<tr><th>Jury</th><th>&nbsp;</th></tr>\n".
	"<tr><th>$selectJury</th><td><input type='submit' name='ok' value='ok' class='fondo'/></td></tr>\n".
	"<input type='hidden' name='exec' value='odb_notes'/>\n".
	"<input type='hidden' name='imprimer' value='auto'/>\n</FORM>\n";
	$msg.="<tr><th colspan=2>Retour aux <A HREF='".generer_url_ecrire('odb_notes')."&jury=$jury&annee=$annee&resultats'>r&eacute;sultats</A></th></tr>\n";
	$msg.="</TABLE>\n";
	if(isset($tParam["_delib1_$annee"][$r_jury])) {
	    $deliberation=2;
	    $msg.=afficherImpressions($jury,$serie,$annee,$tSeries);
	} else {
	    $deliberation=1;
	    $msg.="Vous n'avez pas acc&egrave;s aux impressions du jury $jury, veuillez choisir un autre jury svp.";
	}
    } elseif(isset($_REQUEST['historique'])) {
	//////////////////////////////////////////////// historique (candidat)
	$isPleinEcran=false;
	$isDessineCadrePrincipal=false;
	$titrePrincipal="Historique du candidat $id";
	foreach(array('id','annee','urlRetour','msgRetour') as $col)
	    $$col=$_REQUEST[$col];
	$urlRetour=urldecode($urlRetour);
	if($id!='') {
	    $msg.=afficherHistorique($id,$annee);
	} else $msg.=KO." - Erreur inconnue - ne peut afficher l'historique\n";
	if($msgRetour!='') {
	    $msg.="Navigation rapide : <b><A HREF='$urlRetour'>$msgRetour</A></b>\n<br/>\n";
	}
    } elseif(isset($_REQUEST['resultats'])) {
	//////////////////////////////////////////////// resultats (serie, jury)
	$tRefSerie=getReferentiel('serie');
	foreach(array('jury','serie','id_serie') as $col)
	    $$col=$_REQUEST[$col];
	if($id_serie=='') {
	    $id_serie=$tRefSerie[$serie];
	} elseif ($serie=='') {
	    $serie=$tRefSerie[$id_serie];
	}
	$isPleinEcran=true;
	$titrePrincipal="R&eacute;sultats des notes du jury $jury s&eacute;rie $serie";
	$selectJury="<SELECT NAME='jury' class='fondo' onChange=\"document.forms['form_resultats'].id_serie.value='';document.forms['form_resultats'].submit();\">\n";
	foreach($tJurys as $sJury) {
	    if($jury==$sJury) $selected='selected';else $selected='';
	    $selectJury.="\t<OPTION VALUE='$sJury' $selected>$sJury</OPTION>\n";
	}
	$selectJury.="</SELECT>\n";
	$tSeries=getSeriesFromJury($jury,$annee);
	$selectSerie="<SELECT NAME='id_serie' class='fondo' onChange=\"document.forms['form_resultats'].submit();\">\n\t<OPTION VALUE=''>-=[S&eacute;rie]=-</OPTION>\n";
	foreach($tSeries as $iIdSerie=>$sSerie) {
	    if($id_serie==$iIdSerie) $selected='selected';else $selected='';
	    $selectSerie.="\t<OPTION VALUE='$iIdSerie' $selected>$sSerie</OPTION>\n";
	}
	$selectSerie.="</SELECT>\n";
	$msg="<table class='spip' width='300'>\n<form name='form_resultats' action='".generer_url_ecrire('odb_notes')."' method='get' class='spip_xx-small'/>\n".
	"<tr><th>Jury</th><th>S&eacute;rie</th><th>&nbsp;</th></tr>\n".
	"<tr><th>$selectJury</th><td>$selectSerie</td><td><input type='submit' name='ok' value='ok' class='fondo'/></td></tr>\n".
	"<input type='hidden' name='exec' value='odb_notes'/>\n".
	"<input type='hidden' name='resultats' value='auto'/>\n</FORM>\n";
	if(isset($tParam["_delib1_$annee"][$r_jury])) {
	    if(isset($_REQUEST['total'])) {
		$deliberation=0;
		if($id_serie!='') $extra="<hr size=1/><A HREF='".generer_url_ecrire('odb_notes')."&jury=$jury&serie=$serie&resultats'><b>Filtrer les notes</b> &agrave; la d&eacute;lib&eacute;ration en cours</A>\n";
	    } else {
		$nbCandidatsOral=getNbCandidats($annee,$r_jury,'Oral');
		if($nbCandidatsOral>1) $deliberation=3;
		else $deliberation=2;
		if($id_serie!='') $extra="<hr size=1/><A HREF='".generer_url_ecrire('odb_notes')."&jury=$jury&serie=$serie&resultats&total'>Afficher <b>toutes</b> les notes</A>\n";
	    }
	    //include_once(DIR_ODB_COMMUN.'inc-html.php');
	    $msg.="<tr><td>".vignette('pdf')."</td><td colspan='2'>Vous &ecirc;tes autoris&eacute;(e) &agrave; <b><A HREF='".generer_url_ecrire('odb_notes')."&jury=$r_jury&serie=$serie&annee=$annee&imprimer'>acc&eacute;der aux impressions PDF</A></b><br/><b>Note :</b> acc&eacute;dez &agrave; ce module si les r&eacute;sultats vous semblent incorrects$extra</td></tr>\n";
	} else {
	    $deliberation=1;
	}
	$msg.="</TABLE>\n";
	if($id_serie!='') {
	    $msg.=afficherNotes($jury,$id_serie,$annee,'',$deliberation,false);
	    $msg.="Navigation rapide : <b><A HREF='".generer_url_ecrire('odb_notes')."&jury=$jury&serie=$serie'>mati&egrave;res du jury $jury, s&eacute;rie $serie</A></b>\n";
	} else $msg.="Veuillez choisir une s&eacute;rie pour le jury $jury\n";
    } elseif(isset($_REQUEST['step3']) || isset($_REQUEST['step4'])) {
	//////////////////////////////////////////////// step 4 + step 3 : formulaire de saisie des notes
	foreach(array('type','id_anonyme','note','jury','serie','id_serie','coeff','matiere','id_matiere') as $col)
	    $$col=urldecode($_REQUEST[$col]);
	//print_r($_REQUEST);
	$r_type=$type;
	$msg='';
	$deliberation=guessDeliberation($annee, $jury, $tParam);
	
	$gauche.="<br/><br/>\nNavigation rapide\n<ul class='tout-site'>\n<li class='sec'><A class='titre' href='".generer_url_ecrire('odb_notes')."&jury=$jury&serie=$serie&id_serie=$id_serie&step2=manuel'>Mati&egrave;re</A></li>\n".
	"<li class='sec'><A class='titre' href='".generer_url_ecrire('odb_notes')."&jury=$jury&serie=$serie&id_serie=$id_serie&resultats=manuel'>R&eacute;sultats jury $jury</A></li>\n".
	"<br/><li class='sec'><b><A class='titre' href='../spip.php?action=logout&logout=prive'>Se d&eacute;connecter</A></B></li>\n</ul>\n";
	$gauche2="- Pour enregistrer une note, tapez [<b>Entr&eacute;e</b>] au lieu de cliquer sur le bouton pour gagner du temps<br/><br/>- Saisissez un tiret [<b>-</b>] comme note pour signifier qu'un candidat n'a pas compos&eacute;";
	//echo $id_anonyme;
	if(isset($_REQUEST['step4'])) {
		//////////////////////step 4 : ajout/modification d'une note
		$id=$id_anonyme;
		$deliberation=guessDeliberation($annee,$jury,$tParam);
		$typeId=($deliberation==1)?'id_anonyme':'id_table';
		$sql1="INSERT into odb_histo_notes (id_table, id_anonyme, annee, id_matiere, note, type, coeff, operateur, maj)  \n".
		      "\t(SELECT id_table, id_anonyme, annee, id_matiere, note, type, coeff, operateur, maj from odb_notes \n"
		     ."\twhere $typeId='$id' and annee=$annee and id_matiere=$id_matiere and type='$type' and note is not null)";
		if(in_array($type,array('Pratique','Ecrit'))) {
			$sql="UPDATE odb_notes SET note=$note, operateur='".$GLOBALS['auteur_session']['login']."', maj=NOW()\n"
			."WHERE $typeId='$id' and annee=$annee and id_matiere=$id_matiere and type='$type'";
		} else {
			$id_table=$id;
			$id_anonyme=getIdAnonyme($annee,$id_table);
			$sql="REPLACE into odb_notes (id_table,id_anonyme,annee,jury,id_serie,id_matiere,note,type,coeff,operateur,maj) VALUES\n"
				."\t('$id_table','$id_anonyme',$annee,$jury,$id_serie,$id_matiere,$note,'$type',$coeff,'".$GLOBALS['auteur_session']['login']."',NOW())";
		}
		
		//echo "<pre>$sql</pre>";
		if($note!='') {
			if($note>20)
				$msg.='<small>'.KO." - Vous avez essay&eacute; de saisir la note <b>$note</b>, qui est sup&eacute;rieure &agrave; 20. <b>Saisie ignor&eacute;e</b></small><br/>\n";
			elseif($note=='')
				$msg.='<small>'.KO." - Note inconnue. <b>Saisie ignor&eacute;e</b></small><br/>\n";
			else {
				odb_query($sql1,__FILE__,__LINE__);
				odb_query($sql,__FILE__,__LINE__);
				//echo "<pre>$sql</pre>";
				$id=getIdTableHumain($id);
				$msg.='<small>'.OK." - Le candidat <b>$id</b> a eu ".($coeff==0?"+<b>$note</b>":"<b>$note</b><small>/20</small> <small>(soit ".($note*$coeff).'/'.(20*$coeff).")</small>")." en <b>$matiere</b> <small>($type)</small></small><br/>\n";
			}
		}
	}
	///// fin step 4
	$msg.="<TABLE class='spip' width='100%'>\n<tr class='row_even'>\n".
	"<th>Jury $jury";
	if($isOperateur) $msg.=" (Centre $deliberationCentre)";
	$msg.="</th><th>S&eacute;rie $serie - $r_type</th><th style='text-align:right;'>Mati&egrave;re : ".($matiere)."</th></tr></table>\n";
	$msg.="<A NAME='saisie'></A>\n<table class='spip' width='100%'>\n<tr>\n";
	
	$isEF=false;
	if($deliberation==1) {
	    // sous anonymat
	    $sql="SELECT id_table id, note, coeff\n FROM odb_notes\n"
		." WHERE annee=$annee and id_matiere=$id_matiere and jury=$jury and id_serie=$id_serie and type='$type'\n ORDER BY id";
	    // maintenant il faut
	    // - recuperer les id_anonymes et les gerer
	    // - supprimer la gestion qui depend du $type puisqu'il est maintenant connu
	} else {
		// sans anonymat
		switch($type) {
			case 'Divers':
				switch($id_matiere) {
					case ID_MATIERE_EF1:
						$isEF=true;
						$champ='ef.ef,';
						$from="odb_ref_ef ef, odb_decisions decis,";
						$where="and can.ef1 = ef.id and decis.id_table=rep.id_table and decis.delib1='Admissible' and decis.annee=$annee";
						break;
					case ID_MATIERE_EF2:
						$isEF=true;
						$champ='ef.ef,';
						$from="odb_ref_ef ef, odb_decisions decis,";
						$where="and can.ef2 = ef.id and decis.id_table=rep.id_table and decis.delib1='Admissible' and decis.annee=$annee";
						break;
					case ID_MATIERE_EPS:
						$champ='eps.eps,';
						$from="odb_ref_eps eps, odb_decisions decis,";
						$where="and can.eps=eps.id and eps.eps='Apte' and decis.id_table=rep.id_table and decis.delib1='Admissible' and decis.annee=$annee";
						break;
					default: die(KO." - Cas impr&eacute;vu : id_matiere=$id_matiere dans les divers");
				}
				$sql = "SELECT $champ rep.jury, dep.departement, eta.etablissement centre, ser.serie, rep.id_table id, DECODE(rep.id_anonyme,'$pass') id_anonyme, notes.note, 1 coeff\n"
				. " from $from odb_candidats can, odb_ref_etablissement eta, odb_ref_serie ser, odb_ref_departement dep, odb_repartition rep\n"
				. " left join odb_notes notes on (rep.id_table=notes.id_table and notes.annee=$annee and notes.id_matiere=$id_matiere and notes.type='$type') \n"
				. " where can.serie=$id_serie $where \n"
				. " and eta.id=rep.id_etablissement and dep.id= eta.id_departement and ser.id=can.serie "
				. " and can.id_saisie=rep.id_saisie AND can.annee=$annee and rep.annee=$annee and rep.jury=$jury\n"
				. " ORDER BY jury, departement, centre, ser.serie, id_anonyme"
				;
				break;
			case 'Oral':
				$sql = "SELECT rep.jury, dep.departement, eta.etablissement centre, ser.serie, rep.id_table, DECODE(rep.id_anonyme,'$pass') id_anonyme, notes.note, exa.coeff\n"
				. " from odb_ref_examen exa, odb_candidats can, odb_ref_etablissement eta, odb_ref_serie ser, odb_ref_departement dep, odb_decisions decis, odb_repartition rep\n left join odb_notes notes on (rep.id_table=notes.id_table and notes.annee=$annee and notes.id_matiere=$id_matiere and notes.type='$type') \n"
				. " where exa.id_matiere=$id_matiere and exa.annee=$annee and exa.id_serie=$id_serie and exa.type='$type'\n"
				. " and eta.id=rep.id_etablissement and dep.id= eta.id_departement and ser.id=can.serie "
				. " and can.id_saisie=rep.id_saisie AND can.annee=$annee and rep.annee=$annee and can.serie=$id_serie and rep.jury=$jury\n"
				. " and decis.id_table=rep.id_table and decis.annee=$annee and decis.delib1='Admissible' and (decis.delib2='Oral' or decis.delib2='Reserve')\n"
				. " ORDER BY jury, departement, centre, ser.serie, id_anonyme, duree, examen";
				break;
			default:
				$sql="SELECT id_table id\n FROM odb_notes\n WHERE annee=$annee and id_matiere=$id_matiere and jury=$jury and id_serie=$id_serie and type='$type'";
		}
	}
	//echo $sql;
	$result=odb_query($sql,__FILE__,__LINE__);
	while($row=mysql_fetch_array($result)) {
		foreach(array('id','note','coeff') as $col) $$col=$row[$col];
		$tNotes[$id]=$note;
	}
	$msg.="\t<th>$type ";
	$msg.=$isEF?'(bonus)':"(coeff $coeff)";
	$msg.="</th>\n</tr>\n<tr>\n";
	
	/////

	$isSelected=false;
	$selectNotes="<SELECT name='id_anonyme' class='fondo' onChange=\"document.forms['form_$type'].note.value='';document.forms['form_$type'].note.focus();\">".
	"<OPTION value=''>-=[Candidat]=-</OPTION>";
	foreach ($tNotes as $id_anonyme => $note) {
		if(!$isSelected && $note=='') { // n'a pas deja ete selected
			$selected='selected';
			
			$id_anonyme_selected=$id_anonyme;
			$isSelected=true;
		} else $selected='';
		if($note!='') {
			if($note<0) $aff_note=' -&gt; N/C &lt;- ';
			elseif($isEF) $aff_note=" (+$note)";
			else $aff_note=" ($note/20)";
		} else $aff_note='';
		$id_anonyme_aff=getIdTableHumain($id_anonyme);
		$selectNotes.="<OPTION $selected value='$id_anonyme'>$id_anonyme_aff $tEf[$id_anonyme] $aff_note</OPTION>\n";
	}
	$selectNotes.="</SELECT>\n";
	if($isEF) {
		$inputNote="<INPUT name='note' size=2 maxlength=2 onKeyUp=\"if(isNaN(parseInt(this.value))) this.value='';if(this.value>5) {alert('Un candidat ne peut avoir plus de 5 points de bonus par EF, veuillez saisir cette note de nouveau');this.value='';}document.forms['form_$type'].note_coeff.value='+'+this.value\" value='$note_selected[$type]' class='fondo'/>\n";
		$inputNoteCoeff="<INPUT name='note_coeff' size=2 maxlength=2 value='+".((int)$note_selected)."' style='text-align:right;border:0px;none;#fff;' onFocus='blur();'/>\n";
	} else {
		$inputNote="<INPUT name='note' size=2 maxlength=2 onKeyUp=\"if(this.value!='-' && isNaN(parseInt(this.value))) this.value='';if(this.value>20) {alert('Un candidat ne peut avoir plus de 20/20, veuillez saisir cette note de nouveau');this.value='';}document.forms['form_$type'].note_coeff.value=this.value*$coeff\" value='$note_selected' class='fondo'/>\n";
		$inputNoteCoeff="<INPUT name='note_coeff' size=2 maxlength=3 value='".((int)$note_selected*$coeff)."' style='text-align:right;border:0px;none;#fff;' onFocus='blur();'/>\n";
	}
	$msg.="\t<td style='vertical-align:top;'>".
	"<form name='form_$type' class='spip_xx-small' method='post' action='".generer_url_ecrire('odb_notes')."' ".
	"onSubmit=\"if(document.forms['form_$type'].id_anonyme.value=='') {alert('Veuillez choisir le candidat dont vous souhaitez modifier la note ($type)\\navant de valider');return false;} else if(document.forms['form_$type'].note.value=='-') {document.forms['form_$type'].note.value='-1';return true;} else if(document.forms['form_$type'].note.value=='') {alert('Veuillez saisir une note dans le champ adequat');document.forms['form_$type'].note.focus();return false;} else if(isNaN(document.forms['form_$type'].note_coeff.value)) {alert('Veuillez saisir une note correcte ($type)\\navant de valider');return false;document.forms['form_$type'].note_coeff.value='';document.forms['form_$type'].note_coeff.focus();} ".($type!='Divers'?"else if(document.forms['form_$type'].note.value=='0' || document.forms['form_$type'].note.value=='00' || document.forms['form_$type'].note.value=='0.') {return confirm('Vous avez mis 0 au candidat en $type, ce qui est eliminatoire\\nEtes-vous sur(e) de vous ?');}":'')."\">\n".
	"<table class='spip' width='90%'>\n<tr>\n".
	"\t<td>Candidat</td>\n\t<td>$selectNotes</td>\n".
	"\t<td rowspan=2><INPUT TYPE='submit' name='step4' value='Ok\n$type' class='fondo'/></td>\n</tr>\n".
	"<tr>\n\t<td>Note</td>\n\t<td>$inputNote".($isEF?'':'<small>/20</small>').$inputNoteCoeff.($isEF?'':"<small>/".($coeff*20)."</small>")."</td>\n".
	"</tr>\n</table>\n";
	//"<tr>\n\t<td colspan=2><INPUT TYPE='submit' name='step4' value='Enregistrer $type' class='forml'/></td>\n</tr>\n</table>\n";
	$tNotes=array_reverse($tNotes,true);
	$msg.="<br/>Notes $type (ordre invers&eacute;)<br/>\n".
	"<table class='spip' width='90%'>\n<tr><th>Candidat</th><th>".($isEF?'Bonus':'Note <small>/20</small>')."</th><th>".($isEF?'&Eacute;p. Fac.':"Note <small>/".(20*$coeff)."</small>")."</th>\n</tr>\n";
	foreach ($tNotes as $id_anonyme => $note) {
		if($note!='') {
			if($note<0) {
				$note='<span style="color:#f00;font-weight:bold;" title="Non Connue">N/C</span>';
				$note_coeff='';
			} else {
				$noteReelle=$note;
				if($isEF) {
					$note="+<b>$note</b>";
					$note_coeff=$tEf[$id_anonyme];
				} else {
					$note_coeff=($note*$coeff)."/".(20*$coeff);
					$note="<b>$note</b><small>/20</small>";
				}
			}
			$msg.="<tr><td>".
			"<A href=\"javascript:;\" onclick=\"leForm=document.forms['form_$type'];leForm.id_anonyme.value='$id_anonyme';leForm.note.value='$noteReelle';leForm.note.select();\" title='Modifier la note du candidat $id_anonyme'>"
			.getIdTableHumain($id_anonyme)."</A></td><td>$note</td><td><small>$note_coeff</small></td></tr>\n";
		}
	}
	$msg.="</table>\n</td>\n";
	$exec=$_REQUEST['exec'];
	foreach(array('matiere','id_matiere','jury','serie','id_serie','type','coeff') as $var)
		$msg.="<input type='hidden' name='$var' value='".$$var."'/>\n";
	$msg.="</form>\n";

	$msg.="</tr>\n</table>\n";
	/////
	
	/*
	foreach(array('Ecrit','Pratique','Oral','Divers') as $type) {
	    ${"is$type"}=false;
	    if($id_matiere>0) {
		if ($type=='Oral') {
		    $sql = "SELECT rep.jury, dep.departement, eta.etablissement centre, ser.serie, rep.id_table, DECODE(rep.id_anonyme,'$pass') id_anonyme, notes.note, exa.coeff\n"
		    . " from odb_ref_examen exa, odb_candidats can, odb_ref_etablissement eta, odb_ref_serie ser, odb_ref_departement dep, odb_decisions decis, odb_repartition rep\n left join odb_notes notes on (rep.id_table=notes.id_table and notes.annee=$annee and notes.id_matiere=$id_matiere and notes.type='$type') \n"
		    . " where exa.id_matiere=$id_matiere and exa.annee=$annee and exa.id_serie=$id_serie and exa.type='$type'\n"
		    . " and eta.id=rep.id_etablissement and dep.id= eta.id_departement and ser.id=can.serie "
		    . " and can.id_saisie=rep.id_saisie AND can.annee=$annee and rep.annee=$annee and can.serie=$id_serie and rep.jury=$jury\n"
		    . " and decis.id_table=rep.id_table and decis.annee=$annee and decis.delib1='Admissible' and (decis.delib2='Oral' or decis.delib2='Reserve')\n"
		    . " ORDER BY jury, departement, centre, ser.serie, id_anonyme, duree, examen";
		    //echo "<pre>$sql</pre>\n";
		} else
		$sql = "SELECT rep.jury, dep.departement, eta.etablissement centre, ser.serie, rep.id_table, DECODE(rep.id_anonyme,'$pass') id_anonyme, notes.note, exa.coeff\n"
		. " from odb_ref_examen exa, odb_candidats can, odb_ref_etablissement eta, odb_ref_serie ser, odb_ref_departement dep, odb_repartition rep\n left join odb_notes notes on (rep.id_table=notes.id_table and notes.annee=$annee and notes.id_matiere=$id_matiere and notes.type='$type') \n"
		. " where exa.id_matiere=$id_matiere and exa.annee=$annee and exa.id_serie=$id_serie and exa.type='$type'\n"
		. " and eta.id=rep.id_etablissement and dep.id= eta.id_departement and ser.id=can.serie "
		. " and can.id_saisie=rep.id_saisie AND can.annee=$annee and rep.annee=$annee and can.serie=$id_serie and rep.jury=$jury\n"
		. " ORDER BY jury, departement, centre, ser.serie, id_anonyme, duree, examen"
		;
	    } elseif($id_matiere<0) {
		if($type=='Divers') {
		    switch($id_matiere) {
			case ID_MATIERE_EF1:
			    $champ='ef.ef,';
			    $from="odb_ref_ef ef, odb_decisions decis,";
			    $where="and can.ef1 = ef.id and decis.id_table=rep.id_table and decis.delib1='Admissible' and decis.annee=$annee";
			    break;
			case ID_MATIERE_EF2:
			    $champ='ef.ef,';
			    $from="odb_ref_ef ef, odb_decisions decis,";
			    $where="and can.ef2 = ef.id and decis.id_table=rep.id_table and decis.delib1='Admissible' and decis.annee=$annee";
			    break;
			case ID_MATIERE_EPS:
			    $champ='eps.eps,';
			    $from="odb_ref_eps eps, odb_decisions decis,";
			    $where="and can.eps=eps.id and eps.eps='Apte' and decis.id_table=rep.id_table and decis.delib1='Admissible' and decis.annee=$annee";
			    break;
			default: die(KO." - Cas impr&eacute;vu : id_matiere=$id_matiere dans les divers");
		    }
		    $sql = "SELECT $champ rep.jury, dep.departement, eta.etablissement centre, ser.serie, rep.id_table, DECODE(rep.id_anonyme,'$pass') id_anonyme, notes.note, 1 coeff\n"
		    . " from $from odb_candidats can, odb_ref_etablissement eta, odb_ref_serie ser, odb_ref_departement dep, odb_repartition rep\n".
		    "  left join odb_notes notes on (rep.id_table=notes.id_table and notes.annee=$annee and notes.id_matiere=$id_matiere and notes.type='$type') \n"
		    . " where can.serie=$id_serie $where \n"
		    . " and eta.id=rep.id_etablissement and dep.id= eta.id_departement and ser.id=can.serie "
		    . " and can.id_saisie=rep.id_saisie AND can.annee=$annee and rep.annee=$annee and can.serie=$id_serie and rep.jury=$jury\n".
		    " ORDER BY jury, departement, centre, ser.serie, id_anonyme";
		    //echo "<pre>$sql</pre>";
		} else $sql="SELECT 1 from odb_param where 1=0";
	    } else die(KO.' - Matiere introuvable');
	    $result=odb_query($sql,__FILE__,__LINE__);
	    //echo "<hr/>$type<br/><pre>$sql</pre><br/>";
	    while($row=mysql_fetch_array($result)) {
		foreach(array('ef','id_anonyme','id_table','note','coeff') as $col) {
		    $$col=$row[$col];
		}
		$id_table=getIdTableHumain($id_table);
		if($type=='Oral' || $type=='Divers') {
		    $id=$id_table;
		    $tIdAnonyme[$id_table]=$id_anonyme;
		}
		else $id=$id_anonyme;
		${"is$type"}=true;
		${"t$type"}[$id]=$note;
		if($id_matiere==ID_MATIERE_EF1 || $id_matiere==ID_MATIERE_EF2) {
		    $tCoeff[$type]=0;
		    $tEf[$id]=$ef;
		    $isEf=true;
		}
		else {
		    $tCoeff[$type]=$coeff;
		    $isEf=false;
		}
	    }
	    if(${"is$type"}) {
		$msg.="\t<th>$type ";
		if($isEf) {
		    $msg.='(bonus)';
		}
		else {
		    $msg.="(coeff $tCoeff[$type])";
		}
		$msg.="</th>\n";
	    }
	    //print_r(${"t$type"});
	}
	$msg.="</tr>\n<tr>\n";
	$cpt=0;
	foreach(array('Ecrit','Pratique','Oral','Divers') as $type) {
	    //$cpt++;echo "<br/>$type $cpt";
	    if(${"is$type"}) {
		$isSelected=false;
		${"select$type"}="<SELECT name='id_anonyme' class='fondo' onChange=\"document.forms['form_$type'].note.value='';document.forms['form_$type'].note.focus();\">".
		"<OPTION value=''>-=[Candidat]=-</OPTION>";
		foreach (${"t$type"} as $id_anonyme => $note) {
		    if(!$isSelected && $note=='') { // n'a pas deja ete selected
			$selected='selected';
			$id_anonyme_selected[$type]=$id_anonyme;
			$isSelected=true;
		    } else $selected='';
		    if($note!='') {
			if($note<0) $aff_note=' -&gt; N/C &lt;- ';
			elseif($isEf) $aff_note=" (+$note)";
			else $aff_note=" ($note/20)";
		    }	else $aff_note='';
		    ${"select$type"}.="<OPTION $selected value='$id_anonyme'>$id_anonyme $tEf[$id_anonyme] $aff_note</OPTION>\n";
		}
		${"select$type"}.="</SELECT>\n";
		if($isEf) {
		    $inputNote="<INPUT name='note' size=2 maxlength=2 onKeyUp=\"if(isNaN(parseInt(this.value))) this.value='';if(this.value>5) {alert('Un candidat ne peut avoir plus de 5 points de bonus par EF, veuillez saisir cette note de nouveau');this.value='';}document.forms['form_$type'].note_coeff.value='+'+this.value\" value='$note_selected[$type]' class='fondo'/>\n";
		    $inputNoteCoeff="<INPUT name='note_coeff' size=2 maxlength=2 value='+".((int)$note_selected[$type])."' style='text-align:right;border:0px;none;#fff;' onFocus='blur();'/>\n";
		} else {
		    $inputNote="<INPUT name='note' size=2 maxlength=2 onKeyUp=\"if(this.value!='-' && isNaN(parseInt(this.value))) this.value='';if(this.value>20) {alert('Un candidat ne peut avoir plus de 20/20, veuillez saisir cette note de nouveau');this.value='';}document.forms['form_$type'].note_coeff.value=this.value*$tCoeff[$type]\" value='$note_selected[$type]' class='fondo'/>\n";
		    $inputNoteCoeff="<INPUT name='note_coeff' size=2 maxlength=3 value='".((int)$note_selected[$type]*$tCoeff[$type])."' style='text-align:right;border:0px;none;#fff;' onFocus='blur();'/>\n";
		}
		$msg.="\t<td style='vertical-align:top;'>".
		"<form name='form_$type' class='spip_xx-small' method='post' action='".generer_url_ecrire('odb_notes')."' ".
		"onSubmit=\"if(document.forms['form_$type'].id_anonyme.value=='') {alert('Veuillez choisir le candidat dont vous souhaitez modifier la note ($type)\\navant de valider');return false;} else if(document.forms['form_$type'].note.value=='-') {document.forms['form_$type'].note.value='-1';return true;} else if(document.forms['form_$type'].note.value=='') {alert('Veuillez saisir une note dans le champ adequat');document.forms['form_$type'].note.focus();return false;} else if(isNaN(document.forms['form_$type'].note_coeff.value)) {alert('Veuillez saisir une note correcte ($type)\\navant de valider');return false;document.forms['form_$type'].note_coeff.value='';document.forms['form_$type'].note_coeff.focus();} ".($type!='Divers'?"else if(document.forms['form_$type'].note.value=='0' || document.forms['form_$type'].note.value=='00' || document.forms['form_$type'].note.value=='0.') {return confirm('Vous avez mis 0 au candidat en $type, ce qui est eliminatoire\\nEtes-vous sur(e) de vous ?');}":'')."\">\n".
		"<table class='spip' width='90%'>\n<tr>\n".
		"\t<td>Candidat</td>\n\t<td>".${"select$type"}."</td>\n".
		"\t<td rowspan=2><INPUT TYPE='submit' name='step4' value='Ok\n$type' class='fondo'/></td>\n</tr>\n".
		"<tr>\n\t<td>Note</td>\n\t<td>$inputNote".($isEf?'':'<small>/20</small>').$inputNoteCoeff.($isEf?'':"<small>/".($tCoeff[$type]*20)."</small>")."</td>\n".
		"</tr>\n</table>\n";
		//"<tr>\n\t<td colspan=2><INPUT TYPE='submit' name='step4' value='Enregistrer $type' class='forml'/></td>\n</tr>\n</table>\n";
		${"t$type"}=array_reverse(${"t$type"},true);
		$msg.="<br/>Notes $type (ordre invers&eacute;)<br/>\n".
		"<table class='spip' width='90%'>\n<tr><th>Candidat</th><th>".($isEf?'Bonus':'Note <small>/20</small>')."</th><th>".($isEf?'&Eacute;p. Fac.':"Note <small>/".(20*$tCoeff[$type])."</small>")."</th>\n</tr>\n";
		foreach (${"t$type"} as $id_anonyme => $note) {
		    if($note!='') {
			if($note<0) {
			    $note='<span style="color:#f00;font-weight:bold;" title="Non Connue">N/C</span>';
			    $note_coeff='';
			} else {
			    $noteReelle=$note;
			    if($isEf) {
				$note="+<b>$note</b>";
				$note_coeff=$tEf[$id_anonyme];
			    } else {
				$note_coeff=($note*$tCoeff[$type])."/".(20*$tCoeff[$type]);
				$note="<b>$note</b><small>/20</small>";
			    }
			}
			$msg.="<tr><td>".
			"<A href=\"javascript:;\" onclick=\"leForm=document.forms['form_$type'];leForm.id_anonyme.value='$id_anonyme';leForm.note.value='$noteReelle';leForm.note.select();\" title='Modifier la note du candidat $id_anonyme'>"
			."$id_anonyme</A></td><td>$note</td><td><small>$note_coeff</small></td></tr>\n";
		    }
		}
		$msg.="</table>\n</td>\n";
		$exec=$_REQUEST['exec'];
		$coeff=$tCoeff[$type];
		foreach(array('matiere','id_matiere','jury','serie','id_serie','type','coeff') as $var)
		    $msg.="<input type='hidden' name='$var' value='".$$var."'/>\n";
		$msg.="</form>\n";
	    }
	}
	$msg.="</tr>\n</table>\n";*/
    } else {
	//////////////////////////////////////////////// step 1 + step 2 : formulaire d'acces aux notes
	$msgTmp='';
	if($r_jury=='') {
	    $jury_aff=$tJurys[0];
	} else $jury_aff=$r_jury;
	$msgTmp.="jury $jury_aff ";
	$r_serie=$_REQUEST['serie'];
	if($r_serie!='') {
	    $msgTmp.=" ($r_serie)";
	}
	$gauche.="<br/><br/>\nNavigation rapide\n<ul class='tout-site'>\n".
	"<li class='sec'><A class='titre' href='".generer_url_ecrire('odb_notes')."&jury=$jury_aff&serie=$r_serie&resultats=manuel'>R&eacute;sultats $msgTmp</A></li>\n".
	"<br/><li class='sec'><b><A class='titre' href='../spip.php?action=logout&logout=prive'>Se d&eacute;connecter</A></B></li>\n</ul>\n";
	
	$tdJurys="<INPUT type='hidden' name='jury' value='".$_REQUEST['jury']."'/>\n";
	$styleBouton='font-weight:normal;border-width:1px;';
	$styleBoutonActif='font-weight:bold;border-width:2px;';
	
	$tSeries=getSeries($annee);
	if(!is_array($tSeries[$jury]))
		die(KO." - Aucun jury d&eacute;fini pour vous en $annee");
	if($_REQUEST['jury']>0) {
	    // on commence par la pour pouvoir afficher la bulle des series au survol des jurys
	    $r_jury=$_REQUEST['jury'];
	    $r_serie=$_REQUEST['serie'];
	    if(count($tSeries[$r_jury])==1) {
		// une seule serie => on la choisit
		$r_serie=$tSeries[$r_jury][0];
	    }
	    $tdSeries="<INPUT type='hidden' name='serie' value='$r_serie'/>\n";
	    foreach($tSeries[$r_jury] as $serie) {
		if($serie==$r_serie) $style=$styleBoutonActif;
		else $style=$styleBouton;
		$tdSeries.="<INPUT type='submit' class='forml' name='step2' value='$serie' style='$style' ".
		"onClick=\"document.forms['form_notes'].serie.value='$serie';document.forms['form_notes'].matiere.value='';\"/><br/>\n";
	    }
	    if($r_serie!='') {
		$deliberation=guessDeliberation($annee,$r_jury,$tParam);
		for($i=1;$i<$deliberation;$i++) {
			//echo OK." maj $i<br/>";
			odb_maj_decisions($annee,$r_jury,3,$i);
		}
		
		$r_matiere=$_REQUEST['matiere'];
		$sql="SELECT id_matiere, matiere, id_serie, type from odb_ref_examen exa, odb_ref_serie ser, odb_ref_matiere mat\n where exa.annee=$annee and ser.serie='$r_serie' and ser.id=exa.id_serie and mat.id=exa.id_matiere\n ORDER BY matiere";
		$result=odb_query($sql,__FILE__,__LINE__);
		$id_serie=0;
		while($row=mysql_fetch_array($result)) {
		    $id_matiere=$row['id_matiere'];
		    $type=$row['type'];
		    $tMatieres[$type][$id_matiere]=$row['matiere'];
		    $id_serie=$row['id_serie'];
		}
		$tMatieres['Divers'][ID_MATIERE_EF1]=LIB_MATIERE_EF1;
		$tMatieres['Divers'][ID_MATIERE_EF2]=LIB_MATIERE_EF2;
		$tMatieres['Divers'][ID_MATIERE_EPS]=LIB_MATIERE_EPS;
		//echo $sql;
		$tdMatieres="<INPUT type='hidden' name='matiere' value='$r_matiere'/>\n".
		"<INPUT type='hidden' name='id_matiere' value='$id_matiere'/>\n".
		"<INPUT type='hidden' name='id_serie' value='$id_serie'/>\n".
		"<INPUT type='hidden' name='type' value=''/>\n";
		switch($deliberation) {
		    case 1:$tTypesDeliberation=array('Pratique','Ecrit');break;
		    case 2:$tTypesDeliberation=array('Pratique','Ecrit','Divers');break;
		    case 3:$tTypesDeliberation=array('Pratique','Ecrit','Divers','Oral');break;
		    default:die(KO." - Deliberation $deliberation introuvable");
		}
		$nbCandidatsSerie=getNbCandidatsNotes($annee,$r_jury,$id_serie);
		if($nbCandidatsSerie==0) {
			if($isAdmin) die(KO." - Veuillez demander &agrave; <b>".getNomComplet(getParametresODB('login_anonymes'))."</b> d'<b><A HREF='".generer_url_ecrire('odb_param')."'>initialiser les notes sous anonymat $annee</A></b>");
			die(KO." - Les notes anonymes ne sont pas encores pr&ecirc;tes pour le jury $r_jury en $annee");
		}
		foreach($tTypesDeliberation as $type) {
		    if(count($tMatieres[$type])>0) {
				$nbCandidatsSerie=getNbCandidatsNotes($annee,$r_jury,$id_serie,0,$type);
				$tdMatieres.="<table style='border:1px solid #aae;' class='spip' width='100%'>\n<tr style='text-align:right;background-color:#dde;margin-bottom:2px;padding:1px;border:1px solid #aae;'><th colspan=2>$type</th></tr>\n";
				foreach($tMatieres[$type] as $id_matiere=>$matiere) {
					if($type=='Divers') {
						if($id_matiere==ID_MATIERE_EPS) {
							$nbCandidatsSerie=getNbCandidatsEPS($annee,$r_jury,$id_serie);
						} else {
							$nbCandidatsSerie=getNbCandidatsEF($annee,$r_jury,$id_serie,$id_matiere);
						}
					} 
					if($nbCandidatsSerie>0) {
						if($matiere==$r_matiere) $style='font-weight:bold;';
						else $style='font-weight:normal;';
						if($id_matiere<0) $style.='background-color:#ece;';
						$nbCandidatsMatiere=getNbCandidatsNotes($annee,$r_jury,$id_serie,$id_matiere);
						//echo "$id_matiere : $nbCandidatsMatiere<br/>";
						if($nbCandidatsMatiere==$nbCandidatsSerie) $couleur='#0a0';
						elseif($nbCandidatsMatiere==0) $couleur='#f00';
						else $couleur='rgb(200,'.(80+round(100*$nbCandidatsMatiere/$nbCandidatsSerie)).',0);';
						$nbCandidatsMatiere="<b style='color:$couleur;'>$nbCandidatsMatiere</b>";
						$tdMatieres.="<tr><td><INPUT type='submit' class='forml' name='step3' value=\"$matiere\" style='$style' ".
						"onClick=\"document.forms['form_notes'].type.value='$type';document.forms['form_notes'].id_matiere.value='$id_matiere';document.forms['form_notes'].matiere.value='".urlencode($matiere)."';\"/></td><td width=10>$nbCandidatsMatiere/$nbCandidatsSerie</td></tr>\n";
					} else $tdMatieres.="<tr><td colspan=2>Aucun candidat ne passe <b>$matiere</b></td></tr>";
				}
				$tdMatieres.="</table>\n";
		    }
		}
	    } else $tdMatieres="Veuillez choisir une s&eacute;rie";
	} else $tdSeries='Veuillez s&eacute;lectionner un jury';
	
	foreach($tJurys as $jury) {
	    if($jury==$_REQUEST['jury']) $style=$styleBoutonActif;
	    else $style=$styleBouton;
	    if($isAdmin) {
		$class='fondo'; if($jury % 10==0)
		$style.='color:#f00;';
	    } else $class='forml';
	    if(count($tSeries[$jury])>1)
		$s='s : ';else $s='';
	    $titleJury="header=[Jury $jury]	body=[S&eacute;rie$s ".implode(', ',$tSeries[$jury])."]	fade=[on] fadespeed=[0.5]"; $tdJurys.="<INPUT title=\"$titleJury\" type='submit' class='$class'	name='step2' value='Jury ".str_pad($jury,3,'0',PADDING_LEFT)."' style='$style' ".
	    "onClick=\"document.forms['form_notes'].jury.value='$jury';document.forms['form_notes'].serie.value=''; document.forms['form_notes'].matiere.value='';\"/><br/>\n";
	}
	
	$formNotes="<form name='form_notes' action='".generer_url_ecrire('odb_notes')."' method='get' class='spip_xx-small'>\n"
	. "<INPUT type='hidden' name='exec' value='odb_notes'/>\n"
	. "<TABLE class='spip' width='100%'>\n<tr><th>Jury</th><th>S&eacute;rie</th><th>Mati&egrave;re</th></tr>\n"
	. "<tr>\n\t<td width='20%' style='vertical-align:top;'>$tdJurys</td>"
	. "\n\t<td width='15%' style='vertical-align:top;'>$tdSeries </td>"
	. "\n\t<td width='50%' style='vertical-align:top;'>$tdMatieres </td>\n</tr>\n"
	. "</form>\n"
	;
	//$selectSerie="<OPTION VALUE=''>-=[S&eacute;rie]=-</OPTION>\n";
	//$selectMatiere="<OPTION VALUE=''>-=[Mati&egrave;re]=-</OPTION>\n";
	$msg=$formNotes;
    }
    
    if(!$isPleinEcran) {
	debut_gauche();
	debut_boite_info();
	echo $gauche;
	fin_boite_info();
	if(strlen($gauche2)>0) {
	    echo '<p/>';
	    debut_cadre_relief("", false, "", $titre = _T("Conseils"));
	    echo $gauche2;
	    fin_cadre_relief();
	}
	if($isAdmin) odb_raccourcis('');
	creer_colonne_droite();
    }
    debut_droite();
    if($titrePrincipal=='')	$titrePrincipal="Saisie des notes $annee ($deliberationCentre)";
    if($isDessineCadrePrincipal) debut_cadre_relief("", false, "", $titre = _T($titrePrincipal));
    else debut_boite_info();
    echo $msg;
    if($isDessineCadrePrincipal) fin_cadre_relief();
    else fin_boite_info();
    if($r_type=='') {
	if($isEcrit) $r_type='Ecrit';
	elseif($isPratique) $r_type='Pratique';
	elseif($isOral) $r_type='Oral';
	else $r_type='Divers';
    }
    if(isset($_REQUEST['step3']) || isset($_REQUEST['step4'])) {
	echo putJavascript("document.forms['form_$r_type'].note.focus();");
    }
    
    fin_cadre_relief();
    fin_page();
    exit;
}
?>
