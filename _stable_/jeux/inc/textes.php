<?php

# il s'agit ici d'améliorer la balise <poesie> de spip.
# à voir : ajouter des lettrines
#          inserer le texte dans un cadre sympa
#          possibilité d'inérer une image...

# le code de ce fichier php reste encore à ecrire...

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#
/*

Insere un texte mis en forme dans vos articles !
------------------------------------------------

balises du plugin : <jeux></jeux>
separateurs obligatoires : [poesie] ou [citation]
separateurs optionnels   : [titre], [auteur], [recueil]

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[titre]
	Messieurs les petits oiseaux
	[poesie]
	Messieurs les petits oiseaux,
	On vide ici les assiettes ;
	Venez donc manger les miettes,
	Les chats n'auront que les os.
	
	Messieurs les petits oiseaux sont priés
	De vider les écuelles,
	Et mesdames les souris
	Voudront bien rester chez elles.
	
	C'est le temps des grandes eaux,
	Le pain est dans la mangeoire,
	Venez donc manger et boire,
	Messieurs les petits oiseaux.
	[auteur]
	Victor HUGO
	[recueil]
	L'art d'être grand père, 1877
</jeux>

<jeux>
	[citation]
	Chaque homme doit inventer son chemin.
	[auteur]
	Jean-Paul Sartre
	[recueil]
	Les Mouches
</jeux>

<jeux>
	[citation]
	L'amour est aveugle, il faut donc toucher.
	[auteur]
	Proverbe brésilien
</jeux>

*/

// guillemets simples : “ et ”
define('_GUILLEMET_OUVRANT', '&#8220;'); 
define('_GUILLEMET_FERMANT', '&#8221;');

// guillemets doubles : « et »
define('_GUILLEMET_OUVRANT', '&laquo;');
define('_GUILLEMET_FERMANT', '&raquo;');

function jeux_textes($texte, $indexJeux) {
  $titre = $citation = $poesie = $auteur = $recueil = false;

  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('textes', $texte);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_POESIE) $poesie = '<poesie>'.$tableau[$i+1].'</poesie>';
	  elseif ($valeur==_JEUX_CITATION) $citation = _GUILLEMET_OUVRANT.$tableau[$i+1]._GUILLEMET_FERMANT;
	  elseif ($valeur==_JEUX_AUTEUR) $auteur = $tableau[$i+1];
	  elseif ($valeur==_JEUX_RECUEIL) $recueil = $tableau[$i+1];
  }
  
  return 
      ($titre?"<span class=\"textes_titre\">$titre</span><br />":'')
  	. ( $poesie?"<span class=\"textes_poesie\">$poesie</span>":
		 ($citation?"<span class=\"textes_citation\">$citation</span>":
		 '')
	  ) 
	. ($auteur?"<br /><span class=\"textes_auteur\">$auteur</span>":'')
	. ($recueil?"<br /><span class=\"textes_recueil\">$recueil</span>":'');
}
?>