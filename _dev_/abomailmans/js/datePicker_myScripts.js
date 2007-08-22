// JavaScript Document

$(document).ready(init);
function init()
{

	// OPTIONALLY SET THE DATE FORMAT FOR ALL DATE PICKERS ON THIS PAGE
	$.datePicker.setDateFormat('ymd', '-');
	
	// OPTIONALLY SET THE LANGUAGE DEPENDANT COPY IN THE POPUP CALENDAR
	/**/
	$.datePicker.setLanguageStrings(
		['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
		['Janvier', 'F&eacute;vrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Ao&ucirc;t', 'Septembre', 'Octobre', 'Novembre', 'D&eacute;cembre'],
		{p:'Pr&eacute;c&egrave;dent', n:'Suivant', c:'Fermer', b:'Choisir cette date'}
	);
	
	
	// DIFFERENT OPTIONS SHOWING HOW YOU MIGHT INITIALISE THE DATE PICKER (UNCOMMENT ONE AT A TIME) //
	
	// all inputs with a class of "date-picker" have a date picker which lets you pick any date in the future
	//$('input.date-picker').datePicker();
	// OR
	// all inputs with a class of "date-picker" have a date picker which lets you pick any date after 05/03/2006
	//$('input.date-picker').datePicker({startDate:'05/03/2006'});
	// OR
	// all inputs with a class of "date-picker" have a date picker which lets you pick any date from today till 05/011/2006
	//$('input.date-picker').datePicker({endDate:'05/11/2006'});
	// OR
	// all inputs with a class of "date-picker" have a date picker which lets you pick any date from 05/03/2006 till 05/11/2006
	//$('input.date-picker').datePicker({startDate:'05/03/2006', endDate:'05/11/2006'});
	// OR 
	// the input with an id of "date" will have a date picker that lets you pick any day in the future...
	//$('input#date').datePicker();
	// ...and the input with an id of "date2" will have a date picker that lets you pick any day between the 02/11/2006 and 13/11/2006
	$('input#date').datePicker({startDate:'01-01-2000'});

	/*
	// testing code to check the change event is fired...
	$('input#date1').bind(
		'change',
		function()
		{
			alert($(this).val());
		}
	);
	*/
	
	// END DIFFERENT OPTIONS //
}
