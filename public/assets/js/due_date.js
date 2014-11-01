(function($, moment) {
	$(document).ready(function() {
		var options = {
			startDate: moment(),
			singleDatePicker: true,
			format: 'YYYY-MM-DD'
		};
		$('#nervetask-new-task-due-date').daterangepicker(options);
		$('#nervetask-update-task-due-date').daterangepicker(options);
		$('.daterangepicker').css('display', 'none');
	});
})(jQuery, moment);
