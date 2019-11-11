require(['jquery'], function($){
    $(document).ready( function() {
        function checkRole() {
            var $checked = $('.radio').filter(':checked');
            var userRole = $checked.parent().next().text().trim();
            if (userRole != 'Call-center specialist') {
                $('.entry-edit-head').last().hide();
                $('#user_callcentre_fieldset').hide();
            } else {
                $('.entry-edit-head').last().show();
                $('#user_callcentre_fieldset').show();
            }
        }

        checkRole();

        $('.radio').change(function() {
            checkRole();
        });
    });
});
