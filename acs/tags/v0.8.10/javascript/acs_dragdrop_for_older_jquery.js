// Drag/drop widgets
	var dragid;
	var dfx = 500;
	$(".widget").Draggable({
		helper: "clone",
		delay: 100,
		distance: 10,
		ghosting: true,
		opacity:	0.8,
		revert: true,
		fx: dfx,
		onStart: function(drag) { // store dragid
			dragid = "#" + $(drag).attr("id");
		},
		onStop: function() { // reset select on stop dragging from its ctlWidget
			if ($(dragid).parent().is(".ctlWidget")) {
				var dropid = "#select_" + $(dragid).parent().attr("id");
				$("#widgets").append($(dragid));
				$(dropid).val("");
				this.dragCfg.fx = dfx; // restore desired revert effect
			}
		}
	});

	$(".ctlWidget").Droppable({
		accept: "widget",
		tolerance: "touch",
		activeclass: "ctlWidget_droppable_active",
		hoverclass: "ctlWidget_droppable_over",
		onHover: function(drag) {
			var dropid = "#select_" + $(this).attr("id");
			var val = $(dropid).val();
			$(dropid).val(drag.id);
			if ($(dropid).val() == drag.id) {
				drag.dragCfg.fx = 0; // Avoid unwanted revert effects
			}
			else
				drag.dragCfg.fx = dfx;
			$(dropid).val(val);
		},
		onDrop: function(drag) {
			var dropid = "#select_" + $(this).attr("id");
			var oldval = $(dropid).val();
			$(dropid).val(drag.id);
			if ($(dropid).val() == drag.id) {
			if ($(dragid).parent().is(".ctlWidget")) {
				var olddropid = "#select_" + $(dragid).parent().attr("id");
				$(olddropid).val("");
			}						
				$(this).find(".widget").fx = dfx; // restore desired revert effect
				$("#widgets").append($(this).find(".widget"));
				$(this).find(".widget").remove();
				$(this).append(drag);
			}
			dragid = false;
		},
		fit: true
	});