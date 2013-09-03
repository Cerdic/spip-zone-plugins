<?



// fonction statique permettant la
// d'instancier les modules a partir
// des fichiers qui les decrivent
function fabrique_modules() {

  $dir_modules = DIRMOD;
  $mods = array();

  $handle = opendir($dir_modules);
  while (($fichier = readdir($handle)) != '')
    {
      // Eviter ".", "..", ".htaccess", etc.
      if ($fichier[0] == '.') continue;
      if ($fichier == 'CVS') continue;

      $nom_fichier = $dir_modules."/".$fichier;
      if (is_file($nom_fichier))
        {
          if (!ereg("^module_(.+)\.php$", $fichier, $extlg))
            continue;

          include($nom_fichier);

	  $cname = "module_".$type_module;
	  if (class_exists($cname))
	    {
	      $mod = new $cname;
	      $mod->init_instance();
	      $mods[$nom_mod] = $mod;
	    }
	  else
	    return false;

        }
    } 
  closedir($handle);

  return $mods;
}


// fonction statique permettant de
// générer un modele de fichier de
// description pour un type de module
// donne
function fabrique_fichier_ini($type) {
  
  
}


class module {

  var $nom_module, $nom, $langue, $languec;
  var $export_function, $type_module;

  function module() {
  }

  function init_instance() {
    global $nom_module, $nom_mod, $lang_mere;
    global $export_function, $type_module, $lang_cible;
    
    $this->nom_module = $nom_module;
    $this->nom = $nom_mod;
    $this->langue = $lang_mere;
    $this->languec = $lang_cible;
    $this->export_function = $export_function;
    $this->type_module = $type_module;
  }
}


class module_SPIP extends module {

  var $dir_lang, $dir_bak;
  var $lang_prefix, $lang_suffix;
  var $lang_prolog, $lang_epilog;


  function module_SPIP () {
    parent::module();
  }


  function init_instance() {
    global $dir_lang, $dir_bak;
    global $lang_prefix, $lang_suffix;
    global $lang_prolog, $lang_epilog;

    parent::init_instance();

    $this->dir_lang = $dir_lang;
    $this->dir_bak = $dir_bak;

    $this->lang_prefix = $lang_prefix;
    $this->lang_suffix = $lang_suffix;

    $this->lang_prolog = $lang_prolog;
    $this->lang_epilog = $lang_epilog;
  }

}

?>
