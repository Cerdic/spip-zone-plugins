<?php 

	// barrac_mes_options.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of BarrAc.
	
	BarrAc is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	BarrAc is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with BarrAc; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de BarrAc. 
	
	BarrAc est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	BarrAc est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/

	if (!defined('_DIR_PLUGIN_BARRAC')) {
		$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
		define('_DIR_PLUGIN_BARRAC',(_DIR_PLUGINS.end($p)).'/');
	} 
	
	define("_BARRAC_PREFIX", "barrac");
	define("_BARRAC_LANG", _BARRAC_PREFIX.":");
	define("_DIR_PLUGIN_BARRAC_IMG_PACK", _DIR_PLUGIN_BARRAC."images/");
	define("_BARRAC_META_PREFERENCES", "barrac_preferences");
	
	define("_BARRAC_FAMILLE_DEFAULT", "standard");

	define("_BARRAC_POSITION_DEFAULT", "top_left");
	
	define("_BARRAC_PRESENTATION_VERTICAL", "vertical");
	define("_BARRAC_PRESENTATION_HORIZONTAL", "horizontal");
	define("_BARRAC_PRESENTATION_DEFAULT", _BARRAC_PRESENTATION_HORIZONTAL);
	
	define("_BARRAC_ICONE_TAILLE_DEFAULT", 24);
	define("_BARRAC_ICONE_TAILLE_MAX", 96);
	
	define("_BARRAC_ICONE_MARGE_DEFAULT", 8);
	define("_BARRAC_ICONE_MARGE_MAX", 24);
	
	define("_BARRAC_ACTION_POINTER", 'pointer');
	define("_BARRAC_ACTION_GROSSIR", 'grossir');
	define("_BARRAC_ACTION_REDUIRE", 'reduire');
	define("_BARRAC_ACTION_ESPACER", 'espacer');
	define("_BARRAC_ACTION_RAPPROCHER", 'rapprocher');
	define("_BARRAC_ACTION_ENCADRER", 'encadrer');
	define("_BARRAC_ACTION_DECADRER", 'decadrer');
	define("_BARRAC_ACTION_INVERSER", 'inverser');
	define("_BARRAC_ACTION_REPLACER", 'replacer'); // inverse de inverser
	define("_BARRAC_ACTION_FOND", 'fond'); // fond de l'icone
	
	define("_BARRAC_POINTER_DEFAULT", "#contenu"); // ancre par defaut de SPIP
	
	define("_BARRAC_GROSSIR_TAILLE_MAX", 1000);

	define("_BARRAC_POSITIONS_ARRAY", 
		serialize(
			array(
				'top_left' => -90
				, 'top_right' => 0
				, 'bottom_left' => 180
				, 'bottom_right' => 90
			)
		)
	);

	define("_BARRAC_BOUTONS_RELATIONS", 
		serialize(
			array(
				_BARRAC_ACTION_GROSSIR => _BARRAC_ACTION_REDUIRE
				, _BARRAC_ACTION_ESPACER => _BARRAC_ACTION_RAPPROCHER
				, _BARRAC_ACTION_ENCADRER => _BARRAC_ACTION_DECADRER
				, _BARRAC_ACTION_INVERSER => _BARRAC_ACTION_REPLACER
			)
		)
	);
	
	define("_BARRAC_BOUTONS_PARENTS", 
		serialize(
			array(
				_BARRAC_ACTION_POINTER, _BARRAC_ACTION_GROSSIR, _BARRAC_ACTION_ESPACER, _BARRAC_ACTION_ENCADRER, _BARRAC_ACTION_INVERSER
			)
		)
	);
	
	define("_BARRAC_BOUTONS_FRERES", 
		serialize(
			array(
				_BARRAC_ACTION_REDUIRE, _BARRAC_ACTION_RAPPROCHER, _BARRAC_ACTION_DECADRER, _BARRAC_ACTION_REPLACER
			)
		)
	);
	
	define("_BARRAC_DEFAULT_VALUES_ARRAY", 
	 	serialize(
			array(
					// par défaut, tous les boutons actifs (ne valider que les parents !)
					_BARRAC_ACTION_POINTER => 'oui'
				,	_BARRAC_ACTION_GROSSIR => 'oui'
				,	_BARRAC_ACTION_ESPACER => 'oui'
				,	_BARRAC_ACTION_ENCADRER => 'oui'
				,	_BARRAC_ACTION_INVERSER => 'oui'
				, 'barrac_position_barre' => _BARRAC_POSITION_DEFAULT // coin de l'écran : top_left, top_right, bottom_left, bottom_right
				, 'barrac_position_fixed' => 'non'
				, 'barrac_presentation_barre' => _BARRAC_PRESENTATION_DEFAULT // vertical || horizontal
				, 'barrac_marge_entre_boutons' => _BARRAC_ICONE_MARGE_DEFAULT
				, 'barrac_taille_bouton' => _BARRAC_ICONE_TAILLE_DEFAULT
				, 'barrac_pointeur_ancre' => _BARRAC_POINTER_DEFAULT	// ancre du contenu de la page
				, 'barrac_famille_boutons' => _BARRAC_FAMILLE_DEFAULT
				, 'barrac_flip_pointer' => 'oui' // rotation de la flèche sur le centre de l'écran (pointer)
				, 'barrac_flip_horizontal' => 'oui' 
				, 'barrac_flip_vertical' => 'oui' 
				, 'barrac_flip_contextuel' => 'oui' 
				, 'barrac_grossir_global' => 'oui'
				, 'barrac_grossir_taille' => '600'
				, 'barrac_grossir_cssfile' => '' // si complété, force barrac_grossir_global => 'non'
				, 'barrac_espacer_global' => 'oui'
				, 'barrac_espacer_taille' => '2ex'
				, 'barrac_espacer_cssfile' => '' // si complété, force barrac_espacer_global => 'non'
				, 'barrac_encadrer_global' => 'oui'
				, 'barrac_encadrer_taille' => '12px'
				, 'barrac_encadrer_padding' => '12px'
				, 'barrac_encadrer_couleur' => 'black'
				, 'barrac_encadrer_cssfile' => '' // si complété, force barrac_encadrer_global => 'non'
				, 'barrac_inverser_global' => 'oui'
				, 'barrac_inverser_color' => '#000'
				, 'barrac_inverser_bgcolor' => '#fff'
				, 'barrac_inverser_cssfile' => '' // si complété, force barrac_inverser_global => 'non'
			)
		)
	);

	define("_BARRAC_BOUTONS_LEGENDES", 
	 	serialize(
			array(
				_BARRAC_ACTION_POINTER => "aller_au_contenu"
				, _BARRAC_ACTION_GROSSIR => "grossir_taille_caracteres"
				, _BARRAC_ACTION_REDUIRE => "reduire_taille_caracteres"
				, _BARRAC_ACTION_ESPACER => "espacer_blocs"
				, _BARRAC_ACTION_RAPPROCHER => "retablir_blocs"
				, _BARRAC_ACTION_ENCADRER => "encadrer_paragraphes"
				, _BARRAC_ACTION_DECADRER => "decadrer_paragraphes"
				, _BARRAC_ACTION_INVERSER => "inverser_couleurs"
				, _BARRAC_ACTION_REPLACER => "retrouver_couleurs"
			)
		)
	);


?>