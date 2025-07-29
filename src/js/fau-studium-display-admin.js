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
        //let language = $('input[name="language"]:checked').val();

        $.ajax({
            url: program_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'program_search',
                _ajax_nonce: program_ajax.nonce,
                faculties: selectedFaculties,
                degrees: selectedDegrees,
                //language: language,
            },
            success: function(response) {
                if (response.success) {
                    $('div#degree-program-results').html(response.data.message);
                } else {
                    $this.removeClass('button-disabled');
                }
                $this.removeClass('button-disabled');
                $('span.search-spinner').remove();
            },
            error: function(response) {
                $this.removeClass('button-disabled');
                $('span.search-spinner').remove();
                //console.log('AJAX-Fehler');
                //console.log(response);
            }
        });
    });

    $('#degree-program-results').on('click', 'a.add-degree-program', function(e) {
        e.preventDefault();
        sync_degree_program($(this));
    })

    $('#degree-programs-imported').on('click', 'a.update-degree-program', function(e) {
        e.preventDefault();
        sync_degree_program($(this));
    })

    $('#degree-programs-imported').on('click', 'a.delete-degree-program', function(e) {
        e.preventDefault();
        let $this = $(this);
        let task = $this.data('task');
        $this.addClass('button-disabled');
        $this.parent().find('a.update-degree-program').remove();
        $this.find('span.dashicons-trash').removeClass('dashicons-trash').addClass('dashicons-update').addClass('spin');
//console.log($this.data());
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

    function sync_degree_program($button) {
        let task = $button.data('task');
        $button.addClass('button-disabled');

        if (task === 'add') {
            $button.find('span.dashicons-plus')
                .removeClass('dashicons-plus')
                .addClass('dashicons-update');
        }

        $button.find('span.dashicons-update').addClass('spin');

        $.ajax({
            url: program_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'program_sync',
                _ajax_nonce: program_ajax.nonce,
                program_id: $button.data('id'),
                post_id: $button.data('post_id'),
            },
            success: function(response) {
                $button.find('span.dashicons-update').removeClass('spin');
                if (task === 'add') {
                    $button.find('span.dashicons-update')
                        .removeClass('dashicons-update')
                        .addClass('dashicons-plus');
                }

                if (response.success) {
                    $button.after('<span class="dashicons dashicons-yes sync-ok" title="Sync OK">Sync OK</span>');
                } else {
                    $button.after('<span class="dashicons dashicons-no sync-error" title="Sync Error">Sync Error</span>');
                    console.log('Fehler: ' + response.data);
                }
            },
            error: function() {
                $button.find('span.dashicons-update').removeClass('spin');
                if (task === 'add') {
                    $button.find('span.dashicons-update')
                        .removeClass('dashicons-update')
                        .addClass('dashicons-plus');
                }
                console.log('AJAX-Fehler');
            }
        });
    }

});