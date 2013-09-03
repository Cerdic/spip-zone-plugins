<?


class app {

  var $aut;  // l'auteur
  var $lgi;  // la langue de l'interface
  var $lgo;  // la langue origine
  var $lgc;  // la langue cible
  var $nlgc;  // nouvelle langue cible

  var $lgos;  // les langues dispo
  var $lgis; // les langues dispo pour interface
  var $lgcs; // langues cible dispo
  
  var $left, $right;  // directions

  var $mods; // les modules dispo
  var $mref; // le module selectionne

  var $nom;  // le nom du traducteur
  var $login; // son login spip

  var $menu_adm; // presence ou non du menu adm
  var $menu_trad; // pareil pour le menu traduction

  function app() {

    $this->left="left";
    $this->right="right";

    $this->menu_adm = false;
    $this->menu_trad = true;
  }

  function init_langues() {
    global $lgo, $lgc, $nlgc;
    
    // securite & uniformisation en minuscules (et _) des codes de langue
    $this->lgo  = strtolower(eregi_replace("[^a-z0-9_]", "", $lgo));

    // si langue origine inconnue, prend celle par defaut
    // pour le module
    if ($this->lgo == '')
      $this->lgo = $this->mref->langue;

    $this->lgc = strtolower(eregi_replace("[^a-z0-9_]", "", $lgc));

    // si langue cible inconnue, prend celle par defaut
    // pour le module
    if ($this->lgc == '')
      $this->lgc = $this->mref->languec;

    $this->nlgc = strtolower(eregi_replace("[^a-z0-9_]", "", $nlgc));

    $this->lgis = $this->get_langues("ts");

    if (!is_object($this->mref))
      return;

    $mref = $this->mref;
    $this->lgos = $this->get_langues($mref->nom);
    $this->lgcs = $this->get_langues($mref->nom, $mref->langue);
  }

  function get_langues($nom_mod, $excl="") {

    $quer = "SELECT distinct lang FROM ".TRAD_LANG." WHERE module='".mes($nom_mod)."'";
    $res = mysql_query($quer);
    
    $ret = array();
    while ($row=mysql_fetch_assoc($res))
      {
	if ($excl != $row["lang"])
	  $ret[] = $row["lang"];
      }

    return $ret;
  }

  function calc_lgc() {
    
    $ret = $this->lgc;
    if ($ret == "")
      $ret = $this->nlgc;
 
    return $ret;
  }

  function init_modules($mod) {

    if ($mod=="")
      $mod = "ts";

    $this->mods = fabrique_modules();
    $this->mref = $this->mods[$mod];
  }
 
  function set_dir($lg) {

    $direction = get_dir($lg);
    if ($direction=="rtl")
      {
	$this->left="right";
	$this->right="left";
      }
  }

  function verif_droit($as) {

    if ($as['statut'] == '0minirezo') 
      $this->menu_adm = true;
  }

}


?>