Pour que la saisie puisse fonctionner correctement, notamment pour les
utilisateurs qui n'ont pas activé le javascript, il faut executer des
traitement au début des fonctions vérifier et traiter. Il est impératif
de toujours commencer vos fonctions verifier et traiter par :

  if ( ! traitements_liste_objets_ok('nom_saisie')) return;

où nom_saisie est le nom de la saisie liste_objets que vous avez créé.
Si le formulaire contient plusieurs saisies lister_objets, il faut
executer ces traitement pour chacune d'entre elles.
