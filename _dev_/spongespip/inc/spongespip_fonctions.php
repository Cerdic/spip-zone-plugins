<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of spongespip
# Copyright (c) Bastien Bobe / Samy Rabih. All rights reserved.
#
# spongespip is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# spongespip is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# ***** END LICENSE BLOCK *****


function effectuer_test($item,$texte)
{
	if($item)
		{
		echo "<p><img src='../images/possible.png'> ".$texte." OK</p>";
		}
	else
		{
		echo "<p><img src='../images/impossible.png'> ".$texte." impossible</p>";
		}
}


function affiche_table($gauche,$droite,$url,$icone,$i)
	{
	global $sps_config;
	//Classe pour appliquer l'effet jQuery de coloration des lignes
	echo "<div class=\"ligne\" >\n";
	echo "<span class=\"span-gauche\">";
	if(!empty($icone))
		{
		echo "<span class=\"span-icone\">";
		if($sps_config['display_icones'])
			{
			foreach ($sps_config['excluded_domaines_icones'] as $filtre_icone)
				{
				if(@eregi($filtre_icone,$icone))
					{
					$showicon = "off";
					}
				}
			if(eregi("hostip.info",$icone))
					{
					$class="class=\"flags\"";
					}
				if($showicon != "off")
					{
					echo "<img src=\"$icone\" $class />";
					}
				unset($showicon);
				unset($class);
			}
		echo "</span>";
		}
	if(!empty($url))
		{
		echo "<a href=\"".$url."\" target=\"_blank\">";
		}
	echo $gauche;
	if(!empty($url))
		{
		echo "</a>";
		}
	echo "</span>\n";
	echo "<span class=\"span-droite\">$droite</span>\n";
	echo "</div>";
	}
	
function affiche_aide($texte,$icone)
	{
	global $sps_config;
	if($sps_config['aide']) { echo "<div class=\"help\"><img src=\"$icone\" alt=\"$texte\" /> $texte</div>"; }
	}
?>
