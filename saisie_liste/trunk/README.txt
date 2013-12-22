Pour que la saisie puisse fonctionner correctement, notamment pour les
utilisateurs qui n'ont pas activé le javascript, il faut executer des
traitement au début de la fonction vérifier. Il est impératif de
toujours commencer vos fonctions verifier par :

  if ($err = traitements_liste_objets_ok('nom_saisie')) return $err;

où nom_saisie est le nom de la saisie liste_objets que vous avez créé.
Si le formulaire contient plusieurs saisies lister_objets, il faut
executer ces traitements pour chacune d'entre elles.
Ce code renvoie une erreur au formulaire si le bouton submit qui à été
cliqué est spécifique à la saisie lister_objet. Cela permet de prendre la
main sur les fonctions vérifier et traiter définies pour le formulaire.
Si le bouton cliqué en est un autre, l'execution de vos fonctions de vos
fonctions vérifier et traiter se passera comme d'habitude.
