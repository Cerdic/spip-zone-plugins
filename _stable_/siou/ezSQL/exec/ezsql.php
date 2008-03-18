<?php
/*
 *      Copyright 2008 Ghislain VLAVONOU, Yannick EDAHE, Cedric PROTIERE
 *      
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 3 of the License, or
 *      (at your option) any later version.
 *      
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/charsets');

include('inc-traitements.php');
setlocale(LC_TIME, "fr_FR");
define(OK,"<SPAN style='color:#3C3;font-weight:bold;'>[OK]</SPAN>");
define(KO,"<SPAN style='color:#C33;font-weight:bold;'>[KO]</SPAN>");

// exécuté automatiquement par le plugin au chargement de la page ?exec=ezsql
function exec_ezsql() {
	global $connect_statut, $connect_toutes_rubriques, $debug, $txt_gauche, $txt_droite, $txt_debug, $tab_referentiel, $odb_referentiel,$odb_mapping;

	$annee=date("Y");
	$aide="Cliquez sur une des tables ou tapez une requ&ecirc;te ici puis cliquez sur le bouton [Ex&eacute;cuter]";	
	
	debut_page(_T('ezSQL - Export CSV'), "", "");
//	echo "<br /><br />";
	gros_titre(_T('ezSQL - Export CSV'));
	
	if(isset($_REQUEST['table'])) {
		$isExecute=true;
		$sqlNormale="SELECT *\n FROM ".$_REQUEST['table']."\n LIMIT 0,30";
	}
	elseif(isset($_REQUEST['requete'])) {
		$isExecute=true;
		$sqlNormale = stripslashes($_REQUEST['requete']);
	} else {
		$isExecute=false;
		$sqlNormale=$aide;
	}
	$nomTable='resultat';
	
	if($isExecute) {
		$sqlNormale=ez_propre($sqlNormale);
		$sqlAff=str_replace('=',"<b style='color:#e70;'>=</b>",$sqlNormale);
		foreach(array(')','distinct(','uncompress(','compress(','encode(','decode(') as $mot) {
			// mots sans espace avant/apres => ne pas mettre n'importe quoi !
			$sqlAff=str_ireplace("$mot",strtoupper("<b style='color:#d90;'>$mot</b>"),$sqlAff);
		}
		foreach(array('IN','*') as $mot) {
			$sqlAff=str_ireplace(" $mot ",strtoupper(" <b style='color:#d90;'>$mot</b> "),$sqlAff);
		}
		foreach(array('select','insert','update','delete','replace','truncate','left join',
				'from','where','into','set','values','limit','and','table','order by','group by','having'
			) as $mot) {
			$sqlAff=str_ireplace(" $mot ",strtoupper("<br/> <b style='color:#c33;'> $mot</b> "),$sqlAff);
			$sqlNormale=str_ireplace(" $mot ",strtoupper("\n $mot "),$sqlNormale);
		}
		$sqlNormale=trim($sqlNormale);
		//$sqlAff=substr($sqlAff,6);
		list($typeSQL,$rien)=explode(' ',trim($sqlAff),2);
		$sqlAff="<b style='color:#c33;'>".strtoupper($typeSQL)."</b> $rien";
		
		list($typeSQL,$rien)=explode(' ',trim($sqlNormale),2);
		$sqlNormale=strtoupper($typeSQL)." $rien";
		switch(trim(strtolower($typeSQL))) {
			case 'select':
				$isSelect=true;
				$tmp=trim(stristr($sqlNormale,'from'));//requete apres from
				list($rien,$nomTable,$reste)=explode(' ',$tmp,3);
				$nomTable=trim($nomTable);
				break;
			default:
				$isSelect=false;
				break;
		}
		$nomFichier="$nomTable.csv";
	}
	debut_gauche();
	debut_boite_info();
		$r = ez_query("SELECT DATABASE()",__FILE__,__LINE__);
		$base = mysql_result($r,0);
		echo "Base <b>$base</b><br/><small>".mysql_get_host_info()."<br/>\n".mysql_get_server_info()."<br/>\n</small>\n";
		$sql="SHOW tables";
		$result=ez_query($sql,__FILE__,__LINE__);
		while($row=mysql_fetch_row($result)) {
			$table=$row[0];
			list($prefixe,$reste)=explode(strrchr($table,'_'),$table);
			if(substr_count($prefixe,'spip')>0) $prefixe='spip';
			if((trim(strtolower($table)))==trim(strtolower($nomTable))) {
				$sTableEnCours=$table;
				$table="<A class='table' HREF='".generer_url_ecrire('ezsql')."&table=$table' style='color:#000;'><b>$table</b></a>";
				$sql="SHOW columns from $nomTable";
				$result2=ez_query($sql,__FILE__,__LINE__);
				$cpt=0;
				while($row2=mysql_fetch_row($result2)) {
					$table.= "<br/><span title='".$row2[1]."'>&nbsp;&nbsp;".$row2[0]."</span>\n";
					switch(trim($row2[3])) {
						case 'PRI':
							$html="style='cursor:pointer;font-weight:bold;border-bottom:1px dotted #000;' title='Cl&eacute; primaire'";
							break;
						case '':
							$html="style='cursor:pointer;'";
							break;
						default:
							$html="style='cursor:pointer;border-bottom:1px dotted #000;' title='Cl&eacute; $row2[3]'";
					}
					$tBody[$cpt]="<td><span onclick=\"champ=document.forms['form_requete'].requete;champ.value+='$row2[0], ';champ.focus();\" $html>".$row2[0]."</span></td><td>".$row2[1]."</td><td>".$row2[3]."</td>";
					$cpt++;
				}
			} else
				$table="<A class='table' HREF='".generer_url_ecrire('ezsql')."&table=$table' style='color:#999;'>$table</A>";
			$tTable[$prefixe][]=$table;
		}
		echo "<dl id='groupes'>\n";
		foreach($tTable as $prefixe=>$t1) {
			echo "<br/>\n\t<dt style='font-weight:bold;font-size:12px;cursor:help;'>$prefixe</dt>\n";
			echo "\t<dd style='border:dotted 1px black;background-color:#ddd;'>".join('<br/>',$t1)."</dd>\n";
		}
		echo "</dl>\n";
	fin_boite_info();
	creer_colonne_droite();
	if($isSelect) {
		debut_boite_info();
			echo ez_html_table("<A href='javascript:;' title='Ajouter la table $sTableEnCours' onclick=\"champ=document.forms['form_requete'].requete;champ.value+='$sTableEnCours';champ.focus();\">$sTableEnCours</A>",$tBody,"<th>Colonne</th><th>Type</th>");
			$sql="SELECT count(*) from $sTableEnCours";
			$result=ez_query($sql,__FILE__,__LINE__);
			$nbRows=mysql_result($result,0,0);
			$s=($nbRows>1)?'s':'';
			echo "Contient <b>$nbRows</b> enregistrement$s<hr size=0/>\nCliquez sur un champ ci-dessus pour l'ajouter dans votre requ&ecirc;te";
		fin_boite_info();
	}
	debut_droite();
	debut_cadre_relief( "", false, "", $titre = _T('Requ&ecirc;te SQL'));

	//echo "<IMG SRC='"._DIR_PLUGIN_EZSQL."/img_pack/logo_odb.png' alt='Office du bac' ALIGN='absmiddle'><br><br>\n";

	echo "<form name='form_requete' method='POST' action='".generer_url_ecrire('ezsql')."'>\n";
	if($isExecute) echo "<small style='font-family:monospace;'>$sqlAff</small>\n";
	echo "<textarea name='requete' cols=100 rows=5 class='forml' style='color:#555;'>\n"
		.($sqlNormale)."</textarea>"
		;
	echo "<input name='submit' type='submit' value='Ex&eacute;cuter' class='fondo'>";
	//if($isExecute) echo "<input name='nom_requete' value='$nomFichier' style='border:1px dotted black;margin:1px;'/><input type='submit' name='enregistrer' value='Enregistrer cette requ&ecirc;te' class='fondo'/>\n";
	echo "</form>\n";

	if ($isExecute){
		$result = ez_query($sqlNormale,__FILE__,__LINE__);
		if($isSelect) {
			$nbLignes=mysql_num_rows($result);
			
			if($nbLignes>0) {

				//FIXME Preciser le repertoire du fichier csv
				$destination="../tmp/";
				$fichier = fopen($destination.$nomFichier, 'w+');
				$tRow=array();
				while($tRow[] = mysql_fetch_assoc($result));
				foreach($tRow[0] as $k=>$v)
					$tCol[$k]=ucwords(str_replace('_',' ',$k));
				fputcsv($fichier, (array)$tCol, "\t");
					
				$compteur=0; $min=min($nbLignes, 30);  
				$tbody=array();
				foreach($tRow as $ligne) {
					fputcsv($fichier, (array)$ligne, "\t");
					if($compteur++<$min) {
						$cptCol=0;
						foreach($ligne as $col) {
							if($cptCol<5) $tbody[$compteur].="<td><small>".wordwrap($col,60,'<br/>',true)."</small></td>";
							$cptCol++;
						}
					}
					//$tbody[] = "<td><small>".join('</small></td><td><small>',$ligne)."</small></td>";
				}
				fclose($fichier);
			} else {
				echo "Aucun enregistrement";
				$isSelect=false;
			}
		}
	}
	
	if($isExecute)	{
		echo "<small>".htmlentities(mysql_info())."</small><br/>";
		if($isSelect) {
			echo "<A HREF='$destination$nomFichier'>"
				."<IMG SRC='"._DIR_PLUGIN_EZSQL."/img_pack/csvimport-24.png' ALIGN='absmiddle'/>"
				." T&eacute;l&eacute;charger <b>$nomFichier</b> ($nbLignes lignes)</A>\n"
				;
		}
		else {
			$nb=mysql_affected_rows();
			$s=$nb>1?'s':'';
			echo "<b>$nb</b> ligne$s affect&eacute;e$s<br/>";
		}
	}
	fin_cadre_relief();
	if($isExecute && $isSelect) 
		echo '<br/>'.ez_html_table("Aper&ccedil;u de la requ&ecirc;te",$tbody,"<th><small>".join('</small></th><th><small>',array_slice($tCol,0,5))."</small></th>",'statistiques-24.gif');
	
	$aide=html_entity_decode($aide,ENT_COMPAT,'UTF-8');
	$jquery= <<<FINSCRIPT

$(document).ready(function() {
	$('#groupes').find('dd').hide().end().find('dt').click(function() {
		var suivant = $(this).next();
		suivant.slideToggle();
	});
	$("a[@class=table]").hover(function() {
		$(this).css("color","#222");
	}, function() {
		$(this).css("color","#999");
	});

	$("textarea[@name*=requete]").hover(function() {
		$(this).css("color", "#000");
		if(this.value=='$aide') this.value='';
		this.focus();
		//alert(this.value);
	}, function() {
		$(this).css("color", "#555");
		if(this.value=='') {
			this.value='$aide';
			this.blur();
		}
	});

	$("dd").hover(function() {
		$(this).css("border-style", "solid");
	}, function() {
		$(this).css("border-style", "dotted");
	});
});
FINSCRIPT;
	echo "<script type='text/javascript'><!--\n$jquery\n//-->\n</script>\n";
	fin_page();
	exit;
}


?>
