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

## En cours

### pour 3.2.0 - Migration de 7 champs en groupes de mots clés

Actuellement la table spip_tickets contient sept champs qui servent à décrire sémantiquement les tickets. Pour trois d'entre eux, les choix possibles sont fixés en dur dans le code : severite (bloquant, important, normal, peu_important), tracker (probleme, tache, amélioration) et navigateur (android, firefox...) Les quatre autres sont désactivés par défaut, et ne proposent aucun choix par défaut, mais il est possible d'en ajouter via la page de configuration ou les variables globales : projet, composant, version, jalon.

On pourrait migrer ces 7 champs sous la forme de mots/groupes de mots.

À noter que la création des mots-clés n'aurait lieu que lors de la migration, et pour une installation fraiche, aucun groupe de mots ne serait créé.

Plusieurs problèmes se posent :

* gestion des langues (il faudra éditer tous les mots-clés si on ajoute une nouvelle langue, alors que dans le cas des champs, c'est géré par les fichiers de langue, puisque la liste des choix est fermée)
* tri des tables de tickets : comment choisir les colonnes à afficher si ce sont des groupes de mots-clés, et non plus des champs ? Tous les mots-clés ? Une sélection configurée dans la page de conf des tickets ? Aucun groupe ? Et pour chaque groupe affiché dans la table, comment gérer le tri par colonne dans ces cas ?
* risques de tout casser pendant la migration
* pas besoin de supprimer les champs, il suffit de pouvoir les désactiver si on n'en a pas besoin.

Les avantages à migrer les champs sous la forme de mots/groupes de mots sont :

* faciliter la personnalisation des champs et des choix proposés pour chaque champ (objets éditoriaux, au lieu de valeur en configuration = pénible à changer, ou en dur dans le code). Il sera ensuite possible aux responsables du site d'ajouter/modifier/supprimer des niveaux de sévérité du bug, par exemple, modifier la liste de navigateurs, voire également supprimer des critères (si tracker ou composant ne leur paraît pas utile, par exemple) ou en ajouter d'autres (thème du ticket, région géographique concernée, ou tout autre critère qui leur paraisse pertinent).
* github fait comme ça : chaque projet décide de la sémantique et la classification de ses tickets (encore plus à plat pour github : un seul groupe de mots)

On fera les modifications dans trunk, avec la version 3.2.0. Pour conserver le fonctionnement précédent, utiliser la branche branches/v2 (c'est toujours la version stable, avec ZIP).

#### Partie base de données

On décide de faire la migration automatiquement à la mise à jour du plugin, et non pas sur un déclenchement manuel et par champ, qui était une autre possibilité, afin d'éviter d'avoir à gérer la cohabitation entre les deux situations dans le code.

On met à jour la version de la base (schema) à 2.0.0, et dans autorisations, on ajoute

    $maj['2.0.0'] = array(array('maj_tickets_200'));

Dans la fonction `maj_tickets_200()`, on répétera, pour chacun des 7 champs, les étapes suivantes.

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
3. Dans les squelettes, tout considérer comme des mots-clés, et non plus comme des champ. Dans les formulaires, les crayons (vues et contrôleurs) et les tables qui listent les tickets. Ne pas oublier de prendre en compte la notion de groupe important (pour obliger à choisir une valeur ? Est-ce que ce choix oblige à remplir le champ, ou non ?) et d'unicité du choix, si cette option du groupe de mots est cochée.

## Versions

### 4.0.0

* migration de sept champs (severite, tracker, navigateur, projet, composant, version, jalon) vers des groupes de mots-clés :

 * fonction de migration (81313, 81319) - voir le détail au dessus.
 * numéro de schema pour la migration : 2.0.0 (81340)
 * les mots-clés créés pour le champ severite ont un logo, correspondant à la puce associée dans les squelettes (81326)
 * les groupes de mots-clés et les mots-clés d'un même groupe sont ordonnés, ce qui permet après migration de tout afficher dans le même ordre (81328, 81335)
 
### 3.2.0

* squelettes :

 * simplification du critère de recherche des groupes de mots associables aux tickets (81343)
 * afficher les colonnes de mots dans le tableau de liste de tickets selon la configuration demandée (81469, 81474)
 * privé : afficher une liste de tickets associés pour chaque mot d'un groupe (81482)
 * filtrer le flux RSS des tickets par les mots clés (81476, 81477)
 * afficher le logo d'un mot s'il existe (81327, 81329, 81470)
 * report de commits (81478, 81481, 81486)
 
* fonctionnalités :

 * prendre en compte groupemots_xx dans l'URL de la page d'édition d'un ticket, seulement pour la création d'un nouveau ticket (81342, 81479, 81480)
 * ajouter les mots-clés dans le formulaire de tri des tickets (81345, 81348, 81451)
 * nouveau critère {mots_pargroupe} pour n'afficher que les tickets associés à au moins un mot de chacun des groupes passés en paramètre (81384, 81385, 81455, 81456, 81459, 81460)
 
* configuration :

 * suppression de la configuration "tickets/general/lier_mots" qui n'est pas d'utilité, puisque c'est dans la configuration des groupes de mots qu'on spécifie s'ils peuvent ou non être associés à des tickets (81338)
 * nouveau paramètre "tickets/general/colonnes_groupesmots" pour choisir comment afficher les mots-clés dans les tableaux de tickets (81468)

* bugs :

 * dans le formulaire de forum, ne pas ajouter des saisies pour les champs optionnels des tickets car ils ne sont pas traités, seuls l'assignation et le statut le sont (81349)

* divers :

 * factorisation du code (81371)
 * coquilles (81483, 81484)
 * suppression de code obsolete (81485)

### 3.1.0

* liens tickets-objet:

 * table spip_tickets_liens pour lier tout objet aux tickets (81161, 81210)
 * pipeline affiche_milieu pour montrer et modifier la liste de tickets associés, sur la page privée d'un objet associable aux ticket (81162, 81172)
 * affichage des objets liés sur la page privée (81173) et publique (81174, 81176, 81212) d'un ticket
 * paramètre $associer_objet dans le formulaire EDITER_TICKET pour permettre de créer et associer un ticket, puis revenir à l'objet associé (81170)
 * formulaire d'édition d'un ticket : on ne l'affiche pas si on cherche à l'associer à un objet et que ce n'est pas autorisé (81181, 81182)
 * configuration : choisir les objets associables aux tickets - à noter : tous sont désactivés par défaut, et il n'est pas interdit d'autoriser l'association entre tickets (81171)
 * autorisation : fonction d'autorisation associertickets
 * squelette (noisette) pour créer et associer un ticket à insérer dans la page d'un autre objet (81183)

* squelette public des tickets :

 * critères objet/id_objet pour filtrer la liste des tickets selon leur association ou non à un objet (81177)
 * réduction de la différence entre inclure/liste_tickets_ss_version.html et inclure/liste_tickets.html (81179)

* divers :

 * MAJ du fichier README pour prochaines évolutions (81233, 81239, 81240)
 * squelette public d'un ticket : style (81211)

### 3.0.0

* mots-clés :

 * configuration : permettre les mots-clés sur les tickets
 * configuration : comme seulement pour les groupes de mots-clés configurés pour pouvoir être associés aux tickets, on affiche pour rappel la liste de ces groupes dans la page de configuration
 * fonction d'autorisation "associermots" pour les tickets
 * crayon pour associer/détacher les mots-clés, avec un select par groupe de mots-clés
 * saisies pour les mots-clés dans le formulaire d'édition d'un ticket (80807)
 * si le paramètre de conf "unseul" du groupe de mots est activé, alors on ne permet de sélectionner qu'un seul mot (select simple)

* squelette public d'un ticket :

 * auteur et date sortent de la liste des champs pour aller dans info-publi sous le titre (80622)
 * statut et assignation du ticket passent de formulaire à seulement crayons dans la page publique du ticket (80610, 80695)
 * affichage du logo de l'auteur assigné dans la vue de l'assignation (81123)
 * notification envoyée quand l'assignation est changée par le crayon (80698)
 * forum : pas de réponse directe à un message (thread) si les commentaires sont affichés en liste (81039)
 * forum : on autorise à commenter un ticket fermé (81113)
 * forum : ne pas afficher le titre du commentaire, parce q'il est toujours égal au titre du ticket (81120)
 * réduction de la différence entre content/ et contenu/ (80696)

* divers :

 * configuration : message pour inciter à utiliser le plugin Autorité pour plus de contrôle sur les forums (autoriser l'auteur·e d'un commentaire à le modifier) (81105)
 * configuration : remplacement de saisies oui_non par des saisies checkbox (80514)
 * bug : détail javascript dans les formulaires de statut et assignation (80548)
 * bug : coquille dans la fonction d'assignation (80697)
 * bug : appel des bonnes fonctions d'autorisation (81034)
 * indentation (81038, 81178)
 * fichier README pour la liste d'évolutions (81116, 81119)
