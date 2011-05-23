<?php
// Le traffic HTTP est redirige en HTTPS quand l'uilisateur est connecte ou pour la page de login
// El trafico HTTP esta redireccionado en HTTPS cuando el usuario esta conectado o para la pagina de login
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
