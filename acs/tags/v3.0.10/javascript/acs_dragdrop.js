// Drag/drop widgets pour JQuery 1.8 (06/2010)
	var dragid;
	var dropid;
	jQuery(".widget:not(.widget_unused)").draggable({
		helper: "clone",
		delay: 100,
		distance: 10,
		ghosting: true,
		opacity:	0.8,
		start: function(event, ui) { // store dragid
			dragid = event.target.id;
			if (jQuery("#" + dragid).parent().is(".ctlWidget"))
				jQuery(this).draggable("option", "revert", false);
			else
				jQuery(this).draggable("option", "revert", "invalid");
			ui.helper.find('*').addClass("widget_move");
		},
		stop: function() { // reset select on stop dragging from its ctlWidget
			if (jQuery("#" + dragid).parent().is(".ctlWidget")) {
				var dropid = "#select_" + jQuery("#" + dragid).parent().attr("id");
				jQuery("#widgets").append(jQuery("#" + dragid));
				jQuery(dropid).val("");
			}
		}
	});

	jQuery(".ctlWidget").droppable({
		accept: ".widget",
		tolerance: "pointer",
		activeClass: "ctlWidget_droppable_active",
		hoverClass: "ctlWidget_droppable_over",
		drop: function(event) {
			dropid = "#select_" + event.target.id;
			var oldval = jQuery(dropid).val();
			var val_dragid = dragid.substring(7);
			jQuery(dropid).val(val_dragid);
			if (jQuery(dropid).val() == val_dragid) {
				if (jQuery("#" + dragid).parent().is(".ctlWidget")) {
					var olddropid = "#select_" + jQuery("#" + dragid).parent().attr("id");
					jQuery(olddropid).val("");
				}
				jQuery("#widgets").append(jQuery(this).find(".widget"));
				jQuery(this).find(".widget").remove();
				jQuery(this).append(jQuery("#" + dragid));
			}
			dragid = undefined;
			dropid = undefined;
		},
		fit: true
	});