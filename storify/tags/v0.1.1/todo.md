### Présentation

Le plugin n'est pas utilisable directement, c'est un outil de développement.
Il permet de storifier un article en le rédigeant par registres, chaque registre étant de types différents, prédéfinis par le webmestre

Pour définir un registre on propose une inclusion d'edition dans formulaires/story/xxx.html, ainsi qu'une vue content/story/xxx.html


### TODO

* ajouter un pipeline post-edition et mettre a jour composition=story quand l'article est enregistre et qu'il y a un flag story dedans
et eviter le squelette article.html modifié pour router vers la composition
* un formulaire de configuration pour choisir quels blocs on propose ou non dans les existants
* extension à tout objet avec un champ texte ?
* permettre l'edition par bloc via les crayons
* remplacer l'affichage standard sur ecrire/?exec=article&id_article=xxx par une vue storifiee
* documenter
* utiliser des id de blocs dans l'edition des des registres au lieu d'indice tableau