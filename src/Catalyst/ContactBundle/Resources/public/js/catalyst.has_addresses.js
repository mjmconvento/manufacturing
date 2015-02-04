$(document).ready(function() {
    $('#add-address-submit').click(function() {
        var url = $('#add-address-form').attr('action');
        var data = $('#add-address-form').serializeArray();

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(data, text_status, xhr) {
                // make address node
                var html = '';
                html += '<input type="hidden" name="address_id[]" value="' + data.data.id + '">';
                html += data.data.name;
                html += '<hr>';

                // copy over data and display
                $('#address-section').append(html);
                // hide modal
                $('#add-address-modal').modal('hide');
            },
            error: function(xhr, text_status, error) {
            }
        });
    });
});
