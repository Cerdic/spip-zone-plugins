$(document).ready(function(){
	
	$('.nyroModal').nyroModal({
		hideBackground: function(elt, params, callback) {
			callback();
		}
	});
	
});
