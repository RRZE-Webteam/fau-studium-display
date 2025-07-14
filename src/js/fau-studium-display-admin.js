jQuery(document).ready(function($) {
    $('#degree-search-button').on('click', function(e) {
        e.preventDefault();

        let $this = $(this);
        $this.append('<span class="dashicons dashicons-update search-spinner spin" title="Searching">Searching</span>').addClass('button-disabled');
        $('#degree-program-results').empty();

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
                    $('div#degree-program-results').html(response.data.message);
                    $this.removeClass('button-disabled');
                    $('span.search-spinner').remove();
                } else {
                    console.log('Fehler: ' + response.data);
                }
            },
            error: function() {
                console.log('AJAX-Fehler');
            }
        });
    });

    $('#degree-program-results').on('click', 'a.add-degree-program', function(e) {
        e.preventDefault();

        let $this = $(this);
        let task = $this.data('task');
        $this.addClass('button-disabled');
        if (task === 'add') {
            $this.find('span.dashicons-plus').removeClass('dashicons-plus').addClass('dashicons-update');
        }
        $this.find('span.dashicons-update').addClass('spin');
        $.ajax({
            url: program_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'program_sync',
                _ajax_nonce: program_ajax.nonce,
                program_id: $this.data('id'),
                post_id: $this.data('post_id'),
            },
            success: function(response) {
                console.log(response);
                if (response.success) {
                    $this.find('span.dashicons-update').removeClass('spin');
                    if (task === 'add') {
                        $this.find('span.dashicons-update').removeClass('dashicons-update').addClass('dashicons-plus');
                    }
                    $this.after('<span class="dashicons dashicons-yes sync-ok" title="Sync OK">Sync OK</span>');
                } else {
                    console.log('Fehler: ' + response.data);
                }
            },
            error: function() {
                console.log('AJAX-Fehler');
            }
        });
    })

    $('#degree-program-results').on('click', 'a.delete-degree-program', function(e) {
        e.preventDefault();
        let $this = $(this);
        let task = $this.data('task');
        $this.addClass('button-disabled');
        $this.parent().find('a.add-degree-program button').remove();
        $this.find('span.dashicons-trash').removeClass('dashicons-trash').addClass('dashicons-update').addClass('spin');
console.log($this.data());
        $.ajax({
            url: program_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'program_delete',
                _ajax_nonce: program_ajax.nonce,
                post_id: $this.data('post_id'),
            },
            success: function(response) {
                if (response.success) {
                    $this.find('span.dashicons-update').removeClass('spin');
                    $this.after('<span class="dashicons dashicons-yes sync-ok" title="Deleted">Deleted</span>');
                } else {
                    console.log('Fehler: ' + response.data);
                }
            },
            error: function() {
                console.log('AJAX-Fehler');
            }
        });
    });
});