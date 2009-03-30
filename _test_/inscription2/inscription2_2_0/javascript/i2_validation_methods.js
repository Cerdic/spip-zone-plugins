/**
 * Les methodes pour jquery validate specifiques a Inscription2
 */

jQuery.validator.addMethod("chainenombre", function(value, element) {
 	return this.optional(element) || /^[A-Za-z0-9\-\'\.ßÂâÄäÁÀàËëÈèÊêÉéÏïÎîÌìÍÒòÓÔôÖöÙùÜüÛû‡,˚ ]+$/.test(value);
});
    
jQuery.validator.addMethod("chaine", function(value, element) {
	return this.optional(element) || /^[A-Za-z\-\'\.ßÂâÄäÁÀàËëÈèÊêÉéÏïÎîÌìÍÒòÓÔôÖöÙùÜüÛû‡,˚ ]+$/.test(value);
});

jQuery.validator.addMethod("postal", function(value, element) {
	return this.optional(element) || /^[A-Z]{1,2}[-|\s][0-9]{3,6}$|^[0-9]{3,6}$|^[0-9|A-Z]{2,5}[-|\s][0-9|A-Z]{2,4}$|^[A-Z]{1,2} [0-9|A-Z]{2,5}[-|\s][0-9|A-Z]{2,4}$/.test(value);
});

jQuery.validator.addMethod("numero", function(value, element) {
	return this.optional(element) || /^[0-9\+\. \-]+$/.test(value);
});