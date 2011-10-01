/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Outils génériques
 *
 */
 
 
//// Helpers Javascript, qui, a mon sens, manquent cruellement dans le langage

// Teste si un objet est défini
// C'est à dire que son type n'est pas 'undefined' et qu'il n'est pas null
if (typeof(isObject) != "function")
	function isObject(instance)
	{
	    return ((typeof(instance) != "undefined") && (instance != null)) ? true : false;
	}

// Recherche d'un élément dans un tableau
if (typeof(arrayContains) != "function")
	function arrayContains(array, element)
	{
		for (var index in array)
			if (array[index] == element)
				return true;
		return false;
	}
if (typeof(arrayCount) != "function")
	function arrayCount(array)
	{
		var count = 0;
		for (var index in array)
			count++;
		return count;
	}
	
// Clonage
/*
* Fonction de clonage
* @author Keith Devens
* @see http://keithdevens.com/weblog/archive/2007/Jun/07/javascript.clone
*/
if (typeof(clone) != "function")
	function clone(srcInstance)
	{
		// Si l'instance source n'est pas un objet ou qu'elle ne vaut rien c'est une feuille donc on la retourne
		if(typeof(srcInstance) != 'object' || srcInstance == null)
			return srcInstance;

		// On appelle le constructeur de l'instance source pour crée une nouvelle instance de la même classe
		var newInstance;
		if (typeof(srcInstance.constructor) == "function")
			newInstance = srcInstance.constructor();
		else
			newInstance = new Object();
		
		// On parcourt les propriétés de l'objet et on les recopies dans la nouvelle instance
		for(var i in srcInstance)
		{
			newInstance[i] = clone(srcInstance[i]);
		}

		return newInstance;
	}

/**
* Function : dump()
* Arguments: The data - array,hash(associative array),object
*    The level - OPTIONAL
* Returns  : The textual representation of the array.
* This function was inspired by the print_r function of PHP.
* This will accept some data as the argument and return a
* text that will be a more readable version of the
* array/hash/object that is given.
*/
function dump(arr,level)
{
	var dumped_text = "";
	if (!level)
		level = 0;

	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0; j<level+1; j++)
		level_padding += "    ";

	if(typeof(arr) == 'object')  //Array/Hashes/Objects
	{
		for(var item in arr)
		{
			var value = arr[item];
			if (typeof(value) == 'object') //If it is an array,
			{
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value, level+1);
			}
			else
			{
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	}
	else //Stings/Chars/Numbers etc.
	{
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

//// Helpers jQuery / HTML

// Se déplacer vers un élément HTML
if (typeof(scrollToElement) != "function")
	function scrollToElement(theElement)
	{
		// Rechercher la position
		var selectedPosX = 0;
		var selectedPosY = 0;
		while (isObject(theElement))
		{
			selectedPosX += theElement.offsetLeft;
			selectedPosY += theElement.offsetTop;
			theElement = theElement.offsetParent;
		}
		
		// Faire le scroll
		window.scrollTo(selectedPosX,selectedPosY);
	}

// Tester si un évènement ajax concerne une entité jQuery (basé sur jquery.forms)
jQuery.fn.isAjaxTarget = function(settings)
{
	if (!isObject(settings) || !isObject(settings.data))
		return false;
	var attrId = this.attr("id");
	if (!isObject(attrId))
		return false;
	var id = attrId.split("-");
	if (id.length < 2)
		return false;
	var targetAction = id[0];
	var targetArg = id[1];
	var queryAction = null;
	var queryArg = null;
	var matches = settings.data.match(/(action|arg)=(.[^&]*)/g);
	if (!isObject(matches))
		return false;
	for (var index = 0; index < matches.length; index++)
	{
		var part = matches[index].split("=");
		if (part[0] === "action")
			queryAction = part[1];
		if (part[0] === "arg")
			queryArg = part[1];
	}
	if ((queryAction === null) || (queryArg === null))
		return false;
	return ((targetAction === queryAction) && (targetArg === queryArg)) ? true : false;
};
