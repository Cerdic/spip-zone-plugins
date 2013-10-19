<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function balise_INTRODUCTION_DISTINGUANTE_dist($p) {

	$type = $p->type_requete;

	$_texte = champ_sql('texte', $p);
	$_descriptif = ($type == 'articles' OR $type == 'rubriques') ? champ_sql('descriptif', $p) : "''";
	if ($type == 'articles') {
		$_chapo = champ_sql('chapo', $p);
		$_texte = "(strlen($_descriptif))
		? ''
		: $_chapo . \"\\n\\n\" . $_texte";
	}
	// longueur en parametre, ou valeur par defaut
	if (($v = interprete_argument_balise(1,$p))!==NULL) {
		$longueur = 'intval('.$v.')';
	} else {
		switch ($type) {
			case 'articles':
				$longueur = '500';
				break;
			case 'breves':
				$longueur = '300';
				break;
			case 'rubriques':
			default:
				$longueur = '600';
				break;
		}
	}


	if ($type != 'articles'){
        $f = chercher_filtre('introduction');
        $p->code = "$f($_descriptif, $_texte, $longueur, \$connect)";
    }
    else{
        $p->code="introduction_distinguante($_descriptif, $_chapo, $_texte, $longueur, \$connect)";
    }
	#$p->interdire_scripts = true;
	$p->etoile = '*'; // propre est deja fait dans le calcul de l'intro
	return $p;
}

//
// fonction standard de calcul de la balise #INTRODUCTION_DISTINGUANTE, adaptée de introduction

function introduction_distinguante($descriptif,$chapo, $texte, $longueur, $connect) {
	// Si un descriptif est envoye, on l'utilise directement
	if (strlen($descriptif))
		return propre($descriptif,$connect);

	// De preference ce qui est marque <intro>...</intro>
	$intro = '';
	$texte = preg_replace(",(</?)intro>,i", "\\1intro>", $texte); // minuscules
	while ($fin = strpos($texte, "</intro>")) {
		$zone = substr($texte, 0, $fin);
		$texte = substr($texte, $fin + strlen("</intro>"));
		if ($deb = strpos($zone, "<intro>") OR substr($zone, 0, 7) == "<intro>")
			$zone = substr($zone, $deb + 7);
		$intro .= $zone;
	}

	// [12025] On ne *PEUT* pas couper simplement ici car c'est du texte brut,
	// qui inclus raccourcis et modeles
	// un simple <articlexx> peut etre ensuite transforme en 1000 lignes ...
	// par ailleurs le nettoyage des raccourcis ne tient pas compte
	// des surcharges et enrichissement de propre
	// couper doit se faire apres propre
	//$texte = nettoyer_raccourcis_typo($intro ? $intro : $texte, $connect);

	// Cependant pour des questions de perfs on coupe quand meme, en prenant
	// large et en se mefiant des tableaux #1323

	if (strlen($intro))
		$texte = $intro;

	else
	if (strpos("\n".$texte, "\n|")===false
	  AND strlen($texte) > 2.5*$longueur){
		if (strpos($texte,"<multi")!==false)
			$texte = extraire_multi($texte);
		$texte = couper($texte, 2*$longueur);
	}

	// ne pas tenir compte des notes
	if ($notes = charger_fonction('notes', 'inc', true))
		$notes('','empiler');
	$texte = propre($texte,$connect);
	if ($notes)
		$notes('','depiler');


	if (!defined('_INTRODUCTION_SUITE')) define('_INTRODUCTION_SUITE', '&nbsp;(...)');
	$texte = couper($texte, $longueur, _INTRODUCTION_SUITE);
    
    //marquer le chapeau
    if ($notes)
        $notes('','empiler');
        
    $chapo = couper(propre($chapo),$longueur);
    
    if ($notes)
		$notes('','depiler');
    if ($chapo) {// si on a un chapo
        $texte = str_replace($chapo,"<span class='introduction-chapo'>$chapo</span>",$texte,$replace);
        if ($replace >0){ // si on a remplacé, c'est qu'on a le chapo complet
                $texte = str_replace("<span class='introduction-chapo'>$chapo</span>","<span class='introduction-chapo'>$chapo</span><span class='introduction-texte'>",$texte);
                $texte = str_replace(_INTRODUCTION_SUITE,"</span>"._INTRODUCTION_SUITE,$texte);
            }
        else{//sinon a un chapo coupé
                $texte = "<span class='introduction-texte'>".str_replace(_INTRODUCTION_SUITE,"</span>"._INTRODUCTION_SUITE,$texte);
            }
        }
    else {//si pas de chapo
                $texte = "<span class='introduction-texte'>".str_replace(_INTRODUCTION_SUITE,"</span>"._INTRODUCTION_SUITE,$texte);        
        }
    // et reparagrapher si necessaire (coherence avec le cas descriptif)
	if ($GLOBALS['toujours_paragrapher'])
		// Fermer les paragraphes
		$texte = paragrapher($texte, $GLOBALS['toujours_paragrapher']);
    
	return $texte;
}
?>