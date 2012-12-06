

/*==============================================================================================

	--------------------------------------------------------------------------------------------
	SPIP Immo 3.1d is a SPIP's plugin for real estate agencies & agents
	Copyright (C) 2007-2009 SARL Comme a la Ville http://www.commealaville.com - contact@commealaville.com
	http://spipimmo.commealaville.com

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
	--------------------------------------------------------------------------------------------
	SPIP Immo 3.1d est un plugin de SPIP à destination des agences immobilières
	Copyright (C) 2007-2009 SARL Comme à la Ville http://www.commealaville.com - contact@commealaville.com
	http://spipimmo.commealaville.com

	Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le
	modifier au titre des clauses de la Licence Publique Générale GNU, telle
	que publiée par la Free Software Foundation ; soit la version 2 de la
	Licence, ou (à votre discrétion) une version ultérieure quelconque.

	Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE
	GARANTIE ; sans même une garantie implicite de COMMERCIABILITE ou DE
	CONFORMITE A UNE UTILISATION PARTICULIERE. Voir la Licence Publique
	Générale GNU pour plus de détails.

	Vous devriez avoir reçu un exemplaire de la Licence Publique Générale
	GNU avec ce programme ; si ce n'est pas le cas, écrivez à la Free
	Software Foundation Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.

==============================================================================================*/

/*==================================================================================================
Affiche du bon tableau pour la création ou la modification d'annonce
+ changement du style de l'onglet
==================================================================================================*/

function afficherTableau(tableau)
{
	document.getElementById("tableau"+tableau).style.display="block";
	document.getElementById("intitule_tableau"+tableau).style.backgroundColor="#EEEEEE";
}


/*==================================================================================================
Cahce les tableaux ne devant pas apparaître  lors de la création ou de la modification d'une annonce
+ changement du style de l'onglet
==================================================================================================*/

function cacherTableau(tableau)
{
	var tab=tableau.split(",");
	for (var i=0; i<tab.length; i++)
	{
		document.getElementById("tableau"+tab[i]).style.display="none";
		document.getElementById("intitule_tableau"+tab[i]).style.backgroundColor="transparent";
	}
}

/*==================================================================================================
Vérification d'un champs obligatoire dansle formulaire d'annonce
==================================================================================================*/
function formObligatoire(inputName, id)
{
	if(document.forms['annonce'].elements[inputName].value!="")
	{
		document.getElementById(id).style.backgroundColor="transparent";
	}
	else
	{
		document.getElementById(id).style.backgroundColor="#FFE9A6";
	}
}


/*==================================================================================================
Vérification des champs lors de la création ou la modification d'une annonce
==================================================================================================*/

function verificationChamps()
{
	VenteLocation=document.forms['annonce'].elements['vente_location'].value;
	TypeMandat=document.forms['annonce'].elements['type_mandat'].value;
	TypeOffre=document.forms['annonce'].elements['type_offre'].value;
	CodePostal=document.forms['annonce'].elements['code_postal'].value;
	Ville=document.forms['annonce'].elements['ville'].value;
	PrixLoyer=document.forms['annonce'].elements['prix_loyer'].value;
	SurfaceHabitation=document.forms['annonce'].elements['surface_habitable'].value;
	TexteFr=document.forms['annonce'].elements['texte_francais'].value;
	Prestige=document.forms['annonce'].elements['prestige'].value;
	ChampAbsent="Ce(s) champ(s) sont vide(s):\n";
	var exprNombreVirgule=new RegExp("^[0-9]+$","g");
	erreur=0;



	if(VenteLocation=="")
	{
		erreur++;
		ChampAbsent=ChampAbsent+"- Vente ou location\n";
	}

	if(TypeMandat=="")
	{
		erreur++;
		ChampAbsent=ChampAbsent+"- Le type de mandat\n";
	}

	if(TypeOffre=="")
	{
		erreur++;
		ChampAbsent=ChampAbsent+"- Le type d'offre\n";
	}

	if(CodePostal=="")
	{
		erreur++;
		ChampAbsent=ChampAbsent+"- Le code postal\n";
	}

	if(Ville=="")
	{
		erreur++;
		ChampAbsent=ChampAbsent+"- La ville\n";
	}

	if(PrixLoyer=="")
	{
		erreur++;
		ChampAbsent=ChampAbsent+"- Le prix/loyer\n";
	}

	if(SurfaceHabitation=="")
	{
		erreur++;
		ChampAbsent=ChampAbsent+"- La surface d'habitation\n";
	}

	if(TexteFr=="")
	{
		erreur++;
		ChampAbsent=ChampAbsent+"- Le texte de description (Fran\347ais)\n";
	}

	if(Prestige=="")
	{
		erreur++;
		ChampAbsent=ChampAbsent+"- L'\351tat de prestige\n";
	}

	if(erreur!=0)
	{
		alert(ChampAbsent);
		return false;
	}
	else
	{
		var erreur=0;
		ChampErreur="Ce(s) champ(s) ne sont pas correctement rempli(s) :\n";

		if(isNaN(PrixLoyer))
		{
			ChampErreur=ChampErreur+"- Le prix/loyer doit \352tre un nombre\n";
			erreur++;
		}

		if(isNaN(SurfaceHabitation))
		{
			ChampErreur=ChampErreur+"- La surface habitable doit \352tre un nombre\n";
			erreur++;
		}

		if(isNaN(SurfaceHabitation))
		{
			ChampErreur=ChampErreur+"- La charge doit \352tre un nombre\n";
			erreur++;
		}

		if(erreur!=0)
		{
			alert(ChampErreur);
			return false;
		}
		else
		{
			return true;
		}
	}
}


/*==================================================================================================
Confimer la suppression d'une  annonce
==================================================================================================*/

function confirmerSupprimer()
{
	if (confirm("Supprimer l'annonce?"))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function accesPage(page, nbPage, suiteLien)
{
	if(isNaN(page))
	{
		alert('Merci de saisir un chiffre');
	}
	else if (page <=0)
	{
		alert('Merci de saisir un chiffre positif');
	}
	else if(page>nbPage)
	{
		alert('Merci de saisir un chiffre inf\351rieur ou \351gal au nombre de page total ('+nbPage+')');
	}
	else
	{
		window.location.replace("?exec=spipimmo&pg="+page+suiteLien);
	}
}