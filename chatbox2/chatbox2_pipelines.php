<?php
/**
 * Utilisations de pipelines par chatbox2
 *
 * @plugin     chatbox2
 * @copyright  2018
 * @author     Ptroll
 * @licence    GNU/GPL
 * @package    SPIP\Chatbox2\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/*
 * Insérer la css des chats dans l'espace public 
 *
 * @pipeline header_prive
 * @param string $head
 * @return string
 */
function chatbox2_insert_head_css($flux){
			$flux .= '<link rel="stylesheet" href="'.direction_css(find_in_path('css/chatbox2.css')).'" type="text/css" />';
	return $flux;
}