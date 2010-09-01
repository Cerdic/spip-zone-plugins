<?php

function flattr_safe_output($expression)
{
	return trim(preg_replace('~\r\n|\r|\n~', ' ', addslashes($expression)));
}

