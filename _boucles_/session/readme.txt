Boucle session

  Ce plugin d�finit un type de boucle SESSION qui est similaire � une
boucle AUTEURS mais qui ne fait qu'une it�ration, sur l'auteur correspondant
� la personne identifi�e sur le site, qui appelle la page.

  Si personne n'est identifi� (l'appelant est un visiteur anonyme), la boucle
it�re avec un auteur de statut "anonymous", toutes les autres valeurs �tant
vides.

  Des crit�res sp�cifiques sont �galement d�finis, pour faciliter les choses :
- {anonymous} fait faire un tour de boucle uniquement si l'utilisateur courant
  n'est pas identifi�, sinon, la boucle n'it�re pas, et on interpr�te la partie
  //B si elle existe
- � l'inverse, {!anonymous} s�lectionne la boucle si l'utilisateur courant est
  identifi� (r�dacteur, visiteur identif�, ou admin)
- {admin} s�lectionne la boucle si l'utilisateur courant a le statut d'admin.
- {!admin} s�lectionne la boucle si l'utilisateur courant n'a pas le statut
  d'admin.

  Attention : cette boucle n'a de sens que si les pages sont recalcul�es pour
chaque utilisateur, donc si le squelette appelant � un #CACHE � 0.

  Ainsi, l'exemple suivant permet de demander un login si on n'est pas connu, et
d'afficher des trucs sinon :

<BOUCLE_connu(SESSION){!anonymous}>
  Bonjour #NOM
  bla bla ...
</BOUCLE_connu>
  #LOGIN_PUBLIC
<//B_connu>
