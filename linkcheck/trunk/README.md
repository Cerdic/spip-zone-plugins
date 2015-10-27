# Plugins LinkCheck

Ce plugin permet de chercher et tester l’ensemble des liens présents dans les objets. 

[Documentation](http://contrib.spip.net/LinkCheck-verificateur-de-liens)


## Versions 1.2.x

### Version 1.2.1

* Eviter que les tableaux ne cassent à cause de liens longs
* Amélioration de l'affichage (utilisation de ```#BOITE_OUVRIR``` et ```#BOITE_FERMER```)
* Une autorisation sur la configuration
* Bonnes chaînes de langue dans les comptes de liens
* Correction du mail envoyé, ajout de l'état des liens (malade, deplace ou mort) dans le message
* Nécessite saisies version 2.2.3 pour la fonction ```saisie_balise_structure_formulaire``` (formalisme SPIP 3.1)

### Version 1.2.0

* Refaire fonctionner le post-edition
* Optimisation des images
* Des id numériques seulement ça ne devrait pas exister, on préfixe par ```linkcheck_```
* Une class en minuscule ```linkchecks``` pour avoir l'icone du plugin tout le temps dans les listes d'objets
* Un peu de CSS

## Versions 1.1.x

### Version 1.1.4

* Gérer singulier_ou_pluriel correctement
* Un peu mieux coté CSS
* Ajout du lien de doc
* Passage en stable

### Version 1.1.3

* Petites vérifications dans la liste des champs à traiter
* Petites vérifications dans les pipelines

### Version 1.1.2

* La recherche des liens n'est possible que par le webmestre, donc on se base sur webmestre pour les autorisations

### Version 1.1.1

* Réparer l'affichage dans affiche_milieu suite aux changements de ```linkcheck_tables_a_traiter()```
* Ne pas prendre en compte spip_paquets

### Version 1.1.0

* Compatibilité formalisme 3.1 du formulaire de configuration
* Indentation
* Petites améliorations
* Code css dans une css de plugins
* Chaîne de langue manquante
* Revoir la liste des tables et champs parcourus, on prend toutes les tables dites "principale" sauf spip_syndic_articles (ça mériterait un pipeline ici). On prend tous les champs de type texte (tinytext, longtext, mediumtext, text)
* Utiliser sql_allfetsel + foreach au lieu de sql_select + sql_fetch (moins lourd)
* Pour les statuts inconnus des liens internes => malade au pire

## Versions 1.0.x
	
### Version 1.0.2

* Indentation et sécurité
* Pas de fichier d'options, évite une inclusion inutile

### Version 1.0.1

Version d'origine