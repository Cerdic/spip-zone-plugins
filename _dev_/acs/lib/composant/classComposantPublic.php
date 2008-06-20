<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Classe Composant
 * Chaque composant ACS peut définir une classe <MonComposant>
 * qui étend la classe Composant en définissant des méthodes de l'interface Icomposant
 */

abstract class Composant implements Icomposant {
}

interface Icomposant {
  public function insert_head($flux);
}
?>
