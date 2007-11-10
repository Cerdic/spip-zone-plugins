<?php

// inc/spiplistes_api_globales.php

/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
	Les fonctions qui doivent être chargées par tous les scripts sauf inc/spiplistes_api*
*/

function spiplistes_log($texte, $level = LOG_WARNING) {
	if(__server_in_private_ip_adresses()
		&& __plugin_lire_key_in_serialized_meta('opt_console_syslog', _SPIPLISTES_META_PREFERENCES)
	) {
		__syslog_trace($texte, $level);
	}
	else if($level < LOG_DEBUG) {
		// Taille du log SPIP trop courte
		// Ne pas envoyer si DEBUG sinon tronque sans cesse
		spip_log($texte, _SPIPLISTES_PREFIX);
	}
}



?>
