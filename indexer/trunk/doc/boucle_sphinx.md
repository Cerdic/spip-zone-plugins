
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
    {index spip,visites}       // intérroge 2 index (sur le même serveur sphinx)


SELECT
------

* @syntaxe `{select champ[,champ[,champ[...]]]}`
* @defaut *
* @multiple 

Note: le critère `{snippet ...}` modifie également la partie SELECT de la requête.

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
* @muliple

Critère de SPIP surchargé.

----

    {!par date}
    {par properties.objet, properties.id_objet}
    {!par properties.objet, properties.id_objet}

INVERSE
-------

* @syntaxe `{inverse[ sens]}`
* @muliple

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
    	{facet date, YEAR(date) ORDER BY date DESC}



TRI SÉLECTIF
----------------
Exemple de tri sur une formule de calcul de « time segment », reprise ici de http://sphinxsearch.com/blog/2010/06/27/doing-time-segments-geodistance-searches-and-overrides-in-sphinxql/
```
#SET{tseg,	"INTERVAL(date, NOW()-90*86400, NOW()-30*86400, NOW()-7*86400, NOW()-86400, NOW()-3600) AS tseg"}<BOUCLE_recherche_sphinx(SPHINX)	{index #ENV{source,spip}}	{recherche #ENV*{recherche}}	{select #GET{tseg}}	{!par tseg}	{!par score}	{facet auteurs, properties.authors ORDER BY COUNT(*) DESC}	{facet tags, properties.tags ORDER BY COUNT(*) DESC}	{facet date, YEAR(date) ORDER BY date DESC}>
```