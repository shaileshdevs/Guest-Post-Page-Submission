jQuery( document ).ready( function($) {
    // Initialize datatable.
    $( '#gpps-post-pending-list-table' ).DataTable({
        responsive: true,
        order: [[0, 'desc']],
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': ['gpps-post-edit-link-head', 'gpps-post-approve-button'],
        }],
    });

    // When the post is approved by the admin.
    $( '.gpps-approve-post' ).click(function(e) {
        let formData = new FormData();
        let $this    = $( this );
        formData.append( 'action', 'gpps_post_approved' );
        formData.append( 'gpps-post-approve-nonce', $( '#gpps-post-approve-nonce' ).val());
        formData.append( 'gpps-post-id', $this.data( 'post-id' ) );

        $.ajax({
            type : "POST",
            dataType : "json",
            processData: false,
            contentType: false,
            url : gpps_post_pending_list.ajaxurl,
            beforeSend: function () {
                $( ".gpps-approve-post" ).prop( "disabled", true );
            },
            data : formData,
            success: function(response) {
                if ( 'failure' == response.status ) {
                    alert(response.message);
                } else {
                    $this.prop( 'value', response.message );
                    $this.prop( 'disabled', true ).removeClass('gpps-approve-post').addClass('gpps-approved-post');
                }
            },
            complete: function() {
                $( ".gpps-approve-post" ).prop( "disabled", false );
            }
        });  
    });
});