<?php

# il s'agit ici de proposer la possibilite d'agreger plusieurs jeux differents

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2010             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#
/*

Insere un "multi jeux" dans vos articles !
---------------------------------------------

separateurs obligatoires : [jeu]
separateurs optionnels   : [titre], [config], [score]
parametres de configurations par defaut :
//	formulaire=oui	// formulaire ?
	bouton_corriger=oui	// bouton 'Corriger' ?
	bouton_recommencer=oui	// bouton 'Recommencer' ?
	bouton_reinitialiser=non	// bouton 'Reinitialiser' ?


Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[titre]
		Un ensemble de 3 jeux
	[config]
		bouton_corriger=oui
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
//		formulaire=oui	// formulaire ?
		bouton_corriger=oui	// bouton 'Corriger' ?
		bouton_recommencer=oui	// bouton 'Recommencer' ?
		bouton_reinitialiser=non	// bouton 'Reinitialiser' ?
	";
}

// traitement du jeu : jeu_{mon_jeu}()
function jeux_multi_jeux($texte, $indexJeux) {
	$textes = explode('['._JEUX_MULTI_JEUX.']', $texte); 

	// calcul de tous les jeux
	$c = count($textes); $res = '';
	for($i=1; $i<$c; $i++) {
		// decoder le texte obtenu en fonction des signatures et (re!)inclure le jeu
		// ... mais sans ajouter de formulaire !
		$liste = jeux_liste_des_jeux($textes[$i], $indexJeux, false);
	}

	// parcourir tous les #SEPARATEURS concernant le multi-jeux
	$titre = $html = false;
	$tableau = jeux_split_texte('multi_jeux', $textes[0]);
	foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	  elseif ($valeur==_JEUX_SCORE) $categ_score = $tableau[$i+1];
	}

	unset($textes[0]);
	$texte = join("\n", $textes);
	$tete = '<div class="jeux_cadre multi_jeux_cadre">'.($titre?'<div class="jeux_titre multi_jeux_titre">'.$titre.'<hr /></div>':'');
	$pied = '';

	if(!isset($_POST["var_correction_".$indexJeux])) {
		if(jeux_config('bouton_corriger')) $texte .= jeux_bouton_corriger();
		/*if(jeux_config('formulaire'))*/ $texte = jeux_form_debut('multi_jeux', $indexJeux).$texte.jeux_form_fin();
	} else {
		if(jeux_config('bouton_reinitialiser')) $texte .= jeux_bouton_reinitialiser();
		if(jeux_config('bouton_recommencer')) $texte .= jeux_bouton_recommencer();
		$pied = jeux_afficher_score($scoreJEUX, $scoreTOTAL, _request('id_jeu'), $scoreDETAILLE, $categ_score);
	}
	
	return $tete.$texte.$pied.'</div>';

	global $propositionsTROUS, $scoreTROUS, $score_detailTROUS;
	$indexTrou = $scoreTROUS = 0;
	$score_detailTROUS = array();
	
	// parcourir tous les #SEPARATEURS
	$tableau = jeux_split_texte('trous', $texte); 
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