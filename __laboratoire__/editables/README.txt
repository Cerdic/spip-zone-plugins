L'idée :

  Le mécanisme d'action, d'abord évoqué par Fil sur le lab
(http://svn.berlios.de/svnroot/repos/spiplab/trunk/action/), puis remis au
gout du jour plus récemment (http://trac.rezo.net/trac/spip-zone/browser/_libs_/widgets)

Le principe des widgets :

  Disposer de "composants" pour éditer des champs de façon sexy et sans avoir à
s'occuper des détails techniques. Pour ça, on emballe dans un formulaire les
données à éditer, un "script" expliquant quoi faire de ces données au retour
(sorte de liste de course du genre "mettre à jour la colonne titre dans
spip_articles, de la ligne d'id 42"), et une clé permettant d'assurer que
l'internaute n'a pas tripoté cette liste de courses.
  Ainsi, la destination du formulaire, une urls "générique", peut appliquer
cette liste de courses sans s'occuper des aspects sécurités une fois que la clé
à été validée.
  La liste de courses peut s'accompagner de "callbacks" permettant de valider
certains points qu'on ne peut pas valider dès la génération du formulaire
(modif d'un article alors qu'il a été publié entre temps, ou accrochage à
une rubrique sur laquelle on n'a pas les droits).

  La lib de Fil constitue une ébauche de ce principe, en plus d'un début de
"hiérarchie" de classes permettant de définir des widgets maison, héritant d'un
widget racine qui s'occupe des aspects sécurité et "liste de courses".

Le principe de ce qui est en dans répertoire :

  J'ai essayé de mettre en plce une série de balises #EDITABLE_* permettant
de créer facilement des appels à ces widgets.
- #EDITABLE_DEBUT{actions} : déclare le début d'un formulaire dans
  lequel on va insérer des widgets. leparamètre "action" est un nom de
  squelette qui est interprété lors de l'appel et qui doit contenir un script
  avec les actions à faire avec les valeurs envoyées par le formulaire
- #EDITABLE_FIN : fin de ce formulaire (à faire : permettre de spécifier soi
  même la forme du bouton submit)
- #EDITABLE{nom,widget,valeur} : génère une instance du widget, permettant
  d'éditer une valeur. le nom est celui donné au paramètre du post et doit être
  unique pour l'ensemble du formulaire

  Les actions : le "script" des actions est un extrait d'xml qui correspond à
une liste de "choses à faire" avec les arguments reçus en post
  explication à suivre

  reste à faire :
- coder l'interprétation des scripts d'action
- voir ce que devient la notion de callback
- essayer sur des exemples concrets pour voir si on arrive à faire tout
  ce qu'on veut (notamment la création d'un article et l'upload d'une image
  dans le même formulaire :-)
- coder des widgets ...
