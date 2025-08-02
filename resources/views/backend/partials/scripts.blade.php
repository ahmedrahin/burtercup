<!-- Javascript -->
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>

<script src="{{ asset('assets/js/switcher.js') }}"></script>
<script src="{{ asset('assets/js/theme-settings.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.18/dist/sweetalert2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/js/dropify.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr(".date", {
        dateFormat: "Y-m-d",
        allowInput: true
    });

    flatpickr("#expire_date", {
        enableTime: true,
        dateFormat: "Y-m-d h:i K",
        time_24hr: false,
        allowInput: true,
    });
</script>

<script>
   $('.popup-gallery').magnificPopup({
        delegate: 'a.popup-image',
        type: 'image',
        closeOnContentClick: true,
        closeBtnInside: false,
        mainClass: 'mfp-img-mobile',
        image: {
            verticalFit: true
        },
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1]
        }
    });

    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select Parent Category",
            allowClear: true,
            width: '100%'
        });
    });
</script>

{{-- messages --}}
<script src="{{asset('assets/js/toastify.js')}}"></script>
@if (session('success') || session('error') || session('info') || session('warning'))
<script>

    document.addEventListener("DOMContentLoaded", function () {
        let message = "{{ session('success') ?? session('error') ?? session('info') ?? session('warning') }}";
        let type = "{{ session('success') ? 'success' : (session('error') ? 'error' : (session('info') ? 'info' : 'warning')) }}";

        let iconHtml = "";
        switch (type) {
            case "success":
                iconHtml = '<i class="bi bi-check-circle-fill" style="color: #28a745; font-size: 18px;"></i>';
                break;
            case "error":
                iconHtml = '<i class="bi bi-x-circle-fill" style="color: #e74c3c; font-size: 18px;"></i>';
                break;
            case "info":
                iconHtml = '<i class="bi bi-info-circle-fill" style="color: #3498db; font-size: 18px;"></i>';
                break;
            case "warning":
                iconHtml = '<i class="bi bi-exclamation-circle-fill" style="color: #f39c12; font-size: 18px;"></i>';
                break;
            default:
                iconHtml = "";
        }

        Toastify({
            text: `<span>
                    <span style="margin-right: 6px; font-size: 18px;">${iconHtml}</span>
                    <span>${message}</span>
                </span>`,
            duration: 3000,
            gravity: "bottom",
            position: "left",
            close: true,
            escapeMarkup: false,
            style: {
                background: "#fff",
                color: "#000",
                boxShadow: "0px 4px 10px rgba(0,0,0,0.1)",
                borderRadius: "8px",
                padding: "20px 15px",
                fontSize: "16px",
                fontWeight: '600',
                border: "2px solid #ddd",
                marginBottom: "5px",
                bottom: "100px",
                marginBottom: "30px",
            }
        }).showToast();
    });
</script>
@endif

{{-- ajax messages --}}
<script>
    function ajaxMessage(message, type){
        let iconHtml = "";
        switch (type) {
            case "success":
                iconHtml = '<i class="bi bi-check-circle-fill" style="color: #28a745; font-size: 18px;"></i>';
                break;
            case "error":
                iconHtml = '<i class="bi bi-x-circle-fill" style="color: #e74c3c; font-size: 18px;"></i>';
                break;
            case "info":
                iconHtml = '<i class="bi bi-info-circle-fill" style="color: #3498db; font-size: 18px;"></i>';
                break;
            case "warning":
                iconHtml = '<i class="bi bi-exclamation-circle-fill" style="color: #f39c12; font-size: 18px;"></i>';
                break;
            default:
                iconHtml = "";
        }

        Toastify({
            text: `<span>
                    <span style="margin-right: 6px; font-size: 18px;">${iconHtml}</span>
                    <span>${message}</span>
                </span>`,
            duration: 3000,
            gravity: "bottom",
            position: "left",
            close: true,
            escapeMarkup: false,
            style: {
                background: "#fff",
                color: "#000",
                boxShadow: "0px 4px 10px rgba(0,0,0,0.1)",
                borderRadius: "8px",
                padding: "20px 15px",
                fontSize: "16px",
                fontWeight: '600',
                border: "2px solid #ddd",
                marginBottom: "5px",
                bottom: "100px",
                marginBottom: "30px",
            }
        }).showToast();
    }
</script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>


@stack('scripts')
