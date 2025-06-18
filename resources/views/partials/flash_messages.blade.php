<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to show SweetAlert
        window.showAlert = function(type, message, options = {}) {
            Swal.fire({
                icon: type,
                title: `<span class="font-body">${type.charAt(0).toUpperCase() + type.slice(1)}</span>`,
                html: `<span class="font-body text-dark text-sm">${message}</span>`,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: options.timer || 5000,
                timerProgressBar: true,
                customClass: {
                    popup: 'font-body'
                },
                ...options
            });
        };

        // Handle session-based flash messages
        @if (session('success'))
            showAlert('success', '{{ session('success') }}');
        @endif

        @if (session('error'))
            showAlert('error', '{{ session('error') }}');
        @endif

        @if (session('info'))
            showAlert('info', '{{ session('info') }}');
        @endif

        @if (session('warning'))
            showAlert('warning', '{{ session('warning') }}');
        @endif
    });
</script>
