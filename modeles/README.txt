
PLugin MODELES
===============


{{{De quoi s'agit-il ?}}}

Un {mod�le} est un raccourci qui permet d'int�grer � son texte un �l�ment X dans une mise en forme pr�d�finie. L'id�e provient de Wikip�dia
	[->http://fr.wikipedia.org/wiki/Wikip%C3%A9dia:Mod%C3%A8les]


{{{En pratique}}}

Ici on a d�fini un seul mod�le, qui g�re le raccourci <code><breveXXX></code> en faisant appel au squelette modele_breve.html (qu'on a soigneusement plac� dans plugins/modeles/ pour simplifier l'installation).


{{{Installation}}}

Mettre le r�pertoire <code>modeles/</code> dans <code>plugins/</code> (� la racine du site), et, dans ecrire/mes_options.php3 ajouter :

	<code>	$plugins[] = 'modeles';</code>


{{{A d�velopper}}}

A quoi peut servir un mod�le ?

-- � ins�rer une br�ve dans un article, selon un squelette donn� ; c'est la cas actuellement, avec <code><breve1></code>, ou <code><breve1|squelette></code> qui appellera non plus modele_breve.html mais modele_squelette.html

-- � mettre en forme un texte (libre) selon une mise en page donn�e (par exemple, sur wikip�dia, une table des mati�res. Cette partie reste � d�finir, tant dans sa syntaxe que dans sa programmation (je pense qu'il faut passer le texte � formater en variable de $contexte_inclus).


Voil�, ce n'est qu'un d�but ; on aurait aussi besoin de jolis CSS de "blocs flottants mais pas trop" pour donner envie :-)

