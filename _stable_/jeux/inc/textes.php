<?php

# il s'agit ici d'am�liorer la balise <poesie> de spip.
# � voir : ajouter des lettrines
#          inserer le texte dans un cadre sympa
#          possibilit� d'in�rer une image...

# le code de ce fichier php reste encore � ecrire...

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice�.!vanneufville�@!laposte�.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#
/*

Insere un texte mis en forme dans vos articles !
------------------------------------------------

balises du plugin : <jeux></jeux>
separateurs obligatoires : #POESIE ou #CITATION
separateurs optionnels   : #TITRE, #AUTEUR, #RECUEIL

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	#TITRE
	Messieurs les petits oiseaux
	#POESIE
	Messieurs les petits oiseaux,
	On vide ici les assiettes ;
	Venez donc manger les miettes,
	Les chats n'auront que les os.
	
	Messieurs les petits oiseaux sont pri�s
	De vider les �cuelles,
	Et mesdames les souris
	Voudront bien rester chez elles.
	
	C'est le temps des grandes eaux,
	Le pain est dans la mangeoire,
	Venez donc manger et boire,
	Messieurs les petits oiseaux.
	#AUTEUR
	Victor HUGO
	#RECUEIL
	L'art d'�tre grand p�re, 1877
</jeux>

<jeux>
	#CITATION
	Chaque homme doit inventer son chemin.
	#AUTEUR
	Jean-Paul Sartre
	#RECUEIL
	Les Mouches
</jeux>

<jeux>
	#CITATION
	L'amour est aveugle, il faut donc toucher.
	#AUTEUR
	Proverbe br�silien
</jeux>

*/

// guillemets simples : � et �
define(_GUILLEMET_OUVRANT, '&#8220;'); 
define(_GUILLEMET_FERMANT, '&#8221;');

// guillemets doubles : � et �
define(_GUILLEMET_OUVRANT, '&laquo;');
define(_GUILLEMET_FERMANT, '&raquo;');

function jeux_textes($chaine, $indexJeux) {

  // initialiser  
  $tableau = preg_split('/('._JEUX_TITRE.'|'._JEUX_POESIE.'|'._JEUX_CITATION.'|'._JEUX_AUTEUR.'|'._JEUX_RECUEIL.'|'._JEUX_TEXTE.')/', 
			_JEUX_TEXTE.trim($chaine), -1, PREG_SPLIT_DELIM_CAPTURE);
  $titre = $citation = $poesie = $auteur = $recueil = false;

  // parcourir toutes les #BALISES
  foreach($tableau as $i => $v){
  	 $v = trim($v);
	 if ($v==_JEUX_TITRE) $titre = trim($tableau[$i+1]);
	  elseif ($v==_JEUX_POESIE) $poesie = '<poesie>'.trim($tableau[$i+1]).'</poesie>';
	  elseif ($v==_JEUX_CITATION) $citation = _GUILLEMET_OUVRANT.trim($tableau[$i+1])._GUILLEMET_FERMANT;
	  elseif ($v==_JEUX_AUTEUR) $auteur = trim($tableau[$i+1]);
	  elseif ($v==_JEUX_RECUEIL) $recueil = trim($tableau[$i+1]);
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