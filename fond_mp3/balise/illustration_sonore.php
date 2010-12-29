<?php

// balise/illustration_sonore.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Fmp3.
	
	Fmp3 is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Fmp3 is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Fmp3; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Fmp3. 
	
	Fmp3 est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Fmp3 est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de details. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en même temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;


include_spip('inc/fmp3_api_globales');

/*
 * Balise ILLUSTRATION_SONORE
 * Conseillé: dans inc-pied.html, juste avant SPIP_CRON
 *   (exemple: inc-pied.html dans le répertoire du plugin)
 */
function balise_ILLUSTRATION_SONORE ($p)
{
	return calculer_balise_dynamique($p,'ILLUSTRATION_SONORE', array());
}

function balise_ILLUSTRATION_SONORE_stat ($args, $filtres)
{
	//fmp3_log('balise stat');
	return $filtres ? $filtres : $args;
}

function balise_ILLUSTRATION_SONORE_dyn ($opt) {

	if(
		isset($GLOBALS['page']['contexte'])
		|| isset($GLOBALS['contexte'])
		) {
		
		//fmp3_log('appel balise dyn avec contexte');
		
		if(fmp3_spip_est_inferieur_193())
		{
			$contexte = $GLOBALS['page']['contexte'];
			
			$objet = $result = "";
			
			if(isset($contexte['page']))
			{
				$objet = $contexte['page'];
				$id_objet = $contexte['id_'.$objet];
				$objet = substr($objet, 0, 3);
			}
			else if(count($contexte) == 2) {
				$objet = 'site';
				$id_objet = '0';
			}
	
		}
		else if($contexte = $GLOBALS['contexte'])
		{
			// SPIP 2 ?

			// Dérivé de admin_objet()
			// @see: ecrire/balise/formulaire_admin.php
			function fmp3_detect_objet()
			{
				include_spip('inc/urls');
				$env = array();
			
				foreach(array('rubrique', 'breve', 'article') as $obj)
				{
					$id = $obj;
					$_id_type = id_table_objet($id);
					if (isset($GLOBALS['contexte'][$_id_type])
						&& ($id_type = $GLOBALS['contexte'][$_id_type]))
					{
						$id_type = sql_getfetsel($_id_type, table_objet_sql($id)
												 , $_id_type.'='.intval($id_type));
						if ($id_type) {
							$env['objet'] = $id;
							$env['id_objet'] = $id_type;
						}
					}
				}
				return($env);
			}

			$env = fmp3_detect_objet();
			if(isset($env['objet']))
			{
				$objet = substr($env['objet'], 0, 3);
				
				$id_objet = $env['id_objet'];
			}
		}

		fmp3_log('balise: cherche son pour '.$objet.' '.$id_objet, null, _FMP3_DEBUG);
		
		if(!empty($objet))
		{
			$preferences_meta = fmp3_get_all_preferences();
			
			$heriter = ($preferences_meta['inherit'] == "true");
			
			$son_dest = fmp3_chemin_son($objet, $id_objet);

			$son_exists = file_exists($son_dest);
			
			/*
			 * Si le son de l'article n'existe pas 
			 * prendre celui de la rubrique si héritage souhaité
			 */
			if($heriter && !$son_exists && ($objet == 'art')) {
				
				fmp3_log("balise: pas de son pour $objet $id_objet");
				
				if(fmp3_spip_est_inferieur_193()) 
				{
					//include_spip('base/abstract_sql');
					include_spip('base/db_mysql');
					$row = spip_fetch_array(
						spip_query("SELECT id_rubrique FROM spip_articles WHERE id_article=$id_objet LIMIT 1")
					);
				}
				else 
				{
					$row = sql_fetch(sql_select(
						'id_rubrique'
						, 'spip_articles'
						, "id_article=$id_objet"
						, '', '', 1
						));
				}
				fmp3_log("balise: rubrique ".$row['id_rubrique']);
				if($row && ($row['id_rubrique'])) {
					$objet = 'rub';
					$id_objet = $row['id_rubrique'];
					$son_dest = fmp3_chemin_son($objet, $id_objet);
					$son_exists = file_exists($son_dest);
				}
			}
			
			/*
			 * Si le son de la rubrique n'existe pas
			 * prendre celui du site si héritage souhaité
			 */
			if($heriter && !$son_exists && ($objet == 'rub')) {
				$objet = 'site';
				$id_objet = 0;
				$son_dest = fmp3_chemin_son($objet, $id_objet);
				$son_exists = file_exists($son_dest);
			}
			
			if($son_exists) { 
			
				include_spip('inc/filtres');
				
				fmp3_log("balise: applique son $objet $id_objet");
				
				$son_dest = url_absolue($son_dest);
				
				/* 
				 * bouton ecouter son 
				 */
				$bouton_play = fmp3_bouton_play (
					  $son_dest
					, $preferences_meta['autoStart']
					, $preferences_meta['backColor']
					, $preferences_meta['frontColor']
					, $preferences_meta['repeatPlay']
					, $preferences_meta['songVolume']
					);
					
				$result = 
					$bouton_play
					;
			}
		}
	}
	return ($result);
}
