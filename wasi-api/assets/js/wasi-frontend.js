jQuery(document).ready(function($) {
    // Paginación AJAX
    $(document).on('click', '.wasi-load-more', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var container = button.closest('.wasi-properties-container');
        var page = parseInt(container.data('page')) || 1;
        var args = container.data('args');
        
        button.addClass('loading').text(wasi_vars.loading_text);
        
        $.ajax({
            url: wasi_vars.ajaxurl,
            type: 'POST',
            data: {
                action: 'wasi_load_more',
                page: page + 1,
                args: args,
                security: wasi_vars.ajax_nonce
            },
            success: function(response) {
                if (response.success) {
                    container.find('.wasi-properties-' + args.view_type).append(response.data.html);
                    container.data('page', page + 1);
                    
                    if (response.data.is_last_page) {
                        button.remove();
                    } else {
                        button.removeClass('loading').text(wasi_vars.load_more_text);
                    }
                } else {
                    button.replaceWith('<div class="wasi-notice">' + response.data.message + '</div>');
                }
            },
            error: function() {
                button.removeClass('loading').text(wasi_vars.load_more_text);
                alert(wasi_vars.error_text);
            }
        });
    });
    
    // Filtros AJAX
    $(document).on('click', '.wasi-apply-filters', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var container = button.closest('.wasi-properties-container');
        var args = container.data('args');
        var filters = {};
        
        container.find('.wasi-filter').each(function() {
            var filterType = $(this).data('filter');
            
            switch(filterType) {
                case 'search':
                    filters.search = $(this).find('input').val();
                    break;
                    
                case 'price':
                    filters.price = {
                        min: $(this).find('.wasi-price-min').val(),
                        max: $(this).find('.wasi-price-max').val()
                    };
                    break;
                    
                case 'type':
                    filters.type = $(this).find('select').val();
                    break;
                    
                case 'bedrooms':
                    filters.bedrooms = $(this).find('select').val();
                    break;
            }
        });
        
        button.addClass('loading').text(wasi_vars.loading_text);
        
        $.ajax({
            url: wasi_vars.ajaxurl,
            type: 'POST',
            data: {
                action: 'wasi_filter_properties',
                filters: filters,
                args: args,
                security: wasi_vars.ajax_nonce
            },
            success: function(response) {
                if (response.success) {
                    container.find('.wasi-properties-' + args.view_type).html(response.data.html);
                    container.data('page', 1);
                } else {
                    container.find('.wasi-properties-' + args.view_type).html('<div class="wasi-error">' + response.data.message + '</div>');
                }
                
                button.removeClass('loading').text(wasi_vars.apply_filters_text);
            },
            error: function() {
                button.removeClass('loading').text(wasi_vars.apply_filters_text);
                alert(wasi_vars.error_text);
            }
        });
    });
    
    // Inicializar slider si existe
    if ($('.wasi-properties-slider').length) {
        $('.wasi-properties-slider').slick({
            dots: true,
            arrows: true,
            infinite: false,
            speed: 300,
            slidesToShow: 3,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    }
});