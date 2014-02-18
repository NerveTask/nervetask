(function ( $ ) {
	"use strict";

	$(function () {

		$('.nervetask-new-task').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-new-task' );
		});

		$('.nervetask-update-assignees').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-assignees');
		});

		$('.nervetask-update-status').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-status' );
		});

		$('.nervetask-update-priority').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-priority' );
		});

		$('.nervetask-update-category').submit(function (e) {
			nervetaskAjax(this, e, 'nervetask-update-category' );
		});

		function nervetaskAjax(form, e, triggerType) {

			var xhr;

			if ( xhr ) {
				xhr.abort();
			}

			xhr = $.ajax( {
				type: 'POST',
				url: nervetask.ajaxurl,
				dataType: 'json',
				data: $(form).serialize()
			})
			.done(function( response ) {
				if ( response ) {
					try {


					} catch ( err ) {

					}
				}
			})
			.fail(function( response ) {

			})
			.always(function( response ) {

				$.event.trigger({
					type: triggerType,
					message: response,
					time: new Date()
				});

				console.log(response);

			});

			e.preventDefault();

		}

	});

}(jQuery));