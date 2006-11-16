<?php

/* Balise #CALENDRIER...
	...et outils simples de pagination chronologique
   Auteur James (c) 2006
   Plugin pour spip 1.9.2
   Licence GNU/GPL
*/

//critere {calendrier}
include_spip('public/calendrier_criteres');

//balise #CALENDRIER
include_spip('public/calendrier_balises');

//filtres calendrier_agenda et calendrier_thead
include_spip('inc/calendrier_filtres');

//composition html de mini calendrier mensuel
include_spip('inc/calendrier_agenda');

?>