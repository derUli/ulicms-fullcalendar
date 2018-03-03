function dragAndDropEvent(event, delta, revertFunc, jsEvent, ui, view) {
	var requestData = {
		"csrf_token" : $("input[name='csrf_token']").val(),
		"id" : event.id,
		"start" : event.start.toISOString(),
		"end" : event.end.toISOString()
	};
	$.ajax($('#calendar').data("change-event-timespan-url"), {
		method : "POST",
		data : requestData,
		success : function() {
		},
		error : function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		}
	});
}

function deleteEvent(event) {
	var requestData = {
		"csrf_token" : $("input[name='csrf_token']").val(),
		"id" : event.id,
	};
	$.ajax($('#calendar').data("delete-event-url"), {
		method : "POST",
		data : requestData,
		success : function() {
			$('#calendar').fullCalendar('refetchEvents');

		},
		error : function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
			$('#calendar').fullCalendar('refetchEvents');
		}
	});
}

function renameEvent(calEvent, jsEvent, view) {
	var title = prompt(Translation.EVENT_TITLE, calEvent.title);
	if (title) {
		var url = prompt(Translation.EVENT_URL, calEvent.url);
		if (url == "") {
			$url = null;
		}

		var requestData = {
			"csrf_token" : $("input[name='csrf_token']").val(),
			"title" : title,
			"id" : calEvent.id,
			"url" : url
		};
		$.ajax($('#calendar').data("rename-url"), {
			method : "POST",
			data : requestData,
			success : function() {
				$('#calendar').fullCalendar('refetchEvents');
			},
			error : function(jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			}
		});
	}
	$(jsEvent.target).closest("a").blur();
	return false;
}

$(function() {
	$('#calendar').fullCalendar(
			{
				events : $("#calendar").data("url"),
				locale : $("#calendar").data("locale"),

				editable : true,
				selectable : true,
				selectHelper : true,
				select : function(start, end) {
					var title = prompt(Translation.EVENT_TITLE);
					if (title) {
						var url = prompt(Translation.EVENT_URL);
						if (url == "") {
							$url = null;
						}
						if (title && title != "") {
							var requestData = {
								"csrf_token" : $("input[name='csrf_token']")
										.val(),
								"title" : title,
								"start" : start.toISOString(),
								"end" : end.toISOString(),
								"url" : url
							};
							$.ajax($('#calendar').data("add-url"),
									{
										method : "POST",
										data : requestData,
										success : function() {
											$('#calendar').fullCalendar(
													'refetchEvents');

										},
										error : function(jqXHR, textStatus,
												errorThrown) {
											alert(errorThrown);
										}
									});

							start.toISOString();
						}
					}
					$('#calendar').fullCalendar('unselect');
				},
				eventDrop : dragAndDropEvent,
				eventResize : dragAndDropEvent,
				// TODO: Show context menu to select between rename and
				// delete
				eventRender : function(event, element) {
					var event2 = event;
					element.contextmenu(function(e) {
						e.preventDefault();
						$(e.target).closest("a").blur();
						return renameEvent(event2, e);
					});
				},
				dragRevertDuration : 0,
				eventDragStop : function(event, jsEvent) {
					var trashEl = $("#delete-event");
					var ofs = trashEl.offset();

					var x1 = ofs.left;
					var x2 = ofs.left + trashEl.outerWidth(true);
					var y1 = ofs.top;
					var y2 = ofs.top + trashEl.outerHeight(true);

					if (jsEvent.pageX >= x1 && jsEvent.pageX <= x2
							&& jsEvent.pageY >= y1 && jsEvent.pageY <= y2) {
						if (confirm(Translation.ASK_FOR_DELETE)) {
							$('#calendar').fullCalendar('removeEvents',
									event.id);
							deleteEvent(event);
						}
					}
				}
			});
});