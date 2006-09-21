Boucle session

Ce plugin definit un type de boucle SESSION qui est similaire a une
boucle AUTEURS mais qui ne fait qu'une iteration, sur l'auteur correspondant
a la personne identifiee sur le site, qui appelle la page.

Si personne n'est identifie (l'appelant est un visiteur anonyme), la boucle
itere avec un auteur de statut "anonymous", toutes les autres valeurs etant
vides.
TODO: recuperer les elements de session qui existent malgre tout dans spip pour ce type de visiteur

Des criteres specifiques sont egalement definis, pour faciliter les choses :
- {anonymous} fait faire un tour de boucle uniquement si l'utilisateur courant
n'est pas identifie, sinon, la boucle n'itere pas, et on interprete la partie
//B si elle existe
- a l'inverse, {!anonymous} selectionne la boucle si l'utilisateur courant est
identifie (redacteur, visiteur identife, ou admin)
- {admin} selectionne la boucle si l'utilisateur courant a le statut d'admin.
- {!admin} selectionne la boucle si l'utilisateur courant n'a pas le statut
d'admin.
TODO: controler l'aspect restreint de l'admin

Attention : cette boucle n'a de sens que si les pages sont recalculees pour
chaque utilisateur, donc si le squelette appelant a un #CACHE a 0.

Ainsi, l'exemple suivant permet de demander un login si on n'est pas connu, et
d'afficher des trucs sinon :

<BOUCLE_connu(SESSION){!anonymous}>
Bonjour #NOM
bla bla ...
</BOUCLE_connu>
#LOGIN_PUBLIC
<//B_connu>