SPIP shortcut_url
=======

Créer des liens raccourcis facilement avec SPIP dans la joie, le plaisir et la bonne humeur et partager le dans votre réseau social favoris.

Attention ce plugin supprime des boutons de navigation dans le backoffice de SPIP. Il faut installer une instance de SPIP dédié à cet usage. Seul les boutons pour accéder aux auteurs, les outils de maintenance et la configuration sont accessibles.

Ce plugins permet de stocker le le nombre de click par URL, de consulter la provenance des utilisateurs ...

## Plugin SPIP

* d3js (https://github.com/magikcypress/spip_d3js) [utilise]

## Version 1.0.4 (23/12/2015)

- Fixe le nombre de signe pour le raccourci dans options.php

## Version 1.0.4 (21/12/2015)

- Ajout du bouton pour accéder aux stats les rédacteurs, administrateurs
- Lien vers le detail des stats sur la page d'accueil corrigé
- Le tri par compte de click n'est pas fonctionnel car compte alphabétiquement donc 9 > 123 par exemple. (update de base)

## Version 1.0.3 (22/10/2015)

- Ajout d'un define pour choisir le nombre de signe pour le raccourci

## Version 1.0.3 (18/10/2015)

- Ajout des onglets sur la page des stats
- Ajout d'un bouton statistiques dans le menu principal

## Version 1.0.2 (15/10/2015)

- Oublie de déclarer la table des bots dans base.php pour declarer_tables_interfaces()

## Version 1.0.2 (11/10/2015)

- Ajout d'une table bot pour cibler les robots (mise à jour de base)
- Amélioration de la page des stats (Debug carte du monde et perf)

## Version 1.0.1 (2/10/2015)

- Permettre de changer l'URL lors de la modification d'une URL existante

## Version 1.0.1 (1/10/2015)

- Vérifier si l'url raccourcis existe avant de la créer
- Gérer le tri des URL dans les listes
- Lors de la vérification, si l'URL existe afficher l'objet présent dans la base

### Version 1.0.1 (30/09/2015)

- Pagination dans la page detail des stats
- Ne pas afficher les brèves, les mots, les sites dans le menu édition
- Correction sur les autorisation de l'affichage des menu d'entrée pour fonctionner sur 3.0
- Correction sur la liste des URL par auteur
- Un peu de commentaires sur les fonctions

### Version 1.0.0 (29/09/2015)

- Mettre les liens et form sur exec=accueil
- Ajouter dans la recherche
- Faire les révisions
- Rafraichissement de la page après formulaire $res['redirect'] = self();
- Essayer de virer le plan du site (bidouille css crade)
- Edition des urls (titre + url)
- Gérer la suppression de l'ensemble des stats lots de la suppression du url
- Autoriser redacteur à créer lien
- Limiter l'affichage au minimum pour les redacteurs
- Lier à un auteur les urls
- Stats globales et par url et par auteur
- Bug sur les liens raccourcis, erreur 404
- Ajouter une champs human sur l'URL
- Vérifier l'insertion de lien depuis le formulaire
- Partager le lien raccourcis dans les réseaux sociaux