<?php

function glossaire_insert_head($flux){
	$flux .='<link rel="stylesheet" href="plugins/glossaire_interne/glossaire.css" media="screen" type="text/css" />';
	$flux .='<script type="text/javascript" src="plugins/glossaire_interne/glossaire.js"></script>';
	return $flux;
}

function first_replace($c,$r,$t)
{
	if(strstr($t,$c))
	{
		$d = str_replace(strstr($t,$c),"",$t);
		$f = strstr($t,$c);
		$f = substr($f,strlen($c));
		return $d . $r . $f;
	}
	else
		return $t;
}

function lier_au_glossaire($texte)
{
$r = spip_query("SELECT id_mot, titre FROM spip_mots WHERE type='Glossaire'");
while($o = spip_fetch_array($r))
		{
		$texte = first_replace("$o[titre]","<a href=\"spip.php?mot".$o[id_mot]."\" class=\"affgloss\" title=\"Glossaire\" onclick=\"popupGloss('spip.php?page=mot_glossaire&id_mot=$o[id_mot]'); return false;\">$o[titre]</a>",$texte);
		}
	return $texte;
}

?>