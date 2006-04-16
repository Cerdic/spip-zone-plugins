
PLugin MODELES
===============


{{{De quoi s'agit-il ?}}}

Un {modèle} est un raccourci qui permet d'intégrer à son texte un élément X dans une mise en forme prédéfinie. L'idée provient de Wikipédia
	[->http://fr.wikipedia.org/wiki/Wikip%C3%A9dia:Mod%C3%A8les]


{{{En pratique}}}

Ici on a défini un seul modèle, qui gère le raccourci <code><breveXXX></code> en faisant appel au squelette modele_breve.html (qu'on a soigneusement placé dans plugins/modeles/ pour simplifier l'installation).


{{{Installation}}}

Mettre le répertoire <code>modeles/</code> dans <code>plugins/</code> (à la racine du site), et activer le plugin dans le panneau d'administration des plugins de l'interface privee

{{{A développer}}}

A quoi peut servir un modèle ?

-- à insérer une brève dans un article, selon un squelette donné ; c'est la cas actuellement, avec <code><breve1></code>, ou <code><breve1|squelette></code> qui appellera non plus modele_breve.html mais modele_squelette.html

-- à mettre en forme un texte (libre) selon une mise en page donnée (par exemple, sur wikipédia, une table des matières. Cette partie reste à définir, tant dans sa syntaxe que dans sa programmation (je pense qu'il faut passer le texte à formater en variable de $contexte_inclus).


Voilà, ce n'est qu'un début ; on aurait aussi besoin de jolis CSS de "blocs flottants mais pas trop" pour donner envie :-)


{{{Syntaxe ?}}}

* La syntaxe wikipédia a l'air un peu compliquée, si j'en juge par l'exemple donné là :
http://fr.wikipedia.org/wiki/Wikipédia:Modèles/Infobox#Fruit
et le code "source" :
http://fr.wikipedia.org/w/index.php?title=Wikipédia:Modèles/Infobox&action=edit&section=8

* <breve1|squelette> ne devrait-il pas appeller modele_breve_squelette au lieu de modele_squelette ?
