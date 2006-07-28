<?php
/*****************************************************************************\
* SPIP-CARTO, Solution de partage et d’élaboration d’information 
* (Carto)Graphique sous SPIP
*
* Copyright (c) 2005
*
* Stéphane Laurent, François-Xavier Prunayre, Pierre Giraud, Jean-Claude 
* Moissinac et tous les membres du projet SPIP-CARTO V1 (Annie Danzart - Arnaud
* Fontaine - Arnaud Saint Léger - Benoit Veler - Christine Potier - Christophe 
* Betin - Daniel Faivre - David Delon - David Jonglez - Eric Guichard - Jacques
* Chatignoux - Julien Custot - Laurent Jégou - Mathieu Géhin - Michel Briand - 
* Mose - Olivier Frérot - Philippe Fournel - Thierry Joliveau)
* 
* voir : http://www.geolibre.net/article.php3?id_article=16
*
* Ce programme est un logiciel libre distribue sous licence GNU/GPL. 
* Pour plus de details voir le fichier COPYING.txt ou l’aide en ligne.
* 
— -
This program is free software ; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation ; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY ; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program (COPYING.txt) ; if not, write to
the Free Software Foundation, Inc.,
59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
or check http://www.gnu.org/copyleft/gpl.html
— -
*
\***************************************************************************/
# ou est l'espace prive ?
@define('_DIR_RESTREINT_ABS', 'ecrire/');
include_once _DIR_RESTREINT_ABS.'inc_version.php';

$fond_carte=$_GET["fond_carte"];
if (isset($fond_carte) && !empty($fond_carte))
{
	$fond_carte=base64_decode($fond_carte);
   $lid_carte=intval($fond_carte);
   if ($lid_carte)
   {
       $query="select fichier from spip_documents where id_document=".$lid_carte;
             $result = @spip_query($query);
       if ($result)
       {
           $row = @spip_fetch_array($result);
           redirige_par_entete($row[0]);
       }
   }
   else
   {
       redirige_par_entete($fond_carte);
   }
} 
redirige_par_entete("spip.php?fond=404");
?>