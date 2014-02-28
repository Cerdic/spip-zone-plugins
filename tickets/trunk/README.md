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
[ ] permettre de lier un ticket à un autre objet SPIP. Par exemple, lier à un article, et dans la page de l'article, afficher la liste de tickets associés.
[ ] crayon d'assignation du ticket : afficher la trombinette si gravatar est activé dans le contrôleur -> Non, en tout cas, pas tant que le contrôleur sera un <select> (pas d'images dans les select)
