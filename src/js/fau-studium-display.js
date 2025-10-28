const { __, _x, _n, sprintf } = wp.i18n;

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
        toggleExtendedSearch($(this));
    });
    $('.extended-search-toggle').keydown(function(event) {
        if (event.keyCode == 32 || event.keyCode == 13) {
            event.preventDefault();
            toggleExtendedSearch($(this));
        }
    });

    function toggleNextDiv($this) {
        $this.toggleClass('active');
        $this.parent().next('div').slideToggle();
        $this.find('.dashicons.dashicons-arrow-down-alt2').toggleClass('dashicons-arrow-up-alt2');
    }

    function toggleExtendedSearch($this) {
        $this.toggleClass('active');
        const icon = $this.find('.icon-wrapper');
        icon.toggleClass('icon-plus icon-minus');
        const label = $this.find('.button-label');
        const isActive = $this.hasClass('active');
        label.text(isActive ? __('Less filter options', 'fau-studium-display') : __('More filter options', 'fau-studium-display'));
        $('div.extended-search').slideToggle();
    }

});