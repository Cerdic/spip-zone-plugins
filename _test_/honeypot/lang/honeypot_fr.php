<?php
/*
*   Plugin HoneyPot
*   Copyright (C) 2007 Pierre Andrews
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(
									   'cfg_titre' => 'Projet Pot de Miel',
									   'cfg_descriptif' => "Le projet pot de miel offre un piege pour les robots de spam qui pourraient venir visiter votre site. 

Ce plugin permet de facilement integrer les liens vers votre pot de miel dans vos squelettes. 

Pour qu'il marche, il faut absolument avoir install&eacute; le pot de miel fourni par [->http://projecthoneypot.org].

Une fois un pot de miel install&eacute;, vous pouvez aussi utiliser ce plugin pour limiter l'acc&eacute;s au site et filtrer les visiteurs qui ont &eacute;t&eacute; pris dans un des pots de miels de P.H.Pot. Pour configurer cette partie du plugin, allez sur la page de configuration [httpbl->./?exec=cfg&cfg=httpbl].",
									   'documentation' => "<p>Pour commencer &agrave; utiliser un pot de miel sur votre site, vous devez avant tout cr&eacute;er un compte sur le site du <a href=\"http://projecthoneypot.org\">project honeypot</a>. Suivez ensuite les instructions pour t&eacute;lecharger le script que ce projet fournis.
</p><p>
Une fois t&eacute;l&eacute;charg&eacute;, d&eacute;compresser l'archive Project_Honey_Pot.zip fournie dans un r&eacute;pertoire sur votre ordinateur. Vous y trouverez alors un fichier <em>php</em> qu'il vous faut envoyer &agrave; la racine de votre site SPIP sur le serveur.
</p><p>
Visitez une fois ce fichier sur votre serveur avec votre navigateur. Suivez les instructions pour activer le pot de miel. 
</p>
<hr/>
<p>
Une fois le pot de miel activ&eacute;, vous devez placer des liens \"pi&egrave;ges\" pour les crawlers venant sur votre site. Ce plugin va simplifier cette tache en fournissant une balise <code>#HONEYPOT</code> &agrave; placer dans vos squelettes et qui g&eacute;n&egrave;rera les liens comme il faut. 
</p><p>
Si vous utilisez le squelette par d&eacute;faut de SPIP, le plugin fournis d&eacute;j&agrave; un remplacement du squelette du pied de page (dist/inc-pied.html) et vous n'avez rien &agrave; faire. Sinon, vous devez placer la balise #HONEYPOT quelque part dans vos squelettes personalis&eacute;s de fa&ccedil;on &agrave; ce qu'elle apparaisse sur le plus de page possible (par exemple, un squelette de pied de page, de menu, etc...).</p>",
'cfg_hpfile' => 'Nom du fichier pot de miel (sans le <em>.php</em>) : '

);

?>
