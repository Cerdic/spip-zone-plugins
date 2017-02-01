SPIP - owncloud
=======

Ce plugin permet d'importer avec webdav des médias dans SPIP depuis owncloud.

Vous pouvez récupérer la liste des fichiers depuis votre owncloud en peuplant un fichier json à la racine de tmp/, dans ce fichier json on retrouve la liste des fichiers présents sur le owncloud dans le répertoire que vous avez renseignez dans la configuration. Ensuite, vous pouvez importer vos fichiers dans SPIP un par un.

Vous pouvez activer la syncho sur un répertoire de owncloud pour importer automatiquement les images dans SPIP. On stock le md5 dans une base pour ne pas insérer à nouveau le document. La synchro vous permet d'importer automatiquement beaucoup de document dans SPIP. 

Vous pouvez effacer les documents distants de votre Owncloud après les avoir importer dans SPIP. 
Vous pouvez également purger vos documents déjà importé dans SPIP. 

Nous attirons votre attention sur le fait qu'en activant l'effacement des documents distants sur votre Owncloud et que vous purgez vos documents dans SPIP, vous pouvez perdre l'ensemble de vos fichiers.

# Changelog

## Version 1.x.x

### Version 1.0.9 (01/02/2017)

- Correction bug formulaire de configuration
- Pétouille de #r102585

### Version 1.0.8 (31/01/2017)

- Ajout d'un formulaire de configuration avec traitement des données plus logique
- Ajout de l'importation de tous les médias en un clique
- Mise à jour de la librairie SabreDav
- Traitement des erreurs de connexion

### Version 1.0.7 (12/10/2016)

- Ajout de la fonction curl pour accélérer la récupération des fichiers distants et accessoirement passer https

### Version 1.0.6 (28/09/2016)

- Modification de déclaration des champs de id_owncloud dans les tables, suppression de unsigned pour la compat avec sqlite

### Version 1.0.5 (01/06/2016)

- Ajout de la doc vers contrib dans le paquet.xml
- Suppression du md5 dans la table spip_ownclouds lors de la suppression 
  d'un document inséré dans SPIP.
- Amélioration des erreurs lors de la récupération des fichiers
- Récupérer seulement des fichiers et non les répertoires

### Version 1.0.4 (17/05/2016)

- On sécurise les URL pour ne pas voir apparaître le mot de passe de Owncloud

### Version 1.0.3 (15/05/2016)

- Gérer les sous-répertoires
- Les champs obligatoires dans le formulaire de configuration fonctionnels
- Ajout d'un lien pour accéder directement au document quand il est importé dans SPIP.

### Version 1.0.2 (14/05/2016)

- Ajout d'un test de connexion à webdav sur la liste
- Ajoute la possibilité de purger les documents importer dans SPIP
- Ajoute la possibilité d'activer ou desactiver la syncho vers owncloud
- Ajoute la possibilité d'effacer ou non les documents distants du owncloud avec webdav

### Version 1.0.1 (13/05/2016)

- Detecter avec un md5 si le document est deja inséré dans SPIP
- Ajout un crontab qui aspire les médias automatiquement et les importe dans SPIP

### Version 1.0.0 (11/05/2016)

- Configurer le plugins pour se connecter à owncloud
- Récupérer les médias avec webdav du owncloud
- Ajout d'un formulaire pour peupler le fichier json
- Importer les médias dans la base de SPIP et le système de fichiers
- Concater l'URL et la taille pour faire un MD5 pour identifier les fichiers
- Gérer les erreurs proprement lorsque l'authentification échoue

## TODO

- Lors de la suppression d'un document dans SPIP on peut vérifier si il y a un md5 dans la base spip_ownclouds et le supprimer
- Chiffrer le mot de passe de owncloud dans la table spip_meta (dangereux)