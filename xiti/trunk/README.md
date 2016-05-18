# Xiti pour SPIP

## Installation

`#INCLURE{fond=inclure/marqueur,id_rubrique,id_article,id_mot,id_secteur,lang,page}`

## TODO

### Gestion des niveaux deux

* Affichage de l'`xtsite` dans la liste des niveaux deux (`?exec=xiti_niveaux`)
* Affichage des objets liés à chaque niveaux deux dans la page du niveau
* Sur la page d'un objet, si un niveau 2 est utilisé pour le secteur et / ou pour la langue, l'afficher quelque part
* Suppression des liens des niveaux lors de la suppression d'un niveau
* Ne pouvoir supprimer les liens de niveaux que si rien n'est lié à ce niveau

### Espace privé / Gestion de Xiti

* Sur les pages d'objets, afficher les variables complètes du code Xiti pour les webmestre afin qu'ils puissent voir d'un coup d'oeil ce qui sera généré


## Changelog

### Version 1.x.x 

##### Version 1.5.1

* Suppression des révisions sur les niveaux 2 de Xiti si les révisions sont activées

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