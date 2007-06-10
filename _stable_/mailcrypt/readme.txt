Plugin cryptmail README

La page à l’écran
=============

Un raccourci Spip e-mail [->quelquun@autre.part.org] est d’habitude converti en 
HTML de la façon suivante : 
<a href="mailto:quelquun@autre.part.org" class="spip_out">quelquun@autre.part.org</a>
 -- ce qui laisse le champ ouvert pour les robots spam.

Intercepté par le code du plugin ce lien(1) va être réécrit pour devenir : 
<a href="#" title="quelquun..&aring;t..autre.part.org" onClick="location.href = 
dolink(this.title); return false;">[Email]</a>

Il n’y a plus d’arobase ni de « mailto » pour orienter les robots. Lorsque le 
visiteur clique sur le lien c’est le JavaScript onClick qui est exécuté. Ce 
script reconstitue le lien.(2)


La version imprimable
================

Cela ne sert à rien d’imprimer une page d’informations avec les adresses e-mail cachées.

Pour la version imprimable donc  on transforme le lien autrement. On rend 
l’adresse e-mail visible (dans le cas où elle a été cachée derrière le lien) en 
remplaçant l’arobase avec une petite image d’arobase. L’adresse s’imprime donc 
sur papier, mais il n’y pas de lien cliquable à l’écran de cette version 
imprimable. Tant qu’on y est, on fait la même chose pour les liens web.

Placée dans une boucle ARTICLE dans une squelette, la balise #CRYPTM_IMP affiche un 
lien qui ouvre une page d'impression dans le squelette par défaut livré avec le 
plugin "cryptm_imp.html". On peut aussi utiliser la forme 
#CRYPTM_IMP{squelette,texte_lien} ou 
#CRYPTM_IMP{squelette,"Voir une page pour l'impression"} ou :
- squelette est le nom d'un squelette d'impression personnalisé
- texte_lien est un raccourci de traduction vers un texte contenu par ex. dans fr_local.php, 
en_local.php (ou on peut aussi taper un texte en clair pour ce lien)

L'apparence du lien peut être configurée avec la classe css cryptm_imp. 

- - - -- - - - 

Notes : (1) Il s'agit de liens contenus dans des balises Spip qui passe 
par le traitement du filtre propre. Pour la boucle ARTICLES, ce sont les balises 
#DESCRIPTIF, #CHAPO, #TEXTE, #PS, #NOTES qui sont ainsi traitées (mais non pas 
#TITRE, #SURTITRE, #SOUSTITRE). Pour usage éventuel avec d'autres boucles voir 
la $table_des_traitements dans le fichier ../ecrire/public/interfaces.php, pour 
voir sur quelles balises le traitement "propre" est appliqué.

(2) Voir l'article http://www.spip-contrib.net/Mailcrypt-systeme-antispam pour plus de détails.

- - - - - - -

Problème connu : 
Le plugin ne détecte pas, pour l'instant, un raccourci placé 
tout à la fin d'une note. C'est à dire, [[Contactez-moi [noone@nowhere.net]]] ne 
marche pas. Pour contourner ce problème ajouter un point ou autre caractère 
avant les deux crochets indiquant la fin de la note.
