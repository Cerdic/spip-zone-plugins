# Plugins LinkCheck

Ce plugin permet de chercher et tester l’ensemble des liens présents dans les objets. 

[Documentation](https://contrib.spip.net/LinkCheck-verificateur-de-liens)

## Todo

* [ ] ajout d’un lien vers archive.org sur les articles morts
* [ ] export CSV (à finaliser avec generer_url_objet)
* [ ] ajout d’un picto lien mort optionnel dans propre() [+ éventuellement lien sur archive.org le cas échéant]
* [ ] vérifier/traiter automagiquement les migrations http⟹https
* [ ] trouver une meilleure regexp pour la détection de lien, ça doit bien exister sous forme de lib

## Done

* [x] améliorer la détection des liens (ex de bugs : "gazogène.com", ou "lacite.website" sont coupés bizarrement, les urls terminant par une virgule sont aussi coupés)
* [x] présence du lien sur toutes les pages : ce message pourrait être plus informatif en précisant le nombre de liens
* [x] bloc des liens trop visible sur les pages d'objets (articles, rubriques, il devrait être dans la marge car il ne s’agit pas d’un contenu éditable)
* [x] gestion des autoriser() pas très claire (permettre d'ouvrir le plugin aux rédacteurices)

## Versions 1.4.x
### Version 1.4.13 (2018-03-08)

- Correction d'un bug signalé sur spip-users, si le lien est entouré de simple quotes, on conservait la dernière

### Version 1.4.12 (2018-03-07)

* Correction d'un bug: 1 lien inclus dans plusieurs objets, tous non publiés, obtient le statut publié dans la table de centralisation des liens (@eldk)

### Version 1.4.8 (2017-10-03)

* Sur la boite des liens recensés, changer la classe `bam` par `section` pour pouvoir lire le titre
* Sur les liens dans le tableau en colonne sur les objets, on ajoute `word-wrap:break-word` pour conserver l'ensemble du lien visible
* Accepter les liens avec des points virgule : cf [ce commentaire](https://contrib.spip.net/LinkCheck-verificateur-de-liens#forum493607)
* Enlever le lien d'export CSV en bas, on a beaucoup mieux maintenant dans les tableaux

### Version 1.4.7 (2017-09-08)

* Des parenthèses en trop dans la requète générée, MySQL passait outre, MariaDB pas (@nicod_)

### Version 1.4.6 (2017-07-19)

- Eviter de vérifier les certificats pour éviter de considérer des pages comme mortes lorsque le certificat est pourri ou mal configuré
- Lorsque l'ajax est en erreur sur la page listant tous les liens, le relancer et pas juste planter
- Simplifier la regexp mysql qui ne sert qu'à sortir les champs ayant potentiellement des liens (MySQL ne supportant pas UTF-8), par contre, autoriser les caractères UTF-8 coté PHP qui font partie de la séquence `\u00a1-\uffff`
- Les extensions de domaines `.test`, `.example`, `.invalid` et `.localhost` sont réservées pour des domaines de test (cf [cette RFC2606](https://tools.ietf.org/html/rfc2606#section-2)), on ne les extrait pas.

### Version 1.4.5 (2017-07-10)

- Permettre d'avoir `user:pass@` en début d'URL
- Prise en charge du port dans une URL
- Permetre le caractère tilde `~` dans l'URL

### Version 1.4.4

- Remettre la table `spip_linkchecks` en table principale sinon l'autoincrement ne se fait pas.
- Fonction d'upgrade qui permet de corriger ce problème sur des versions buggées déjà installées
- Enlever la table `spip_linkchecks` de la liste des tables à traiter

### Version 1.4.3

- La détection des liens ne prenait qu'un champ à chaque fois, il réinitialisait le compte de liens à chaque champ testé.

### Version 1.4.2

* Dans le pipeline `post_edition` s'assurer que le second argument passé à `in_array` est bien un tableau

### Version 1.4.1

* Ne passer dans le pipeline post_edition avec action instituer que si le statut change
* Interdire la duplication d'une tâche `genie_linkcheck_test_postedition`

### Version 1.4.0

* Permettre de filtrer les liens distants et internes
* Permettre de filtrer par type d'objet (articles, rubriques...)
* Ajouter un formulaire de recherche qui s'applique sur les urls (en faisant un `LIKE` mysql)
* Permettre d'exporter en CSV uniquement la liste filtrée
* Amélioration de la détection de liens
* Déplacement du bloc sur les objets dans la colonne de gauche
* Limiter l'affichage du blocs de liens sur les objets aux auteurs pouvant modifier l'objet
* Déclaration plus moderne de la table linkcheck (dans `declarer_tables_objets_sql()`)
* Utiliser les mêmes limitations de statuts entre la détection de liens générale et celle par le pipeline `post_edition`. Seuls sont conservés les liens sur des objets pouvant être prévisualisés.
* Réparation de la détection des liens sur les rubriques
* Ajout d'un champ `publie` à la fois sur la table `spip_linkchecks` et `spip_linkchecks_liens` testant si l'objet parent est publié.
* Permettre de filtrer sur les liens "Visibles en ligne" (`publie == oui`) et "Non visibles en ligne" (`publie == non`)
* Changement du coté des autorisations, dorénavant : 
  * les administrateurs et les rédacteurs peuvent voir la page linkchecks
  * seuls les webmestres peuvent réinitialiser la base
  * les personnes autorisées à modifier un objet voient les liens contenus dans l'objet éditorial

## Versions 1.3.x

### Version 1.3.7

* Correction d'un bug mysql dans le parcours

### Version 1.3.6

* `.brussels` c'est 8 caractères, on en autorise 9 maintenant (cf [post sur spip-contrib](cf [post sur spip-contrib](https://contrib.spip.net/LinkCheck-verificateur-de-liens#forum489219)

### Version 1.3.5

* Accepter le caractère `@` dans une Url (cf [post sur spip-contrib](https://contrib.spip.net/LinkCheck-verificateur-de-liens#forum489219)
* Limiter l'affichage de l'alerte au webmestre que s'il y a un lien mort ou malade, pas d'affichage s'il n'y a que des liens déplacés par exemple (cf [post sur contrib.spip](https://contrib.spip.net/LinkCheck-verificateur-de-liens#forum489219))
* présence du lien sur toutes les pages : ce message devient plus informatif en précisant le nombre de liens morts, malades et déplacés

### Version 1.3.4

* Accepter le caractère `+` dans une Url (cf [post sur spip-contrib](https://contrib.spip.net/LinkCheck-verificateur-de-liens#forum489212)
* Détecter les liens sur les statuts d'objets que l'on peut prévisualiser et qui sont publiés et non pas seulement sur une sélection arbitraitre de statuts

### Version 1.3.3

* Accepter les caractères `:` et `,` dans une Url (cf [post sur spip-contrib](https://contrib.spip.net/LinkCheck-verificateur-de-liens#forum489081)
* Accepter les accents dans le nom de domaine
* Accepter des extensions de noms de domaine jusqu'à 7 caractères (du type `.website` par exemple)

### Version 1.3.1

* Ne plus analyser `spip_plugins`, ce n'est pas un objet édito du site réellement, [cf ce message sur contrib](https://contrib.spip.net/LinkCheck-verificateur-de-liens?var_mode=calcul#forum488940)
* Petites améliorations de chaînes de langue

### Version 1.3.0

* Ne pas enlever le dernier `/` des urls pour éviter des liens déplacés pour rien.
* Si plusieurs redirections successives, il se peut que l'entête `Location` montre un path local, on récupère donc le domaine
* Bien supprimer `Location:`, `location:` et `content-location` des urls de redirection
* Si la redirection mène sur notre site, afficher les liens internes en redirection, du coup on décode l'url avec `inc/urls`
* Afficher correctement la redirection qu'elle soit interne ou externe dans les détails d'un lien

## Versions 1.2.x

### Version 1.2.3

* Mettre url et redirection dans la même case de tableau pour faciliter la lecture des liens
* Ajouter un bouton de vérification sur chaque lien dans les listes
* Utiliser un User Agent de navigateur lors de la récupération des entêtes pour éviter les anti-bots
* En cas de redirection, on récupère le dernier `Location:` et non le premier (cas de redirections multiples)
* En cas de redirection, on vérifie si la finale ne renvoie pas une 404
* Mettre l'url de redirection détectée dans l'export CSV
* Améliorer les entêtes de colonnes dans l'export CSV
* `linkcheck_en_url()` renvoie false si l'url fournie est vide



### Version 1.2.2

* Un peu de refactoring
* Ajout d'un champ `redirection` dans la table `spip_linkckecks` afin de stocker l'adresse de redirection si s'en est une
* Affichage des redirections dans les tableaux de liens
* Utiliser les fonctions SPIP de `inc/distant` pour analyser les entêtes des liens au lieu d'avoir une fonction personnelle
* Lors de l'analyse totale des liens, les traiter de 5 en 5
* Utiliser `set_time_limit()` pour essayer d'éviter de planter avec un max execution time
* Mettre un timeout de 30 secondes pour la récupération des entêtes
* Si c'est une redirection, analyser les entêtes afin de stocker l'URL finale dans le champ redirection

### Version 1.2.1

* Eviter que les tableaux ne cassent à cause de liens longs
* Amélioration de l'affichage (utilisation de ```#BOITE_OUVRIR``` et ```#BOITE_FERMER```)
* Une autorisation sur la configuration
* Bonnes chaînes de langue dans les comptes de liens
* Correction du mail envoyé, ajout de l'état des liens (malade, deplace ou mort) dans le message
* Nécessite saisies version 2.2.3 pour la fonction ```saisie_balise_structure_formulaire``` (formalisme SPIP 3.1)
* Bien supprimer les liens qui ne sont plus liés à aucun objet (dans le pipeline ```post_edition```)

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

## Todo

* Ajouter la possibilité de remplacer automatiquement (via un bouton) les liens déplacés par la redirection découverte
