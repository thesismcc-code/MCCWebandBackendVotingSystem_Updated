/**
 * CSRF Token Handler
 * Automatically refreshes CSRF token and handles 419 errors
 */

// Add CSRF token to all AJAX requests
if (typeof $ !== 'undefined') {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
}

// Handle 419 errors globally
document.addEventListener('DOMContentLoaded', function() {
    // Intercept form submissions
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            // Check if CSRF token exists
            const csrfInput = form.querySelector('input[name="_token"]');
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            
            if (csrfInput && metaToken) {
                // Update form token with latest meta token
                csrfInput.value = metaToken.getAttribute('content');
            }
        });
    });

    // Refresh CSRF token every 5 minutes
    setInterval(function() {
        fetch('/refresh-csrf', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.token) {
                // Update meta tag
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', data.token);
                }
                
                // Update all form tokens
                document.querySelectorAll('input[name="_token"]').forEach(function(input) {
                    input.value = data.token;
                });
                
                console.log('CSRF token refreshed');
            }
        })
        .catch(error => {
            console.warn('Failed to refresh CSRF token:', error);
        });
    }, 5 * 60 * 1000); // 5 minutes
});
