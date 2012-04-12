<?php
/**
 * Media auteurs
 *
 * Copyright (c) 2012
 * Yohann Prigent
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
 * Pour plus de details voir le fichier COPYING.txt.
 *  
 **/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_liaison_docaut() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($id_auteur, $id_document) = preg_split('/\W/', $arg);
	$id_auteur = intval($id_auteur);
	$id_document = intval($id_document);
	delete_liaison($id_auteur,$id_document);
}
function delete_liaison($id_auteur,$id_document){
	sql_delete('spip_documents_liens', "id_objet=".$id_auteur." AND id_document=".$id_document);
}
?>