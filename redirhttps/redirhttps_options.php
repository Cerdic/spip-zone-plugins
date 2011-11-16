<?php
// Le traffic HTTP est redirige en HTTPS quand l'utilisateur est connecte ou pour la page de login
// a noter: il faut avoir applique le patch: http://core.spip.org/projects/spip/repository/revisions/17941
//    -> dans le core depuis la version 2.1.11
if (
        (
		($_COOKIE['spip_session']) OR
		(strpos($_SERVER['REQUEST_URI'], '/spip.php?page=login')===0)
	) AND
        (!$_SERVER['HTTPS']) AND
        ($_SERVER['REQUEST_METHOD'] == 'GET')
        ) {
        include_spip('inc/headers');
        redirige_par_entete('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}
?>
