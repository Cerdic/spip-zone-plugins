Boucle session

  Ce plugin définit un type de boucle SESSION qui est similaire à une
boucle AUTEURS mais qui ne fait qu'une itération, sur l'auteur correspondant
à la personne identifiée sur le site, qui appelle la page.

  Si personne n'est identifié (l'appelant est un visiteur anonyme), la boucle
itère avec un auteur de statut "anonymous", toutes les autres valeurs étant
vides.

  Des critères spécifiques sont également définis, pour faciliter les choses :
- {anonymous} fait faire un tour de boucle uniquement si l'utilisateur courant
  n'est pas identifié, sinon, la boucle n'itère pas, et on interprète la partie
  //B si elle existe
- à l'inverse, {!anonymous} sélectionne la boucle si l'utilisateur courant est
  identifié (rédacteur, visiteur identifé, ou admin)
- {admin} sélectionne la boucle si l'utilisateur courant a le statut d'admin.
- {!admin} sélectionne la boucle si l'utilisateur courant n'a pas le statut
  d'admin.

  Attention : cette boucle n'a de sens que si les pages sont recalculées pour
chaque utilisateur, donc si le squelette appelant à un #CACHE à 0.

  Ainsi, l'exemple suivant permet de demander un login si on n'est pas connu, et
d'afficher des trucs sinon :

<BOUCLE_connu(SESSION){!anonymous}>
  Bonjour #NOM
  bla bla ...
</BOUCLE_connu>
  #LOGIN_PUBLIC
<//B_connu>
