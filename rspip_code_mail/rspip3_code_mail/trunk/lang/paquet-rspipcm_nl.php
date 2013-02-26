<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
$GLOBALS[$GLOBALS['idx_lang']] = array(

	rspipcm_slogan		=>	"Ce plugin encode les raccourcis email spip contre les robots collecteurs d'adresses email.",

	rspipcm_description	=>	"
	
	Ce plugin encode les raccourcis email spip pour éviter la récupération des adresses email par des robots collecteurs de ces adresses. Il remplace le lien mailto par un lien javascript crypté. Si le robot tente de suivre le lien javascript et de le décoder via le DOM, il sera bloqué par une captcha mathématique obligatoire pour le décodage.
 
Le plugin supporte des paramètres à l'adresse email. Ces paramètres doivent être alphanumériques. Ils peuvent aussi contenir l'espace, _ et -. Si l'encodage UTF-8 est disponible, les caractères accentués sont autorisés. Les doubles et simples quotes (même échappés), # et les caractères html étendus sont à proscrire. Le : dans les paramètres retournera @.

Langues actuelles disponibles (en, nl à compléter): fr, en, nl

La documentation [est ici->http://contrib.spip.net/Codeur-d-adresse-email-2-plugin]."

   );
     
?>