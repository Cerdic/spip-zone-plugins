<?php

@define('_sommaire_NB_TITRES_MINI', 2);
@define('_sommaire_SANS_FOND', '[!fond]');

// TODO : ajouter un fichier css pour le sommaire

// aide le Couteau Suisse a calculer la balise #INTRODUCTION
function sommaire_retire_raccourcis($texte) {
	return str_replace(array(_sommaire_SANS_FOND, _sommaire_SANS_SOMMAIRE, _sommaire_AVEC_SOMMAIRE), '', $texte);
}
$GLOBALS['cs_introduire'][] = 'sommaire_retire_raccourcis';

// renvoie le sommaire d'une page d'article
// $page=false reinitialise le compteur interne des ancres
function sommaire_d_une_page(&$texte, &$nbh3, $page=0) {
	static $index; if(!$index || $page===false) $index=0;
	if ($page===false) return;
	@define('_sommaire_NB_CARACTERES', 30);
	// si on n'est pas en mode impression, on calcule l'image de retour au sommaire
	if($_GET['cs']!='print') {
		$titre = _T('cout:sommaire_titre');
		$haut = "<a title=\"$titre\" href=\"".self()."#outil_sommaire\" class=\"sommaire_ancre\">&nbsp;</a>";
	} else $haut = '';
	// traitement des titres <h3>
	preg_match_all(',(<h3[^>]*)>(.*)</h3>,Umsi',$texte, $regs);
	$nbh3 += count($regs[0]);
	$pos = 0; $sommaire = '';
	// calcul de la page
	$p = $page?_T('cout:sommaire_page', array('page'=>$page)):'';
	for($i=0;$i<count($regs[0]);$i++,$index++){
		$ancre = " id=\"outil_sommaire_$index\">";
		if (($pos2 = strpos($texte, $regs[0][$i], $pos))!==false) {
			$titre = preg_replace(',^<p[^>]*>(.*)</p>$,Umsi', '\\1', trim($regs[2][$i]));
			$texte = substr($texte, 0, $pos2) . $regs[1][$i] 
				. $ancre . $haut . $titre
				. substr($texte, $pos2 + strlen($regs[1][$i])+1 + strlen($regs[2][$i]));
			$pos = $pos2 + strlen($ancre) + strlen($regs[0][$i]);
			$lien = couper($regs[2][$i], _sommaire_NB_CARACTERES);
			$lien = preg_replace('/[!?,;.:]+$/', '', $lien); // eviter une ponctuation a la fin
			$titre = attribut_html(propre(couper($regs[2][$i], 100)));
			$sommaire .= "<li><a $st title=\"$titre\" href=\"".parametre_url(self(),'artpage', $page)."#outil_sommaire_$index\">$lien</a>$p</li>";
		}
	}
	return $sommaire;
}

// fonction appellee sur les parties du textes non comprises entre les balises : html|code|cadre|frame|script|acronym|cite
function sommaire_d_article_rempl($texte0, $sommaire_seul=false) {
	// si le sommaire est malvenu ou s'il n'y a pas de balise <h3>, alors on laisse tomber
	$inserer_sommaire =  defined('_sommaire_AUTOMATIQUE')
		?strpos($texte0, _sommaire_SANS_SOMMAIRE)===false
		:strpos($texte0, _sommaire_AVEC_SOMMAIRE)!==false;
	if (!$inserer_sommaire || strpos($texte0, '<h3')===false) 
		return $sommaire_seul?'':sommaire_retire_raccourcis($texte0);
	// on retire les raccourcis du texte
	$texte = sommaire_retire_raccourcis($texte0);
	// et la, on y va...
	$sommaire = ''; $i = 1; $nbh3 = 0;
	// reinitialisation de l'index interne de la fonction
	sommaire_d_une_page($texte, $nbh3, false);
	// couplage avec l'outil 'decoupe_article'
	if(defined('_decoupe_SEPARATEUR') && ($_GET['cs']!='print')) {
		$pages = explode(_decoupe_SEPARATEUR, $texte);
		if (count($pages) == 1) $sommaire = sommaire_d_une_page($texte, $nbh3);
		else {
			foreach($pages as $p=>$page) { $sommaire .= sommaire_d_une_page($page, $nbh3, $i++); $pages[$p] = $page; }
			$texte = join(_decoupe_SEPARATEUR, $pages);
		}
	} else $sommaire = sommaire_d_une_page($texte, $nbh3);
	if(!strlen($sommaire) || $nbh3<_sommaire_NB_TITRES_MINI) 
		return $sommaire_seul?'':sommaire_retire_raccourcis($texte0);

	// calcul du sommaire en recuperant le fond qui va bien
	$fond = strpos($texte0, _sommaire_SANS_FOND)!==false ?2:1;
	include_spip('public/assembler');
	@define('_sommaire_TITRE', _T('cout:sommaire_titre'));
	$sommaire = recuperer_fond('fonds/sommaire'.$fond, array('sommaire'=>$sommaire, 'titre'=>_sommaire_TITRE));

	// si on ne veut que le sommaire, on renvoie le sommaire
	// sinon, on n'insere ce sommaire en tete de texte que si la balise #CS_SOMMAIRE n'est pas activee
	if($sommaire_seul) return $sommaire;
	if(defined('_sommaire_BALISE')) return $texte;
	return _sommaire_REM.$sommaire._sommaire_REM.$texte;
}

// fonction appelee par le traitement de #TEXTE/articles
function sommaire_d_article($texte) {
	// s'il n'y a aucun intertitre, on ne fait rien
	// si la balise est utilisee, il faut quand meme inserer les ancres de retour
	if((strpos($texte, '<h3')===false)) return $texte;
		else return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'sommaire_d_article_rempl', $texte, false);
}

// fonction appelee par le traitement pre_propre de #CS_SOMMAIRE
function sommaire_sauve_notes($texte) {
	$GLOBALS['cs_notes'] = array($GLOBALS['les_notes'], $GLOBALS['compt_note']);
	return $texte;
}

// fonction appelee par le traitement post_propre de #CS_SOMMAIRE
function sommaire_d_article_balise($texte) {
	list($GLOBALS['les_notes'], $GLOBALS['compt_note']) = $GLOBALS['cs_notes'];
	unset($GLOBALS['cs_notes']);
	// si la balise n'est pas utilisee ou s'il n'y a aucun intertitre, on ne fait rien
	if(!defined('_sommaire_BALISE') || (strpos($texte, '<h3')===false)) return '';
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'sommaire_d_article_rempl', $texte, true);
}

// on veut la balise
if (defined('_sommaire_BALISE')) {
	// Balise #CS_SOMMAIRE
	function balise_CS_SOMMAIRE($p) {
		$type = $p->type_requete;
		if ($type == 'articles') {
			$_texte = champ_sql('texte', $p);
			$p->code = "$_texte";
		} else {
			$p->code = "''";
		}
		$p->interdire_scripts = true;
		return $p;
	}
}
?>