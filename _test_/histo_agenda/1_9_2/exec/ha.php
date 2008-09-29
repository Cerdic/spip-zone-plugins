<?php 

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/commun");
include_spip("inc/agenda_gestion");

function ha_verifier_admin() 
{
  global $connect_statut, $connect_toutes_rubriques;
  return (($connect_statut == '0minirezo') AND $connect_toutes_rubriques);
}

function ha_mktime($dt)
{
	$ret = mktime(0,0,0,intval(substr($dt,4,2)),
		      intval(substr($dt,6,2)),intval(substr($dt,0,4)));
	return $ret;
}

// renvoie la liste des fichiers trouves
// dans le rep des archives
function ha_get_fichiers()
{
	$liste = array("---"=>"---");
	$liste["maintenant"] = "Maintenant";
	$ardir = _DIR_PLUGIN_HA_ARCH;
	$d = dir($ardir);
	$lf = array();
	while (false !== ($entry = $d->read())) 
	{
		if (preg_match("/^ar_([0-9]*)\.txt$/", $entry,$mt))
		{
			$dt = $mt[1];
			$tm = ha_mktime($dt);
			$lf[$dt] = date("d/m/Y", $tm);
		}
	}
	krsort($lf);	
	return $liste + $lf;
}

function ha_voir_modifications($dt1, $dt2)
{
	if (!ha_verifier_admin())
		return;

	$ardir = _DIR_PLUGIN_HA_ARCH;
	$titre = _T('ha:titre_aide');
	$contenu = _T("ha:aide");

	if ( ($dt1!="maintenant") && 
	    ( (($dt2!="maintenant") && ($dt1<$dt2)) || ($dt2=="maintenant") ) )
	{
		$contenu = "La date en haut doit &ecirc;tre sup&eacute;rieure &agrave; la date du bas...";
		return array($titre, $contenu);
	}

 	if (($dt1 == "maintenant") || ($dt2 == "maintenant"))
	{
		// regenere le fichier de mainteant
		// datelim permet de regenerer tous les evenements
		// a la date donnee (date avec laquelle on veut faire
		// les comparaisons) sinon tous les evenement disparus
		// depuis lors seront traces dans les modifs
		$datelim = "";
		if (preg_match("/^[0-9]+$/", $dt1))
			$datelim = $dt1;
		if (preg_match("/^[0-9]+$/", $dt2))
			$datelim = $dt2;
		$fn = $ardir."/ar_maintenant.txt";
		// attention, ici on utilise le squelette histom a la place 
		// de histo
		$fdi = fopen("http://".$_SERVER["SERVER_NAME"]."/spip.php?page=histom&var_mode=recalcul&datelim=".$datelim, "r");
		$fdo = fopen($fn, "w");
		$deb = false;
		while($line = fread($fdi, 1024))
		{
			// ne commence a tracer qu'au debut
			// de la page 
			if (($pos = strpos($line, "XXX<div id=\"sep\"")) > 0)
			{
				if ($deb == false)
					$line = substr($line, $pos);
				$deb = true;
			}
			if ($deb)
				fwrite($fdo, $line);
		}
		fclose($fdo);
		fclose($fdi);
	}
	else if ($dt1 != "maintenant")
	{
		// il faut regerer le fichier dt1 avec des XXX pour que
		// le diff fonctionne bien
		$fno = $ardir."/ar_".$dt1.".txt";
		$fnd = $ardir."/ar_".$dt1.".comp.txt";
		$fdo = fopen($fno, "r");
		$fdd = fopen($fnd, "w");
		if ($fdd == false)
		{
			$contenu = "Erreur cr&eacute;ation fichier";
			return array($titre, $contenu);
		}
		while(!feof($fdo))
		{
			$line = fgets($fdo);
			// attention, en cas de modif du squelette histo.html,
			// il faut verifier que la ligne suivante reste valide
			$line = str_replace("<div id=\"sep\" ", "XXX<div id=\"sep\" ", $line);
			fwrite($fdd, $line);
		}
		fclose($fdo);
		fclose($fdd);
		
	}

	$fn1 = $fn2 = "";
	if (preg_match("/^[0-9]+$/", $dt1) || ($dt1 == "maintenant") )
	{
		if ($dt1 == "maintenant")
			$fn1 = $ardir."/ar_maintenant.txt";
		else	
			$fn1 = $ardir."/ar_".$dt1.".comp.txt";
	}
	if (preg_match("/^[0-9]+$/", $dt2) || ($dt2 == "maintenant") )
		$fn2 = $ardir."/ar_".$dt2.".txt";

	if (is_file($fn1) && is_file($fn2) )
	{
		// fais la diff entre les ficheirs en utilisant 
		// htmldiff sous python
		$dts1 = $dts2 = "maintenant";
		if ($dt1 != "maintenant") 
			$dts1 = date("d/m/Y", ha_mktime($dt1));
		if ($dt2 != "maintenant") 
			$dts2 = date("d/m/Y", ha_mktime($dt2));
		$titre = _T('ha:titre_modification').
			$dts1._T('ha:titre_modification_et').$dts2;
		$output = array();
		exec("python "._DIR_PLUGIN_HA."/exec/diff.py ".
				$fn2." ".$fn1, $output);
		$contenu = implode("\n", $output);

		// remplace les balises XXX (utilisee pour
		// separer les composants lors du diff)
		$contenu = str_replace("<ins class=\"diff\">XXX</ins>", "", $contenu);
		$contenu = str_replace("XXX", "", $contenu);
	}
	
	$fn = $ardir."/ar_".$dt1.".comp.txt";
	if (is_file($fn))
		unlink($fn);
	$contenu = trim($contenu);
	if (empty($contenu))
		$contenu = "Aucune diff&eacute;rence...";

	//$fd = fopen("/tmp/debug.log", "w");
	//fwrite($fd, $contenu);
	//fclose($fd);

	return array($titre, $contenu);
}


// PAS UTILISE  (TEST) : les evenements sont
// edites en appelant directement la page de
// modif de l'aritcle
function ha_voir_evenement_unique($idevar)
{
	if (!ha_verifier_admin())
		return;
	$ardir = _DIR_PLUGIN_HA_ARCH;
	$contenu = "";

	// decompose id even-article
	$idevar = trim($idevar);
	if (preg_match("/^([0-9]+)\-([0-9]+)$/", $idevar, $mt))
	{
		$idev = intval($mt[1]);
		$idar = intval($mt[2]);
		$_POST["edit"] = 1;
		$_POST["id_evenement"] = $idev;
		$contenu = Agenda_formulaire_article_ajouter_evenement($idar,$idev,1,"");
	}

        include_spip('inc/headers');
        include_spip('inc/commencer_page');	
	echo init_entete("");
        echo "<div id='page' align='center'>";
	echo $contenu;
	echo "</div>";
	echo "</body></html>";
}
 
function ha_voir_evenements($dt)
{
	if (!ha_verifier_admin())
		return;
	$ardir = _DIR_PLUGIN_HA_ARCH;
	$titre = _T('ha:titre_aide');
	$contenu = _T("ha:aide");

	if ($dt == "maintenant")
	{
		// regenere le fichier
		$dt = date("Ymd");
		$fn = $ardir."/ar_".$dt.".txt";
		$fdi = fopen("http://".$_SERVER["SERVER_NAME"]."/spip.php?page=histo&var_mode=recalcul", "r");
		$fdo = fopen($fn, "w");
		while($line = fread($fdi, 1024))
			fwrite($fdo, $line);
		fclose($fdo);
		fclose($fdi);
	}

	$fn = $ardir."/ar_".$dt.".txt";
	if (is_file($fn) && preg_match("/^[0-9]*$/", $dt))
	{
		$dts = date("d/m/Y", ha_mktime($dt));
		$titre = _T('ha:titre_visualisation').$dts;
		$contenu = "<style>#sep { display:block; }</style>\n";
		$contenu .= file_get_contents($fn);
	}

	return array($titre, $contenu);
}

function exec_ha() 
{
	if (!ha_verifier_admin())
		return;

	include_spip("inc/commun.php");

	if ($_GET["haaction"] == "visuev")
	{
		ha_voir_evenement_unique($_GET["idevar"]);
		return;
	}

	debut_page('&laquo; '._T('ha:titre_page').' &raquo;', 'documents', 'mots', '', _DIR_PLUGIN_HA."/css/ha.css");
	
	echo "<style>".
		"#sep { display:none; }".
		"ins, del {".
		"text-decoration:none;".
		"}".
		"ins {".
		"background:#BBFFBB none repeat scroll 0%; }".
		"del {".
		"background:#FFCCCC none repeat scroll 0%;".
		"}".
		"</style>";
	
	echo "<script type='text/javascript'>".
		" $(function() {".
		"   $('.diff').parents().parents().show();".
		"   $('.diff').children().show();".
		"   $('ins').children().css('background-color','#BBFFBB');".
		"   $('del').children().css('background-color','#FFCCCC');".
		" });".
		"</script>";

	echo "<script type='text/javascript'>".
		"function ouvrirev(id,ida) {".
		"wd=window.open ('/ecrire/?exec=articles&id_article='+ida+'&id_evenement='+id+'&edit=oui#modifevenement',".
		"'evenement_'+id,'location=0,status=0,scrollbars=1,width=800,height=700');".
		"wd.moveTo(50,50);".
		"}".
		"</script>";
/*
		"wd=window.open ('".html_entity_decode(generer_url_ecrire('ha','haaction=visuev&idevar=')).
		"'+id+'-'+ida,'evenement_'+id,'location=0,status=0,scrollbars=1,width=400,height=500');".
*/	
	echo '<br><br><center>';
	gros_titre(_T('ha:titre_page'));
	echo '</center>';
	
	$fics = ha_get_fichiers();
	debut_gauche();
	
	echo '<form method="post" action="'.generer_url_ecrire('ha','haaction=evs').'">';
	debut_cadre_enfonce('',false,'',_T('ha:voir_evenements'));
	echo "<div><span>"._T("ha:dates_sauvegardes")."</span>";
	echo "<br /><select name='dateev' style='width:150px;'>";
	foreach($fics as $opt=>$fic)
	{
		$sel = ""; 
		if ($_POST["dateev"] == $opt)
			$sel = "selected";
		echo "<option value='".$opt."' ".$sel.">".$fic."</option>";
	}
	echo "</select>";
	echo "<br /><input type='submit' value='"._T("ha:visualiser")."'>";
	echo "</div>";
	fin_cadre_enfonce();
	echo "</form>";
	
	echo '<form method="post" action="'.generer_url_ecrire('ha','haaction=modifs').'">';
	debut_cadre_enfonce('',false,'',_T('ha:voir_modifications'));
	echo "<div><span>"._T("ha:modification_entre")."</span>";
	echo "<br /><select name='datem1' style='width:150px;'>";
	foreach($fics as $opt=>$fic)
	{
		$sel = "";
		if ($_POST["datem1"] == $opt)
			$sel = "selected";
		echo "<option value='".$opt."' ".$sel.">".$fic."</option>";
	}
	echo "</select>";
	echo "<br /><span>"._T("ha:modification_et")."</span>";
	echo "<br /><select name='datem2' style='width:150px;'>";
	foreach($fics as $opt=>$fic)
	{
		$sel = "";
		if ($_POST["datem2"] == $opt)
			$sel = "selected";
		echo "<option value='".$opt."' ".$sel.">".$fic."</option>";
	}
	echo "</select>";
	echo "<br /><input type='submit' value='"._T("ha:visualiser")."'>";
	echo "</div>";
	fin_cadre_enfonce();
	echo "</form>";
	
	fin_gauche();

	debut_droite();
	
	$titre = "";
	switch($_GET["haaction"])
	{
	case "visuev":
		list($titre, $contenu) = ha_voir_evenement_unique($_GET["idevar"]);
		break;		
	case "evs":
		list($titre, $contenu) = ha_voir_evenements($_POST["dateev"]);
		break;
	case "modifs":
		list($titre, $contenu) = ha_voir_modifications($_POST["datem1"], $_POST["datem2"]);
		break;
	default:
		$titre = _T('ha:titre_aide');
		$contenu = _T("ha:aide");
		break;
	}
	
	debut_cadre_enfonce('',false,'',$titre);
	echo "<div>".$contenu."</div>";
	fin_cadre_enfonce();
	
	echo fin_page();
	
}
?>
