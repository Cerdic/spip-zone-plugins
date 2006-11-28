<?php

/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */
	// compatibilite 1.9.1

	if (!function_exists('_q')){
		function _q($arg) { return spip_abstract_quote($arg); }
	}
	if (!function_exists('ajax_retour')) {
		// http://doc.spip.org/@ajax_retour
		function ajax_retour($corps)
		{
			$c = $GLOBALS['meta']["charset"];
			header('Content-Type: text/html; charset='. $c);
			$c = '<' . "?xml version='1.0' encoding='" . $c . "'?" . ">\n";
			echo $c, $corps;
			exit;
		}
	}
	
	if (!function_exists('concat')){
		// Concatener des chaines
		// #TEXTE|concat{texte1,texte2,...}
		// http://doc.spip.org/@concat
		function concat(){
			$args = func_get_args();
			return join('', $args);
		}	
	}
	if (!function_exists('balise_GET')){
		//
		// #GET
		// Recupere une variable locale au squelette
		// #GET{nom,defaut} renvoie defaut si la variable nom n'a pas ete affectee
		//
		// http://doc.spip.org/@balise_GET_dist
		function balise_GET($p) {
			$p->interdire_scripts = false; // le contenu vient de #SET, donc il est de confiance
			if (function_exists('balise_ENV'))
				return balise_ENV($p, '$Pile["vars"]');
			else
				return balise_ENV_dist($p, '$Pile["vars"]');
		}
	}
	if (!function_exists('balise_URL_ACTION_AUTEUR_dist')){
		//
		// #URL_ACTION_AUTEUR{converser,arg,redirect} -> ecrire/?action=converser&arg=arg&hash=xxx&redirect=redirect
		//
		// http://doc.spip.org/@balise_URL_ACTION_AUTEUR_dist
		function balise_URL_ACTION_AUTEUR_dist($p) {
		
			$p->code = interprete_argument_balise(1,$p);
			$args = interprete_argument_balise(2,$p);
			if ($args != "''" && $args!==NULL)
				$p->code .= ".'\",\"'.".$args;
			$redirect = interprete_argument_balise(3,$p);
			if ($redirect != "''" && $redirect!==NULL)
				$p->code .= ".'\",\"'.".$redirect;
		
			$p->code = "'<"."?php echo generer_action_auteur(\"'." . $p->code .".'\"); ?>'";
		
			$p->interdire_scripts = false;
			return $p;
		}
	}
	if (!function_exists('interprete_argument_balise')){
		// http://doc.spip.org/@interprete_argument_balise
		function interprete_argument_balise($n,$p){
			if (($p->param) && (!$p->param[0][0]) && (count($p->param[0])>$n))
				return calculer_liste($p->param[0][$n],
											$p->descr,
											$p->boucles,
											$p->id_boucle);	
			else 
				return NULL;
		}
	}
	
?>