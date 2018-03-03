$(function() {
	$('#calendar').fullCalendar({
		events : $("#calendar").data("url"),
		locale : $("#calendar").data("locale")
	});
});