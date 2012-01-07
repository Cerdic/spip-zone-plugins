Pour l'heure, spip 2.1 only !!!

Descriptif :

Ce plugin doit permettre :
-* de créer en B.O. des couples URL + liste de mot-clés
-* pour pouvoir générer des liens hypertextes dynamiques à l'aide d'une balise (statique ou dynamique ??? à priori plutôt statique si je m'en réfère à programmer.spip...)
dans le but d'obtenir des liens dont l'ancre change à chaque fois qu'une page embarquant la balise dans son squelette est calculée


exemple de couple :

* identifiant : lien_spip
* url : http://spip.net
* liste de mot clé à utiliser comme ancres : spip; systeme de publication; gestion de contenu; best cms ever; ...

Ensuite, l'idée est d'avoir une balise #BLINKS{lien_spip} (et pourquoi pas un modèle <blinks|lien_spip>) 
qui, placée en pied de page de spip.net par exemple pourrait créer un lien vers le site de spip, dont l'ancre change d'une page à l'autre...
