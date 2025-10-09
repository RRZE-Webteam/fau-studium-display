//const { __, _x, _n, sprintf } = wp.i18n;

jQuery(document).ready(function($) {
    $('.checklist-toggle').bind('mousedown', function(event) {
        event.preventDefault();
        let $checklist = $(this).parent();
        toggleChecklist($checklist);
    });

    // Keyboard navigation for accordions
    $('.checklist-toggle').keydown(function(event) {
        if (event.keyCode == 32 || event.keyCode == 13) {
            event.preventDefault();
            let $checklist = $(this).parent();
            toggleChecklist($checklist);
        }
    });

    function toggleChecklist($checklist) {
        $($checklist).children('.checklist-toggle').toggleClass('active');
        $($checklist).children('.checklist').slideToggle();
        $($checklist).children().find('.dashicons.dashicons-arrow-down-alt2').toggleClass('dashicons-arrow-up-alt2');
    }

    $('.extended-search-toggle').bind('mousedown', function(event) {
        event.preventDefault();
        toggleNextDiv($(this));
    });
    $('.extended-search-toggle').keydown(function(event) {
        if (event.keyCode == 32 || event.keyCode == 13) {
            event.preventDefault();
            ltoggleNextDiv($(this));
        }
    });

    function toggleNextDiv($this) {
        $this.toggleClass('active');
        $this.parent().next('div').slideToggle();
        $this.find('.dashicons.dashicons-arrow-down-alt2').toggleClass('dashicons-arrow-up-alt2');
    }

});