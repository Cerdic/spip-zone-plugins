<?php

@define('_sommaire_NB_TITRES_MINI', 2);
@define('_sommaire_SANS_FOND', '[!fond]');

// aide le Couteau Suisse a calculer la balise #INTRODUCTION
$GLOBALS['cs_introduire'][] = 'sommaire_nettoyer_raccourcis';

// renvoie le sommaire d'une page d'article
// $page=false reinitialise le compteur interne des ancres
function sommaire_d_une_page(&$texte, &$nbh3, $page=0, $num_pages=0) {
	static $index = 0;
	if($page===false) $index = 0;
	static $self = NULL; 
	if(!isset($self)) 
		$self = str_replace('&', '&amp;', nettoyer_uri());//self();//$GLOBALS['REQUEST_URI'];
	if($page===false) return;
	// trouver quel <hx> est utilise
	$hierarchie = preg_match(',<h(\d),',$GLOBALS['debut_intertitre'],$regs)?$regs[1]:'3';
	@define('_sommaire_NB_CARACTERES', 30);
	// traitement des intertitres <hx>
	preg_match_all(",(<h{$hierarchie}[^>]*)>(.*)</h{$hierarchie}>,Umsi", $texte, $regs);
	$nbh3 += count($regs[0]);
	$pos = 0; $sommaire = '';
	// calcul de la page
	$suffixe = $page?_T('couteau:sommaire_page', array('page'=>$page)):'';
	$fct_lien_retour = function_exists('sommaire_lien_retour')?'sommaire_lien_retour':'sommaire_lien_retour_dist';
	for($i=0;$i<count($regs[0]);$i++,$index++){
		$ancre = " id=\"outil_sommaire_$index\">";
		if (($pos2 = strpos($texte, $regs[0][$i], $pos))!==false) {
			$titre = preg_replace(',^<p[^>]*>(.*)</p>$,Umsi', '\\1', trim($regs[2][$i]));
			// ancre 'retour au sommaire', sauf :
			// si on imprime, ou si les blocs depliables utilisent h{$hierarchie}...
			$haut = (defined('_CS_PRINT') OR (strpos($regs[0][$i], 'blocs_titre')!==false))
				?''
				:$fct_lien_retour($self, $titre);
			$texte = substr($texte, 0, $pos2) . $regs[1][$i] . $ancre . $haut
				. substr($texte, $pos2 + strlen($regs[1][$i])+1 + strlen($regs[2][$i]));
			$pos = $pos2 + strlen($ancre) + strlen($regs[0][$i]);
			// tout le texte, sans les notes
			$brut = preg_replace(',\[<a href=["\']#nb.*?</a>\],','', echappe_retour($regs[2][$i],'CS'));
			// pas de glossaire
			if(function_exists('cs_retire_glossaire')) $brut = cs_retire_glossaire($brut);
			// texte brut
			$brut2 = preg_replace(',[\n\r]+,',' ',textebrut($brut));
			// cas des intertitres en image_typo
			if(!strlen($brut2)) $brut2 = extraire_attribut($brut, 'alt');
			// pas trop long quand meme...
			$lien = cs_propre(couper($brut2, _sommaire_NB_CARACTERES));
			// eviter une ponctuation a la fin, surtout si la page est precisee
			$lien = preg_replace('/(&nbsp;|\s)*'.($page?'[!?,;.:]+$/':'[,;.:]+$/'), '', $lien);
			$titre = attribut_html(couper($brut2, 100));
			// si la decoupe en page est active...
			$artpage = (function_exists('decoupe_url') && (strlen(_request('artpage')) || $page>1) )
				?decoupe_url($self, $page, $num_pages):$self;
			$sommaire .= "<li><a $st title=\"$titre\" href=\"{$artpage}#outil_sommaire_$index\">$lien</a>$suffixe</li>";
		}
	}
	return $sommaire;
}

/*
 Fonction surchargeable qui reconstruit les titres de la page 
 en ajoutant une ancre de retour au sommaire.
 La fonction de surcharge a placer dans config/mes_options.php est : 
   sommaire_lien_retour($self, $titre)
 Exemple sans lien de retour : 
   function sommaire_lien_retour($self, $titre) { return $titre; }
*/
function sommaire_lien_retour_dist($self, $titre) {
	static $haut = NULL;
	if(!isset($haut)) 
		$haut = '<a title="'._T('couteau:sommaire_titre').'" href="'.$self.'#outil_sommaire" class="sommaire_ancre">&nbsp;</a>';
	return $haut . $titre;
}

// fonction appellee sur les parties du textes non comprises entre les balises : html|code|cadre|frame|script|acronym|cite
function sommaire_d_article_rempl($texte0, $sommaire_seul=false) {
	// pour sommaire_nettoyer_raccourcis()
	include_spip('outils/sommaire');
	// si le sommaire est malvenu ou s'il n'y a pas de balise <hx>, alors on laisse tomber
	$inserer_sommaire =  defined('_sommaire_AUTOMATIQUE')
		?strpos($texte0, _CS_SANS_SOMMAIRE)===false
		:strpos($texte0, _CS_AVEC_SOMMAIRE)!==false;
	if (!$inserer_sommaire || strpos($texte0, '<h')===false) 
		return $sommaire_seul?'':sommaire_nettoyer_raccourcis($texte0);
	// on retire les raccourcis du texte
	$texte = sommaire_nettoyer_raccourcis($texte0);
	// on masque les onglets s'il y en a
	if(defined('_onglets_FIN'))
		$texte = preg_replace_callback(',<div class="onglets_bloc_initial.*'._onglets_FIN.',Ums', 'sommaire_echappe_onglets_callback', $texte);
	// et la, on y va...
	$sommaire = ''; $i = 1; $nbh3 = 0;
	// reinitialisation de l'index interne de la fonction
	sommaire_d_une_page($texte, $nbh3, false);
	// couplage avec l'outil 'decoupe_article'
	if(defined('_decoupe_SEPARATEUR') && !defined('_CS_PRINT')) {
		$pages = explode(_decoupe_SEPARATEUR, $texte);
		if (($num_page=count($pages)) == 1) $sommaire = sommaire_d_une_page($texte, $nbh3);
		else {
			foreach($pages as $p=>$page) { $sommaire .= sommaire_d_une_page($page, $nbh3, $i++, $num_page); $pages[$p] = $page; }
			$texte = join(_decoupe_SEPARATEUR, $pages);
		}
	} else $sommaire = sommaire_d_une_page($texte, $nbh3);
	if(!strlen($sommaire) || $nbh3<_sommaire_NB_TITRES_MINI)
		return $sommaire_seul?'':sommaire_nettoyer_raccourcis($texte0);

	// calcul du sommaire
	include_spip('public/assembler');
	$sommaire = recuperer_fond('fonds/sommaire', array(
		'sommaire'=>$sommaire,
		'fond_css'=>strpos($texte0, _sommaire_SANS_FOND)===false ?'avec':'sans',
	));

	// si on ne veut que le sommaire, on renvoie le sommaire
	// sinon, on n'insere ce sommaire en tete de texte que si la balise #CS_SOMMAIRE n'est pas activee
	if($sommaire_seul) return $sommaire;
	if(defined('_sommaire_BALISE')) return $texte;
	return _sommaire_REM.$sommaire._sommaire_REM.$texte;
}

// fonction de callback qui echappe les onglets
function sommaire_echappe_onglets_callback($matches) {
 return cs_code_echappement($matches[0], 'CS');
}

// fonction appelee par le traitement de #TEXTE/articles
function sommaire_d_article($texte) {
	// s'il n'y a aucun intertitre, on ne fait rien
	// si la balise est utilisee, il faut quand meme inserer les ancres de retour
	if((strpos($texte, '<h')===false)) return $texte;
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite|onglets|table', 'sommaire_d_article_rempl', $texte, false);
}

// fonction appelee par le traitement post_propre de #CS_SOMMAIRE
function sommaire_d_article_balise($texte) {
	// si la balise n'est pas utilisee ou s'il n'y a aucun intertitre, on ne fait rien
	if(!defined('_sommaire_BALISE') || (strpos($texte, '<h')===false)) return '';
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite|onglets|table', 'sommaire_d_article_rempl', $texte, true);
}

// si on veut la balise #CS_SOMMAIRE
if (defined('_sommaire_BALISE')) {
	function balise_CS_SOMMAIRE_dist($p) {
		if ($p->type_requete == 'articles') {
			$p->code = 'cs_supprime_notes('.champ_sql('texte', $p).')';
		} else {
			$p->code = "''";
		}
		$p->interdire_scripts = true;
		return $p;
	}
}
?>