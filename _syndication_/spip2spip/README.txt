-----------------------------------------------------------------------------------------------------------
README spip2spip (version plugin)

auteur:  erational - http://www.erational.org
licence: GPL version 2
version: 1.4 
date:    2006.09.13
compatibilte: spip 1.9.1

notice, commentaires: ???
-----------------------------------------------------------------------------------------------------------

spip2spip �tend le principe de la syndication RSS.
il permet de recopier int�gralement des articles d'un spip � l'autre

historique des version
- version manuelle: http://www.spip-contrib.net/SPIP2SPIP-Recopier-des-articles-d
- version automatique : http://www.a-brest.net/article2340.html


cette nouvelle version est disponible sous forme de *plugin*
et normalement beaucoup plus simple � installer que la pr�c�dente

cela permet une synchronisation de plusieurs sites SPIP entre eux.

attention: SPIP 1.9 propose d�j� un syst�me de flux RSS �tendu reprenant l'int�gralit� de l'article
cette contribution permet un suivi par th�matique et importer les articles distants en conservant le formatage spip
(voir aussi le plugin miroir de syndication)

+ principe de fonctionnement
-----------------------------------------------
Le syst�me est pass� sur un groupe de mots-cl�s commun appel� "- spip2spip -"
(auparavant nomm� "spip2spip" renomm� en "- spip2spip -" pour pouvoir facilement l'exclure � l'affichage

       ex.<BOUCLE_listemot(MOTS){id_article}{type!==^[-]}>....</BOUCLE_listemot>
)

chaque mot-cl� de ce groupe sert � d�signer une th�matique donn�e:
- cr�er un mot cl� dans ce groupe permet de cr�er un canal th�matique
- attribuer ce mot-cl� � vos articles pour le diffuser aux autres sites
- attribuer ce mot-cl� � une rubrique pour importer les articles des autres sites

les sites se recopient les articles entre eux gr�ce au cron:
- le formatage des articles spip des articles est conserv�.
- citation automatiquement la source (url de l'article d'origine)
- reprise de la licence (voir contrib licence)
- cr�ation de l'auteur si celui n'est pas pr�sent le site SPIP cible.
- les images et documents sont convertis en documents distants

pour �viter les boucles sans fins et les conflits, 
seuls les articles avec un titre qui n'existent pas dans le SPIP de destination sont import�s

+ installation
-----------------------------------------------

 1) copier le plugin dans le repertoire plugins
 2) activer le plugin
 3) aller dans le plugin, lancer l'installation 
 
 pour les personnes averties, il est possible d'�diter le fichier inc-spip2spip.php
 avec des parametres suppl�mentaires (statut des articles importes, alerte email, mode debug)
  
 
 + utilisation
-----------------------------------------------
 - Aller dans menu de mots cl�s, groupe "- spip2spip -".
 ajouter les mots-cl�s qui vous voulez r�cup�rer des sites distants
 pour connaitre les mots-cl�s des autres sites, consulter dans leurs backend-spip2spip les balises <thema>
 
 par exemple: 
 le site @-brest a comme flux spip2spip http://www.a-brest.net/backend-spip2spip.php
 ce site propose les th�matiques "Libre", "Brest", ....
 je suis interess� par Brest, je rajoute donc le mot "Brest" dans mon groupe de mot-cl� "spip2spip"
 
 - Attribuer chaque  mot-cl� de votre groupe "spip2spip" � une rubrique donn�e 
 les articles syndiqu�s qui portent ce mot-cl� seront recopi�s dans cette rubrique
 
 par exemple:
 j'attribue mon mot-cl� "brest" � ma rubrique "Agenda Brestois"
 ainsi d�s que @-brest propose un articl� li� � Brest, il sera int�gralement dans ma rubrique "Agenda Brestois"

 - De votre cot�, n'oubliez pas d'attribuer les mots cl�s du groupe "licence"
 aux articles que vous voulez partager avec les autres
 
 par exemple:
 j'�cris un article qui parle du dernier ap�ro SPIP de Brest. Je lui ajoute le mot-cl� "Brest".
 Comme cela les sites qui veulent me syndiquer avec SPIP2SPIP pourront r�cup�rer l'article entier. 
 

+ compatibilit�
-----------------------------------------------
Test� sous SPIP 1.9.1

Le script reste compatible avec les fils spip2spip des versions pr�c�dentes 

+ s�curit�
-----------------------------------------------
attention si vous avez des articles cach�s ou des donn�es sensibles, 
utiliser ce script avec pr�caution car il recopie tout :)

+ alternative / projets proches
-----------------------------------------------
- Miroir de syndication


-----------------------------------------------------------------------------------------------------------


