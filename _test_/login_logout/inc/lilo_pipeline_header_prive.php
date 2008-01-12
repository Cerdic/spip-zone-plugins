<?php 

	// exec/lilo_pipeline_header_prive.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	
	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of LiLo.
	
	LiLo is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	LiLo is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with LiLo; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de LiLo. 
	
	LiLo est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	LiLo est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
	
if (!defined("_ECRIRE_INC_VERSION")) return;

// pipeline (plugin.xml)
// Ajoute l'appel aux feuilles de style dans le header privé
function lilo_header_prive ($flux) {

	global $connect_statut
		, $connect_toutes_rubriques
		;

	$exec = _request('exec');
	
	if(
		($exec == 'lilo_configuration')
		&& ($connect_statut == '0minirezo')
		&& $connect_toutes_rubriques
		) {

		//		
		$flux .= ""
			. "

<style type='text/css'>
<!--
fieldset { border:1px solid gray; margin-top:0.5em; }
.description { font-style: italic; }
.description em { font-style: normal; }
.lilo-screen {
	width:auto; height:60px;
	text-align:center;
}
.lilo-screen ul { padding:0; list-style:none; background-color:white; 
	position:relative; 
	border:3px solid black; width:80px; height:60px;
	margin:0.5em auto;
	top:2em;
}
.lilo-screen>ul {
	top:0;
}
.lilo-screen label { display:none; }
.lilo-screen li { position:absolute; display:block; }
.lilo-screen li.tl, .lilo-screen .tc, .lilo-screen .tr { top:0; }
.lilo-screen li.bl, .lilo-screen .bc, .lilo-screen .br { bottom:0; }
.lilo-screen li.tl, .lilo-screen .bl {left:0; }
.lilo-screen li.tc, .lilo-screen .bc {left:25px; }
.lilo-screen li.tr, .lilo-screen .br {right:0; }
ul.meta-info-liste p { display:inline; } /* supprimes les <p> ajoute's aux infos par 193 */

-->
</style>
"
		;
	}
	
	return ($flux);
}

?>