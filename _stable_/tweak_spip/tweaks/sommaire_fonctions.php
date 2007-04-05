<?php

define('_sommaire_NB_CARACTERES', 30);
define('_sommaire_NB_TITRES_MINI', 2);

// TODO : ajouter un fichier css pour le sommaire

// renvoie le sommaire d'une page d'article
function sommaire_d_une_page(&$texte, $page=0) {
	static $index; if(!$index) $index=0;
	// image de retour au sommaire
	$titre = _T('tweak:sommaire');
	$img = 'spip_out.gif';
	$path = dirname(find_in_path(($GLOBALS['spip_version']<1.92?"img_pack/":"images/").$img));
	list(,,,$size) = @getimagesize("$path/$img");
	$haut = "<img class=\"no_image_filtrer\" alt=\"$titre\" title=\"$titre\" src=\"".tweak_htmlpath($path)."/$img\" $size/>";
	$haut = "<a title=\"$titre\" href=\"".self()."#tweak_sommaire\">$haut</a>&nbsp;";
	// traitement des titres <h3>
	preg_match_all(',(<h3[^>]*>)(.*)</h3>,Umsi',$texte, $regs);
	if (count($regs[0])<_sommaire_NB_TITRES_MINI) return '';
	$pos = 0; $sommaire = '';
	$p = $page?",&nbsp;p$page":'';
	for($i=0;$i<count($regs[0]);$i++,$index++){
		$ancre = "<a id=\"tweak_sommaire_$index\" name=id=\"tweak_sommaire_$index\"></a>";
		if (($pos2=strpos($texte, $regs[0][$i], $pos))!==false) {
			$texte=substr($texte, 0, $pos2) . $ancre . $regs[1][$i] . $haut . $ancre . substr($texte, $pos2+strlen($regs[1][$i]));
			$pos=$pos2+strlen($ancre)+strlen($regs[0][$i]);
			$lien = couper($regs[2][$i], _sommaire_NB_CARACTERES);
			$titre = htmlentities(textebrut(couper($regs[2][$i], 100)));
			$sommaire .= "<li><a $st title=\"$titre\" href=\"".parametre_url(self(),'artpage', $page)."#tweak_sommaire_$index\">$lien</a>$p</li>";
		}
	}
	return $sommaire;
}

// fonction appellee sur les parties du textes non comprises entre les balises : html|code|cadre|frame|script|acronym|cite
function sommaire_d_article_rempl($texte) {
	if (strpos($texte, '<h3')===false) return $texte;
	if (strpos($texte, _sommaire_SANS_SOMMAIRE)!==false) return str_replace(_sommaire_SANS_SOMMAIRE, '', $texte);
	$sommaire = ''; $i = 1;
	// couplage avec le tweak 'decoupe_article'
	if(defined('_decoupe_SEPARATEUR')) {
		$pages = explode(_decoupe_SEPARATEUR, $texte);
		if (count($pages) == 1) $sommaire = sommaire_d_une_page($texte);
		else {
			foreach($pages as $p=>$page) { $sommaire .= sommaire_d_une_page($page, $i++); $pages[$p] = $page; }
			$texte = join(_decoupe_SEPARATEUR, $pages);
		}
	} else $sommaire = sommaire_d_une_page($texte);
//print_r($regs);
if(!strlen($sommaire)) return $texte;
$sommaire='<a name="tweak_sommaire" id="tweak_sommaire"></a><div id="tweak_sommaire" style="background-color:white;
border:1px solid gray;
display:block;
float:right;
margin:0pt 0pt 0pt 1em;
overflow:hidden;
/*width:160px;*/
">
<div style="border-bottom:1px dotted silver;
line-height:1.2em;
font-weight:bold;
text-align:center;">&nbsp;'._T('tweak:sommaire').'&nbsp;</div>
<ul style="font-size:84%;
list-style-image:none;
list-style-position:outside;
list-style-type:none;
/*list-style-type:square;*/
margin:0.1em 0.5em 0.1em 0.7em;
padding:0pt;">'.$sommaire.'</ul></div>';

	return _sommaire_REM.$sommaire._sommaire_REM.$texte;
}

function sommaire_d_article($texte){
	if (strpos($texte, '<h3')===false) return $texte;
	return tweak_exclure_balises('html|code|cadre|frame|script|acronym|cite', 'sommaire_d_article_rempl', $texte);
}

?>