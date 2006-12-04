<?php

// Copyright (C) 2006 Pierre Andrews
// 
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// The GNU General Public License is available by visiting
//   http://www.gnu.org/copyleft/gpl.html
// or by writing to
//   Free Software Foundation, Inc.
//   51 Franklin Street, Fifth Floor
//   Boston, MA  02110-1301
//   USA

function pi_test_plugin_pas_installe($plugin) {
  include_spip('inc/plugin');
  $arr = preg_grep('#^.*'.preg_quote($plugin).'/?$#i',liste_plugin_actifs());
  var_dump($arr);
  return !count($arr);
}

function balise_PLUGIN_PAS_INSTALLE_dist($p) {
  if ($p->param && !$p->param[0][0]){
	$plugin = calculer_liste($p->param[0][1],
							  $p->descr,
							  $p->boucles,
							  $p->id_boucle);
	$p->code="pi_test_plugin_pas_installe($plugin)";
	$p->interdire_scripts = false;
  }
  return $p;
}
?>