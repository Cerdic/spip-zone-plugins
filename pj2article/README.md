# Plugin PJ2ARTICLE

## Objectif

Ce plugin sert à créer des articles à partir d'emails. Il récupère les
pièces jointes dans une boite mail, via IMAP, et crée un article pour
chaque pièce jointe, avec le plugin doc2article.

Les mails sont pris depuis une boîte aux lettres (INBOX par défaut),
traités, puis déplacés dans une autre boîte aux lettres (Trash par défaut).

Si un mail ne contient pas de pièce jointe, pas de traitement. Si un mail contient une pièce jointe, elle est importée comme article. Si un mail contient plusieurs pièces jointes, elles sont zippées, puis le zip est importé comme article.

## Fonctionnalités

Ce plugin fournit :

* la configuration des boites d'entrée et de sortie des mails

## TODO :

* logo
* fonction d'import des fichiers sous forme d'articles
* appliquer un cache sur la page de configuration (pour ne pas appeler le serveur IMAP à chaque visite) - éventuellement avec un bouton pour rafraîchir la liste des boîtes aux lettres disponibles.
* surcharger tous les paramètres par des variables globales de type "@define('_PJ2ARTICLE_INTERVALLE_CRON',180)"
