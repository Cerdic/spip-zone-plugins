// Drag/drop widgets
// Pour JQuery 1.8 (06/2010)
	var dragid;
	var dropid;
	$(".widget:not(.widget_unused)").draggable({
		helper: "clone",
		delay: 100,
		distance: 10,
		ghosting: true,
		opacity:	0.8,
		start: function(event, ui) { // store dragid
			dragid = event.target.id;
			if ($("#" + dragid).parent().is(".ctlWidget"))
				$(this).draggable("option", "revert", false);
			else
				$(this).draggable("option", "revert", "invalid");
			ui.helper.find('*').addClass("widget_move");
		},
		stop: function() { // reset select on stop dragging from its ctlWidget
			if ($("#" + dragid).parent().is(".ctlWidget")) {
				var dropid = "#select_" + $("#" + dragid).parent().attr("id");
				$("#widgets").append($("#" + dragid));
				$(dropid).val("");
			}
		}
	});

	$(".ctlWidget").droppable({
		accept: ".widget",
		tolerance: "pointer",
		activeClass: "ctlWidget_droppable_active",
		hoverClass: "ctlWidget_droppable_over",
		drop: function(event) {
			dropid = "#select_" + event.target.id;
			var oldval = $(dropid).val();
			var val_dragid = dragid.substring(7);
			$(dropid).val(val_dragid);
			if ($(dropid).val() == val_dragid) {
				if ($("#" + dragid).parent().is(".ctlWidget")) {
					var olddropid = "#select_" + $("#" + dragid).parent().attr("id");
					$(olddropid).val("");
				}
				$("#widgets").append($(this).find(".widget"));
				$(this).find(".widget").remove();
				$(this).append($("#" + dragid));
			}
			dragid = undefined;
			dropid = undefined;
		},
		fit: true
	});