<?php

// Limiter la longueur des messages
if(function_exists('lire_config')){
	define('_FORUM_LONGUEUR_MINI', lire_config('comments/forum_longueur_mini',10));
	define('_FORUM_LONGUEUR_MAXI', lire_config('comments/forum_longueur_maxi',1500));
}else{
	define('_FORUM_LONGUEUR_MAXI', 1500);
	define('_FORUM_LONGUEUR_MINI', 10);
}

?>