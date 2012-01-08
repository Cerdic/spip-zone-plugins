CONCEPT
-----------------------------------------------

Pour l'heure, spip 2.1 only !!!

Descriptif :

Ce plugin doit permettre :
-* de creer en B.O. des couples URL + liste de mot-cles
-* pour pouvoir generer des liens hypertextes dynamiques a l'aide d'une balise (statique ou dynamique ??? a priori plutot statique si je m'en refere a programmer.spip...)
dans le but d'obtenir des liens dont l'ancre change a chaque fois qu'une page embarquant la balise dans son squelette, est calculee


exemple de couple :

* identifiant : lien_spip
* url : http://spip.net
* liste de mot cles a utiliser comme ancres : spip; systeme de publication; gestion de contenu; best cms ever; ...

Ensuite, l'idee est d'avoir une balise #BLINKS{lien_spip} (et pourquoi pas un modele <blinks|lien_spip>) 
qui, placee en pied de page de spip.net par exemple pourrait creer un lien vers le site de spip, dont l'ancre change d'une page a l'autre...

NOTES DE DEV
-----------------------------------------------

2012.08.01 : corrections de la confusion sur le type d'objet. Les liens s'inserent cdesormais correctement en base de donnees.

reste a faire :
-* trouver moyen d'integrer les verif sur les saisies comme suit :
--* identifiant_blink : comme ds 'menus' > doit etre en un seul mot, en minuscule et ne contenir que des '_' comme caracteres speciaux
--* url_blink : comme son nom l'indique...
--* keywords_blink : chaque ligne du textearea correspond a une valeur et doit etre traitee independament de l'autre. trouver moyen de traiter comme un tableau de valeur (ca c'est chaud !)

-* trouver le moyen de generer la balise #BLINKS{<identifiant_blink>} et le modele <blinks|identifiant_blink> et leur faire cracher le lien souhaite... (encore plus chaud...)