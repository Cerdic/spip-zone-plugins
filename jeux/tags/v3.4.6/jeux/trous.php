<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
# il s'agit ici de proposer des textes a trous.

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : https://contrib.spip.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#
/*

Insere un test de closure dans vos articles !
---------------------------------------------

separateurs obligatoires : [texte], [trou]
separateurs optionnels   : [titre], [config], [score]
parametres de configuration par defaut :
	voir la fonction jeux_trous_init() ci-dessous

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

// configuration par defaut : jeu_{mon_jeu}_init()
function jeux_trous_init() {
	return "
		taille=auto	// taille des trous
		indices=oui	// afficher les indices ?
		couleurs=oui	// afficher les indices ?
		solution=non // donne la(les) bonne(s) reponse(s) lors de la correction
		bouton_corriger=corriger // fond utilise pour le bouton 'Corriger'
		bouton_refaire=recommencer // fond utilise pour le bouton 'Reset'
	";
}

function trous_inserer_le_trou(&$trous, $indexJeux, $indexTrou, $size) {
	// Initialisation du code a retourner
	$mots = $trous['propositions'][$indexTrou];
	$prop = $oups = $disab = $color = '';
	// en cas de correction
	if(jeux_form_correction($indexJeux)) {
		$prop = jeux_form_reponse($indexJeux, $indexTrou, 'T');
		$ok = strlen($prop) && jeux_in_liste($prop, $mots);
		if($ok) ++$trous['score'];
		if(jeux_config('couleurs')) $color = $ok?' juste':' faux';
		if(!$ok && jeux_config('solution')) {
			if(!strlen($prop)) {
				$prop = '??&nbsp;';
				$oups = "<span class='faux'>$prop</span>";
			} else $oups = "<span class='faux barre'>$prop</span>";
			$prop = trous_liste_reponses($mots);
			$size = strlen($prop);
			$color = ''; //' faux';
		}
//$solution = ' <span class="juste">('.trous_liste_reponses($mots).')</span> ';
		// on renseigne le resultat detaille
		$trous['score_detaille'][] = 'T'.($indexTrou+1).":$prop:".($ok?'1':'0');
		$disab = " disabled='disabled'";
	}
	list($idInput, $nameInput) = jeux_idname($indexJeux, $indexTrou, 'T');
	$codeHTML = "<input id='sidInput' name='$nameInput' class='jeux_input trous$color' size='$size' type='text'$disab' value=\"$prop\" />";
//	if(jeux_form_correction($indexJeux)) $codeHTML .= '(T'.($indexTrou+1).":$prop/".$GLOBALS['meta']['charset']."[".join('|',$mots)."]:".($ok?'1':'0').'pt)';
	return $oups . $codeHTML;
}

function trous_inserer_les_trous(&$trous, $chaine, $indexJeux) {
	if (preg_match(',<ATTENTE_TROU>([0-9]+)</ATTENTE_TROU>,', $chaine, $regs)) {
	$indexTROU = intval($regs[1]);
	list($texteAvant, $texteApres) = explode($regs[0], $chaine, 2); 
	$texteApres = trous_inserer_les_trous($trous, $texteApres, $indexJeux);
	if (($sizeInput = intval(jeux_config('taille')))==0)
		foreach($trous['propositions'] as $trou) foreach($trou as $mot) $sizeInput = max($sizeInput, strlen($mot));
	$chaine = $texteAvant.jeux_rem('TROU-DEBUT', $indexTROU, '', 'span')
		. trous_inserer_le_trou($trous, $indexJeux, $indexTROU, $sizeInput)
		. jeux_rem('TROU-FIN', $indexTROU, '', 'span')
		. $texteApres; 
	}
	return $chaine;
}

// renvoyer l'ensemble des solutions dans le desordre...
// si plusieurs solutions sont possibles, seule la premiere est retenue
function trous_afficher_indices(&$trous) {
	foreach ($trous['propositions'] as $prop) 
		$indices[] = preg_match(',(.*)/M$,', $prop[0], $reg)?$reg[1]:$prop[0];
	shuffle($indices);
	return '<div class="jeux_indices"><html>'.str_replace(array("'",'&#8217;'),"&#039;",charset2unicode(join(' -&nbsp;', $indices))).'</html></div>';
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

// traitement du jeu : jeu_{mon_jeu}()
function jeux_trous($texte, $indexJeux, $form=true) {
	// initialisation
	$trous_[$indexJeux] = array(
		'propositions'=> array(), 
		'score' => 0, 'score_detaille' => array()
	); 
	$trous = &$trous_[$indexJeux];
	$titre = $html = false;
	$indexTrou = 0;
  // parcourir tous les [separateurs]
	$tableau = jeux_split_texte('trous', $texte); 
	foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	  elseif ($valeur==_JEUX_SCORE) $categ_score = $tableau[$i+1];
	  elseif ($valeur==_JEUX_TROU) {
		// remplacement des trous par : <ATTENTE_TROU>ii</ATTENTE_TROU>
		$html .= "<ATTENTE_TROU>$indexTrou</ATTENTE_TROU>";
		$trous['propositions'][$indexTrou] = jeux_liste_mots($tableau[$i+1]);
		$indexTrou++;
	  }
	}
	// reinserer les trous mis en forme
	$texte = trous_inserer_les_trous($trous, $html, $indexJeux);
	// mode correction ?
	if($correction = jeux_form_correction($indexJeux))
		sort($trous['score_detaille']);
	$id_jeu = _request('id_jeu');
	// recuperation du fond 'jeux/trous.html'
	include_spip('public/assembler');
	$fond = recuperer_fond('jeux/trous', array(
		'id_jeu' => $id_jeu,
		'titre' => $titre,
		'texte' => $texte,
		'correction' => $correction,
		'indices' => jeux_config('indices')?trous_afficher_indices($trous):'',
		'fond_score' => $correction?
			jeux_afficher_score($trous['score'], $indexTrou, $id_jeu, join(', ', $trous['score_detaille']), $categ_score):'',
	));
	// mise en place du formulaire
	$fond = str_replace(
		array('@@FORM_JEUX_DEBUT@@', '@@FORM_JEUX_FIN@@', '@@FORM_CORRIGER@@', '@@RECOMMENCER@@'), 
		$form?array(
			jeux_form_debut('trous', $indexJeux, '', 'post', self()),
			jeux_form_fin(),
			jeux_bouton(jeux_config('bouton_corriger'), $id_jeu),
			jeux_bouton(jeux_config('bouton_refaire'), $id_jeu)):'', 
		$fond
	);
	return $fond;
}
?>
