<?php 

	// inc/lido_api_presentation.php
	
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of LiDo.
	
	LiDo is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	LiDo is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with LiDo; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de LiDo. 
	
	LiDo est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publi�e par 
	la Free Software Foundation (version 2 ou bien toute autre version ult�rieure 
	choisie par vous).
	
	LiDo est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU 
	pour plus de d�tails. 
	
	Vous devez avoir re�u une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez � la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.
	
	*****************************************************/
	
if (!defined("_ECRIRE_INC_VERSION")) return;



function lido_chercher_rubrique ($id_rubrique, $type, $name = "") {

	$chercher_rubrique = charger_fonction('chercher_rubrique', 'inc');
	$result = $chercher_rubrique($id_rubrique, $type, true);
	
	if($type == 'article') {
		// supprime les options en doublon (bug de chercher_rubrique() ou effet recherch� ?)
		$result = preg_replace(",<option value='[[:digit:]]+'></option>,", "", $result);
	}

	if(!empty($name)) {
		// nom du select
		$result = preg_replace(",id='id_parent' name='id_parent',", "id='".$name."' name='".$name."'", $result);
	}
	
	$result = preg_replace("@url\(dist/images/secteur-12.gif\);background-color:[[:space:]]+;@"
		, "url(../dist/images/secteur-12.gif);background-color: #C5E41C;'"
		, $result);
	
	return ($result);
}

?>