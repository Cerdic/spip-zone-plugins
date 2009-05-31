<?php 

	// action/lido_chercher_rubrique.php
	

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
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	LiDo est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
	
	if (!defined("_ECRIRE_INC_VERSION")) return;

/*
/* Ajax, renvoie la boite chercher rubrique (article ou brève)
/**/
function action_lido_chercher_rubrique_dist () {
	
	include_spip('base/db_mysql');
	include_spip('inc/texte');
	include_spip('inc/utils');
	include_spip('inc/lido_api_presentation');
	
	$type = rtrim($_POST['type'], 's');
	$id_rubrique = intval($_POST['id_rubrique']);
	
	if(
		($type == 'breve') || ($type == 'article')
	) {
		$result = lido_chercher_rubrique($id_rubrique, $type, 'lido_id_rubrique');
		echo($result);
		return (true);
	}
	return (false);
}

?>