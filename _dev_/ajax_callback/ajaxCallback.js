// A plugin that wraps all ajax calls introducing a fixed callback function on ajax complete
jQuery.fn._load = jQuery.fn.load;

jQuery.fn.load = function( url, params, callback, ifModified ) {

	callback = callback || function(){};

	// If the second parameter was provided
	if ( params ) {
		// If it's a function
		if ( params.constructor == Function ) {
			// We assume that it's the callback
			callback = params;
			params = null;
		} 
	}
	var callback2 = function(res,status) {triggerAjaxLoad(this);callback(res,status)};
	
	this._load( url, params, callback2, ifModified );
}
