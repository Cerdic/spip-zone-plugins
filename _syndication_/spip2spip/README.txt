-----------------------------------------------------------------------------------------------------------
README spip2spip (version plugin)

auteur:  erational - http://www.erational.org
licence: GPL version 2
version: 1.4 
date:    2006.09.13
compatibilte: spip 1.9.1

notice, commentaires: ???
-----------------------------------------------------------------------------------------------------------

spip2spip étend le principe de la syndication RSS.
il permet de recopier intégralement des articles d'un spip à l'autre

historique des version
- version manuelle: http://www.spip-contrib.net/SPIP2SPIP-Recopier-des-articles-d
- version automatique : http://www.a-brest.net/article2340.html


cette nouvelle version est disponible sous forme de *plugin*
et normalement beaucoup plus simple à installer que la précédente

cela permet une synchronisation de plusieurs sites SPIP entre eux.

attention: SPIP 1.9 propose déjà un système de flux RSS étendu reprenant l'intégralité de l'article
cette contribution permet un suivi par thématique et importer les articles distants en conservant le formatage spip
(voir aussi le plugin miroir de syndication)

+ principe de fonctionnement
-----------------------------------------------
Le système est passé sur un groupe de mots-clés commun appelé "- spip2spip -"
(auparavant nommé "spip2spip" renommé en "- spip2spip -" pour pouvoir facilement l'exclure à l'affichage

       ex.<BOUCLE_listemot(MOTS){id_article}{type!==^[-]}>....</BOUCLE_listemot>
)

chaque mot-clé de ce groupe sert à désigner une thématique donnée:
- créer un mot clé dans ce groupe permet de créer un canal thématique
- attribuer ce mot-clé à vos articles pour le diffuser aux autres sites
- attribuer ce mot-clé à une rubrique pour importer les articles des autres sites

les sites se recopient les articles entre eux grâce au cron:
- le formatage des articles spip des articles est conservé.
- citation automatiquement la source (url de l'article d'origine)
- reprise de la licence (voir contrib licence)
- création de l'auteur si celui n'est pas présent le site SPIP cible.
- les images et documents sont convertis en documents distants

pour éviter les boucles sans fins et les conflits, 
seuls les articles avec un titre qui n'existent pas dans le SPIP de destination sont importés

+ installation
-----------------------------------------------

 1) copier le plugin dans le repertoire plugins
 2) activer le plugin
 3) aller dans le plugin, lancer l'installation 
 
 pour les personnes averties, il est possible d'éditer le fichier inc-spip2spip.php
 avec des parametres supplémentaires (statut des articles importes, alerte email, mode debug)
  
 
 + utilisation
-----------------------------------------------
 - Aller dans menu de mots clés, groupe "- spip2spip -".
 ajouter les mots-clés qui vous voulez récupérer des sites distants
 pour connaitre les mots-clés des autres sites, consulter dans leurs backend-spip2spip les balises <thema>
 
 par exemple: 
 le site @-brest a comme flux spip2spip http://www.a-brest.net/backend-spip2spip.php
 ce site propose les thématiques "Libre", "Brest", ....
 je suis interessé par Brest, je rajoute donc le mot "Brest" dans mon groupe de mot-clé "spip2spip"
 
 - Attribuer chaque  mot-clé de votre groupe "spip2spip" à une rubrique donnée 
 les articles syndiqués qui portent ce mot-clé seront recopiés dans cette rubrique
 
 par exemple:
 j'attribue mon mot-clé "brest" à ma rubrique "Agenda Brestois"
 ainsi dès que @-brest propose un articlé lié à Brest, il sera intégralement dans ma rubrique "Agenda Brestois"

 - De votre coté, n'oubliez pas d'attribuer les mots clés du groupe "licence"
 aux articles que vous voulez partager avec les autres
 
 par exemple:
 j'écris un article qui parle du dernier apéro SPIP de Brest. Je lui ajoute le mot-clé "Brest".
 Comme cela les sites qui veulent me syndiquer avec SPIP2SPIP pourront récupérer l'article entier. 
 

+ compatibilité
-----------------------------------------------
Testé sous SPIP 1.9.1

Le script reste compatible avec les fils spip2spip des versions précédentes 

+ sécurité
-----------------------------------------------
attention si vous avez des articles cachés ou des données sensibles, 
utiliser ce script avec précaution car il recopie tout :)

+ alternative / projets proches
-----------------------------------------------
- Miroir de syndication


-----------------------------------------------------------------------------------------------------------


