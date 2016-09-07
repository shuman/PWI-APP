/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    $('.oder-history-details,.oder-history-message').hide();
    $('.order-details').on('click', function () {
        $('.order-details').addClass('btn btn-success');
        var oder_details_id = $(this).data('index'),
                $this = $(this);


        $.ajax({
            url: '/user/order',
            type: 'POST',
            data: {
                oder_detail_id: oder_details_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (xhr) {

            },
            complete: function (jqXHR, textStatus) {
                $this.removeClass('btn btn-success');
                $this.addClass('btn btn-primary');
            },
            success: function (data, textStatus, jqXHR) {
                $('.oder-history-message').hide();
                $('.oder-history-details').show();
                $('.show-order-details').html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
    });
    $('.show-order-details').on('click', '.contact-message', function () {
        //alert("clicked");
        $('.contact-message').prop('disabled', true);
        $('.oder-history-message').show();
        $('.first-name-err,.last-name-err,.order-ref-err,.order-msg-err').text('');
        $('.success-message').text('').removeClass('alert alert-success');
    });
    $('.order-msg-cancel').on('click', function () {
        $('.oder-history-message').hide();
        $('.contact-message').prop('disabled', false);
        $('.success-message').text('').removeClass('alert alert-success');
        $('.first-name-err,.last-name-err,.order-ref-err,.order-msg-err').text('');

        $('.first-name-err,.last-name-err,.order-ref-err,.order-msg-err').text('');
    });

    $('.send-msg').on('click', function () {
        $('.success-message').text('').removeClass('alert alert-success');
        var first_name = $('.first-name').val(),
                last_name = $('.last-name').val(),
                order_ref = $('.order-ref').val(),
                order_msg = $('.order-msg').val(),
                base_url = $('.base-url').val();
        if (first_name == '') {
            $('.first-name-err').text('Required To Fill First Name').css('color', 'red');
            return false;
        } else {
            $('.first-name-err').text('');
        }
        if (last_name == '') {
            $('.last-name-err').text('Required To Fill Last Name').css('color', 'red');
            return false;
        } else {
            $('.last-name-err').text('');
        }
        if (order_ref == '') {
            $('.order-ref-err').text('Required To Select Order Reference').css('color', 'red');
            return false;
        } else {
            $('.order-ref-err').text('');
        }
        if (order_msg == '') {
            $('.order-msg-err').text('Required To Fill Message').css('color', 'red');
            return false;
        } else {
            $('.order-msg-err').text('');
        }
        $.ajax({
            url: base_url + '/user/ordermessage',
            type: 'post',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                first_name: first_name,
                last_name: last_name,
                order_ref: order_ref,
                order_msg: order_msg
            },
            beforeSend: function (xhr) {
                $('.send-msg').text('sending..');
            },
            complete: function (jqXHR, textStatus) {
                $('.send-msg').text('Send Message');
            },
            success: function (data, textStatus, jqXHR) {
                var obj_data = JSON.parse(JSON.stringify(data));
                if (obj_data.status === 'success') {
                    $('.success-message').addClass('alert alert-success').text(data.msg);
                    $('#my_message_form').each(function () {
                        this.reset();
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        })
    });
});
