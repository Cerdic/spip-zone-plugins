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

### Version 1.0.3 (15/05/2015)

- Gérer les sous-répertoires
- Les champs obligatoires dans le formulaire de configuration fonctionnels
- Ajout d'un lien pour accéder directement au document quand il est importé dans SPIP.

### Version 1.0.2 (14/05/2015)

- Ajout d'un test de connexion à webdav sur la liste
- Ajoute la possibilité de purger les documents importer dans SPIP
- Ajoute la possibilité d'activer ou desactiver la syncho vers owncloud
- Ajoute la possibilité d'effacer ou non les documents distants du owncloud avec webdav

### Version 1.0.1 (13/05/2015)

- Detecter avec un md5 si le document est deja inséré dans SPIP
- Ajout un crontab qui aspire les médias automatiquement et les importe dans SPIP

### Version 1.0.0 (11/05/2015)

- Configurer le plugins pour se connecter à owncloud
- Récupérer les médias avec webdav du owncloud
- Ajout d'un formulaire pour peupler le fichier json
- Importer les médias dans la base de SPIP et le système de fichiers
- Concater l'URL et la taille pour faire un MD5 pour identifier les fichiers
- Gérer les erreurs proprement lorsque l'authentification échoue

## TODO

- Cacher le user/password dans les URL dans le HTML avec javascript
- Tester le mime-type des documents dans la liste pour éviter de récupérer la racine du répertoire et pour éviter d'avoir une boite mediabox vide sur les documents autres que des images
- Lors de la suppression d'un document dans SPIP on peut vérifier si il y a une md5 dans la base spip_ownclouds et le supprimer
- Si on déplace des fichiers identiques dans un sous-répertoire sur Owncloud, le fichier n'a plus le même md5 