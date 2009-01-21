<?php
	/**
	* Plugin Bannières
	*
	* Copyright (c) 2008
	* François de Montlivault
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
	
	function action_visit_url() {
		$id_banniere=$_GET['ban'];
		$query=spip_query("SELECT * FROM spip_bannieres WHERE id_banniere=$id_banniere");
		while ($data=spip_fetch_array($query)){
			$url=$data['site'];
			spip_query("UPDATE spip_bannieres SET clics=clics+1 WHERE id_banniere=$id_banniere");
			header("location:".$url);
		}
	}
?>