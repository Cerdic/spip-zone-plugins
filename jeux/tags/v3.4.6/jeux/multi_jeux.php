<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
# il s'agit ici de proposer la possibilite d'agreger plusieurs jeux differents

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2010             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : https://contrib.spip.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#
/*

Insere un "multi jeux" dans vos articles !
---------------------------------------------

separateurs obligatoires : [jeu]
separateurs optionnels (a placer AVANT le premier [jeu])  : [texte], [titre], [config], [score]
parametres de configurations par defaut :
	voir la fonction jeux_multi_jeux_init() ci-dessous

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[titre]
		Un ensemble de 3 jeux
	[config]
		bouton_refaire=rejouer
	[jeu]
	Tout ce qu'il faut pour le 1er jeu
	[jeu]
	Tout ce qu'il faut pour le 2e jeu
	[jeu]
	Tout ce qu'il faut pour le 3e jeu
</jeux>
*/

// Separateur pour l'enregistrement en base des resultats longs
@define('_SEP_BASE_MULTI_JEUX', '<br />');

// configuration par defaut : jeu_{mon_jeu}_init()
function jeux_multi_jeux_init() {
	return "
		bouton_corriger=corriger // fond utilise pour le bouton 'Corriger' (non ou 0 : pas de bouton)
		bouton_refaire=recommencer // fond utilise pour le bouton 'Reset' (non ou 0 : pas de bouton)
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
		jeux_decode_les_jeux($textes[$i], $indexJeux+$i/1000);
	}

	// sortir du mode 'multi-jeux'
	$scores = $scoreMULTIJEUX;
	$scoreMULTIJEUX = array();
	// detail des scores intermediaires
	$scores['details'][] = join(', ', $scores['score']).' / '.join(', ', $scores['total']);

	unset($textes[0]);
	$texte = join("\n", $textes);
	$tete = '<div class="jeux_cadre multi_jeux_cadre">'.($titre?'<div class="jeux_titre multi_jeux_titre">'.$titre.'<hr /></div>':'');
	$pied = '';
	$id_jeu = _request('id_jeu');

	if(!jeux_form_correction($indexJeux)) {
		if($b = jeux_config('bouton_corriger', $scores['config'])) $texte .= jeux_bouton(strlen($b)?$b:'corriger', $id_jeu);
		$texte = jeux_form_debut('multi_jeux', $indexJeux).$texte.jeux_form_fin();
	} else {
		// obsolete, ici par compatibilite
		if(jeux_config('bouton_reinitialiser', $scores['config'])) $texte .= jeux_bouton('reinitialiser', $id_jeu);
		elseif(jeux_config('bouton_recommencer', $scores['config'])) $texte .= jeux_bouton('recommencer', $id_jeu);
		// affichage du bouton 'Reset'
		elseif($b = jeux_config('bouton_refaire', $scores['config'])) $texte .= jeux_bouton($b, $id_jeu);
		$pied = jeux_afficher_score(array_sum($scores['score']), array_sum($scores['total']), $id_jeu, join(_SEP_BASE_MULTI_JEUX,$scores['details']), $categ_score);
	}
	return $tete.$html.$texte.$pied.'</div>';
}

?>