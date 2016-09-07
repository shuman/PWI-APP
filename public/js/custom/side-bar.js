/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    /* ------------------ Start Left side bar code for editing ---------------------------------- */

    /*---------- Edit Following List -------------------------------- */

    $('.enableRemoveBtn').on('click', function () {
        $('.deleteFollow').slideToggle();
    })

    $('.deleteFollow').on('click', function (event) {
        event.preventDefault();
        var click_btn = $(this),
                remove_from_follow = $(this).data("title"),
                follow_type_id = $(this).data("id");
        $.ajax({
            url: '/user/remove-follow',
            type: 'POST',
            dataType: 'JSON',
            data: {
                remove_from_follow: remove_from_follow,
                follow_type_id: follow_type_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (xhr) {
                click_btn.prev().text("Processing......");
            },
            complete: function (jqXHR, textStatus) {

            },
            success: function (data, textStatus, jqXHR) {
                if (data.status == 'OK') {
                    console.log(data.list)
                    if (data.list != '') {
                        $('.causesList').append('<li>' +
                                '<a href="javascript:void(0)" class = "followCauses" data-id = "' + data.list.cause_id + '" >' +
                                '<i class="icon ' + data.list.icon_class + '" ></i>' + data.list.cause_name + ' </a>' +
                                '</li>');

                    }
                    click_btn.parent().remove();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert("Error Occured");
            }

        });
        return false;
    });
    $('.search-country').select2({
        placeholder: "Seach For a Country",
        maximumSelectionLength: 1
    });
    // FOllow Country List 

    $('.add-country').on('click', function () {
        var country = $('select[name^="follow_country"]').val();
        $.ajax({
            url: '/user/follow-country',
            type: 'POST',
            dataType: 'json',
            data: {
                country: country,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (xhr) {
                $('.add-country').addClass('disabled');
            },
            complete: function (jqXHR, textStatus) {
                $('.add-country').removeClass('disabled');
            },
            success: function (objData, textStatus, jqXHR) {
                if (objData.status == 'OK') {
                    $('.followCountryWrap').removeClass('hide');
                    for (var i = 0, length = objData.data.length; i < length; i++) {
                        $('.follow-country-add').append('<li><a href="' + window.location.protocol + "//" + window.location.host + '/country/' + objData.data[i].country_alias + '"><span class="flag-icon flag-icon-' + objData.data[i].country_iso_code.toLowerCase() + '"></span>' + objData.data[i].country_name + '</a></li> ');
                    }
                    $("#search-country").val(null).trigger("change");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        })
    });
    //Follow Causes List

    $('.causesList').on('click', '.followCauses', function () {
        var causes_id = $(this).data('id'),
                followIcon = $(this);
        $.ajax({
            url: '/user/follow-causes',
            type: 'POST',
            dataType: 'JSON',
            data: {
                causes_id: causes_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (xhr) {

                followIcon.text('Following.....');
            },
            complete: function (jqXHR, textStatus) {
                followIcon.text('');
            },
            success: function (data, textStatus, jqXHR) {
                $('.followCausesWrp').removeClass('hide');
                if (data.status = 'OK') {
                    for (var i = 0; i < data.causes.length; i++) {
                        $('.followedCauses').append('<li>' +
                                '<a href="' + data.causes[i].cause_alias + '">' +
                                '<i class="icon ' + data.causes[i].icon_class + '"></i> ' + data.causes[i].cause_name +
                                '<span class="deleteFollow" data-id="' + data.causes[i].cause_id + '" data-title="cause"><i class="icon pwi-icon-close"></i></span>' + '</a>' +
                                '</li>');
                    }
                    followIcon.parent().remove();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
    });
    /* End Follow Causes*/

    // View All My Reviews 
    $('.myReviewsHide').hide();
    $('.showAllReviews').on('click', function () {
        $('.myReviewsHide').slideToggle();
    });
    // View All Crowdfunding 
    $('.hideCrowd').hide();
    $('.showAllCrowd').on('click', function () {
        $('.hideCrowd').slideToggle();
    });
    $('.ship-add-delete').hide();
    $('.shiping-add-enable').on('click', function () {
        $('.ship-delete-msg').text('').removeClass('text-danger');
        $('.ship-error').text('');
        $('.ship-error').text('');
        $('.new-shiping-address').toggle();
        $('.ship-add-delete').toggle();
    });
    $('.ship-account-pref').on('click', function () {

        var address = $('.ship-address').val(),
                city = $('.ship-city').val(),
                state_zipcode = $('.ship-state-zipcode').val();
        if (address == '') {
            $('.ship-error').text('Fillup Address').css('color', 'red');
            return false;
        } else if (city == '') {
            $('.ship-error').text('Fillup City').css('color', 'red');
            return false;
        } else if (state_zipcode == '') {
            $('.ship-error').text('Fillup State Zipcode').css('color', 'red');
            return false;
        } else {

        }
        $.ajax({
            url: '/user/ship-account-preff',
            type: 'POST',
            data: {
                address: address,
                city: city,
                state_zipcode: state_zipcode,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (xhr) {
                $('.ship-account-pref').text('Saving...');
                $('.ship-account-pref').prop('disabled', true);
            },
            complete: function (jqXHR, textStatus) {
                $('.ship-account-pref').text('Save Settings');
                $('.ship-account-pref').prop('disabled', false);
            },
            success: function (data, textStatus, jqXHR) {
                var obj_data = jQuery.parseJSON(JSON.stringify(data));
                $('.shipping').append('');
                if (obj_data.status === 'success') {
                    $('.ship-delete-msg').text('').removeClass('text-danger');
                    $('.ship-error').text('Successfully Added').css('color', 'green');
                    $('.shipping_address_wrap').append(
                            '<div class="shipping_address">' +
                            obj_data.first_name + ' ' + obj_data.last_name + '<br>' +
                            obj_data.address + ',<br>' +
                            obj_data.city + ',<br>' +
                            obj_data.state_zipcode +
                            '<a href="javascript:void(0)" class="ship-add-delete" data-index="' + obj_data.insert_id + '">Delete</a>' +
                            '</div>'
                            );
                    $('.ship-address,.ship-city,.ship-state-zipcode').val('');
                } else {
                    alert('error');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Please wait some times');
            }
        });
    });
    // delete shipping address 

    $('.shipping_address_wrap').on('click', '.ship-add-delete', function () {
        $('.ship-error').text('').css('color', 'green');
        var user_add_id = $(this).data('index'),
                deletebtn = $(this);
        $.ajax({
            url: '/user/shipaddress-delete',
            type: 'post',
            data: {
                user_add_id: user_add_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (xhr) {
                deletebtn.text('deleting..');
            },
            complete: function (jqXHR, textStatus) {
                deletebtn.text('Delete');
            },
            success: function (data, textStatus, jqXHR) {
                var obj_data = jQuery.parseJSON(JSON.stringify(data));
                if (obj_data.status == 'success') {
                    $('.ship-delete-msg').text(obj_data.msg).addClass('text-danger');
                    deletebtn.parent().remove();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert("Opps ! Something wrong with you.");
            }
        });
    });
    /*$('.ship-add-delete').hide();*/
    $('.bill-add-delete').hide();
    $('.billing-pref-enable').on('click', function () {
        $('.bill-delete-msg').text('').css('text-danger');
        $('.bill-error').text('');
        $('.new-billing-pref').toggle();
        $('.bill-add-delete').toggle();
    });
    $('.bill-account-pref').on('click', function () {
        var billing_address = $('.billing-address').val(),
                billing_city = $('.billing-city').val(),
                billing_state_zipcode = $('.billing-state-zipcode').val();
        if (billing_address == '') {
            $('.bill-error').text('Fill Up Address').css('color', 'red');
            return false;
        } else if (billing_city == '') {
            $('.bill-error').text('Fill Up City').css('color', 'red');
            return false;
        } else if (billing_state_zipcode == '') {
            $('.bill-error').text('Fill Up State Zip Code').css('color', 'red');
            return false;
        } else {

        }
        $.ajax({
            url: '/user/bill-account-pref',
            type: 'POST',
            data: {
                address: billing_address,
                city: billing_city,
                state_zipcode: billing_state_zipcode,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (xhr) {
                $('.bill-account-pref').text('Saving...')
                $('.bill-account-pref').prop('disabled', true);
            },
            complete: function (jqXHR, textStatus) {
                $('.bill-account-pref').text('Save Settings')
                $('.bill-account-pref').prop('disabled', false);
            },
            success: function (data, textStatus, jqXHR) {
                var obj_data = jQuery.parseJSON(JSON.stringify(data));
                $('.billing').append('');
                if (obj_data.status === 'success') {
                    $('.bill-delete-msg').text('').css('text-danger');
                    $('.bill-error').text('Successfully Added').css('color', 'green');
                    $('.billing_address_wrap').append(
                            '<div class="billing_address">' + obj_data.first_name + ' ' + obj_data.last_name + '<br>' +
                            obj_data.address + ',<br>' +
                            obj_data.city + ',<br>' +
                            obj_data.state_zipcode +
                            '<a href="javascript:void(0)" class="bill-add-delete" data-index="' + obj_data.insert_id + '">Delete</a>' +
                            '</div>'
                            );
                    $('.billing-address,.billing-city,.billing-state-zipcode').val('');
                } else {

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Please Wait for some time.');
            }
        });
    });
    $('.billing_address_wrap').on('click', '.bill-add-delete', function () {
        $('.bill-error').text('').css('color', 'green');
        var delete_btn = $(this),
                user_add_id = $(this).data('index');
        $.ajax({
            url: '/user/billadd-delete',
            type: 'post',
            data: {
                user_add_id: user_add_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (xhr) {
                delete_btn.text('deleting..');
            },
            complete: function (jqXHR, textStatus) {
                delete_btn.text('Delete');
            },
            success: function (data, textStatus, jqXHR) {
                var obj_data = jQuery.parseJSON(JSON.stringify(data));
                if (obj_data.status == 'success') {
                    $('.bill-delete-msg').text(obj_data.msg).addClass('text-danger');
                    delete_btn.parent().remove();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
    });
    /* ---------------------- Start Right Side bar code for editing ------------------------------ */

    // $('.donation-details').hide();
    $('.see-details').on('click', function (e) {
        e.preventDefault();
        $(this).next('.details').slideToggle();
        return false;
    });
    /* Start Social Media  Edit enable/Disabled */

    $('.social-edit').hide();
    $('.social-media-edit').on('click', function () {
        $('.social-media').hide();
        $('.social-edit').show();
    });
    $('.close-edit').on('click', function () {
        $('.social-edit').hide();
        $('.social-media').show();
    });
    /* Right side bar user social media update */
    $('.social-update').on('click', function () {
        var social_name = $(this).data('title'),
                social_status = $(this).data('index'),
                social_media_id = $(this).data('id'),
                status = $(this);
        $.ajax({
            url: '/user/social',
            type: 'POST',
            dataType: 'json',
            data: {
                social_name: social_name,
                social_status: social_status,
                social_media_id: social_media_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (xhr) {
                status.parent().prev().find('.status').text('Saving..').css('color', 'green');
                status.prop('disabled', true);
            },
            complete: function (jqXHR, textStatus) {
                status.prop('disabled', false);
            },
            success: function (objData, textStatus, jqXHR) {
                status.parent().prev().find('.status').text(objData.value).css('color', '');
               
                if (objData.status == 'Y') {
                    status.parents('li').find('.social-status').addClass('active');
                    status.removeClass('btn-blue');
                    status.addClass('btn-grey');
                    status.next().removeClass('btn-grey');
                    status.next().addClass('btn-blue');
                    status.addClass('.social-disable');
                    status.next().removeClass('.social-disable');
                } else {
                    status.parents('li').find('.social-status').removeClass('active');
                    status.prev().removeClass('btn-grey');
                    status.prev().addClass('btn-blue');
                    status.removeClass('btn-blue');
                    status.addClass('btn-grey');
                    status.addClass('.social-disable');
                    status.prev().removeClass('.social-disable');

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Please wait for some times');
            }

        });
    });
    /* End Social Media Update */

    /* 
     * Start News Letter Update code
     */
    $('.news-letter-disable').hide();
    $('.edit-newsletter').on('click', function () {
        $('.news-letter').hide();
        $('.news-letter-disable').show();
        $('.msg').text('');
    });
    $('.close-edit-newsletter').on('click', function () {
        $('.news-letter-disable').hide();
        $('.news-letter').show();
        $('.msg').text('');
    });
    $('.news-update-status').on('click', function () {
        $('.msg').text('');
        var active_btn = $(this).parent().find('.btn-green');
        active_btn.removeClass('btn-green');
        active_btn.addClass('btn-grey');
        $(this).removeClass('btn-grey');
        $(this).addClass('btn-green');
    });
    $('.news-leteer-update').on('click', function () {
        var btn_active_type = $(this).parent().find('.btn-green').data('title');
        $.ajax({
            url: '/user/news-letter-update',
            type: 'POST',
            data: {
                update_type: btn_active_type,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (xhr) {
                $('.news-leteer-update').text('Saving ....');
            },
            complete: function (jqXHR, textStatus) {
                $('.news-leteer-update').text('Save Settings');
            },
            success: function (data, textStatus, jqXHR) {
                var objData = jQuery.parseJSON(JSON.stringify(data));
                if (objData.status == 'Success') {
                    $('.msg').text(objData.msg).addClass('text-center text-success');
                    $('.news-update-type').text(objData.update_type);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }

        })
    });
    /*
     * Get More News for Country 
     * 
     * 
     */
    $('.moreCountryNews').on('click', function () {

        var last_news_id = parseInt($('input[name="more_country_news"]').val()),
                limit_news = last_news_id + 3,
                seeMore = $(this);
        $.ajax({
            url: '/user/more-country-news',
            type: 'post',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (xhr) {
                seeMore.text('');
                seeMore.text('Loading.....').css('color', 'red');
            },
            complete: function (jqXHR, textStatus) {
                seeMore.text('');
                seeMore.text('See More').css('color', '');
            },
            success: function (data, textStatus, jqXHR) {
                var objData = jQuery.parseJSON(JSON.stringify(data)),
                        news = objData.news;
                if (objData.status === 'OK') {
                    var length = news.length;
                    for (var i = last_news_id + 1; i < length; i++) {
                        if (i <= limit_news) {
                            $('#showMore').prepend(
                                    '<div class = "news-feed">' +
                                    '<img src="' + news[i]['image'] + '" alt = "' + news[i]['image'] + '" class="propic-sm">' +
                                    '<h2 class = "news-title newsTitle" data-index="' + i + '"> ' + news[i]['title'] + ' </h2>' +
                                    '<div class = "news-meta">' +
                                    '<span class="source">' + news[i]['source'] + '</span> <span class="timeago">' + news[i]['date'] + '</span>' +
                                    '</div>' +
                                    '<div class="news-desc"> <p> ' + news[i]['text'] + ' </p><a class="read-more" href="' + news[i]['link'] + '">Read More</a></div>' +
                                    '</div>')
                        }
                    }
                    ;
                    $('input[name="more_country_news"]').val(limit_news);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        })

    });
    $('.moreCauses').on('click', function () {
        var last_news_id = parseInt($('input[name="newslimit"]').val()),
                limit_news = (last_news_id) + 3,
                seeMore = $(this);
        $.ajax({
            url: '/user/more-causes-news',
            type: 'post',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (xhr) {
                seeMore.text('Loading...').css('color', 'red');
            },
            complete: function (jqXHR, textStatus) {
                seeMore.text('See More').css('color', '');
            },
            success: function (data, textStatus, jqXHR) {
                var objData = jQuery.parseJSON(JSON.stringify(data)),
                        news = objData.news;
                if (objData.status === 'OK') {
                    var length = news.length;
                    for (var i = last_news_id + 1; i < length; i++) {
                        if (i <= limit_news) {
                            $('#showCausesMore').prepend('<div class = "news-feed">' +
                                    '<img src = "' + news[i]['image'] + '" alt = "' + news[i]['image'] + '" class = "propic-sm">' +
                                    '<h2 class = "news-title causeNewsTitle" data-index = "' + i + '" >' + news[i]['title'] + ' </h2>' +
                                    '<div class = "news-meta"><span class = "source">' + news[i]['source'] + '</span> <span class="timeago">' + news[i]['date'] + '</span> </div>' +
                                    '<div class = "news-desc truncate"> <p>' + news[i]['text'] + '</p><a class="read-more" href="">Read More</a> </div>' +
                                    '</div>'
                                    );
                        }
                    }
                }
                $('.causes_news_last_id').val(limit_news);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert("Error");
            }
        })
    });
});
