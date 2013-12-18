# Plugin IMAP

Ce plugin fournit :

* un test d'installation de la librairie [imap](http://php.net/manual/en/book.imap.php) de PHP,
* des fonctions additionnelles pour récupérer les fichiers joints (pas d'URL de référence),
* une page de configuration des données de connexion à un compte IMAP,
* un test de connexion.

Le plugin pourrait aussi :

* avoir un logo
* fournir une fonction pour lister les mails de la boîte
* lancer un pipeline pour chaque mail (qui ne fait rien par défaut)
* fournir un génie pour lister les mails régulièrement (et donc lancer les actions du pipeline)
* remplacer la classe de fonctions additionnelles par des fonctions plus "spip" (pas de classe, mettre dans imap_fonctions.php) et ne garder que ce qui est nécessaire.
* ...
