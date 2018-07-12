
(function($){

	$('#rp-close').on('click', function(){

		$(this).closest('.rp-first-login').hide();

		$.ajax({

			url: rpp_ajax.ajaxurl,
			method: 'POST',
			data: {
				action: 'rpp_update_display',
				security: rpp_ajax.security,
				is_hide: 1
			}

		});

	});

	$('.rp-first-login .button').on('click', function(){

		$(this).closest('.rp-first-login').hide();

		$.ajax({

			url: rpp_ajax.ajaxurl,
			method: 'POST',
			data: {
				action: 'rpp_update_display',
				security: rpp_ajax.security,
				is_hide: 1
			}

		});

	})


})(jQuery)