Pour l'heure, spip 2.1 only !!!

Descriptif :

Ce plugin doit permettre :
-* de cr�er en B.O. des couples URL + liste de mot-cl�s
-* pour pouvoir g�n�rer des liens hypertextes dynamiques � l'aide d'une balise (statique ou dynamique ??? � priori plut�t statique si je m'en r�f�re � programmer.spip...)
dans le but d'obtenir des liens dont l'ancre change � chaque fois qu'une page embarquant la balise dans son squelette est calcul�e


exemple de couple :

* identifiant : lien_spip
* url : http://spip.net
* liste de mot cl� � utiliser comme ancres : spip; systeme de publication; gestion de contenu; best cms ever; ...

Ensuite, l'id�e est d'avoir une balise #BLINKS{lien_spip} (et pourquoi pas un mod�le <blinks|lien_spip>) 
qui, plac�e en pied de page de spip.net par exemple pourrait cr�er un lien vers le site de spip, dont l'ancre change d'une page � l'autre...
