<?php

//    Fichier créé pour SPIP
//    Distribué sans garantie sous licence GPL./
//    Copyright (C) 2006  Quentin Drouet
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

include_spip("inc/spipmotion");

function spipmotion_affiche_droite($flux) {
	if ($flux['args']['exec'] =='articles_edit'){
		$spipmotion = charger_fonction('spipmotion', 'inc');
		$flux['data'] .= $spipmotion($flux['arg']['id_article']);
	}
	return $flux;
}

function spipmotion_ajouter_onglets($flux) {
	global $connect_statut, $connect_toutes_rubriques;
	if($connect_statut == '0minirezo' AND $connect_toutes_rubriques)
		if($flux['args']=='configuration')
		$flux['data']['config_spipmotion']= new Bouton("../"._DIR_PLUGIN_SPIPMOTION."images/flv.png",
		_T('spipmotion:titre'),
		parametre_url(generer_url_ecrire('cfg'),'cfg','spipmotion'));
	return $flux;
}

?>