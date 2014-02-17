(function ( $ ) {
	"use strict";

	$(function () {

		var xhr;

		$('.nervetask-new-task').submit(function (e) {

			if ( xhr ) {
				xhr.abort();
			}

			var data = '';
			data = {
				action: 'nervetask_new_task',
				form_data: $(this)
			};

			xhr = $.ajax( {
				type: 'POST',
				action: 'nervetask_new_task',
				url: nervetask.ajaxurl,
				dataType: 'json',
				data: $(this).serialize()
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
			});

			e.preventDefault();

		});

	});

}(jQuery));