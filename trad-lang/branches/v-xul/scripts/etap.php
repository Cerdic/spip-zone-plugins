<?


function fabrique_etape($nom, $app)
{
  global $g_deb, $errscrap;

  $cname = "etape_".$nom;
  $g_deb->log(0, "nom etape=".$cname);
  $g_deb->log(0, "scrap=".$errscrap);

  $ret = null;
  if (class_exists($cname))
    {
      $ret = new $cname;
      $ret->init_instance($app, $nom);
      $ret->init();
    }

  return $ret;
}


class etape {

  var $nom, $xul, $app, $def;
  var $langues, $langues_c, $langue;
  var $langues_ihm, $modules, $module;

  var $menu, $menu_app,$menu_mod,$menu_lgo,$menu_lgc;
  var $comm_app,$comm_mod,$comm_lgo,$comm_lgc;

  function etape() {

    $this->def = array();
  }

  function init_instance($app, $nom) {

    $this->app = $app;
    $this->nom = $nom;
    $this->xul = DIRTD."/xul/".$nom.".xul";

    $this->menu = true;
    $this->menu_app = true;
    $this->menu_mod = true;
    $this->menu_lgo = true;
    $this->menu_lgc = true;

    $this->comm_app = $nom;
    $this->comm_mod = $nom;
    $this->comm_lgo = $nom;
    $this->comm_lgc = $nom;
  }

  function init() {
    // initialisations propres a la classe
    // surchargee
  }

  function explode_rech($fld, $ntbl='') {

    $ret = " (";
    if ($ntbl != '')
      $ntbl = $ntbl.".";

    $arr = explode(" ", $fld);
    $or = "";
    foreach($arr as $item)
      {
	$ret .= $or.$ntbl."str LIKE '%".mes($item)."%' ";
	$or = "OR ";
      }

    $ret .= ")";
    return $ret;
  }

  function run($app) {

    include(DIRTD."/xul/header.xul");

    if ($this->menu)
      include(DIRTD."/xul/menu.xul");

    include($this->xul);
  }


  function defaut($nm, $val) {   

    if ($val == '')
      $this->$nm = $this->def[$nm];

    else
      $this->$nm = $val;
  }

  function erreur($messerreur="") {
    global $errscrap, $g_deb;

    $g_deb->log(0, ">>>etap::erreur");
    $g_deb->log(0, "scrap=<".$errscrap.">");

    if ($messerreur=="")
      $messerreur = "Erreur inconnue.";

    // positionnement du champ erreur teste
    // pour afficher l'erreur
    $ch = "?erreur=1&messerreur=".rawurlencode($messerreur);

    // recup de tous les elements passes en GET
    // dans l'etape precedente. Ces elements sont
    // envoyes dans la var. errscrap
    if ($errscrap)
      $ch .= "&".$errscrap;

    $ch = "Location: int.php".$ch;
    $g_deb->log(0, "redirection vers <".$ch.">");

    // retour vers la meme page avec champ erreur
    // positionne
    header($ch);
    exit;
  }

};


class etape_page_garde extends etape {

  function etape_page_garde() {
    parent::etape();
  }

}


class etape_verif extends etape {

  function etape_verif() {
    parent::etape();
  }

  function init_instance($app, $nom) {

    parent::init_instance($app, $nom);
    $this->menu = false;
  }

  function run($app) {

    global $g_deb;
    $g_deb->log(0, ">>>etap_verif::run");

    $lgo = $app->lgo;  // langue origine
    $lgc = $app->calc_lgc();   // langue cible
    $lgm = $app->mref->langue;   // langue mere du module
    $mod = $app->mref;   // module

    if (!is_object($mod))
      $this->erreur("xxx:Module invalide : ".$mod->nom);

    if ($lgo == $lgc)
      $this->erreur(_TT("ts:texte_langues_differentes"));

    if ($lgc == $lgm)
      $this->erreur("xxx:La langue cible doit etre differente de la langue mere du module.".
	    "Si vous voulez faire des modifications sur la langue mere, et que ".
	    "vous êtes administrateur, vous devez cliquer sur le bouton 'Administrer'");

    // cree les nouveaux identifiants (cad ceux qui existent dans
    // la langue mere, mais pas dans la langue cible du module)
    $req = "SELECT t1.id,'".mes($mod->nom)."','".mes($lgc)."',t1.str,t1.comm,'NOUV' FROM ".TRAD_LANG." AS t1 LEFT JOIN ".
      TRAD_LANG." AS t2 ON t1.id=t2.id AND t1.module=t2.module AND t2.lang='".mes($lgc)."' ".
      "WHERE t1.lang='".mes($lgm)."' AND t1.module='".mes($mod->nom)."' AND t2.id IS NULL;";

    $rep = mysql_query($req);
    while($r = mysql_fetch_row($rep))
      {
	$req2 = "INSERT ".TRAD_LANG." (id, module, lang, str, comm, status) VALUES ".
	  "('".mes($r[0])."', '".mes($r[1])."', '".mes($r[2])."', '".mes($r[3])."', '".mes($r[4])."', '".mes($r[5])."');";

	$rep2 = mysql_query($req2);
	if ($rep2 == false)
	  $this->erreur("Probleme technique dans la base lors du calcul des nouveaux identfiants");
      }

    // passe les identifiants effaces aux statut EFFACE
    $req = "SELECT t1.id FROM ".TRAD_LANG." AS t1 LEFT JOIN ".TRAD_LANG." AS t2 ON t1.id=t2.id ".
      " AND t1.module=t2.module AND t2.lang='".mes($lgm)."' WHERE t1.lang='".mes($lgc)."' AND ".
      " t1.module='".mes($mod->nom)."' AND t2.id IS NULL;";

    $rep = mysql_query($req);
    while($r = mysql_fetch_row($rep))
      {
	$req2 = "UPDATE ".TRAD_LANG." SET status='CONFLIT' WHERE id='".mes($r[0])."' AND lang='".mes(mes($lgc))."'";
	$rep2 = mysql_query($req2);
	if ($rep2 == false)
	  $this->erreur("Erreur technique lors de la remise a jour de identifiants.");
      }

    // cherche les identifiants modifies 
    $req = "SELECT MAX(ts) FROM ".TRAD_LANG." WHERE module='".mes($mod->nom)."' AND lang='".mes($lgc)."';";

    $rep = mysql_query($req);
    $row = mysql_fetch_row($rep);
    $max = $row[0];

    $req = "SELECT id FROM ".TRAD_LANG." WHERE module='".mes($mod->nom)."' AND lang='".mes($lgm)."' ".
      " AND ts > '".$max."';";

    $rep = mysql_query($req);
    while($r = mysql_fetch_row($rep))
      {
	$req2 = "UPDATE ".TRAD_LANG." SET status='MODIF' WHERE id='".mes($r[0])."'";
	$rep2 = mysql_query($req2);
	if ($rep2 == false)
	  $this->erreur("Erreur technique lors de la remise a jour des identifiants modifies.");
      }

    // zappe l'etape
    $etp = fabrique_etape('traduire', &$app);
    $etp->run(&$app);
  }
}



class etape_traduire extends etape {
  
  // las variables du formulaire
  var $lgi, $tm, $dt, $flt, $cr;
  var $ts, $to, $lt, $rech, $cmd, $idxf;

  function etape_traduire() {

    global $lgi, $tm, $dt, $flt, $cr;
    global $ts, $to, $lt, $cmd, $idxf;

    parent::etape();

    $this->def = array( 'tm'=>'revise', 'dt'=>'', 'flt'=>'', 'idxf'=>0,
	'cr'=>$this->app->lgc, 'ts'=>false , 'to'=>'modifier', 'cmd'=>'');

    $this->lgi = $lgi;

    $this->defaut("tm", $tm);   
    $this->defaut("dt", $dt);
    $this->defaut("flt", $flt);
    $this->defaut("cr", $cr);
    $this->defaut("ts", $ts);
    $this->defaut("to", $to);
    $this->defaut("cmd", $cmd);
    $this->defaut("idxf", $idxf);

    $this->lt = $lt;  

    $this->rech = array();
  }


  function init_instance($app, $nom) {

    parent::init_instance($app, $nom);

    $this->menu = true;
    $this->comm_mod = "verif";
    $this->comm_lgo = "verif";
    $this->comm_lgc = "verif";
  }

  function get_date() {
    global $g_deb;
    
    $req = "SELECT DISTINCT substring(date_modif,1,10) FROM ".TRAD_LANG." WHERE date_modif IS NOT NULL ".
      "AND module='".mes($this->app->mref->nom)."' AND lang='".mes($this->app->lgc)."';";

    $res = mysql_query($req);

    $rep = array();
    while ($row = mysql_fetch_row($res))
      $rep[] = $row[0];

    return $rep;
  }

  function run($app) {

    global $g_deb;
    $g_deb->log(0, "run etape traduire, commande=<".$this->cmd.">");

    // selection sur le module
    $mod = $app->mref->nom;

    if ($this->cmd == 'valider')
      {
	global $commdest, $dest;

	$g_deb->log(0, "ecriture de la chaine : <".$dest.">");
	$req = "UPDATE ".TRAD_LANG." SET str='".mes($dest)."',comm='".mes($commdest)."',status='',".
	  "date_modif=NOW(),traducteur='".mes($app->login)."' ".
	  "WHERE id='".mes($this->lt)."' AND module='".mes($mod)."' AND lang='".mes($app->lgc)."' ";

	$rep = mysql_query($req);
	if ($rep == false)
	  $this->erreur("impossible de mettre a jour");
      }

    // initialisation des items en fonction 
    // des criteres selectionnes

    $req = "SELECT distinct t1.id,substring(t1.date_modif,1,10),t1.traducteur,t1.status ".
      "FROM ".TRAD_LANG." t1,".TRAD_LANG." t2 ";

    if ($mod == '')
      $this->erreur("erreur : module non valide");
    else
      $req .= "WHERE t1.id=t2.id AND t1.module='".mes($mod)."' ";

    // selection sur la langue    
    $req .= " AND t1.lang='".mes($this->app->lgc)."' ";

    if ($this->dt != "")
      $req .= " AND t1.date_modif like '".mes($this->dt)."%'";

    if ($this->flt != "")
      {
	$req .= " AND t2.str like '%".mes($this->flt)."%'";
	$req .= " AND t2.lang='".mes($this->cr)."' ";
      }

    // selection sur le statut
    switch ($this->tm)
      {
      case "revise":
	$req .= " AND (t1.status='NOUV' OR t1.status='MODIF')";
	break;
      case "traduit":
	$req .= " AND (t1.status='' OR t1.status IS NULL)";
	break;
      case "non_traduit":
	$req .= " AND t1.status='NOUV'";
	break;
      case "conflit":
	$req .= " AND t1.status='CONFLIT'";
	break;
      case "modifie":
	$req .= " AND t1.status='MODIF'";
	break;
      default:
      case "tous":
	break;
      }
  
    $rep = mysql_query($req);

    while ($row = mysql_fetch_row($rep))
      $this->rech[] = $row;

    parent::run(&$app);
  }

}
  

class etape_traduction extends etape {
  
  // las variables du formulaire
  var $lgi, $tm, $dt, $flt, $cr;
  var $ts, $to, $lt, $cmd, $idxf;
  var $orig, $dest, $commorig, $commdest;

  function etape_traduction() {

    global $g_deb;
    global $lgi, $tm, $dt, $flt, $cr;
    global $ts, $to, $lt, $cmd, $idxf;
    global $orig, $dest, $commorig, $commdest;

    parent::etape();

    $this->def = array( 'cmd'=>'', 'tm'=>'revise', 'dt'=>'', 'flt'=>'', 
	'cr'=>$this->app->lgc, 'ts'=>false , 'to'=>'modifier', 'idxf'=>0,
	'orig'=>'', 'dest'=>'', 'commorig'=>'', 'commdest'=>'');

    $this->lgi = $lgi;

    $this->defaut("cmd", $cmd);
    $this->defaut("tm", $tm);   
    $this->defaut("dt", $dt);
    $this->defaut("flt", $flt);
    $this->defaut("cr", $cr);
    $this->defaut("ts", $ts);
    $this->defaut("to", $to);
    $this->defaut("idxf", $idxf);

    $this->defaut("orig", $orig);
    $this->defaut("dest", $dest);
    $this->defaut("commorig", $commorig);
    $this->defaut("commdest", $commdest);

    $this->lt = $lt;  
  }

  function init_instance($app, $nom) {

    parent::init_instance($app, $nom);
    $this->menu = false;
  }

  function run($app) {

    $mod = $app->mref;   // module
    $lgm = $mod->langue;   // langue mere du module
    $lgo = $app->lgo;
    $lgc = $app->lgc;

    if ($this->cmd == '')
      {
	// initialisation
	$req = "SELECT str,comm FROM ".TRAD_LANG." WHERE id='".
	  $this->lt."' AND module='".mes($mod->nom)."' AND lang='".mes($lgo)."' ";
	
	$rep = mysql_query($req);
	if ($rep == false)
	  $this->erreur("impossible de trouver une reponse, item faux");
	
	$row = mysql_fetch_row($rep);
	$this->orig = $row[0];
	$this->commorig = $row[1];
	
	$req = "SELECT str,comm FROM ".TRAD_LANG." WHERE id='".
	  mes($this->lt)."' AND module='".mes($mod->nom)."' AND lang='".mes($lgc)."' ";
	
	$rep = mysql_query($req);
	if ($rep == false)
	  $this->erreur("impossible de trouver une reponse, item faux");
	
	$row = mysql_fetch_row($rep);
	$this->dest = $row[0];
	$this->commdest = $row[1];
      }

    parent::run(&$app);
  }

}

class etape_administrer extends etape {

  // las variables du formulaire
  var $flt, $nouv, $lt, $val, $idxf, $cmd;

  function etape_administrer() {

    global $flt, $nouv, $lt, $val, $idxf, $cmd;

    parent::etape();

    $this->def = array( 'flt'=>'', 'nouv'=>'', 'val' => '', 'idxf' => 0,
			'cmd'=>'' );
    
    $this->defaut("flt", $flt);
    $this->defaut("nouv", $nouv);
    $this->defaut("val", $val);
    $this->defaut("idxf", $idxf);
    $this->defaut("cmd", $cmd);

    $this->lt = $lt;  
    $this->rech = array();

  }

  function init_instance($app, $nom) {

    parent::init_instance($app, $nom);

    $this->menu = true;
    $this->menu_lgo = false;
    $this->menu_lgc = false;
  }

  function modifier($mod, $lgm) {
   
    global $g_deb;
    $g_deb->log(0, "fonction modifier");

    $req = "UPDATE ".TRAD_LANG." SET str='".mes($this->val)."' ".
      "WHERE id='".mes($this->lt)."' AND module='".mes($mod->nom)."' AND lang='".mes($lgm)."' ";

    $rep = mysql_query($req);
    if ($rep == false)
      $this->erreur("xxx:Impossible de faire la modification");

    return;
  }
 
  function supprimer($mod, $lgm) {

    global $g_deb;
    $g_deb->log(0, "fonction supprimer");
    
    $req = "DELETE FROM ".TRAD_LANG." WHERE id='".mes($this->lt)."' AND ".
      "module='".mes($mod->nom)."'  AND lang='".$lgm."' ";

    $rep = mysql_query($req);
  }

  function run($app) {

    global $g_deb;
    $g_deb->log(0, "val=".$this->val);

    // initialisation des items en fonction 
    // des criteres selectionnes
    $mod = $app->mref;   // module
    $lgm = $mod->langue;   // langue mere du module

    if ($this->cmd == 'modifier')
      // il faut prendre en compte une modif
      $this->modifier($mod, $lgm);

    else if ($this->cmd == 'supprimer')
      $this->supprimer($mod, $lgm);

    $req = "SELECT distinct id, substring(date_modif,1,10) FROM ".TRAD_LANG." ".
      "WHERE module='".mes($mod->nom)."' AND lang='".mes($lgm)."'";

    if ($this->flt != '')
      $req .= " AND ".$this->explode_rech($this->flt);

    $req .= " ORDER BY id";
    $rep = mysql_query($req);

    while ($row = mysql_fetch_row($rep))
      $this->rech[] = $row;

    // recherche item selection
    if ($this->lt != "")
      {
	$req = "SELECT str FROM ".TRAD_LANG." WHERE id='".mes($this->lt)."' AND module='".
	  mes($mod->nom)."' AND lang='".mes($lgm)."'";

	$req .= " ORDER BY id";
	$rep = mysql_query($req);
	if ($row = mysql_fetch_row($rep))
	  $this->val = $row[0];
      }

    parent::run(&$app);
  }

}


class etape_chercher extends etape {

  // las variables du formulaire
  var $lgr, $rech, $res;

  function etape_chercher() {
    
    global $rech, $lgr;

    parent::etape();

    $this->def = array("lgr"=>"", "rech"=>"");
    $this->defaut("rech", $rech);
    $this->defaut("lgr", $lgr);
    $this->res = array();
  }

  function init_instance($app, $nom) {

    parent::init_instance($app, $nom);
    $this->menu = false;
  }

  function run($app) {

    global $g_deb;
    $g_deb->log(0, ">>> etape_chercher::run");
    $g_deb->log(0, "rech=<".$this->rech.">");

    $mod = $app->mref->nom;   // module
    $lgc = $app->lgc;
    $lgr = $this->lgr;

    if ($this->rech != '')
      {
	$cls = " AND ".$this->explode_rech($this->rech, "t1");

	$req = "SELECT t1.str,t2.str FROM ".TRAD_LANG." t1, ".TRAD_LANG." t2 WHERE ".
	  "t1.id=t2.id AND t1.module=t2.module AND t1.module='".mes($mod)."' ".
	  " AND t2.lang='".mes($lgc)."' AND t1.lang='".mes($lgr)."' ".$cls;
    
	$rep = mysql_query($req);
	if ($rep == false)
	  $this->erreur("impossible d'effectuer la recherche");

	while($row = mysql_fetch_row($rep))
	  $this->res[] = $row;
      }

    parent::run(&$app);
  }  

}

class etape_creer extends etape {

  var $cmd, $nouv, $lt, $idxf;

  function etape_creer() {
    
    global $cmd, $nouv, $lt, $idxf;

    parent::etape();

    $this->def = array("cmd"=>"", "nouv"=>"", "lt"=>"", "idxf"=>0);
    $this->defaut("cmd", $cmd); 
    $this->defaut("nouv", $nouv); 
    $this->defaut("lt", $lt); 
    $this->defaut("idxf", $idxf); 
  }

  function init_instance($app, $nom) {

    parent::init_instance($app, $nom);
    $this->menu = false;
  }

  function run($app) {

    global $g_deb;
    $g_deb->log(0, ">>> etape_creer::run");

    $mod = $app->mref;   // module
    $lgm = $mod->langue;  // langue mere du module

    if ($this->cmd == "valider")
      {
	// insertion nouvel item

	$req = "INSERT ".TRAD_LANG."(id, module, lang, str, comm, status) VALUES (".
	  "'".mes($this->nouv)."', '".mes($mod->nom)."', '".mes($lgm)."', '', '', '' )";

	$rep = mysql_query($req);

	// selection de l'endroit oùsqu'on veut aller
	$this->lt = $this->nouv;
	$req = "SELECT count(*) FROM ".TRAD_LANG." WHERE id < '".mes($this->lt)."'".
	  " AND module='".mes($mod->nom)."' AND lang='".mes($lgm)."' ORDER BY id";

	$rep = mysql_query($req);
	$row = mysql_fetch_row($rep);

	$this->idxf = $row[0] - 3;
	$g_deb->log(0, "la valeur de idxf est=".$this->idxf);

	// zappe l'etape
	$etp = fabrique_etape('administrer', &$app);
	$etp->idxf = $this->idxf;
	$etp->lt = $this->lt;
	$etp->run(&$app);
	exit;
      }
    
    parent::run(&$app);
  }  

}


class etape_commenter extends etape {

  // las variables du formulaire
  var $lgi, $lt, $comm, $cmd, $cmd2, $lgc;

  function etape_commenter() {

    global $lgi, $lt, $comm, $cmd, $cmd2, $lgc;

    parent::etape();

    $this->def = array("cmd"=>"", "cmd2"=>"", "lgc"=>"", "comm"=>"");
    
    $this->lgi = $lgi;
    $this->defaut("cmd", $cmd);
    $this->defaut("cmd2", $cmd2);
    $this->lt = $lt;  
    $this->defaut("comm", $comm);
    $this->defaut("lgc", $lgc);
    
  }

  function init_instance($app, $nom) {

    parent::init_instance($app, $nom);
    $this->menu = false;
  }

  function commenter($mod, $lgc) {
   
    global $g_deb;
    $g_deb->log(0, "fonction modifier");

    $req = "UPDATE ".TRAD_LANG." SET comm='".mes($this->comm)."' ".
      "WHERE id='".mes($this->lt)."' AND module='".mes($mod->nom)."' AND lang='".mes($lgc)."' ";

    $rep = mysql_query($req);
    if ($rep == false)
      $this->erreur("xxx:Impossible de faire la modification");

    return;
  }

  function run($app) {

    global $g_deb;
    $g_deb->log(0, ">>> etape_commenter::run");
    $g_deb->log(0, "commande = <".$this->cmd.">");
    $g_deb->log(0, "lgc = <".$this->lgc.">");

    $mod = $app->mref;   // module
    $lgm = $mod->langue;   // langue mere du module

    // langue a commenter
    if ($this->cmd2 == 'traduc')
      $lgc = $this->lgc;
    else
      $lgc = $lgm; 

    if ($this->cmd == 'valider')
      {
	// dans ce cas, on utilise le comm passe en
	// parametre (pas celui de la base)
	$this->commenter($mod, $lgc);
	exit;
      }

    // recherche du commentaire
    $req = "SELECT comm FROM ".TRAD_LANG." WHERE id='".mes($this->lt)."' AND module='".
      mes($mod->nom)."' AND lang='".mes($lgc)."'";

    $rep = mysql_query($req);
    if ($row = mysql_fetch_row($rep))
      $this->comm = $row[0];

    parent::run(&$app);
  }

}

?>