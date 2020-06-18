<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2020             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : https://contrib.spip.net/Des-jeux-dans-vos-articles     #
#--------------------------------------------------------------------------#
/*

Insere un jeu de points a relier dans vos articles !
----------------------------------------------------
	https://github.com/baptx/connect-points/
	https://drawcode.eu/projects/connect-points/

separateurs obligatoires : [gauche] [droite]
separateurs optionnels   : [solution] [config] [texte] [titre] [copyright]
parametres de configurations par defaut :
	Couleur lignes = rouge		// couleur lors du jeu
	Couleur erreurs = noir		// couleur lors de la correction
	Espace vertical = 40		// espace vertical entre les points
	Espace horizontal = 140		// espace horizontal entrre les points
	Marge horizontale = 30		// marge droite et gauche
	Transparence = non			// couleur de transparence des images
	Recadre auto = non			// recadrage automatique des images
	Compteur = non				// Affichage d'un compteur de secondes
	Aléatoire = non				// melange des colonnes de droite et de gauche
	Tout relier = oui			// faut-il tout relier pour la bonne reponse

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
[config]
	Compteur = oui
	Aléatoire = oui
[titre]
	Traductions Français/Allemand
[gauche]
	la voiture
	l'ordinateur
	l'horloge
	l'école
	le vélo
	les devoirs
	les amis
	la mer
	les vacances
	la casquette
[droite]
	die Uhr
	das Fahrrad
	das Meer
	der Computer
	die Schule
	die Hausaufgaben
	die Ferien
	die Freunde
	die Mütze
	das Auto
[solution]
	10,4,1,5,2,6,8,3,7,9
</jeux>

Deux solutions pour afficher un jeu de points e relier :
- code inline du jeu dans un article
- modele <jeuXXX> : jeu cree grace a l'interface du plugin

*/

// configuration par defaut : jeux_{mon_jeu}_init()
function jeux_relier_init() {
	return "
		Couleur lignes = rouge
		Couleur erreurs = noir
		Espace vertical = 40
		Espace horizontal = 140
		Marge horizontale = 30
		Transparence = non
		Recadre auto = non
		Compteur = non
		Aléatoire = non
		Tout relier = oui
	";
}

define('_relier_balise', '@@RELIER@@');

// liste des libelles (textes, images ou autres) à relier
function relier_liste_mots($texte) {
	// tenir compte des retours à la ligne manuels
	$texte = preg_replace(",[\n\r]+_ +,s", "<br class='manualbr' />", $texte);
	return array_filter(preg_split('/[\r\n]+/', trim($texte)));
}

// le jeux est inséré à la place de la première balise
function relier_placer_jeu($html, $jeu){
    if (strpos($html, _relier_balise) !== false)
        $html = substr_replace($html, $jeu, strpos($html, _relier_balise), strlen(_relier_balise));
	return str_replace(_relier_balise, '', $html);
}

// tente de sécuriser les données à relier
function relier_safe($val) {
	if(!$val) return '';
	// si c'est un texte simple, on traite les raccourcis sans paragraphage
	if(strpos('</', $val)===false)
		$val = PtoBR(traiter_raccourcis(trim($val)));
	// $val = preg_replace(',<br[^>]*>,', ' ', $val);
	return $val;
}

// fonction principale : jeux_{mon_jeu}()
function jeux_relier($texte, $indexJeux, $form = true) {
  $titre = $html = $gauche = $droite = $ordreGauche = $ordreDroite = $newDroite = $newGauche = $solution = $idImage = false;
  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('relier', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_GAUCHE) { $html .= _relier_balise; $gauche = relier_liste_mots($tableau[$i+1]); }
	  elseif ($valeur==_JEUX_DROITE) { $html .= _relier_balise; $droite = relier_liste_mots($tableau[$i+1]); }
	  elseif ($valeur==_JEUX_SOLUTION) $solution = jeux_liste_mots($tableau[$i+1], false);

	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
	  elseif ($valeur==_JEUX_COPYRIGHT) $html .= '<div class="jeux_copyright">' . $tableau[$i+1] . '</div>';
  }

  // cas particulier : une seule image de fond
  // syntaxe : [->imgNNN] (nbPtsG, nbPtsD) (offserX, offset Y)
  // exemple : [->img1341] (7, 7) (25, 15)
  $nbG = $gauche ? count($gauche) : 0;
  $nbD = $droite ? count($droite) : 0;
  if($uneImage = ($nbG + $nbD === 1)) {
	 $imageFond = $nbG ? $gauche[0] : $droite[0];
	 $uneImage = preg_match('@^(?:image|doc|emb|img)(\d+)[^\d]+(\d+)[^\d]+(\d+)[^\d]+([\d\.]+)%[^\d]+([\d\.]+)%[^\d]+([\d\.]+)%[^\d]+([\d\.]+)%$@', trim($imageFond), $regs);
	 if($uneImage) {
		list(, $idImage, $nbGauche, $nbDroite, $distanceX, $distanceY, $pixelsGauche, $pixelsHaut) = $regs;
		 
		$gauche = array_fill(0, $nbGauche, '');
		$droite = array_fill(0, $nbDroite, '');
		//$uneImage = $lienImage . join('|', $regs);
		jeux_config_set('espaceHorizontal', $distanceX);
		jeux_config_set('espaceVertical', $distanceY);
		jeux_config_set('margeHorizontale', $pixelsGauche);
		jeux_config_set('margeVerticale', $pixelsHaut);
	 }
  }
  
  if(!$uneImage && (!$gauche || !$droite))
	  return _L('ERREUR de syntaxe, il manque : ') . (!$droite?_L("la liste de droite"):_L("la liste de gauche"));
  if(!$solution)
	  $solution = range(1, min(count($gauche), count($droite)));
  if($ordreGaucheOK = (strpos($solution[0], '=') !== false)) {
	  // syntaxe : [solution] 1=3, 2=2, 3=2, 4=5, 5=4
	  foreach($solution as $i => $val) {
		  $arr = explode('=', $val);
		  $ordreGauche[$i] = strval(trim($arr[0]));
		  $ordreDroite[$i] = strval(trim($arr[1]));
	  }
  } else {
		// syntaxe : [solution] 1, 2, 3, 4, 5
		foreach($solution as $i=>$val) $ordreDroite[$i] = strval($val);
  }
  foreach($gauche as $i => &$val) {
	  if(!$ordreGaucheOK) $ordreGauche[$i] = $i + 1;
	  $val = array(
		  'index' => $i + 1,
		  'data' => "<div>" . relier_safe($val) . "</div>",
	  );
  }
  foreach($droite as $i => &$val) {
	  $val = array(
		  'index' => $i + 1,
		  'data' => "<div>" . relier_safe($val) . "</div>",
	  );
  }

  if (jeux_config('aleatoire')) {
	  shuffle($gauche); shuffle($droite);
	  foreach ($gauche as $i => &$val) $newGauche[$i + 1] = $val['index'];
	  foreach ($droite as $i => &$val) $newDroite[$i + 1] = $val['index'];
	  foreach ($ordreGauche as &$val) $val = array_search($val, $newGauche);
	  foreach ($ordreDroite as &$val) $val = array_search($val, $newDroite);
  }
  foreach ($gauche as &$val) $val = $val['data'];
  foreach ($droite as &$val) $val = $val['data'];
  $gauche = join('', $gauche);
  $droite = join('', $droite);

  $tete = '<div class="jeux_cadre relier">';
  if ($titre) $tete .= '<div class="jeux_titre relier_titre">' . $titre . '<hr /></div>';

  // recuperation du fond 'jeux/relier.html' en protegeant le javascript
  // traitement ulterieur sur les colonnes droite et gauche
  include_spip('public/assembler');
  $solution = protege_js_modeles(recuperer_fond('modeles/relier', array(
  		'indexJeux' => $indexJeux,
  		'id_jeu' => _request('id_jeu'),
  		'colGauche' => '@@GAUCHE@@',
  		'colDroite' => '@@DROITE@@',
  		'ordreGauche' => base64_encode(join('/', $ordreGauche)),
  		'ordreDroite' => base64_encode(join('/', $ordreDroite)),
	  	'couleurLignes' => jeux_rgb(jeux_config('couleurLignes')),
		'couleurErreurs' => jeux_rgb(jeux_config('couleurErreurs')),
	  	'espaceVertical' => floatval(jeux_config('espaceVertical')),
	  	'espaceHorizontal' => floatval(jeux_config('espaceHorizontal')),
	  	'margeHorizontale' => floatval(jeux_config('margeHorizontale')),
	  	'margeVerticale' => floatval(jeux_config('margeVerticale')),
	  	'toutRelier' => floatval(jeux_config('toutRelier')),
	  	'compteur' => intval(jeux_config('compteur')),
	  	'imageFond' => $idImage,
	)));

  // placer les boutons à la fin
  $solution = explode('<!-- Boutons -->', $solution, 2);
  // mise en clair des images et autres modeles échappés 
  $solution[0] = echappe_retour($solution[0]);	// jeux lui-meme et tous ses textes
  $gauche = echappe_retour($gauche);			// donnees colonne de gauche
  $droite = echappe_retour($droite);			// donnees colonne de droite
  // config avec demande de transparence des images sur un fond de couleur ?
  if($couleur = jeux_config('transparence')) {
	  $couleur = jeux_rgb($couleur, false);
	  $solution[0] = filtrer('image_fond_transparent', $solution[0], $couleur);
	  $gauche = filtrer('image_fond_transparent', $gauche, $couleur);
	  $droite = filtrer('image_fond_transparent', $droite, $couleur);
  }
  $hauteurMaxGD = intval(jeux_config('espaceVertical'));
  // on reduit l'image au plus petit rectangle possible ?
  if(jeux_config('recadreAuto')) {
	  $solution[0] = filtrer('image_recadre_mini', $solution[0]);
	  $gauche = filtrer('image_recadre_mini', $gauche);
	  $droite = filtrer('image_recadre_mini', $droite);
	  // Format : nn%
	  if(preg_match(',(\d+)%,', jeux_config('recadreAuto'), $regs))
		  $hauteurMaxGD = round($hauteurMaxGD * $regs[1] / 100);
  }

  $gauche = filtrer('image_reduire', $gauche, 0, $hauteurMaxGD);
  $droite = filtrer('image_reduire', $droite, 0, $hauteurMaxGD);

  $solution[0] = str_replace(array('@@GAUCHE@@', '@@DROITE@@'), array($gauche, $droite), $solution[0]);
	
  // nouvel echappement
  $html = relier_placer_jeu($html, code_echappement($solution[0])) . $solution[1];
  return $tete . $html  . '</div>';

}

?>