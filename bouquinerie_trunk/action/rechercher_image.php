<?php

/*
 *  Plugin Bouquinerie pour SPIP
 *  Copyright (C) 2008  Polez Kévin
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_rechercher_image_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_livre = $arg;
	$r = sql_fetsel('id_reference,titre','spip_livres',"id_livre=$id_livre");

	recupere_image($id_livre,$r['id_reference'],$r['titre']);

	$redirect = parametre_url(urldecode(generer_url_ecrire('livres')),"id_livre",$id_livre,'&');

	redirige_par_entete($redirect);
}

function recupere_image($id_livre,$id_reference,$titre) {
	$url_price = 'http://www.priceminister.com/offer/buy/'.$id_reference.'/';
	if ($page = file_get_contents($url_price)) {

		if (preg_match('#<div id="fp_pix">#',$page,$match, PREG_OFFSET_CAPTURE) > 0) {
			$tab = preg_split('#<div id="fp_pix">#',$page);
			$tab_2 = preg_split('#</div>#',$tab[1]);

			if (preg_match('#src="(.+?)"#',$tab_2[0],$m) > 0 ) {
				include_spip('inc/ajouter_documents'); // pour l'ajout de documents
				sql_updateq('spip_livres',array('url_image' => $m[1]),"id_livre=$id_livre");
				// gerer l'upload et document distant

				//upload
				$fichier = 'img_'.$titre.'.jpg';
				$mode = "image";
				$ajouter_document = charger_fonction('ajouter_documents','inc');
				$ajouter_document($m[1],$fichier,"livre",$id_livre,$mode,$id_document,$documents_actifs);
			}
		}
	}
}

?>
