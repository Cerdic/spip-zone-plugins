Plugin Fabrique pour SPIP
Ce plugin génère d'autres plugins !
-----------------------------------


Configuration de PHP
--------------------

PHP limite le nombre de champs envoyés dans un formulaire, pour des
questions de sécurité, à 1000 par défaut, via la définition max_input_vars.

Lorsqu'on a beaucoup d'objets et de champs déclarés via la fabrique
(ici 9 objets pour 68 champs au total), l'ensemble des envois dépasse ce nombre.
Il faut donc modifier la directive  max_input_vars pour augmenter sa valeur

; How many GET/POST/COOKIE input variables may be accepted
max_input_vars = 5000

