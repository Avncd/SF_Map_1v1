(function($) {
	
	$(document).on( 'click', 'a.post-month-ajax-call', function( event ) {
		event.preventDefault();
		$('a.post-month-ajax-call').removeClass('active');
		$(this).addClass('active');
		var month = $(this).attr('data-month-id');

		// alert(month);

		$.ajax({
			url: ajaxcall.ajaxurl,
			type: 'post',
			data: {
				action : 'ajax_get_post_by_month',
				month  : month 
			},
			beforeSend: function() {
				$('#places #places-list').html("");
				$(document).scrollTop();
				$('#places #places-list').append( '<div class="post-content" id="loader">Loading...</div>' );
				$('#place').css({
					'left' : '2000px',
					'transition' : '3s'
				});
			},
			success: function( html ) {
				$('#places #places-list #loader').remove();
				$('#places #places-list').html( html );
			}
		});
        
	});


	$(document).on( 'click', '.ajax-post-call', function( event ) {
		event.preventDefault();
		$('a.lists').removeClass('active');
		$(this).find('a.lists').addClass('active');
		var post_id = $(this).attr('data-post-id');

		// alert(post_id);

		$.ajax({
			url: ajaxcall.ajaxurl,
			type: 'post',
			data: {
				action   : 'ajax_get_post_by_id',
				post_id  : post_id 
			},
			beforeSend: function() {
				$('#place #place-content').html("");
				$(document).scrollTop();
			},
			success: function( html ) {
				$('#place #place-content').html( html );
				$('#place').css({
					'left'  : '',
					'right' : '0px',
					'transition' : '1s'
				});
			}
		});
        
	});


})(jQuery);