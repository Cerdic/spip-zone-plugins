<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

/**
 * Determiner le nombre de nouveaux messages non lus par l'auteur
 *
 * @param int $id_auteur
 * @return int ou string vide
 */
function messagerie_messages_non_lus($id_auteur){
	static $messages = array();
	if (!isset($messages[$id_auteur])){
		include_spip('base/abstract_sql');
		$messages[$id_auteur] = sql_countsel('spip_auteurs_messages',array('id_auteur='.intval($id_auteur),"vu<>'oui' AND vu<>'poub'"));
		if (!$messages[$id_auteur]) $messages[$id_auteur] = '';
	}
	return $messages[$id_auteur];
}

/**
 * Afficher un texte 'Vous avez un nouveau messages' ou 'Vous avez N nouveaux messages'
 * en fonction du nombre de messages
 *
 * @param int ou string vide
 * @return string
 */
function messagerie_texte_nouveaux_messages($nombre){
	if ($nombre=='') return '';
	$nombre = intval($nombre);
	if ($nombre==1)
		return _T('messagerie:texte_un_nouveau_message',array('nb'=>$nombre));
	else
		return _T('messagerie:texte_des_nouveaux_messages',array('nb'=>$nombre));
}

?>