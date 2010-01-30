<?php

# il s'agit ici de proposer la possibilite d'agreger plusieurs jeux differents

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2010             #
#  Contact : patrice�.!vanneufville�@!laposte�.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#
/*

Insere un "multi jeux" dans vos articles !
---------------------------------------------

separateurs obligatoires : [jeu]
separateurs optionnels (a placer AVANT le premier [jeu])  : [texte], [titre], [config], [score]
parametres de configurations par defaut :
	bouton_corriger=oui	// bouton 'Corriger' ?
	bouton_recommencer=oui	// bouton 'Recommencer' ?
	bouton_reinitialiser=non	// bouton 'Reinitialiser' ?
	scores_intermediaires=oui	// scores intermediaires ?


Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[titre]
		Un ensemble de 3 jeux
	[config]
		bouton_recommencer=non
		bouton_reinitialiser=oui
	[jeu]
	Tout ce qu'il faut pour le 1er jeu
	[jeu]
	Tout ce qu'il faut pour le 2e jeu
	[jeu]
	Tout ce qu'il faut pour le 3e jeu
</jeux>
*/

// configuration par defaut : jeu_{mon_jeu}_init()
function jeux_multi_jeux_init() {
	return "
		bouton_corriger=oui	// bouton 'Corriger' ?
		bouton_recommencer=oui	// bouton 'Recommencer' ?
		bouton_reinitialiser=non	// bouton 'Reinitialiser' ?
		scores_intermediaires=non	// scores intermediaires ?
	";
}

// traitement du jeu : jeu_{mon_jeu}()
function jeux_multi_jeux($texte, $indexJeux) {
	global $scoreMULTIJEUX;

	// separer les jeux et obtenir la config du 'multi-jeux'
	$textes = explode('['._JEUX_MULTI_JEUX.']', $texte); 
	// parcourir tous les #SEPARATEURS concernant le 'multi-jeux', dont la config
	$titre = $html = '';
	$tableau = jeux_split_texte('multi_jeux', $textes[0]);
	foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	  elseif ($valeur==_JEUX_SCORE) $categ_score = $tableau[$i+1];
	}

	// entrer en mode 'multi-jeux' : initialiser les scores
	$scoreMULTIJEUX = array('score'=>array(), 'total'=>array(), 'details'=>array(), 'config'=>jeux_config_tout() );
	// decoder tous les jeux
	$c = count($textes); $res = '';
	for($i=1; $i<$c; $i++) {
		// decoder le texte obtenu en fonction des signatures et (re!)inclure le jeu
		jeux_decode_les_jeux($textes[$i], $indexJeux);
	}

	// sortir du mode 'multi-jeux'
	$scores = $scoreMULTIJEUX;
	$scoreMULTIJEUX = array();

	unset($textes[0]);
	$texte = join("\n", $textes);
	$tete = '<div class="jeux_cadre multi_jeux_cadre">'.($titre?'<div class="jeux_titre multi_jeux_titre">'.$titre.'<hr /></div>':'');
	$pied = '';

	if(!isset($_POST["var_correction_".$indexJeux])) {
		if(jeux_config('bouton_corriger', $scores['config'])) $texte .= jeux_bouton_corriger();
		$texte = jeux_form_debut('multi_jeux', $indexJeux).$texte.jeux_form_fin();
	} else {
		if(jeux_config('bouton_reinitialiser', $scores['config'])) $texte .= jeux_bouton_reinitialiser();
		if(jeux_config('bouton_recommencer', $scores['config'])) $texte .= jeux_bouton_recommencer();
		$pied = jeux_afficher_score(array_sum($scores['score']), array_sum($scores['total']), _request('id_jeu'), join('<br />',$scores['details']), $categ_score);
	}
	
	return $tete.$html.$texte.$pied.'</div>';

}
?>