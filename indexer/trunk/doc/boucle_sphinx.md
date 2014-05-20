
Boucle (SPHINX)
===============

Description de la boucle SPIP (SPHINX) qui crée un itérateur
spécifique pour intérroger des données d'un serveur sphinx.

Tags utilisés dans ce document :

* `@defaut x` : Le critère n'est pas obligatoire, sa valeur par défaut en absence est 'x'
* `@unique` : Le critère ne peut être utilisé qu'une fois (la 2è utilisation écrase la première description)
* `@multiple` : Le critère peut être présent plusieurs fois; dans ce cas, les éléments sont cumulatifs.
* `@syntaxe` : Syntaxe du critère


INDEX
-----

* @syntaxe `{index nom[,nom[,nom[...]]]}`
* @defaut SPHINX_DEFAULT_INDEX.
* @unique

----

    {index *}
    {index #ENV{source,spip}}
    {index #ENV{source,''}}    // '' prend l'index par défaut
    {index spip,visites}       // interroge 2 index (sur le même serveur sphinx)


SELECT
------

* @syntaxe `{select champ[,champ[,champ[...]]]}`
* @defaut *
* @multiple

Note: Certains critères modifient également la partie SELECT de la requête.
C'est le cas de : `{snippet ...}`, `{recherche ...}`

----

    {select *}
    {select YEAR(date) AS annee}
    {select MONTH(date) AS mois}

    Les 3 peuvent aussi s'écrire :
    {select *, YEAR(date) AS annee, MONTH(date) AS mois}


RECHERCHE
---------

* @syntaxe `{recherche phrase[,phrase[,phrase[...]]]}`
* @unique

Si plusieurs phrases sont passées, elles seront concaténées par un espace.

Note: le critère `{recherche ...}` modifie également la partie SELECT de la requête
en ajoutant le calcul du score de recherche dans le champ `score`.

----

    {recherche #ENV*{recherche}}
    {recherche #ENV*{recherche},#ENV*{phrase}}


PAR
---

* @syntaxe `{[!]par champ[,champ[,champ[...]]]}`
* @multiple

Critère de SPIP surchargé.

----

    {!par date}
    {par properties.objet, properties.id_objet}
    {!par properties.objet, properties.id_objet}

  voir aussi, plus bas, la section "tri sélectif"


INVERSE
-------

* @syntaxe `{inverse[ sens]}`
* @multiple

Critère de SPIP surchargé

----

    {par date}{inverse}


SNIPPET
-------

* @syntaxe `{snippet champ[,phrase[,limit[,as]]]}`
* @defaut `{snippet content,'',200,snippet}`
* @multiple

Permet d'obtenir des extraits de contenu proche d'un ou des mots de la phrase transmise.

Les mots, qui seront mis en gras par le snippet, sont extraits de la phrase
en ne prenant que les caractères pertinents. Les mots ou chiffres trop courts sont
ignorés.

En absence de critère, dès que des phrases sont disponibles
(critère recherche, filtres tags/auteurs…), un snippet est créé automatiquement
avec les valeurs par défaut et les mots extraits de ces phrases.


----

    {snippet content}                                // calculera automatiquement les mots
    {snippet content,#ENV*{recherche}}
    {snippet content,#ENV*{recherche},200}
    {snippet content,#ENV*{recherche},200,snippet}
    {snippet content,'',200,snippet}                 // calculera automatiquement les mots


FACET
-----

* @syntaxe `{facet alias,query}`
* @multiple

Ajoute une sous requête de facette selon la syntaxe de sphinx.

----

    {facet auteurs, properties.authors ORDER BY COUNT(*) DESC}
    {facet tags, properties.tags ORDER BY COUNT(*) DESC}
    {facet annee, YEAR(date) ORDER BY date DESC}
	  {facet favs, LENGTH(properties.share) AS favs ORDER BY FACET() DESC}



TRI SÉLECTIF
----------------

## Exemple de tri sur un score calculé
	{select WEIGHT()*(1+LENGTH(properties.share)) AS score2}
	{!par score2}



## Exemple de tri sur un « time segment »
formule reprise de
http://sphinxsearch.com/blog/2010/06/27/doing-time-segments-geodistance-searches-and-overrides-in-sphinxql/

```
#SET{tseg,
	"*,INTERVAL(date, NOW()-90*86400, NOW()-30*86400, NOW()-7*86400, NOW()-86400, NOW()-3600) AS tseg"
}
<BOUCLE_recherche_sphinx(SPHINX)
	{index #ENV{source,spip}}
	{recherche #ENV*{recherche}}
	{select #GET{tseg}}
	{!par tseg}
	{!par score}
	{facet auteurs, properties.authors ORDER BY COUNT(*) DESC}
	{facet tags, properties.tags ORDER BY COUNT(*) DESC}
	{facet date, YEAR(date) ORDER BY date DESC}
>
```



FILTRER
-------

En attendant mieux…

Cette histoire de filtres n'est vraiment pas simple.
En attendant mieux, on propose de définir la présence d'un select (et d'un where associé)
si la valeur transmise possède du contenu, sinon le filtre n'est pas appliqué.

Le 3è paramètre utilise une autre sélection, si la valeur vaut '-'.
Les clés @valeur et @valeurs sont remplacés par la donnée attendue quotée, ou les données attendues quotées et séparés par des virgules.

Si `#ENV{tags}` vaut array('toto','tata'), @valeurs aura `"'toto', 'tata'"`

Chaque filtre crée le where associé (filtre = 1).

----

    {filter #TRUC, select si contenu, select si '-'}
    {filter #ENV{auteur}, 'IN(properties.authors, @valeurs)', 'LENGTH(properties.authors) = 0'}
    {filter #ENV{tag}, 'IN(properties.tag, @valeurs)', 'LENGTH(properties.tags) = 0'}
    {filter #ENV{annee}, 'YEAR(date) = @valeur' }
    {filter #ENV{favs}, @valeur <= LENGTH(properties.share)}



PAGES
-----

Permet de décaler le tableau de résultats pour exploiter ensuite la pagination d'une boucle DATA avec la liste des documents.

----

    {pages #DEBUT_DOCUMENTS}
    {pages #DEBUT_DOCUMENTS, 20}