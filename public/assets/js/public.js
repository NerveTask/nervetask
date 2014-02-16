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
				beforeSend: function() {
					console.log( data );
				},
				data: $(this).serialize()
			})
			.done(function( response ) {
				if ( response ) {
					try {

						nervetask_updates( 'Success!', response );

					} catch ( err ) {
						nervetask_updates( 'Error', err );
					}
				}
			})
			.fail(function( response ) {
				console.log( response );

				nervetask_updates( 'fail', response );
			})
			.always(function( response ) {
				console.log( 'complete' );
			});

			e.preventDefault();

		});

		$('#nervetask-updates').on('hidden.bs.modal', function (e) {

			var title = '';
			var body = '';

			$('#nervetask-updates .modal-title').text( title );
			$('#nervetask-updates .modal-body').html( body );

		})

	});

	function nervetask_updates( status, response ) {

		var modal = $('#nervetask-updates');
		var title = '';
		var body = '';

		$(modal).modal('show');

		title += status;
		body += '<h2><a href="'+ response.post.guid +'">'+ response.post.post_title +'</a></h2>';

		$('#nervetask-updates .modal-title').text( title );
		$('#nervetask-updates .modal-body').html( body );
	}

}(jQuery));