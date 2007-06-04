<?php

define('_sommaire_NB_CARACTERES', 30);
define('_sommaire_NB_TITRES_MINI', 2);
define('_sommaire_SANS_FOND', '[!fond]');

// TODO : ajouter un fichier css pour le sommaire

// Filtre local utilise par le filtre 'cs_imprimer' afin d'eviter la decoupe
// Exemple : lors d'une impression a l'aide du squelette imprimer.html,
// remplacer la balise #TEXTE par [(#TEXTE*|propre|cs_imprimer)].
function sommaire_imprimer($texte) {
	return str_replace(array(_sommaire_SANS_FOND, _sommaire_SANS_SOMMAIRE), '', $texte);
}

// aide le Couteau Suisse a calculer la balise #INTRODUCTION
$GLOBALS['cs_introduire'][] = 'sommaire_imprimer';

// renvoie le sommaire d'une page d'article
function sommaire_d_une_page(&$texte, &$nbh3, $page=0) {
	static $index; if(!$index) $index=0;
	// image de retour au sommaire
	$titre = _T('cout:sommaire');
	$img = 'spip_out.gif';
	$path = dirname(find_in_path(($GLOBALS['spip_version']<1.92?"img_pack/":"images/").$img));
	list(,,,$size) = @getimagesize("$path/$img");
	$haut = "<img class=\"no_image_filtrer\" alt=\"$titre\" title=\"$titre\" src=\"".cs_htmlpath($path)."/$img\" $size/>";
	$haut = "<a title=\"$titre\" href=\"".self()."#outil_sommaire\">$haut</a> ";
	// traitement des titres <h3>
	preg_match_all(',(<h3[^>]*>)(.*)</h3>,Umsi',$texte, $regs);
	$nbh3 += count($regs[0]);
	$pos = 0; $sommaire = '';
	$p = $page?",&nbsp;p$page":'';
	for($i=0;$i<count($regs[0]);$i++,$index++){
		$ancre = "\n<a id=\"outil_sommaire_$index\" name=id=\"outil_sommaire_$index\"></a>";
		if (($pos2 = strpos($texte, $regs[0][$i], $pos))!==false) {
			$titre = preg_replace(',^<p[^>]*>(.*)</p>$,Umsi', '\\1', trim($regs[2][$i]));
			$texte = substr($texte, 0, $pos2) . $ancre . $regs[1][$i] . $haut . $titre . substr($texte, $pos2 + strlen($regs[1][$i]) + strlen($regs[2][$i]));
			$pos = $pos2 + strlen($ancre) + strlen($regs[0][$i]);
			$lien = couper($regs[2][$i], _sommaire_NB_CARACTERES);
			$titre = attribut_html(propre(couper($regs[2][$i], 100)));
			$sommaire .= "<li><a $st title=\"$titre\" href=\"".parametre_url(self(),'artpage', $page)."#outil_sommaire_$index\">$lien</a>$p</li>";
		}
	}
	return $sommaire;
}

// fonction appellee sur les parties du textes non comprises entre les balises : html|code|cadre|frame|script|acronym|cite
function sommaire_d_article_rempl($texte) {
	// s'il n'y a pas de balise <h3> ou si le raccourcis _sommaire_SANS_SOMMAIRE est present dans le texte, alors on laisse tomber
	if (strpos($texte, '<h3')===false || strpos($texte, _sommaire_SANS_SOMMAIRE)!==false) 
		return sommaire_imprimer($texte);
	$sommaire = ''; $i = 1; $nbh3 = 0;
	// couplage avec l'outil 'decoupe_article'
	if(defined('_decoupe_SEPARATEUR')) {
		$pages = explode(_decoupe_SEPARATEUR, $texte);
		if (count($pages) == 1) $sommaire = sommaire_d_une_page($texte, $nbh3);
		else {
			foreach($pages as $p=>$page) { $sommaire .= sommaire_d_une_page($page, $nbh3, $i++); $pages[$p] = $page; }
			$texte = join(_decoupe_SEPARATEUR, $pages);
		}
	} else $sommaire = sommaire_d_une_page($texte, $nbh3);
	if(!strlen($sommaire) || $nbh3<_sommaire_NB_TITRES_MINI) return $texte;

	$img = find_in_path('img/sommaire/coin.gif');
	$sansfond = !$img || strpos($texte, _sommaire_SANS_FOND)!==false;
	if ($sansfond) {
		$texte = str_replace(_sommaire_SANS_FOND, '', $texte);
		$fond = 'background-color:white; border:thin solid gray;';
	} else {
		$img = cs_htmlpath($img);
		$fond = "background:transparent url($img) no-repeat scroll left top; border-bottom:thin solid #999999; border-right:1px solid #999999;";
	}


$sommaire='<a name="outil_sommaire" id="outil_sommaire"></a><div id="outil_sommaire" class="cs_sommaire" style="'.$fond.'"><div style="margin:3pt;"><div style="
border-bottom:1px dotted silver;
line-height:1;
position:inherit;
font-weight:bold;'.($sansfond?'':'margin-left:15px;').'
text-align:center;">'._T('cout:sommaire').'</div>
<ul style="font-size:84%;
list-style-image:none;
list-style-position:outside;
list-style-type:none;
/*list-style-type:square;*/
margin:0.3em 0.5em 0.1em 0.7em;
padding:0pt;">'.$sommaire.'</ul></div></div>';

	return _sommaire_REM.$sommaire._sommaire_REM.$texte;
}

function sommaire_d_article($texte){
	if (strpos($texte, '<h3')===false) return $texte;
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'sommaire_d_article_rempl', $texte);
}

?>