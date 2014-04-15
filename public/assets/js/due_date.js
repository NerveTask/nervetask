(function($, moment) {
	$(document).ready(function() {
		var options = {
			startDate: moment(),
			singleDatePicker: true,
			timePicker: true,
			format: 'MM/DD/YYYY h:mm A'
		};
		$('#nervetask-new-task-due-date').daterangepicker(options);
		$('#nervetask-update-task-due-date').daterangepicker(options);
		$('.daterangepicker').css('display', 'none');
	});
})(jQuery, moment);