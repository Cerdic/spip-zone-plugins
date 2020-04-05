# Xiti pour SPIP

## Installation

`#INCLURE{fond=inclure/marqueur,id_rubrique,id_article,id_mot,id_secteur,lang,page}`

## Documentation

### Elements du marqueur xiti

#### Elements obligatoires

* `xtnv` : niveau d'arborescence HTML du site (`document` ou `parent.document`)
* `xtsd` : sous-domaine du collecteur AT Internet
* `xtsite` (`s` dans le hit.xiti) : numéro de site

#### Elements facultatifs

* `xtn2` (`s2` dans le hit.xiti) : niveau 2 dans lequel sera rangée la page (peut être remplacé par `xtcustom`)
* `xtpage` (`p` dans le hit.xiti) : nomme la page auditée et de la ranger dynamiquement dans un chapitre (peut être remplacé par `xtcustom`)

## TODO

### Espace privé / Gestion de Xiti

* Sur les pages d'objets, afficher les variables complètes du code Xiti pour les webmestre afin qu'ils puissent voir d'un coup d'oeil ce qui sera généré

### Marqueur Javascript

* Ajouter la prise en compte des [visiteurs identifiés](http://help.atinternet-solutions.com/fr/implementation/specific_tags/tg_population_fr.htm)
* Regarder si les [résultats de recherche peuvent être loggés](https://help.atinternet-solutions.com/FR/launch_page.htm#implementation/specific_tags/tg_clicks_FR.htm)

## Changelog

### Version 1.x.x 

#### Version 1.8.2

* Trim sur les éléments de configuration qui peuvent couper les stats en cas d'espaces ou de retour ligne

#### Version 1.8.1

* Amélioration de la liste des objets liés à un niveau deux de Xiti

#### Version 1.8.0

* Sur la page d'un objet, si un niveau 2 est utilisé pour le secteur ou pour une rubrique de la hiérarchie l'afficher quelque part

#### Version 1.7.0

* Ne pouvoir supprimer les liens de niveaux que si rien n'est lié à ce niveau
* Affichage des objets liés à chaque niveaux deux dans la page du niveau
* Pouvoir délier facilement un niveau deux de xiti depuis la liste des objets liés dans la page de visu d'un niveau 2
* Dans la liste de tous les niveaux deux, afficher le nombre d'usages (comme les mots clés)
* Corrections mineures

#### Version 1.6.0

* Affichage de l'`xtsite` et du numéro du niveau deux dans la liste des niveaux deux (`?exec=xiti_niveaux`)
* Concaténation de `xtcore.js` et `xtclick.js` via `#PRODUIRE`
* Mettre un fichier `xtcore.js` neutre (nom de domaine `.mondomaine.tld`) et remplacer ce `.mondomaine.tld` par la valeur de `xtdmc` de la conf
* Rendre la valeur de conf `xtdmc` obligatoire.
* Pouvoir donner un niveaux deux X à la home et ce dans les marqueurs par secteur également si les secteurs sont considérés comme home.
* Mise à jour des script xtcore et xtclick

#### Version 1.5.1

* Suppression des révisions sur les niveaux 2 de Xiti si les révisions sont activées
* Grosse simplification des fonctions, on utilise une seule fonction de remplacement de caractères : `strtoascii()` qui est appelée par la fonction `xiti()`, `xiti_nettoyeur` n'est plus nécessaire

#### Version 1.5.0

* Gestion des seconds niveaux (`xtn2`)
* Ajout d'une configuration pour 
  * activer les niveaux deux spécifiques;
  * lier les niveaux deux aux objets choisis;
* Ajout d'un objet `xiti_niveau` disposant de trois champs : 
  * un titre
  * son identifiant `xtn2` (fourni dans l'interface de Xiti)
  * son identifiant `xtsite` (fourni dans l'interface de Xiti)
* Ajout d'une table de liens `xiti_niveaux_liens` et d'un formulaire permettant de lier des niveaux deux aux différents
* Prise en compte des niveaux deux dans le marqueur

#### Version 1.4.0

* Pouvoir configurer un code xtsite par langue si on souhaite loguer chaque langue différemment
* Réorganisation du formulaire autour de trois fieldset principaux (configuration générale, configuration par secteur, configuration par langue)

#### Version 1.3.1

* Stipuler dans la configuration si l'on souhaite que les secteurs ayant une conf particulière soient considérés comme la home

#### Version 1.3.0

* Pouvoir configurer un code xtsite par secteur si on souhaite loguer chaque secteur différemment
* On améliorer le marqueur pour plus de facilité de lecture

#### Version 1.2.0

* Suppression du squelette `marqueur.html` à la racine du site
* Ajout du fichier de traduction du `paquet.xml`

#### Version 1.1.1

* Première version du changelog
* Forcer la validation des champs obligatoires sur le formulaire de configuration