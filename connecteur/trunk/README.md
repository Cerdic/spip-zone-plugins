# Connecteur Universel SPIP

Le connecteur universel implémente la partie création et connexion d'auteur SPIP via un provider indépendant.

## Implémentation d'un SDK

### Configurer un connecteur

Il faut créer, dans un fichier connecteur/*type*_config.php une fonction connecteur_*type*_config_dist.

Cette fonction renvoie simplement un tableau contenant la configuration du connecteur :

```php
return array(
	'connecteur' => 'token', // Type de connecteur
	'type' => 'facebook', // Pour la forme
	// Fonction de l'API pour trouver le token
	'trouver_token' => 'facebook_access_token',
	// Charger un fichier avant d'executer la fonction trouver_token
	'charger_fichier' => 'inc/facebook'
);
```

### Lien de connexion

#### Le lien
La première étape consiste à définir la fonction qui créer le lien (html) de connexion dans un fichier connecteur/*type*_lien.php avec une fonction connecteur_*type*_lien_dist.
Cette fonction reçois en paramètre l'URL de callback

Cette fonction doit renvoyer directement l'url de connexion.

La balise #CONNECTEUR_*TYPE* est alors disponible et renverra l'URL de connexion via le provider.

#### Callback

Il est important que l'envoie du token d'accès ce fasse sur l'action **connexion** de SPIP, sans quoi le connecteur n'activera pas le reste de la procédure.
L'URL de l'action connexion

Cette action prend en paramètre le type de connexion à effectuer.

### Inscription/connexion de l'auteur

L'inscription de l'auteur dans la base de donnée ce fait via une fonction de récupération des informations.
Cette fonction dois ce trouver dans un fichier *connecteur/*type*_info.php*
Cette fonction  doit renvoyer un tableau au minimum une clé nom et une clé email :

```php
array('nom' => 'truc', 'email' => 'truc@machin.be')
```

L'action de connexion va alors créer l'auteur s'il n'existe pas et le connecter au site.
