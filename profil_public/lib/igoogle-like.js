	// function that writes the list order to a cookie
function saveOrder() {
    $(".column").each(function(index, value){
        var colid = value.id;
        var cookieName = "cookie-" + colid;
        // Get the order for this column.
        var order = $('#' + colid).sortable("toArray");
        // For each portlet in the column
        for ( var i = 0, n = order.length; i < n; i++ ) {
            // Determine if it is 'opened' or 'closed'
            var v = $('#' + order[i] ).find('.portlet-content').is(':visible');
            // Modify the array we're saving to indicate what's open and
            //  what's not.
            order[i] = order[i] + ":" + v;
        }
        $.cookie(cookieName, order, { path: "/", expiry: new Date(2012, 1, 1)});
    });
}

// function that restores the list order from a cookie
function restoreOrder() {
    $(".column").each(function(index, value) {
        var colid = value.id;
        var cookieName = "cookie-" + colid
        var cookie = $.cookie(cookieName);
        if ( cookie == null ) { return; }
        var IDs = cookie.split(",");
        for (var i = 0, n = IDs.length; i < n; i++ ) {
            var toks = IDs[i].split(":");
            if ( toks.length != 2 ) {
                continue;
            }
            var portletID = toks[0];
            var visible = toks[1]
            var portlet = $(".column")
                .find('#' + portletID)
                .appendTo($('#' + colid));
            if (visible === 'false') {
                portlet.find(".ui-icon").toggleClass("ui-icon-minus");
                portlet.find(".ui-icon").toggleClass("ui-icon-plus");
                portlet.find(".portlet-content").hide();
            }
        }
    });
} 


$(document).ready( function () {
    $(".column").sortable({
        connectWith: ['.column'],
        stop: function() { saveOrder(); }
    }); 

    $(".portlet")
        .addClass("ui-widget ui-widget-content")
        .addClass("ui-helper-clearfix ui-corner-all")
        .find(".portlet-header")
        .addClass("ui-widget-header ui-corner-all")
        .prepend('<span class="ui-icon ui-icon-minus"></span>')
        .end()
        .find(".portlet-content");

    restoreOrder();

    $(".portlet-header .ui-icon").click(function() {
        $(this).toggleClass("ui-icon-minus");
        $(this).toggleClass("ui-icon-plus");
        $(this).parents(".portlet:first").find(".portlet-content").toggle();
        saveOrder(); // This is important
    });
    $(".portlet-header .ui-icon").hover(
        function() {$(this).addClass("ui-icon-hover"); },
        function() {$(this).removeClass('ui-icon-hover'); }
    );
}); 	
	
//toggle div
$(document).ready(function(){
	$(".head").addClass("deroule");
	});
			
	
$(document).ready(function(){

			//hide message_body after the first one
			$(".content").hide();
			
				
			//toggle message_body
			$(".head").click(function(){
			  $(this).next(".content").slideToggle(500);
			  $(this).toggleClass("enroule"); 
			  return false;
			});
			});
			
//toggle div avec plus d'options 
//pour documentation et utlisation future
//pas encore utilise			
$(document).ready(function(){
	
	//hide message_body after the first one
	$(".message_list .message_body:gt(0)").hide();
	
	

	
	//toggle message_body
	$(".message_head").click(function(){
		$(this).next(".message_body").slideToggle(500)
		return false;
	});

	//collapse all messages
	$(".collpase_all_message").click(function(){
		$(".message_body").slideUp(500)
		return false;
	});

	//show all messages
	$(".show_all_message").click(function(){
		$(this).hide()
		$(".show_recent_only").show()
		$(".message_list li:gt(4)").slideDown()
		return false;
	});

	//show recent messages only
	$(".show_recent_only").click(function(){
		$(this).hide()
		$(".show_all_message").show()
		$(".message_list li:gt(4)").slideUp()
		return false;
	});

});


//configuration affichage des widgets

$(document).ready(function(){
    $("#case-articles").click(function () {
        if ($('#case-articles:checked').val() !== undefined) {
            // la case est cochée -> on affiche
            $("#box-articles").show("drop", { direction: "down" }, 1000);
        }
        else {
            // la case n'est pas cochée -> on cache
            $("#box-articles").hide("drop", { direction: "down" }, 1000);
        }
    });
	
	$("#case-com").click(function () {
        if ($('#case-com:checked').val() !== undefined) {
            // la case est cochée -> on affiche
            $("#box-com").show("drop", { direction: "down" }, 1000);
        }
        else {
            // la case n'est pas cochée -> on cache
            $("#box-com").hide("drop", { direction: "down" }, 1000);
        }
    });
	
		
	$("#case-aide").click(function () {
        if ($('#case-aide:checked').val() !== undefined) {
            // la case est cochée -> on affiche
            $("#box-aide").show("drop", { direction: "down" }, 1000);
        }
        else {
            // la case n'est pas cochée -> on cache
            $("#box-aide").hide("drop", { direction: "down" }, 1000);
        }
    });
	
	$("#case-fav").click(function () {
        if ($('#case-fav:checked').val() !== undefined) {
            // la case est cochée -> on affiche
            $("#box-fav").show("drop", { direction: "down" }, 1000);
        }
        else {
            // la case n'est pas cochée -> on cache
            $("#box-fav").hide("drop", { direction: "down" }, 1000);
        }
    });
	
	$("#case-messages").click(function () {
        if ($('#case-messages:checked').val() !== undefined) {
            // la case est cochée -> on affiche
            $("#box-messages").show("drop", { direction: "down" }, 1000);
        }
        else {
            // la case n'est pas cochée -> on cache
            $("#box-messages").hide("drop", { direction: "down" }, 1000);
        }
    });
	
	$("#case-rss").click(function () {
        if ($('#case-rss:checked').val() !== undefined) {
            // la case est cochée -> on affiche
            $("#box-rss").show("drop", { direction: "down" }, 1000);
        }
        else {
            // la case n'est pas cochée -> on cache
            $("#box-rss").hide("drop", { direction: "down" }, 1000);
        }
    });
	
	$("#case-annonce").click(function () {
        if ($('#case-annonce:checked').val() !== undefined) {
            // la case est cochée -> on affiche
            $("#box-annonce").show("drop", { direction: "down" }, 1000);
        }
        else {
            // la case n'est pas cochée -> on cache
            $("#box-annonce").hide("drop", { direction: "down" }, 1000);
        }
    });
	
	$("#case-nav").click(function () {
        if ($('#case-nav:checked').val() !== undefined) {
            // la case est cochée -> on affiche
            $("#box-nav").show("drop", { direction: "down" }, 1000);
        }
        else {
            // la case n'est pas cochée -> on cache
            $("#box-nav").hide("drop", { direction: "down" }, 1000);
        }
    });
	
	$("#case-image").click(function () {
        if ($('#case-image:checked').val() !== undefined) {
            // la case est cochée -> on affiche
            $("#box-image").show("drop", { direction: "down" }, 1000);
        }
        else {
            // la case n'est pas cochée -> on cache
            $("#box-image").hide("drop", { direction: "down" }, 1000);
        }
    });
	
	$("#case-tag").click(function () {
        if ($('#case-tag:checked').val() !== undefined) {
            // la case est cochée -> on affiche
            $("#box-tag").show("drop", { direction: "down" }, 1000);
        }
        else {
            // la case n'est pas cochée -> on cache
            $("#box-tag").hide("drop", { direction: "down" }, 1000);
        }
    });
	
	$("#case-rainette").click(function () {
        if ($('#case-rainette:checked').val() !== undefined) {
            // la case est cochée -> on affiche
            $("#box-rainette").show("drop", { direction: "down" }, 1000);
        }
        else {
            // la case n'est pas cochée -> on cache
            $("#box-rainette").hide("drop", { direction: "down" }, 1000);
        }
    });
});