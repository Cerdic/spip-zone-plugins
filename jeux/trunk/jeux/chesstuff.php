<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2011             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : https://contrib.spip.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#
/*

Insere des parties d'echecs dans vos articles !
-----------------------------------------------
	http://chesstuff.blogspot.com/

Attention : utilisation d'une librairie externe : 
-------------------------------------------------
	http://chesstuff.googlecode.com/svn/deployChessViewer.js

separateur obligatoire : [chesstuff]
separateur optionnel   : [config]
parametres de configurations par defaut :
		LightSquares = F3DCC2
		DarkSquares = DDA37B
		Background = CCCCCC
		PuzzleMode = "off"

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
[config]
	PuzzleMode = "on"
[chesstuff]
[Event "Le mat du berger"]
[Site "CPE95"]
[Date "-"]
[Round "1"]
[White "Blancs"]
[Black "Noirs"]
[Result "1-0"]
[WhiteElo "1500"]
[BlackElo "1499"]
1.e4 e5 2.Qh5 Nc6 3.Bc4 Nf6 4.Qxf7#
1-0
</jeux>

Trois solutions pour afficher une partie d'echecs :
- code inline du jeu dans un article
- modele <chesstuffXXX> : fichier PGN joint a l'article. XXX est l'identifiant du fichier joint
- modele <jeuXXX> : jeu cree grace a l'interface du plugin

*/

// configuration par defaut : jeu_{mon_jeu}_init()
function jeux_chesstuff_init() {
	return "
		LightSquares = F3DCC2
		DarkSquares = DDA37B
		Background = CCCCCC
		PuzzleMode = \"off\"
	";
}

// fonction principale, pas de formulaire
function jeux_chesstuff($texte, $indexJeux, $form=true) {
  $pgn = '';
  
  // parcourir tous les [separateurs]
  $tableau = jeux_split_texte('chesstuff', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1)
	 if ($valeur==_JEUX_CHESSSTUFF) $pgn .= $tableau[$i+1];

  // renvoyer le modele du jeu
  return code_echappement(recuperer_fond('modeles/chesstuff', array(
  		'PGN' => $pgn,
  		'LightSquares' => jeux_config('LightSquares'),
  		'DarkSquares' => jeux_config('DarkSquares'),
  		'Background' => jeux_config('Background'),
  		'PuzzleMode' => jeux_config('PuzzleMode'),
	)));

}

?>