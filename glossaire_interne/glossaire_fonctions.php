<?php

function glossaire_insert_head($flux){
	$flux .="<link rel='stylesheet' href='".find_in_path('glossaire.css')."' type='text/css' media='all' />\n";
	$flux .="<script type='text/javascript' src='".find_in_path('glossaire.js')."'></script>";
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

	/*contrib de philippe auriol pour zapper les liens */
	    $search= '@<a[^>]*?>.*?</a>@msi'; 	    preg_match_all ($search, $texte, $tagMatches);	    $replace = "#MaChaine#";
 	    $texte = preg_replace($search, $replace, $texte);

		while($o = spip_fetch_array($r))
			{
			$texte = first_replace("$o[titre]","<a href=\"spip.php?mot".$o[id_mot]."\" class=\"affgloss\" title=\"Glossaire\" onclick=\"popupGloss('spip.php?page=mot_glossaire&amp;id_mot=$o[id_mot]'); return false;\">$o[titre]</a>",$texte);
			}

 	    	for($i=0;$i<sizeof($tagMatches[0]);$i++)
	            {
 	            $texte= first_replace ($replace,$tagMatches[0][$i], $texte);	            }

		return $texte;
}

?>