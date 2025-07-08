jQuery(document).ready(function($) {
    $('#mein-button').on('click', function(e) {
        e.preventDefault();
        let selectedFaculties = $('input[name="faculty[]"]:checked').map(function() {
            return $(this).val();
        }).get();
        let selectedDegrees = $('input[name="degree[]"]:checked').map(function() {
            return $(this).val();
        }).get();
        let language = $('input[name="language"]:checked').val();

        $.ajax({
            url: program_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'program_search',
                _ajax_nonce: program_ajax.nonce,
                faculties: selectedFaculties,
                degrees: selectedDegrees,
                language: language,
            },
            success: function(response) {
                if (response.success) {
                    console.log(response.data.message);
                } else {
                    alert('Fehler: ' + response.data);
                }
            },
            error: function() {
                alert('AJAX-Fehler');
            }
        });
    });
});