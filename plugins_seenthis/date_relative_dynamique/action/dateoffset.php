<?php

// renvoyer le diff en millisecondes entre la date envoyee et la date sql
function action_dateoffset() {
	if ($s = sql_query('SELECT UNIX_TIMESTAMP(NOW()) AS date')
	AND $t = sql_fetch($s)) {
		echo $t['date'];
	}
	else
		echo time();
}

?>
