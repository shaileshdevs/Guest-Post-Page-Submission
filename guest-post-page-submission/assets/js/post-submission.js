jQuery( document ).ready( function($) {
    // When the post form is submitted.
    $( '#gpps-post-form-submit' ).click(function(e) {
        e.preventDefault();

        // Make post title field mandatory.
        if ( 0 === $( '#gpps-post-title' ).val().length ) {
            // If post title field is empty.
            $( '.invalid-feedback-wrapper, .valid-feedback-wrapper' ).hide();
            $( '.invalid-feedback-wrapper .invalid-feedback' ).text( gpps_post_sub_data.post_title_required_error );
            $( '.invalid-feedback-wrapper' ).show();
            return;
        }

        var formData = new FormData();
        formData.append( 'action', 'post_form_submission' );
        formData.append( 'gpps-submit-post-form', $( '#gpps-submit-post-form' ).val());
        formData.append( 'gpps-post-title', $( '#gpps-post-title' ).val());
        formData.append( 'gpps-post-description', $( '#gpps-post-description' ).val());
        formData.append( 'gpps-post-excerpt', $( '#gpps-post-excerpt' ).val());
        formData.append( 'file', $( '#gpps-post-featured-image' )[0].files[0]);

        $.ajax({
            type : 'POST',
            dataType : 'json',
            url : gpps_post_sub_data.ajaxurl,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $( '#gpps-post-form-submit' ).prop( 'disabled', true );
                $( '.invalid-feedback-wrapper, .valid-feedback-wrapper' ).hide();
            },
            data : formData,
            success: function(response) {
                if ( 'failure' == response.status ) {
                    $( '.invalid-feedback-wrapper .invalid-feedback' ).text( response.message );
                    $( '.invalid-feedback-wrapper' ).show();
                } else {
                    $( '.valid-feedback-wrapper .valid-feedback' ).text( response.message );
                    $( '.valid-feedback-wrapper' ).show();
                }
            },
            complete: function() {
                $( '#gpps-post-form-submit' ).prop( 'disabled', false );
            }
        });  
    });
});