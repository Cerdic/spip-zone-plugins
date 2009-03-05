<?php


	/**
	 * SPIP-Notifications
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('inc/plugin');
	include_spip('inc/texte');
	include_spip('base/create');
	include_spip('base/abstract_sql');
	include_spip('base/notifications');


	/**
	 * notifications_addstyle
	 *
	 * @author Eric Dols
	 **/
	function notifications_addstyle($matches) {

		// $matches[1]=tag, $matches[2]=tag attributes (if any), $matches[3]=xhtml closing (if any)

		// variables values set in calling function
		global $styledefinition, $styletag, $styleclass;

		// convert the style definition to a one-liner
		$styledefinition = preg_replace ("!\s+!mi", " ", $styledefinition );
		// convert all double-quotes to single-quotes
		$styledefinition = preg_replace ('/"/','\'', $styledefinition );

		if (preg_match ("/style\=/i", $matches[2])) {
				// add styles to existing style attribute if any already in the tag
				$pattern = "!(.* style\=)[\"]([^\"]*)[\"](.*)!mi";
				$replacement = "\$1".'"'."\$2 ".$styledefinition.'"'."\$3";
				$attributes = preg_replace ($pattern, $replacement , $matches[2]);
		} else {
				// otherwise add new style attribute to tag (none was present)
				$attributes = $matches[2].' style="'.$styledefinition.'"';
		}

		if ($styleclass!="") {
			// if we were injecting a class style, remove the now useless class attribute from the html tag

			// Single class in tag case (class="classname"): remove class attribute altogether
			$pattern = "!(.*) class\=['\"]".$styleclass."['\"](.*)!mi";
			$replacement = "\$1\$2";
			$attributes = preg_replace ( $pattern, $replacement, $attributes);

			// Multiple classes in tag case (class="classname anotherclass..."): remove class name from class attribute.
			// classes are injected inline and removed by order of appearance in <head> stylesheet
			// exact same behavior as where last declared class attributes in <style> take over (IE6 tested only)
			$pattern = "!(.* class\=['\"][^\"]*)(".$styleclass." | ".$styleclass.")([^\"]*['\"].*)!mi";
			$replacement = "\$1\$3";
			$attributes = preg_replace ( $pattern, $replacement, $attributes);

		}

		return "<".$matches[1].$attributes.$matches[3].">";
	}


	/**
	 * notifications_insertion_notification
	 *
	 * @param string nom_notification
	 * @param string fichier langue
	 * @author Pierre Basson
	 **/
	function notifications_insertion_notification($nom_notification, $fichier_langue) {
		$langues = explode(',', $GLOBALS['meta']['langues_multilingue']);
		$titre		= '<multi>';
		$descriptif	= '<multi>';
		$texte		= '<multi>';
		$ps			= '<multi>';
		foreach ($langues as $langue) {
			lang_select($langue);
			$titre		.= '['.$langue.']'.addslashes(_T($fichier_langue.':notification_'.$nom_notification.'_titre'));
			$descriptif	.= '['.$langue.']'.addslashes(_T($fichier_langue.':notification_'.$nom_notification.'_descriptif'));
			$texte		.= '['.$langue.']'.addslashes(_T($fichier_langue.':notification_'.$nom_notification.'_texte'));
			$ps			.= '['.$langue.']'.addslashes(_T($fichier_langue.':notification_'.$nom_notification.'_ps'));
			lang_dselect();
		}
		$titre		.= '</multi>';
		$descriptif	.= '</multi>';
		$texte		.= '</multi>';
		$ps			.= '</multi>';
		return sql_insertq('spip_notifications', array('notification' => $nom_notification, 'titre' => $titre, 'descriptif' => $descriptif, 'texte' => $texte, 'ps' => $ps));
	}
	
	
	/**
	 * notifications_titre_notification
	 *
	 * @param string nom_notification
	 * @param string langue
	 * @author Pierre Basson
	 **/
	function notifications_titre_notification($nom_notification, $langue) {
		$res = sql_select('titre', 'spip_notifications', 'notification="'.$nom_notification.'"');
		$arr = sql_fetch($res);
		lang_select($langue);
		$titre = typo($arr['titre']);
		lang_dselect();
		return $titre;
	}
	
	
?>