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

### Version 2.x.x (Passage à smarttag.js)

#### Version 2.3.2 (2018-03-28)

- Tri des niveaux deux par ordre alphabétique dans le formulaire
- Enlever l'usage du pipeline `trig_supprimer_objets_lies` mal utilisé et non pertinent
- Petite optimisation sur le code du marqueur

#### Version 2.3.1 (2017-11-13)

- Compatibilité SPIP 3.2.x

#### Version 2.3.0 (2017-10-17)

* Permettre d'utiliser le [marqueur asynchone](https://developers.atinternet-solutions.com/javascript-en/advanced-features-javascript-en/asynchronous-tag-javascript-en/), c'est une checkbox à activer dans la configuration

#### Version 2.2.4 (2017-10-17)

- Utiliser les fonctions d'édition de liens afin de bénéficier de l'API (stockage des révisions)

#### Version 2.2.3 (2017-10-17)

* Autorisation à voir les révisions sur les niveaux 2 (problème avec le fait d'être un objet avec un underscore)

#### Version 2.2.2 (2017-10-12)

* Eviter de retourner quelque chose de vide avec la fonction `slugify` dans la fonction `xiti()`

#### Version 2.2.1 (2017-08-07)

* Bien avoir une valeur par défaut pour  dans le formulaire de liaison ;
* Un peu de style sur la fiche d'un niveau 2 de Xiti pour prendre moins de place en hauteur;
* Mettre `xiti_id_site` dans le fichier js;
* Revu de la structure html du formulaire de configuration ;
* Formulaire de configuration : il manquait un crochet sur les class `editer_**` ;

#### Version 2.2.0 (2017-07-25)

* passer par le pipeline `optimiser_base_disparus` pour supprimer les liens de niveaux 2 sur des objets qui auraient été supprimés définitivement
* passer par le pipeline `trig_supprimer_objets_lies` pour supprimer les liens de niveaux 2 sur des objets du core supprimés directement
* sur la page d'édition d'un niveau 2, on utilise l'échafaudage  simple de SPIP, il n'y a que deux paramètres passés au formulaire, l'id de l'objet et le retour. L'id_rubrique n'est absolument pas utile. Du coup cela rend fonctionnel le retour ajax après validation du formulaire.

#### Version 2.1.2 (2017-07-13)

- Ne prendre en compte le niveau 2 de hiérarchie que si on n'a pas de niveau x deux sur l'objet actuel

#### Version 2.1.1 (2017-06-27)

* Bien prendre en compte le niveau 2 lorsqu'on est sur la rubrique sur laquelle il a été stipulé

#### Version 2.1.0 (2017-05-23)

- Ne pas limiter la taille des "pages", dixit le support AT-Internet : *La limite du nombre de caractères au sein d'un nom de page est de 255. Au delà de ce nombre, nous ne prendrons plus en compte ceux-ci*. Du coup, ne pas limiter plutôt que de couper aléatoirement.
- Limiter le nombre de chapitres à 3 qui est limité ainsi chez AT-Internet.
- Pour les pages qui auraient dû avoir plus de 3 chapitres, ajouter le titre des rubriques manquantes dans la variable page en les séparant par des `/`.
- Si présence du plugin spip-bonux, utiliser le filtre `slugify` pour générer le nom de page.

#### Version 2.0.4 (2017-04-19)

- Éviter un niveau 2 "0"
- Un timestamp sur le js produit
- Une variable js `conf_page` modifiable

#### Version 2.0.3 (2017-02-27)

* Amélioration de la prise en charge des niveaux 2

#### Version 2.0.2 (2017-02-23)

- Correction d'un bug dans le squelette du marqueur

#### Version 2.0.1 (2017-02-23)

- Oubli d'une virgule dans le marqueur js
- Avoir le lien en https aussi pour le hit en `<noscript>`

#### Version 2.0.0

* Pouvoir configurer la valeur `logssl` pour chaque cas de figure
* Utiliser dans les chaînes de langue le nom des nouvelles options
* Enlever les variables de configuration qui ne semblent plus utilisées par le `smarttag.js`
* Ne plus utiliser les vieux `xtcore.js` et `xtclick.js` dépréciés au profit de `smarttag.js`

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