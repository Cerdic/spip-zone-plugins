<?php

// Tweak SMILEYS - 25 décembre 2006
// serieuse refonte 2006 : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/?article1561

define('_CHEMIN_SMILEYS', dirname(find_in_path('img/smileys/diable.png')).'/');

global $smileys_rempl;
$smileys_rempl = array(

// les doubles :
 ':-(('	=> 'en_colere.png',
 ':-))'	=> 'mort_de_rire.png',
 ':))'	=> 'mort_de_rire.png',
 ":'-))"=> 'pleure_de_rire.png',

// les simples :
 ':->'	=> 'diable.png',
 ':-('	=> 'pas_content.png',
 ':-D'	=> 'mort_de_rire.png',
 ':-)'	=> 'sourire.png',
 '|-)'	=> 'rouge.png',
 ":'-D"	=> 'pleure_de_rire.png',
 ":'-("	=> 'triste.png',
 ':o)'	=> 'rigolo.png',
 'B-)'	=> 'lunettes.png',
 ';-)'	=> 'clin_d-oeil.png',
 ':-p'	=> 'tire_la_langue.png',
 ':-P'	=> 'tire_la_langue.png',
 ':-|'	=> 'bof.png',
 ':-/'	=> 'mouais.png',
 ':-o'	=> 'surpris.png',
 ':-O'	=> 'surpris.png',

// les courts a tester...
 ':)'	=> 'sourire.png',
 ';)'	=> 'clin_d-oeil.png',
 ':|'	=> 'bof.png',
 '|)'	=> 'rouge.png',
// ':/'	=> 'mouais.png',	// conflit avec 'http://' par exemple
);

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function tweak_rempl_smileys($texte) {
	global $smileys_rempl;
	// accessibilite : protection de alt et title
	foreach ($smileys_rempl as $smy=>$val) $texte = str_replace($smy, '<img alt="@@64@@'.base64_encode($smy).'@@65@@" title="@@64@@'.base64_encode($smy).'@@65@@" src="'._CHEMIN_SMILEYS.$val.'">', $texte);
	// accessibilite : alt et title avec le smiley en texte
	while(preg_match('`@@64@@([^@]*)@@65@@`', $texte, $regs)) $texte = str_replace('@@64@@'.$regs[1].'@@65@@', base64_decode($regs[1]), $texte);
	return $texte;
}

function tweak_smileys($texte) {
	return tweak_exclure_balises('html|code|cadre|frame|script|acronym|cite', 'tweak_rempl_smileys', $texte);
}
?>