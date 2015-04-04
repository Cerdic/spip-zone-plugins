

# Documentation du plugin Migrateur


## Description

Ce plugin permet de migrer un site SPIP existant vers un nouvel emplacement.

Soient deux sites SPIP fonctionnels :
- l'un source (par exemple un site en production),
- l'autre destination (par exemple une future version du site en construction).

Le plugin permet de définir des actions de migrations, par exemple pour :
- synchroniser le répertoire IMG du site source vers celui de destination,
- exporter et récupérer la base de données source,
- insérer cet export SQL dans le site destination (cela nécessite de se ré-authentifier
ensuite au SPIP de destination dont la BDD vient d'être écrasée !),
- activer des plugins,
- exécuter toute tâche de migration que vous avez définie, tel que migrer des tables SQL
dans un nouveau format.


## Installation

Ce plugin doit être présent, actif et configuré sur les 2 sites.

La configuration permet d'indiquer le statut du site (source ou destination),
ainsi que de créer différentes clés de sécurités (côté source) qui serviront
à l'authentification et au chiffrement des échanges avec le site destination.

Ces clés doivent être renseignées sur le site de destination.

L'une de ces clés est périssable (validité de 12h par défaut)
et doit donc être redéfinie régulièrement.



## Liste des actions

Une fois ce plugin installé, il est nécessaire, de surcharger, dans un plugin
dépendant de migrateur, ou dans le répertoire `squelettes/` le fichier
 `migrateur/config.php` de ce plugin. Donc par exemple dans
 `squelettes/migrateur/config.php`

Il faudra alors configurer ce fichier avec les données adéquates,
en indiquant les différentes actions souhaitées.


## Configuration

La configuration se passe en 2 parties.
- d'une part il faut indiquer l'ensemble des configurations (chemins, nom d'utilisateurs
  et mots de passe) nécessaires pour les sites source et destination
- d'autre part il faut indiquer la liste des étapes de migration à réaliser.


## Étapes de migration

Les étapes de migration sont définies par un tableau global `MIGRATEUR_ETAPES`
du fichier de configuration.

Il est composé de couples où
- la clé identifie l'étape de migration
- la valeur décrit (texte) cette étape de façon synthétique.

Dans l'espace privé, la liste des étapes définies seront alors affichées
sur la page du migrateur (exec=migrateur). Il sera possible de lancer n'importe
quelle étape (quelque soit leur ordre) mais il est là encore conseillé
de placer les différentes étapes dans un ordre logique, l'interface
facilitant l'accès à l'étape suivante définie, une fois qu'une étape est terminée.


### Fonctionnement d'une étape

Il est conseillé d'avoir une clé d'étape préfixée pour mieux se repérer.
Par exemple `mig_sync_img` est préfixé de `mig_` donc, pour montrer
que cette étape est définie dans le plugin migrateur directement
(et non dans le répertoire squelettes ou dans un plugin dépendant de migrateur).

Chaque étape, définie par sa clé appelle une fonction PHP spécifique
définie dans le répertoire `migrateur`. Ainsi, la clé `mig_sync_img`
appelle la fonction `migrateur_mig_sync_img` définie dans le fichier
 `migrateur/mig_sync_img.php`, ceci par l'intermédiaire de la fonction
 SPIP `charger_fonction($cle, 'migrateur')`.

Donc, créer une étape consiste à définir un couple clé/valeur dans le tableau
 `MIGRATEUR_ETAPES` et sa fonction correspondante `migrateur_$cle` dans un fichier
 `migrateur/$cle.php` (ce fichier peut être dans votre plugin ou dans votre répertoire
 squelettes, donc dans ce cas dans `squelettes/migrateur/$cle.php` (note : remplacer `$cle`
 par le nom réel de votre clé !)


### Fonction d'étape

Une fonction d'étape effectue donc les actions qu'elle doit réaliser
(à vous de les définir évidemment),

Elle peut générer des logs spécifiques au migrateur, qui s'afficheront
alors dans l'espace privé une fois l'étape effectuée, par l'intermédiaire
de la fonction `migrateur_log`

Vous pouvez utiliser les constantes à disposition pour vous aider dans vos
étapes.

 
### Étapes fournies avec le migrateur

00_rien
: Ne fait rien !

mig_test
: Teste le bon dialogue entre le site destination et le site source

mig_sync_img
: Synchronise le répertoire IMG source avec le répertoire IMG destination.
  Cela supprimera aussi sur le site de destination les fichiers de IMG éventuellement en trop.

mig_bdd_source_make_dump
: Crée un export de la base de données source, sur le site source (dans tmp/dump).
  Cela n'affecte pas la base de données de destination.

mig_bdd_source_get_dump
: Télécharge le dernier export fait sur le site source, dans site destination (dans tmp/dump)
  Cela n'affecte pas la base de données de destination.

mig_bdd_destination_put_dump
: Copie le dernier export réalisé dans la base de données de destination (dans tmp/dump).
  Attention, une fois cela fait, vous n'aurez plus accès au migrateur
  aussitôt. Vous devrez vous reconnecter, et peut être effectuer des mises
  à jour de base de données de SPIP si vos versions de SPIP sont différentes
  entre la source et la destination.

  Si tout s'est bien passé, le plugin Migrateur (et tout autre plugin
  qui était actif dont le préfixe contient le terme `migrateur`) sera toujours actif.
  Vous pourrez donc vous rendre facilement à la page exec=migrateur pour continuer.

  Une partie des plugins du site source ne seront peut être plus actifs.
  C'est le cas si, dans le site de destination, ils n'ont pas le même chemin depuis le
  répertoire `plugins/` que sur le site source. Il vous faudra, si vous le souhaitez,
  les réactiver. Pour cela, une fonction de migration peut s'en charger (le migrateur
  dispose de quelques outils pour faciliter la tâche).


### Autres étapes d'exemple

Dans le fichier `migrateur/99_autres_exemples.php` se trouvent
quelques exemples de fonctions de migrations.



### Fonctions d'aide

Des fonctions d'aide se trouvent dans `inc/migrateur.php` (chargé par défaut
lors de l'exécution d'une action de migration).

Ces fonctions peuvent être utilisées dans vos fonctions d'étapes.
Certaines étapes d'exemples en mettent en application, ce qui peut vous
donner des indications supplémentaires de leur fonctionnement.

migrateur_log()
: Crée un log spécifique au migrateur (sera affiché ensuite au retour de l'étape
  en bas de l'interface de la page du migrateur)

migrateur_vider_cache()
: Vide tous les caches de SPIP

migrateur_obtenir_commande_serveur()
: Retourne le chemin d'execution d'une commande sur le serveur, si cette commande existe

migrateur_activer_plugin_prefixes()
: Active un ou plusieurs plugins en connaissant leurs préfixes

migrateur_deplacer_table_complete()
: Déplace le contenu d'une table SQL dans une autre, en permettant
  d'indiquer les correspondances entre les tables source et destination



