<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/agenda_gestion");
include_spip("inc/autoriser");

function exec_editer_evenement_dist()
{
	$id_article = intval(_request('id_article'));
	//if (autoriser())
	$res = Agenda_formulaire_article($id_article, autoriser('modifier','article',$id_article),'articles');

	ajax_retour($res);
}

?>