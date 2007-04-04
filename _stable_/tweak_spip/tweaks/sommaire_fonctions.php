<?php /*
 *   +----------------------------------+
 *    Nom du Filtre : sommaire_article
 *   +----------------------------------+
 *    Date : mardi 03 avril 2007
 *    Auteur :  Patrice Vanneufville
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     Presenter un petit sommaire en haut
 *     de l'article base sur les balises <h3>
 *   +-------------------------------------+ 
 *
*/
define('_sommaire_NB_CARACTERES', 30);

// renvoie le sommaire d'une page d'article
function sommaire_d_une_page(&$texte, $page=0) {
	static $index; if(!$index) $index=0;
	$titre = _T('Tweaks:sommaire');
	preg_match_all(',<h3[^>]*>(.*)</h3>,Umsi',$texte, $regs);
	$pos = 0; $sommaire = '';
	$p = $page?",&nbsp;p$page":'';
	for($i=0;$i<count($regs[0]);$i++,$index++){
		$haut = 'style="background:transparent url(dist/images/spip_out.gif) no-repeat scroll left center;"';
		$haut = "<a href=\"#sommaire\" $haut>&nbsp; &nbsp;</a>";
		$ancre = "<a id=\"sommaire_$index\" name=id=\"sommaire_$index\"></a>";
		if (($pos2=strpos($texte, $regs[0][$i], $pos))!==false) {
			$texte=substr($texte, 0, $pos2) . $ancre . substr($texte, $pos2);
			$pos=$pos2+strlen($ancre)+strlen($regs[0][$i]);
			$lien = couper($regs[1][$i], _sommaire_NB_CARACTERES);
			$titre = htmlentities(textebrut(couper($regs[1][$i], 100)));
			$sommaire .= "<li><a $st title=\"$titre\" href=\"".parametre_url(self(),'artpage', $page)."#sommaire_$index\">$lien</a>$p</li>";
		}
	}
	return $sommaire;
}

// fonction appellee sur les parties du textes non comprises entre les balises : html|code|cadre|frame|script|acronym|cite
function sommaire_d_article_rempl($texte) {
	if (strpos($texte, '<h3')===false) return $texte;
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

$sommaire='<div id="tweak_sommaire" style="background-color:white;
border:1px solid gray;
display:block;
float:right;
margin:0pt 0pt 0pt 1em;
overflow:hidden;
/*width:160px;*/
">
<div style="border-bottom:1px dotted silver;
line-height:1em;
font-weight:bold;
text-align:center;">&nbsp;'._T('Tweaks:sommaire').'&nbsp;</div>
<ul style="font-size:84%;
list-style-image:none;
list-style-position:outside;
list-style-type:none;
/*list-style-type:square;*/
margin:0.1em 0.5em 0.1em 0.7em;
padding:0pt;">'.$sommaire.'</ul></div>';

	return $sommaire.$texte;
}

function sommaire_d_article($texte){
	if (strpos($texte, '<h3')===false) return $texte;
	return tweak_exclure_balises('html|code|cadre|frame|script|acronym|cite', 'sommaire_d_article_rempl', $texte);
}

?>