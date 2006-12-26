<?php

// Tweak SMILEYS - 25 décembre 2006
//
// pour toute suggestion, remarque, proposition d'ajout d'un 
// smileys, etc ; reportez vous au forum de l'article :
// http://www.spip-contrib.net/?id_article=1561

function tweak_smileys($chaine) {
$chemin = dirname(find_in_path('img/smileys/diable.png')).'/';

$rempl =array(
 ':->'	=> 'diable.png',
 ':-(('	=> 'en_colere.png',
 ':-('	=> 'pas_content.png',
 ':-))'	=> 'mort_de_rire.png',
 ':))'	=> 'mort_de_rire.png',
 ':-D'	=> 'mort_de_rire.png',
 ':-)'	=> 'sourire.png',
 ':)'	=> 'sourire.png',
 '|-)'	=> 'rouge.png',
 '|)'	=> 'rouge.png',
 ":'-))"	=> 'pleure_de_rire.png',
 ":'-D"	=> 'pleure_de_rire.png',
 ":'-("	=> 'triste.png',
 ':o)'	=> 'rigolo.png',
 'B-)'	=> 'lunettes.png',
 ';-)'	=> 'clin_d-oeil.png',
 ';)'	=> 'clin_d-oeil.png',
 ':-p'	=> 'tire_la_langue.png',
 ':-P'	=> 'tire_la_langue.png',
 ':-|'	=> 'bof.png',
 ':|'	=> 'bof.png',
 ':-/'	=> 'mouais.png',
 ':/'	=> 'mouais.png',
 ':-o'	=> 'surpris.png',
 ':-O'	=> 'surpris.png',
);

foreach ($rempl as $smy=>$val) $chaine = str_replace($smy, '<img ALT="smiley" src="'.$chemin.$val.'">', $chaine);

// $t="<table border=1 cellpadding=4 cellspacing=0><tr>";	foreach ($rempl as $smy=>$val) $t .= "<th align=\"center\" style=\"border:1px solid gray; padding: 2px;\">$smy</th>"; $t.="</tr><tr>";	foreach ($rempl as $smy=>$val) $t .= '<th align="center" style="border:1px solid gray; padding: 2px;"><img ALT="smiley" style="padding: 0px;" src="'.$chemin.$val.'"></th>'; $chaine=$t."</tr></table>";

return $chaine;
}
?>
