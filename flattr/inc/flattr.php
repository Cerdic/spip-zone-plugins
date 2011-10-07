<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function flattr_safe_output($expression)
{
	return trim(preg_replace('~\r\n|\r|\n~', ' ', addslashes($expression)));
}

