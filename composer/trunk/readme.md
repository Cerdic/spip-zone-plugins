Plugin Composer pour SPIP (Expérimental)
========================================

Ce plugin cherche une solution acceptable pour que les plugins
SPIP puissent utiliser des librairies provenant du Packagiste via Composer
sans qu'elles aient à ajouter le code dans chaque plugin en question,
ce qui duplique beaucoup de librairies de bas niveau et crée un fort
risque de déclarer N fois des classes PHP (et PHP n'aime pas !).

## Plugins packagés (réflexions)

Si chaque plugin SPIP était packagé, au sens de composer (avec des composer.json
et un type 'spip-plugin' par exemple), on aurait pu imaginer que chaque site
indique dans un composer.json à la racine la liste des plugins qu'il souhaite,
et leurs librairies dépendentes pourraient être stockées dans vendor/,
tandis que les plugins eux-même, avec un peu de passe passe, pourraient
attérir dans plugins/auto/ ou autre

Voir pour cela :

- [Créer un plugin pour Composer](https://github.com/composer/installers/blob/master/src/Composer/Installers/DrupalInstaller.php)
- [Installer un paquet dans un chemin spécifique](https://getcomposer.org/doc/faqs/how-do-i-install-a-package-to-a-custom-path-for-my-framework.md)
- [Liste des installeurs existants](https://github.com/composer/installers/tree/master/src/Composer/Installers)
- [Exemple de l'installeur de Drupal](https://github.com/composer/installers/blob/master/src/Composer/Installers/DrupalInstaller.php)
- [Créer son installeur](https://getcomposer.org/doc/articles/custom-installers.md)

Ceci étant précisé, nos plugins ne sont actuellement pas packagés,
ni même SPIP d'ailleurs, pour Composer. C'est certainement un point à réfléchir,
même s'il pose d'autres problèmes. À commencer par le fait que Composer
s'exécute uniquement en ligne de commande, et donc il faut pouvoir accéder
à un terminal (ou à `shell_exec()` et des bidouilles) et que tout cela n'est pas
forcément permis par les hébergeurs.

C'est pourtant ce qu'arrive à faire [Bolt](https://bolt.cm/) en faisant afficher
le résultat du terminal dans une modale, avec recherche de packages pour bolt,
installation ou mise à jour de ceux-ci. Ce n'est peut être donc pas impossible.



## En attendant

Ce plugin fait le choix de générer un fichier `composer.json` alimenté
par le pipeline `preparer_composer_json`.

Ce fichier est créé par défaut dans `tmp/composer.json` (malheureusement
on ne peut pas être certain que apache puisse écrire à la racine de SPIP,
donc on n'essaie pas).





### Exemple minimal

Déclarer l'appel et la dépendance dans le `paquet.xml`

	<pipeline nom="preparer_composer_json" inclure="saml_pipelines.php" />
	<necessite nom="composer" compatibilite="[1.0.0;1.*.*]" />

Ajouter un élément requis par Composer dans ce pipeline

	function saml_preparer_composer_json($Composer) {
		$Composer->add_require("simplesamlphp/simplesamlphp", "dev-master");
		return $Composer;
	}
