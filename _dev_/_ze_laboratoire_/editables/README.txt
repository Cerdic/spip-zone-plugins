L'id�e :

  Le m�canisme d'action, d'abord �voqu� par Fil sur le lab
(http://svn.berlios.de/svnroot/repos/spiplab/trunk/action/), puis remis au
gout du jour plus r�cemment (http://trac.rezo.net/trac/spip-zone/browser/_libs_/widgets)

Le principe des widgets :

  Disposer de "composants" pour �diter des champs de fa�on sexy et sans avoir �
s'occuper des d�tails techniques. Pour �a, on emballe dans un formulaire les
donn�es � �diter, un "script" expliquant quoi faire de ces donn�es au retour
(sorte de liste de course du genre "mettre � jour la colonne titre dans
spip_articles, de la ligne d'id 42"), et une cl� permettant d'assurer que
l'internaute n'a pas tripot� cette liste de courses.
  Ainsi, la destination du formulaire, une urls "g�n�rique", peut appliquer
cette liste de courses sans s'occuper des aspects s�curit�s une fois que la cl�
� �t� valid�e.
  La liste de courses peut s'accompagner de "callbacks" permettant de valider
certains points qu'on ne peut pas valider d�s la g�n�ration du formulaire
(modif d'un article alors qu'il a �t� publi� entre temps, ou accrochage �
une rubrique sur laquelle on n'a pas les droits).

  La lib de Fil constitue une �bauche de ce principe, en plus d'un d�but de
"hi�rarchie" de classes permettant de d�finir des widgets maison, h�ritant d'un
widget racine qui s'occupe des aspects s�curit� et "liste de courses".

Le principe de ce qui est en dans r�pertoire :

  J'ai essay� de mettre en plce une s�rie de balises #EDITABLE_* permettant
de cr�er facilement des appels � ces widgets.
- #EDITABLE_DEBUT{actions} : d�clare le d�but d'un formulaire dans
  lequel on va ins�rer des widgets. leparam�tre "action" est un nom de
  squelette qui est interpr�t� lors de l'appel et qui doit contenir un script
  avec les actions � faire avec les valeurs envoy�es par le formulaire
- #EDITABLE_FIN : fin de ce formulaire (� faire : permettre de sp�cifier soi
  m�me la forme du bouton submit)
- #EDITABLE{nom,widget,valeur} : g�n�re une instance du widget, permettant
  d'�diter une valeur. le nom est celui donn� au param�tre du post et doit �tre
  unique pour l'ensemble du formulaire

  Les actions : le "script" des actions est un extrait d'xml qui correspond �
une liste de "choses � faire" avec les arguments re�us en post
  explication � suivre

  reste � faire :
- coder l'interpr�tation des scripts d'action
- voir ce que devient la notion de callback
- essayer sur des exemples concrets pour voir si on arrive � faire tout
  ce qu'on veut (notamment la cr�ation d'un article et l'upload d'une image
  dans le m�me formulaire :-)
- coder des widgets ...
