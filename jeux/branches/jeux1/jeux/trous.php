<?php

# il s'agit ici de proposer des textes a trous.

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice�.!vanneufville�@!laposte�.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#
/*

Insere un test de closure dans vos articles !
---------------------------------------------

separateurs obligatoires : [texte], [trou]
separateurs optionnels   : [titre], [config], [score]
parametres de configurations par defaut :
	taille=auto	// taille des trous
	indices=oui	// afficher les indices ?
	couleurs=oui // appliquer des couleurs sur les corrections ?
	solution=non // donne la(les) bonne(s) reponse(s) lors de la correction


Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[texte]
	Ceci est un exemple de closure (exercice a trous).
	L'utilisateur doit entrer ses 
	[trou]
	reponses 
	[texte]
	dans les espaces vides.
	Pour chaque mot manquant, plusieurs reponses correctes 
	peuvent	etre acceptees. Par exemple, ce  
	[trou]
	trou, vide, blanc
	[texte]
	autorise les reponses "trou", "vide" ou "blanc".
	[config]
	indices = oui
</jeux>

La liste des mots a placer apres [trou] peut accepter 
les separateurs usuels : 
	retours a la ligne, tabulations, espaces
	virgules, point-virgules, points
Pour une expression comprenant des espaces, utiliser les 
guillemets ou le signe + :
	par ex. : "afin de" est equivalent a : afin+de
Les comparaisons sont insensibles a la casse.
Pour une expression sensible a la casse, ajouter /M
en fin d'expression :
	par ex. : "la France/M" (ou : la+France/M)
Pour une expression reguliere, utiliser les guillemets et
les virgules comme separateur :
	par ex. : ",stylo(graphe)?,"
A propos de la casse, voici des expressions equivalentes :
	"la France/M", la+France/M, ",la France,"
ou :
	"la France", la+France, la+france, ",la france,i"
Pour l'affichage des indices, veillez a ce que la premiere
expression ne soit pas une expression reguliere

*/

function trous_inserer_le_trou($indexJeux, $indexTrou, $size, $corriger) {
	global $propositionsTROUS, $scoreTROUS, $score_detailTROUS;
	// Initialisation du code a retourner
	$nomVarSelect = "var{$indexJeux}_T{$indexTrou}";
	$mots = $propositionsTROUS[$indexTrou];
	$prop = trim($_POST[$nomVarSelect]);
	$oups = $disab = $color = '';
	// en cas de correction
	if($corriger) {
		$ok = strlen($prop) && jeux_in_liste($prop, $mots);
		if($ok) ++$scoreTROUS;
		if(jeux_config('couleurs')) $color = $ok?' juste':' faux';
		if(!$ok && jeux_config('solution')) {
			if(!strlen($prop)) $prop = '&nbsp; &nbsp;';
			$oups = "<span class='faux barre'>$prop</span>";
			$prop = trous_liste_reponses($mots);
			$size = strlen($prop);
			$color = ' juste';
		}
//$solution = ' <span class="juste">('.trous_liste_reponses($mots).')</span> ';
		// on renseigne le resultat detaille
		$score_detailTROUS[] = 'T'.($indexTrou+1).":$prop:".($ok?'1':'0');
		$disab = " disabled='disabled'";
	}
	$codeHTML = "<input name='$nomVarSelect' class='jeux_input trous$color' size='$size' type='text'$disab' value=\"$prop\" />";
//	if($corriger) $codeHTML .= '(T'.($indexTrou+1).":$prop/".$GLOBALS['meta']['charset']."[".join('|',$mots)."]:".($ok?'1':'0').'pt)';
	return $oups . $codeHTML;
}

function trous_inserer_les_trous($chaine, $indexJeux) {
	global $propositionsTROUS;
	if (ereg('<ATTENTE_TROU>([0-9]+)</ATTENTE_TROU>', $chaine, $eregResult)) {
	$indexTROU = intval($eregResult[1]);
	list($texteAvant, $texteApres) = explode($eregResult[0], $chaine, 2); 
	$texteApres = trous_inserer_les_trous($texteApres, $indexJeux);
	if (($sizeInput = intval(jeux_config('taille')))==0)
		foreach($propositionsTROUS as $trou) foreach($trou as $mot) $sizeInput = max($sizeInput, strlen($mot));
	$chaine = $texteAvant.jeux_rem('TROU-DEBUT', $indexTROU, '', 'span')
		. trous_inserer_le_trou($indexJeux, $indexTROU, $sizeInput, isset($_POST["var_correction_".$indexJeux]))
		. jeux_rem('TROU-FIN', $indexTROU, '', 'span')
		. $texteApres; 
	}
	return $chaine;
}

// renvoyer l'ensemble des solutions dans le desordre...
// si plusieurs solutions sont possibles, seule la premiere est retenue
function trous_afficher_indices($indexJeux) {
	global $propositionsTROUS;
	foreach ($propositionsTROUS as $prop) 
		$indices[] = strpos($prop[0], '/M')===($len=strlen($prop[0])-2) ?substr($prop[0],0,$len):$prop[0];
	shuffle($indices);
	return '<div class="jeux_indices">'.str_replace(array("'",'&#8217;'),"&#039;",charset2unicode(join(' -&nbsp;', $indices))).'</div>';
}

// revoyer une liste de reponses possibles
function trous_liste_reponses($mots) {
	$reponses = array(); $etc = '';
	foreach ($mots as $mot) {
		if(substr($mot,0,1)===',') $etc = 'etc.';
		 else $reponses[] = strpos($mot, '/M')===($len=strlen($mot)-2) ?substr($mot,0,$len):$mot;
	}
	return join(' / ', $reponses) . $etc;
}

function jeux_trous($texte, $indexJeux) {
	global $propositionsTROUS, $scoreTROUS, $score_detailTROUS;
	$titre = $html = false;
	$indexTrou = $scoreTROUS = 0;
	$score_detailTROUS = array();
	
	// parcourir tous les #SEPARATEURS
	$tableau = jeux_split_texte('trous', $texte); 
	// configuration par defaut
	jeux_config_init("
	taille=auto	// taille des trous
	indices=oui	// afficher les indices ?
	couleurs=oui	// afficher les indices ?
	solution=non // donne la(les) bonne(s) r�ponse(s) lors de la correction
  ", false);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	  elseif ($valeur==_JEUX_SCORE) $categ_score = $tableau[$i+1];
	  elseif ($valeur==_JEUX_TROU) {
		// remplacement des trous par : <ATTENTE_TROU>ii</ATTENTE_TROU>
		$html .= "<ATTENTE_TROU>$indexTrou</ATTENTE_TROU>";
		$propositionsTROUS[$indexTrou] = jeux_liste_mots($tableau[$i+1]);
		$indexTrou++;
	  }
	}
	// reinserer les trous mis en forme
	$texte = trous_inserer_les_trous($html, $indexJeux);
	// correction ?
	$correction = isset($_POST["var_correction_".$indexJeux]);
	if($correction) sort($score_detailTROUS);
	// recuperation du fond 'jeux/trous.html'
	include_spip('public/assembler');
	$fond = recuperer_fond('jeux/trous', 
		array('id_jeu' => _request('id_jeu'), 'titre' => $titre,
			'texte' => $texte, 'correction' => $correction,
			'indices' => jeux_config('indices')?trous_afficher_indices($indexJeux):'',
			'fond_score' => !$correction?''
				:jeux_afficher_score($scoreTROUS, $indexTrou, _request('id_jeu'), join(', ', $score_detailTROUS), $categ_score),
		)
	);
	// mise en place du formulaire
	$fond = str_replace(
		array('@@FORM_JEUX_DEBUT@@', '@@FORM_JEUX_FIN@@'), 
		$correction?'':array(jeux_form_debut('trous', $indexJeux), jeux_form_fin()), 
		$fond
	);
	// nettoyage
	unset($propositionsTROUS, $scoreTROUS, $score_detailTROUS);
	return $fond;
}
?>
