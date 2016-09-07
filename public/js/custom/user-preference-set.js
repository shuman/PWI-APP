/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    $('.user-set-pref').on('click', function () {
        $('.edit-settings-content').show();
        $('.user-preference-init').hide();
    });
    $('.edit-settings-content').on('click', '.cancel-btn', function () {
        $('.edit-settings-content').hide();
        $('.user-preference-init').show();
        $('.success-msg').text('').removeClass('alert alert-success');
        $('.success-msg').text('').removeClass('alert alert-danger');

        $('.user_name_confirm,.user_pass_confirm,.user_email_confirm').text('');
        $('.curr-password,.new-password,.confirm-password').val('');
    });

    $('.save-user-pref').on('click', function () {
        $('.success-msg').text('').removeClass('alert alert-danger');
        $('.success-msg').text('').removeClass('alert alert-success');
        var base_url = $('.base_url').val(),
                curr_username = $('.curr-username').val(),
                new_username = $('.new-username').val(),
                conf_username = $('.confirm-username').val(),
                curr_password = $('.curr-password').val(),
                new_password = $('.new-password').val(),
                conf_password = $('.confirm-password').val(),
                curr_email = $('.curr-email').val(),
                new_email = $('.new-email').val(),
                conf_email = $('.confirm-email').val(),
                user_bio = $('.user-bio-info').val();

        if (new_username != '') {
            if (new_username != conf_username) {
                $('.user_name_confirm').text('Not Match Confirmed Username').css('color', 'red');
                return false;
            }
        }

        if (new_password != '') {
            if (new_password != conf_password) {
                $('.user_pass_confirm').text('Not Match Confirmed Password').css('color', 'red');
                return false;
            }
        }

        if (new_email != '') {
            if (new_email != conf_email) {
                $('.user_email_confirm').text('Not Match Confirmed Email').css('color', 'red');
                return false;
            }
        }

        $.ajax({
            url: base_url + '/user/settings',
            type: 'post',
            data: {
                curr_username: curr_username,
                new_username: new_username,
                curr_password: curr_password,
                new_password: new_password,
                curr_email: curr_email,
                new_email: new_email,
                user_bio: user_bio,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (xhr) {
                $('.save-user-pref').text('Sending...');
            },
            complete: function (jqXHR, textStatus) {
                $('.save-user-pref').text('Save Settings');
            },
            success: function (data, textStatus, jqXHR) {
                var objData = jQuery.parseJSON(JSON.stringify(data));
                if (objData.status == 'success') {
                    $('.success-msg').text(objData.msg).addClass('alert alert-success');
                    $('.curr-password').val('');
                    $('.new-password').val('');
                    $('.confirm-password').val('');
                } else {
                    $('.success-msg').text(objData.msg).addClass('alert alert-danger');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error Occured');
            }
        });
    });

    function readURL(input) {

    }

    $("#imgInp").on('change', function () {
        //
        var file_data = this.files[0];
        var form_data = new FormData();
        form_data.append('file', file_data);

        /*
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.user-image').attr('src', e.target.result);
            }
            reader.readAsDataURL((this).files[0]);
        }
        */
        
        $('.image-msg').html('').removeClass('text-danger', 'text-success');

        $.ajax({
            url: '/user/profile-picture',
            type: 'post',
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function (xhr) {
                $('.upload-text').text('Upload...');
            },
            complete: function (jqXHR, textStatus) {
                $('.upload-text').text('Upload');
            },
            success: function (data, textStatus, jqXHR) {
                var objData = jQuery.parseJSON(JSON.stringify(data));
                if (objData.status == 'OK') {
                    $('.image-msg').html(objData.msg).addClass('text-success');
                    $('.user-image').attr('src', objData.url);
                } else {
                    $('.image-msg').html(objData.msg).addClass('text-danger');
                }
            },
            error: function(){
                $('.image-msg').text('Upload failed! Try again!').addClass('text-danger');
            }
        });

    });
});
