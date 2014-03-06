# README

Système de suivi de bugs

Références :

 * http://contrib.spip.net/Tickets-suivi-de-bugs
 * http://plugins.spip.net/tickets.html

## TODO

Quelques idées d'évolution du plugin Tickets

[ ] mutualiser le code entre contenu/ et content/
[ ] remplacer les champs "version", "jalon", "composant", "projet", "sévérité", "navigateur", "tracker" par des mots-clés
[ ] permettre à l'auteur d'un commentaire de le supprimer
[ ] ajouter la possibilité de suivre un ticket (bouton Suivre sur la page du ticket), et recevoir les notifications des modifications ou ajouts de commentaires - ça simplifiera aussi le formulaire de forum, en supprimant la case à cocher "Prévenez-moi de tous les nouveaux commentaires de cette discussion par email" (plugin comments)
[ ] supprimer la redondance : le statut ou l'assignation du ticket peuvent déjà être modifiés par les crayons, ou dans le formulaire d'édition du ticket - on peut les retirer du formulaire de forum.
[ ] lier les plugins tickets et agenda pour permettre de créer facilement des dates limites, pour les tickets, et les exporter dans un agenda (CalDAV ?), et même éventuellement déclencher des notifications
[ ] afficher, au lieu du fil de commentaires, un fil d'activité, mélangeant commentaires et révisions (champs et mots-clés associés ou supprimés) - en passant, on pourrait mettre le texte du ticket dans le flux, come premier commentaire.
[ ] crayon d'assignation du ticket : afficher la trombinette si gravatar est activé dans le contrôleur -> Non, en tout cas, pas tant que le contrôleur sera un <select> (pas d'images dans les select)

## En cours de discussion

### Migration de 7 champs en groupes de mots clés

Actuellement la table spip_tickets contient sept champs qui servent à décrire sémantiquement les tickets. Pour trois d'entre eux, les choix possibles sont fixés en dur dans le code : severite (bloquant, important, normal, peu_important), tracker (probleme, tache, amélioration) et navigateur (android, firefox...) Les quatre autres sont désactivés par défaut, et ne proposent aucun choix par défaut, mais il est possible d'en ajouter via la page de configuration ou les variables globales : projet, composant, version, jalon.

On pourrait migrer ces 7 champs sous la forme de mots/groupes de mots.

À noter que la création des mots-clés n'aurait lieu que lors de la migration, et pour une installation fraiche, aucun groupe de mots ne serait créé.

#### Arguments contre la migration

Plusieurs problèmes se posent :

* gestion des langues (il faudra éditer tous les mots-clés si on ajoute une nouvelle langue, alors que dans le cas des champs, c'est géré par les fichiers de langue, puisque la liste des choix est fermée)
* tri des tables de tickets : comment choisir les colonnes à afficher si ce sont des groupes de mots-clés, et non plus des champs ? Tous les mots-clés ? Une sélection configurée dans la page de conf des tickets ? Aucun groupe ? Et pour chaque groupe affiché dans la table, comment gérer le tri par colonne dans ces cas ?
* risques de tout casser pendant la migration
* sortir de l'idée originale des tickets, faits pour du débuggage de logiciel, à la redmine.

#### Arguments pour la migration

Les avantages à migrer les champs sous la forme de mots/groupes de mots sont :

* faciliter la personnalisation des champs et des choix proposés pour chaque champ (objets éditoriaux, au lieu de valeur en configuration = pénible à changer, ou en dur dans le code). Il sera ensuite possible aux responsables du site d'ajouter/modifier/supprimer des niveaux de sévérité du bug, par exemple, modifier la liste de navigateurs, voire également supprimer des critères (si tracker ou composant ne leur paraît pas utile, par exemple) ou en ajouter d'autres (thème du ticket, région géographique concernée, ou tout autre critère qui leur paraisse pertinent).
* github fait comme ça : chaque projet décide de la sémantique et la classification de ses tickets (encore plus à plat pour github : un seul groupe de mots)

#### Partie base de données

On décide de faire la migration automatiquement à la mise à jour du plugin, et non pas sur un déclenchement manuel et par champ, qui était une autre possibilité, afin d'éviter d'avoir à gérer la cohabitation entre les deux situations dans le code.

On met à jour la version de la base (schema) à 1.8.0, et dans autorisations, on ajoute

    $maj['1.8.0'] = array(array('maj_tickets_180'));

Dans la fonction `maj_tickets_180()`, on répétera, pour chacun des 7 champs, les étapes suivantes.

1. Récupérer la liste des choix

On récupère dans un tableau, vide par défaut. Dans les trois premiers cas, c'est directement dans le code, il suffit de copier. Dans les quatre autres cas, on ajoute au tableau les éventuelles valeurs trouvées dans la table spip_meta (configuration) et dans la variable globale _TICKETS_LISTE_.... Si le tableau est vide, on passe à l'étape 5.

2. Créer un groupe de mots.

Pour le titre, le nom du champ. On ne vérifie pas s'il existe un autre groupe avec le même nom, puisqu'il n'y a pas de condition d'unicité sur le titre. On ne gère pas le multilinguisme (on le laisse aux adminsitrateurs/trices), on met juste le nom dans la langue du site.

Mettre aussi dans le descriptif rapide une indication que le groupe a été créé par le plugin tickets ?

Enfin, dans la configuration du groupe :

* cocher (façon de parler) "associable avec tickets"
* cocher "On ne peut sélectionner qu’un seul mot-clé à la fois dans ce groupe", sauf peut être pour "navigateurs".
* cocher uniquement "Les mots de ce groupe peuvent être attribués par : les administrateurs du site" et pas les rédacteurs.

3. Créer les mots-clés

Pour chaque élément du tableau, créer un mot-clé : titre et éventuellement descriptif rapide dans la langue du site.

4. Création des liens

Associer chaque ticket au nouveau mot-clé qui correspond à la valeur du champ (si le champ est rempli).

5. Ménage

Supprimer la colonne de la table spip_tickets, et l'éventuelle configuration des choix possible du champ dans spip_meta.

#### Partie code

1. Virer les textarea dans la page de configuration des tickets.
2. Virer la configuration "utiliser les mots-clés" - elle n'a pas de sens, puisque c'est géré dans la configuration de chaque groupe de mots (associer ou non avec les tickets). On laisse par contre une explication avec la liste des groupes de mots associables aux tickets, avec lien vers leur page privée, plus un lien globale de gestion des groupes de mots. Si aucun groupe de mots n'est associable aux tickets, un message spécifique pour expliquer qu'on peut lier des mots aux tickets.
3. Dans les squelettes, tout considérer comme des mots-clés, et non plus comme des champ. Dans les formulaires, les crayons (vues et contrôleurs) et les tables qui listent les tickets. Ne pas oublier de prendre en compte la notion de groupe important (pour obliger à choisir une valeur) et d'unicité du choix, si cette option du groupe de mots est cochée.
