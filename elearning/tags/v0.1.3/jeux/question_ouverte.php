<?php

// configuration par defaut : jeu_{mon_jeu}_init()
function jeux_question_ouverte_init() {
	return "
		bouton_corriger=corriger // fond utilise pour le bouton 'Corriger'
		bouton_refaire=recommencer // fond utilise pour le bouton 'Reset'
	";
}

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
function jeux_question_ouverte($texte, $indexJeux, $form=true){
	$titre = $html = $reponse = "";
	$id_jeu = _request('id_jeu');
	
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
	
	$tete = '<div class="jeux_cadre question_ouverte">' . ($titre?'<div class="jeux_titre question_ouverte_titre">'.$titre.'</div>':'');
	$pied = '</div>';
	
	// Avant envoi du formulaire
	if (!jeux_form_correction($indexJeux)) {
		$champs = '<textarea name="reponse" class="forml" rows="20">'._T('question_ouverte:veuillez_repondre').'</textarea>';
		
		if($form) {
			$tete .= jeux_form_debut('question_ouverte', $indexJeux, '', 'post', self());
			$pied = '<br />' . jeux_bouton(jeux_config('bouton_corriger'), $id_jeu) . jeux_form_fin() . $pied;
		}
		
		return $tete.$html.$champs.$pied;
	}
	// Après envoi du formulaire
	else{
		$reponse = _request('reponse');
		
		if ($id_jeu){
			//jeux_ajouter_resultat(_request('id_jeu'), 0, 0, $reponse);
			jeux_afficher_score(0, 0, _request('id_jeu'), $reponse);
		}
		
		if($form) {
			$pied = jeux_bouton(jeux_config('bouton_refaire'), $id_jeu, $indexJeux) . $pied;
		}
		
		return
			$tete
			.'<p>{{'._T('question_ouverte:merci').'}}</p>'
			.$html
			.'<h5>'._T('question_ouverte:votre_reponse').'</h5>'
			.propre(_request('reponse'))
			.'<h5>'._T('question_ouverte:correction').'</h5>'
			.$correction
			.$pied;
	}
}

