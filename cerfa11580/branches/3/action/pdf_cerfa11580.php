<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
    return;

// Production du recu fiscal a partir du formulaire standard
// cerfa 11580*03 : "Recu Dons aux oeuvres",
// (article 200-5 du Code General des Impots)
// http://www.impots.gouv.fr/portal/deploiement/p1/fichedescriptiveformulaire_5184/fichedescriptiveformulaire_5184.pdf

define('RECU_FISCAL', find_in_path('pdf/cerfa_11580.pdf'));
if (!defined('SIGNATURE_PRES'))
    define('SIGNATURE_PRES', find_in_path('signature_pres.png'));

function action_pdf_cerfa11580() {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();

        if (!preg_match(',^(\d+)\D(\d*)$,', $arg, $r)) {
                spip_log("action_pdf_cerfa11580 incompris: " . $arg);
        } else {
	if (!test_plugin_actif('fpdf'))
        return;
	list(,$id_auteur, $annee) = $r;
	$mbr_qui = sql_fetsel('*', 'spip_asso_membres', "id_auteur=$id_auteur");
        include_spip('fpdf');
        include_spip('pdf/fpdf_tpl');
        include_spip('pdf/fpdi_parser');
        include_spip('pdf/fpdi');
        include_spip('chiffreEnLettre');
        include_spip('inc/association_comptabilite');
	$association_imputation = charger_fonction('association_imputation', 'inc');
	if (!preg_match('/^\d{4}$/', $annee))
			$annee = date('Y')-1;
	$montants = sql_getfetsel('SUM(recette) AS montant', 'spip_asso_comptes', $association_imputation('pc_cotisations', $id_auteur) ." AND vu AND DATE_FORMAT(date_operation, '%Y')=$annee");
	if ($taux = $GLOBALS['association_metas']['tauxfiscal'])
		$montants = $montants*($taux/100);
	$montants += sql_getfetsel('SUM(D.argent) AS montant',
            'spip_asso_dons AS D LEFT JOIN spip_asso_comptes AS C ON C.id_journal=D.id_don',
            $association_imputation('pc_dons', '', 'C') ." AND C.vu AND DATE_FORMAT(D.date_don, '%Y')=$annee AND id_auteur=$id_auteur AND contrepartie=''");
           //$mail = sql_getfetsel('email', 'spip_auteurs',"id_auteur=$id_auteur");
	$prenom = $mbr_qui['prenom'];
	$nom =  $mbr_qui['nom_famille'];

	if (!$montants) {
		include_spip('inc/minipres');
		$h = generer_url_ecrire('auteur_infos', "id_auteur=$id_auteur");
		$h = _T('asso:versement_valide_annee_pour',
			array('url' => $h,
			      'annee' => $annee,
			      'nom' => "$prenom $nom"));
		echo minipres($h, generer_form_ecrire('accueil', '','',_T('public:accueil_site')));
	} else {
            $mbr_ou=sql_fetsel('*','spip_adresses AS p JOIN spip_adresses_liens AS l ON p.id_adresse=l.id_adresse', "objet='auteur' AND id_objet=$id_auteur AND type IN ('pref','dom','home','perso','domicile','main','principale','principal') LIMIT 0, 1");
            if (!$mbr_ou)
                $mbr_ou = sql_fetsel('*','spip_adresses AS p JOIN spip_adresses_liens AS l ON p.id_adresse=l.id_adresse', " objet='auteur' AND id_objet=$id_auteur LIMIT 0, 1)");
            if ($mbr_ou['pays']=='FR')
                $cp = $mbr_ou['code_postal'];
            elseif ($mbr_ou['code_postal'])
                $cp = $mbr_ou['pays'].'--'.$mbr_ou['code_postal'];
            else
                $cp = $mbr_ou['pays'].'-99999';
            if ($mbr_ou['boite_postale'])
                $rue = $mbr_ou['voie'].' -- '.$mbr_ou['boite_postale'];
            elseif ($mbr_ou['complement'])
                $rue = $mbr_ou['voie'].' -- '.$mbr_ou['complement'];
            else
                $rue = $mbr_ou['voie'];
	    build_pdf("$annee-$id_auteur", $montants, $annee, $nom, $prenom, $rue, $cp, $mbr_ou['ville'] );
		}
	}
}

function build_pdf($code, $montant, $isodate, $nom, $prenoms, $adresse, $cp, $commune, $forme=0, $nature=0, $mode=0) {
    $pdf =& new FPDI();
    $pdf->setSourceFile(RECU_FISCAL);
    $pdf->SetFont('Arial');
    $pdf->SetFontSize(9);
    $pdf->SetTextColor(20,20,246);

// Page 1.
    $pdf->addPage();
    $pdf->useTemplate($pdf->importPage(1, '/MediaBox'), 0, 0, 210);
    // Numero d'ordre du recu
    $pdf->SetXY(170, 18);
    $pdf->Write(0, $code);
    // Beneficiaire des versements : Nom ou denomination
    $pdf->SetXY(20, 42);
    $pdf->Write(0, utf8_decode($GLOBALS['association_metas']['nom']));
    // Beneficiaire des versements : Adresse : No - Rue
    #$rue=strtok($GLOBALS['association_metas']['rue'], ' '); // on debute par le numero s'il es mentionne, mais toutes les adresses n'ont pas de numero...
    $pdf->SetXY(24, 53);
    #$pdf->Write(0, $rue[0]);
    $pdf->SetXY(42, 53);
    $pdf->Write(0, $rue[1]);
    #$pdf->Write(0, utf8_decode($rue[1]));
    $pdf->Write(0, str_replace("\n", ' - ', utf8_decode($GLOBALS['association_metas']['rue'])) );
    // Beneficiaire des versements : Adresse : Code postal - Commune
    $pdf->SetXY(40, 59);
    $pdf->Write(0, utf8_decode($GLOBALS['association_metas']['cp']));
    $pdf->SetXY(74, 59);
    $pdf->Write(0, utf8_decode($GLOBALS['association_metas']['ville']));
    // Deneficiaire : Objet sur 4 lignes max
    $objet = explode("\n", $GLOBALS['association_metas']['objet']);
    $pdf->SetXY(20, 69);
    $pdf->Write(0, utf8_decode($objet[0]));
    $pdf->SetXY(20, 73);
    $pdf->Write(0, utf8_decode($objet[1]));
    $pdf->SetXY(20, 78);
    $pdf->Write(0, utf8_decode($objet[2]));
    $pdf->SetXY(20, 82);
    $pdf->Write(0, utf8_decode($objet[3]));

    // Beneficiaire : case habilitation a delivrer recu fiscal
    switch (1) #$GLOBALS['association_metas']['recufiscal'])
    {
        case 1:
            $ycase = 99;
            $lesdates = explode(' ', $GLOBALS['association_metas']['infofiscal']);
            $ladate=explode('/', $lesdates[0]); // date du decret : jj/mm/aaa
            $pdf->SetXY(142, 99);
            $pdf->Write(0, $ladate[0]);
            $pdf->SetXY(150, 99);
            $pdf->Write(0, $ladate[1]);
            $pdf->SetXY(158, 99);
            $pdf->Write(0, $ladate[2]);
            $ladate=explode('/', $lesdates[1]); // date du JO : jj/mmm/aaa
            $pdf->SetXY(42, 104);
            $pdf->Write(0, $ladate[0]);
            $pdf->SetXY(50, 104);
            $pdf->Write(0, $ladate[1]);
            $pdf->SetXY(58, 104);
            $pdf->Write(0, $ladate[2]);
            break;
        case 2:
            $ycase = 99;
            $lesdates = explode('/', $GLOBALS['association_metas']['infofiscal']);
            $ladate = explode('/', $lesdates[0]); // date du decret : jj/mm/aaaa
            $pdf->SetXY(170, 108);
            $pdf->Write(0, $ladate[0]);
            $pdf->SetXY(177, 108);
            $pdf->Write(0, $ladate[1]);
            $pdf->SetXY(184, 108);
            $pdf->Write(0, $ladate[2]);
            break;
        case 3:
            $ycase = 116;
            break;
        case 4:
            $ycase = 128;
            break;
        case 5:
            $ycase = 135;
            break;
        case 6:
            $ycase = 142;
            break;
        case 7:
            $ycase = 150;
            break;
        case 8:
            $ycase = 161;
            break;
        case 9:
            $ycase = 169;
            break;
        case 10:
            $ycase = 176;
            break;
        case 11:
            $ycase = 183;
            break;
        case 12:
            $ycase = 195;
            break;
        case 13:
            $ycase = 211;
            break;
        case 14:
            $ycase = 219;
            break;
        case 15:
            $ycase = 230;
            break;
        case 16:
            $ycase = 238;
            break;
        case 17:
            $ycase = 245;
            break;
        case 18:
            $ycase = 252;
            break;
        case 19:
            $ycase = 260; // 259 ou 260
            break;
        case 20:
            $ycase = 267;
            $pdf->SetXY(53, $ycase-1);
            $pdf->Write(0, utf8_decode($GLOBALS['association_metas']['infofiscal']));
            break;
        default:
            $ycase = 267;
    }
    $pdf->SetXY(19, $ycase);
    $pdf->Write(0, 'X');

// page 2.
    $pdf->addPage();
    $pdf->useTemplate($pdf->importPage(2, '/MediaBox'), 0, 0, 210);
    // Donateur : Nom
    $pdf->SetXY(20, 25);
    $pdf->Write(0, utf8_decode($nom));
    // Donateur : Prenoms
    $pdf->SetXY(109, 25);
    $pdf->Write(0,utf8_decode($prenoms));
    // Donateur : Adresse
    $pdf->SetXY(20, 39);
    $pdf->Write(0, utf8_decode($adresse));
    // Donateur : Code postal
    $pdf->SetXY(40, 44);
    $pdf->Write(0, utf8_decode($cp));
    // Donateur : Commune
    $pdf->SetXY(80, 44);
    $pdf->Write(0, utf8_decode($ville));
    // Donation : somme en chiffres (notation decimale)
    $pdf->SetXY(86, 69);
    #$pdf->SetXY(90, 70);
    $pdf->Write(0, $montant);
    // Donation : somme en toutes lettres... (ou chiffres entoures de *** )
    $lettre=new ChiffreEnLettre();
    $nombre=$lettre->Conversion($montant);
    $pdf->SetXY(59, 79);
    $pdf->Write(0, $nombre.'  EUROS');
    // Date du versement/don
    $ladate=explode('-', $isodate);
    $pdf->SetXY(98, 88);
    $pdf->Write(0, $ladate[0]);
    $pdf->SetXY(80, 88);

    switch (intval($ladate[1]))
    {
        case 1:
            $mois = 'janvier';
            break;
        case 2:
            $mois = 'février';
            break;
        case 3:
            $mois = 'mars';
            break;
        case 4:
            $mois = 'avril';
            break;
        case 5:
            $mois = 'mai';
            break;
        case 6:
            $mois = 'juin';
            break;
        case 7:
            $mois = 'juillet';
            break;
        case 8:
            $mois = 'août';
            break;
        case 9:
            $mois = 'septembre';
            break;
        case 10:
            $mois = 'octobre';
            break;
        case 11:
            $mois = 'novembre';
            break;
        case 12:
            $mois = 'décembre';
            break;
        case 0:
        default:
            $mois = '';
    }
    if (!$mois AND !$ladate[0])
        $mois = 'année';
    $pdf->Write(0, utf8_decode($mois));
    $pdf->SetXY(73, 88);
    if ($ladate[2])
        $pdf->Write(0, $ladate[2]);
    // Beneficiaire : certification legale
    if ($GLOBALS['association_metas']['cgi200'])
    {
        $pdf->SetXY(56, 103);
        $pdf->SetXY(57, 103);
        $pdf->Write(0, 'X');
    }
    if ($GLOBALS['association_metas']['cgi238'])
    {
        $pdf->SetXY(106, 103);
        $pdf->SetXY(107, 103);
        $pdf->Write(0, 'X');
    }
    if ($GLOBALS['association_metas']['cgi885'])
    {
        $pdf->SetXY(156, 103);
        $pdf->SetXY(157, 103);
        $pdf->Write(0, 'X');
    }
    // Donation : forme du don
    switch ($forme)
    {
        case 1:
            $xcase=19;
            break;
        case 2:
            $xcase=61; // 62 ou 61
            break;
        case 3:
            $xcase=119; // 120 ou 119
            break;
        case 4:
            $xcase=179;
            break;
        case 0:
        default:
            $xcase=119;
    }
    $pdf->SetXY($xcase, 120);
    if ($xcase)
        $pdf->Write(0, 'X');
    // Donation : nature du don
    switch ($nature)
    {
        case 1:
            $xcase=19;
            break;
        case 2:
            $xcase=61; // 62 ou 61
            break;
        case 3:
            $xcase=119; // 120 ou 119
            break;
        case 0:
        default:
            $xcase=19;
    }
    $pdf->SetXY($xcase, 143);
    if ($xcase)
        $pdf->Write(0, 'X');
    // Donation en numeraire : mode de paiement
    switch ($mode)
    {
        case 1:
            $xcase=19;
            break;
        case 2:
            $xcase=61; // 62 ou 61
            break;
        case 3:
            $xcase=119; // 120 ou 119
            break;
        case 0:
        default:
            $xcase=0;
    }
    $pdf->SetXY($xcase, 165);
    if ($xcase)
        $pdf->Write(0, 'X');
    // Date d'emission recu : aujourd'hui
    $pdf->SetXY(145, 245);
    $pdf->Write(0, date('d   m    Y'));
    // Signature du president/tresorier et cachet de l'association
    if (SIGNATURE_PRES)
        $pdf->Image(SIGNATURE_PRES,143,248);
#    $pdf->SetFillColor(255,255,255);
#    $pdf->Rect(68,228,12,2,'F');
#    $pdf->Rect(135,247,25,4,'F');

// telechargement pages
    $pdf->Output('recu_fiscal_' . $isodate . '.pdf', 'I');
}

?>