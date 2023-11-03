jQuery(document).ready(function ($) {
    var current_page = 1;
    $('#load_more').on('click', function () {
        current_page++;
        
        $.ajax({
            type: 'POST',
            url: almfw_data.ajax_url,
            data: {
                action: 'almfw_load_more',
                paged: current_page,
                post_type: $('#load_more').data('post'),
                post_per_page: $('#load_more').data('per-page')
            },
            success: function(res){
                $('.almfw_post_wrapper').append(res);
            }
        })
    });
});