<?php

/*

Exemple de syntaxe
------------------

<jeux>
	[titre]
	Commentaire de texte
	[texte]
	"Défiez-vous de ces cosmopolites qui vont chercher loin dans leurs livres des devoirs qu’ils dédaignent de remplir autour d’eux. Tel philosophe aime les Tartares, pour être dispensé d’aimer ses voisins."
	[question_ouverte]
	Vous commenterez cette citation de Jean-Jacques Rousseau.
	[reponse]
	Bla bla bla un exemple de réponse.
</jeux>

*/
function jeux_question_ouverte($texte, $indexJeux){
	
	$titre = $html = $reponse = "";
	jeux_block_init();
	
	// parcourir tous les [separateurs]
	$tableau = jeux_split_texte('question_ouverte', $texte);
	$nb_questions = 0;
	foreach($tableau as $i => $valeur) if ($i & 1) {
		
		if ($valeur == _JEUX_TITRE)
			$titre = $tableau[$i+1];
		elseif ($valeur == _JEUX_TEXTE)
			$html .= $tableau[$i+1];
		elseif ($valeur == _JEUX_QUESTION_OUVERTE){
			if (($nb_questions += 1) > 1) return _T("question_ouverte:erreur_trop_questions");
			else $html .= $tableau[$i+1];
		}
		elseif ($valeur == _JEUX_REPONSE)
			$correction .= $tableau[$i+1];
		
	}
	
	$tete = '<div class="jeux_cadre">' . ($titre?'<div class="jeux_titre">'.$titre.'</div>':'');
	$pied = '</div>';
	
	// Avant envoi du formulaire
	if (!isset($_POST["var_correction_".$indexJeux])) {
	
		$form = '<div class="formulaire_spip">'.jeux_form_debut('question_ouverte', $indexJeux, 'noajax');
		$form .= '<textarea name="reponse" class="forml" rows="20">'._T('question_ouverte:veuillez_repondre').'</textarea>';
		$form .= '<p class="spip_bouton"><input type="submit" value="'._T('jeux:corriger').'" class="jeux_bouton"></p>'.jeux_form_fin().'</div>';
		
		return $tete.$html.$form.$pied;
	
	}
	// Après envoi du formulaire
	else{
		find_in_path('jeux_ajouter_resultat.php', 'base/', true);
		$reponse = _request('reponse');
		jeux_ajouter_resultat(_request('id_jeu'), 0, 0, $reponse);
		return $tete.'<p>{{'._T('question_ouverte:merci').'}}</p>'.$html.'<p>{{'._T('question_ouverte:reponse').'}}</p>'.$correction.$pied;
	
	}
	
	return;
	
}

?>
