<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function mathjax_latex_format($str) {
	if (substr($str,-1) == "\\")
		$str .= "&nbsp;";
	return $str;
}