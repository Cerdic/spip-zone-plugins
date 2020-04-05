
SPIP - shortcut_url
=======

Créer des liens raccourcis facilement avec SPIP dans la joie, le plaisir et la bonne humeur et partager les dans votre réseau social favori.

Attention ce plugin supprime des boutons de navigation dans le backoffice de SPIP. Il faut installer une instance de SPIP dédié à cet usage. Seuls les boutons pour accéder aux auteurs, les outils de maintenance et la configuration sont accessibles.

Ce plugins permet de stocker le nombre de click par URL, de consulter la provenance des utilisateurs ...

## Notes serveur

Si vous utilisez mod_security ou mod_security2, il faut désactiver la directive 340162 si elle est activée, sinon vous ne pourrez pas faire de recherches sur les Urls.

## Plugins SPIP

* [geoip_lite](https://zone.spip.org/trac/spip-zone/browser/_plugins_/geoip/branches/v1) [nécessite]
* [Fulltext](https://plugins.spip.net/fulltext.html) [nécessite]
* [d3js](https://zone.spip.org/trac/spip-zone/browser/_plugins_/d3js) [utilise]

## Changelog

### Versions 1.x.x

#### Version 1.3.4 (2017-10-11)

* Bien utilisée l'url en paramètre s'il y en a une dans `$url = parametre_url(_request('url'), 'var_mode', '');`
* Décoder les `&amp;` dans les URLS

#### Version 1.3.3 (10/02/2017)

* Améliorer la page d'un raccourci d'URL
  * On utilise la boite info
  * On met le partageur plus bas en dehors du formulaire
  * On recharge ces éléments en ajax lors de la modification
* On peut modifier l'url du lien que l'on modifie


#### Version 1.3.2 (28/10/2016)

- Modification de déclaration des champs de id_shortcut_url dans les tables, suppression de unsigned pour la compat avec sqlite

#### Version 1.3.1 (15/06/2016)

- Stocker vraiment le referer et non pas l'adresse IP une seconde fois

#### Version 1.3.0 (14/06/2016)

- Gérer la modification et l'insertion des liens dans ```action/editer_shortcut_url```
- Faire fonctionner correctement l'API de création de liens
- Permettre de configurer certaines IPs qui peuvent créer des liens sans identification

#### Version 1.2.0 (13/06/2016)

- Traduction chaîne de langue et pétouille de commentaire
- Nécessite le plugin [fulltext](https://plugins.spip.net/fulltext.html) sinon la recherche sur les urls ne fonctionne pas.
- Ajout dans le README.md de la remarque concernant mod security 

#### Version 1.1.6 (05/06/2016)

- Suppression de la lib geoip
- Necessite le plugin [geoip V1.0.0](https://zone.spip.org/trac/spip-zone/browser/_plugins_/geoip/branches/v1)

#### Version 1.1.5 (02/05/2016)

- Traduction du fichier de langue en Anglais

#### Version 1.1.4 (02/05/2016)

- Nombre de total des clics sur les graphs
- Content-type sur les fichiers json
- Fix un bug sur les graphs des bots
- i18n pour les dates sur les graphs

#### Version 1.1.3 (30/04/2016)

- Affichage du total des clics dans un tableau sur page détail
- Amélioration des graphs sur les bots
- Suppression des tags php fermant
- Suppression class css inutile

#### Version 1.1.2 (29/04/2016)

- Amélioration des graphs
- Mettre des fils d'Ariane partout
- Modification CSS pour éviter que les listes se barrent en sucette

#### Version 1.1.1 (29/04/2016)

- Suppression du fichier d'options qui définissait une valeur qui doit être mise par défaut dans le code
- Nettoyage de code

#### Version 1.1.0 (29/04/2016)

- Amélioration du formulaire d'édition de raccourcis
- Supprimer les urls lors de la modification

#### Version 1.0.9 (18/04/2016)

- Supprimer l'autorisation redacteur pour supprimer un lien (Eratum: Trop risqué, on n'autorise pas la suppression par tous le monde.)
- Ajout des révisions (modification du formulaire d'edition)

#### Version 1.0.8 (13/04/2016)

- Fixe une régression sur la taille des raccourcis dans le formulaire de création.

#### Version 1.0.7 (12/04/2016)

- Index sur la table des logs (spip_shortcut_urls_logs) pour accélérer l'export de stat

#### Version 1.0.6 (11/04/2016)

- Confirmation lors de la suppression d'une URL
- Changement de la génération des titres des URL'S, avec obligation d'avoir un numérique dans la chaîne
- Fixe le bug sur la taille des titres (raccourci) lors de la génération des URL's

#### Version 1.0.5 (08/04/2016)

- Ajout du détail des clicks par bots et par humains
- Ajout du graph en ligne pour le nombre de click par les bots
- Ajout de l'export des statistiques au format CSV
- Fixe le liens vers la liste des stats sur la liste des stats des bots
- Fixe les chaînes de langue qui ne le sont pas
- Fixe le bug de lien vers la page de détail de stats sur la page résumé du click
- Fixe le nombre de click par URL, compte aussi les bots dans le total

#### Version 1.0.4 (23/12/2015)

- Fixe le nombre de signe pour le raccourci dans options.php
- Ajout du bouton pour accéder aux stats les rédacteurs, administrateurs
- Lien vers le detail des stats sur la page d'accueil corrigé
- Le tri par compte de click n'est pas fonctionnel car compte alphabétiquement donc 9 > 123 par exemple. (update de base)

#### Version 1.0.3 (18/10/2015)

- Ajout des onglets sur la page des stats
- Ajout d'un bouton statistiques dans le menu principal
- Ajout d'un define pour choisir le nombre de signe pour le raccourci

#### Version 1.0.2 (15/10/2015)

- Oublie de déclarer la table des bots dans base.php pour declarer_tables_interfaces()
- Ajout d'une table bot pour cibler les robots (mise à jour de base)
- Amélioration de la page des stats (Debug carte du monde et perf)

#### Version 1.0.1 (2/10/2015)

- Permettre de changer l'URL lors de la modification d'une URL existante
- Vérifier si l'url raccourcis existe avant de la créer
- Gérer le tri des URL dans les listes
- Lors de la vérification, si l'URL existe afficher l'objet présent dans la base
- Pagination dans la page detail des stats
- Ne pas afficher les brèves, les mots, les sites dans le menu édition
- Correction sur les autorisation de l'affichage des menu d'entrée pour fonctionner sur 3.0
- Correction sur la liste des URL par auteur
- Un peu de commentaires sur les fonctions

#### Version 1.0.0 (29/09/2015)

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