$(document).ready(function() {
    $('#add-phone-submit').click(function() {
        var url = $('#add-phone-form').attr('action');
        var data = $('#add-phone-form').serializeArray();

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(data, text_status, xhr) {
                // make phone node
                var html = '';
                html += '<input type="hidden" name="phone_id[]" value="' + data.data.id + '">';
                html += data.data.type;
                html += '<hr>';

                // copy over data and display
                $('#phone-section').append(html);
                // hide modal
                $('#add-phone-modal').modal('hide');
            },
            error: function(xhr, text_status, error) {
            }
        });
    });
});
