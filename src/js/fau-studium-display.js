jQuery(document).ready(function($) {
    $('.fau-studium-display .checklist-toggle').bind('mousedown', function(event) {
        event.preventDefault();
        let $checklist = $(this).parent();
        toggleChecklist($checklist);
    });

    // Keyboard navigation for accordions
    $('.fau-studium-display .checklist-toggle').keydown(function(event) {
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

    $('.fau-studium-display .extended-search-toggle').bind('mousedown', function(event) {
        event.preventDefault();
        toggleExtendedSearch($(this));
    });
    $('.fau-studium-display .extended-search-toggle').keydown(function(event) {
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
        const { __, _x, _n, _nx } = wp.i18n;
        $this.toggleClass('active');
        const icon = $this.find('.icon-wrapper');
        icon.toggleClass('icon-plus icon-minus');
        const label = $this.find('.button-label');
        const isActive = $this.hasClass('active');
        label.text(isActive ? __('Less filter options', 'fau-studium-display') : __('More filter options', 'fau-studium-display'));
        $('div.extended-search').slideToggle();
    }

});