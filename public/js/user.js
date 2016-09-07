
jQuery(document).ready(function ($) {
    /* Start: Container height fix */

    setTimeout(function () {
        adjustContainerHeight();
    }, 100);
    $(window).resize(function (event) {
        adjustContainerHeight();
    });
    $('.config').click(function (event) {
        setTimeout(function () {
            adjustContainerHeight();
        }, 100);
        $(this).parents('.widget').toggleClass('u_edit');
    });
    /* Start: share popover */
    $('html').click(function () {
        setTimeout(function () {
            adjustContainerHeight();
        }, 100);
        $('.share-pop').removeClass('active');
    });
    $('.share-pop').click(function (event) {
        event.stopPropagation();
    });
    $(".sharer").click(function (event) {
        $this = $(this).find('.share-pop');
        event.preventDefault();
        event.stopPropagation();
        $('.share-pop').removeClass('active');
        $this.addClass('active');
        return false;
    });
    $('.truncate').dotdotdot({
        ellipsis: '… ',
        watch: true,
        wrap: 'letter',
        height: parseInt($('.truncate').css('line-height'), 10) * 2, // this is the number of lines
        lastCharacter: {
            remove: [' ', ',', ';', '.', '!', '?'],
            noEllipsis: []
        },
    });
    $('.truncateOff').children().on('click', function (event) {
        event.preventDefault();
        $(this).parents('.truncateOff').find('.truncate').trigger("destroy");
    });
    $('html').on('click', function (e) {
        if (typeof $(e.target).data('original-title') == 'undefined' && !$(e.target).parents().is('.popover.in')) {
            $('[data-original-title]').popover('hide');
        }
// console.log('click testing');
    });
});

/*
 * Dashboard You Tube video show 
 */
$(document).ready(function () {
    $('.videoPlay').on('click', function () {
        var org_video_id = $(this).data('id'),
                clickBtn = $(this),
                iframe = $('#video-iframe').html();
        $('.video_wrapper').html(iframe);
    });
    $('.fb-share').click(function (e) {
        var link = $(this).attr('href');

    });
    $('.shareLink').on('click', function () {
//        e.preventDefault();
        var media = $(this).data('index'),
                link = $(this).data('title');
        var share_link = '';
        if (media === 'facebook') {
            share_link = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURI(link);
        } else if (media === 'twitter') {
            share_link = "http://twitter.com/home?status=" + encodeURI(link);
        } else if (media === 'instagram') {
            share_link = " https://pinterest.com/pin/create/button/?url=" + encodeURI(link);
        } else {

        }
        window.open(share_link, 'fbShareWindow', 'height=640, width=480, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
//        return false;
    });
});
/* 
 * Sidebar scripts
 */
$(document).ready(function () {
    dotdot();
    /* ------------------ Start Left side bar code for editing ---------------------------------- */

    /*---------- Edit Following List -------------------------------- */

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
                click_btn.prev().text('Unfollowing..');
            },
            complete: function (jqXHR, textStatus) {
                click_btn.prev().text('');
            },
            success: function (data, textStatus, jqXHR) {
                if (data.status == 'OK') {
                    if (data.list != '') {
                        $('.causesList').append('<li>' +
                                '<a href="javascript:void(0)" class = "followCauses" data-id = "' + data.list.cause_id + '" >' +
                                '<i class="icon ' + data.list.icon_class + '" ></i>' + data.list.cause_name + ' </a>' +
                                '</li>');
                    }
                    click_btn.parent().parent().remove();
                    //$('#dashboardContent').load(url + ' #dashboardContent');
                    window.location.reload();
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
                setTimeout(function () {
                    $('.add-country').addClass('disabled');
                }, 1000);
            },
            complete: function (jqXHR, textStatus) {
                $('.add-country').removeClass('disabled');
            },
            success: function (objData, textStatus, jqXHR) {
                if (objData.status == 'OK') {
                    $('.followCountryWrap').removeClass('hide');
                    for (var i = 0, length = objData.data.length; i < length; i++) {
                        $('.follow-country-add').append('<li>\n\
                            <a href="' + window.location.protocol + "//" + window.location.host + '/country/' + objData.data[i].country_alias + '">' +
                                '<span class="flag-icon flag-icon-' + objData.data[i].country_iso_code.toLowerCase() + '"></span>' + objData.data[i].country_name +
                                '<span class="u_action deleteFollow" data-id="' + objData.data[i].country_id + '" data-title="country"><i class="icon pwi-icon-close"></i></span>' +
                                '</a>' +
                                '</li> ');
                    }
                    $("#search-country").val(null).trigger("change");
                    window.location.reload();

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        })
    });
    //Follow Causes List

    $('.causesList').on('click', '.followCauses', function () {
        var url = '/user/dashboard';
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
                                '<a href="/cause/' + data.causes[i].cause_alias + '">' +
                                '<i class="icon ' + data.causes[i].icon_class + '"></i> ' + data.causes[i].cause_name +
                                '<span class="u_action deleteFollow" data-id="' + data.causes[i].cause_id + '" data-title="cause"><i class="icon pwi-icon-close"></i></span>' + '</a>' +
                                '</li>');
                    }
                    followIcon.parent().remove();
                    window.location.reload();
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
        $('.new-shiping-address,.ship-add-delete').slideToggle();
    });
    $('.ship-account-pref').on('click', function () {
        var address = $('.ship-address').val(),
                city = $('.ship-city').val(),
                state = $('.ship-state').val(),
                zipcode = $('.ship-zipcode').val();
        if (address === '') {
            $('.ship-error').text('Fillup Address').css('color', 'red');
            return false;
        } else if (city === '') {
            $('.ship-error').text('Fillup City').css('color', 'red');
            return false;
        } else if (state === '') {
            $('.ship-error').text('Fillup State').css('color', 'red');
            return false;
        } else if (zipcode === '') {
            $('.ship-error').text('Fillup Zipcode').css('color', 'red');
        }
        $.ajax({
            url: '/user/ship-account-preff',
            type: 'POST',
            data: {
                address: address,
                city: city,
                state: state,
                zipcode: zipcode,
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
                            obj_data.state + " " + obj_data.zipcode +
                            '<a href="javascript:void(0)" class="ship-add-delete" data-index="' + obj_data.insert_id + '">Delete</a>' +
                            '</div>'
                            );
                    $('.ship-address,.ship-city,.ship-state,.ship-zipcode').val('');
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
        $('.new-billing-pref,.bill-add-delete').slideToggle();
    });
    $('.bill-account-pref').on('click', function () {
        var billing_address = $('.billing-address').val(),
                billing_city = $('.billing-city').val(),
                billing_state = $('.billing-state').val(),
                billing_zipcode = $('.billing-zipcode').val();
        if (billing_address === '') {
            $('.bill-error').text('Fillup billing address.').css('color', 'red');
            return false;
        } else if (billing_city === '') {
            $('.bill-error').text('Fillup billing city.').css('color', 'red');
            return false;
        } else if (billing_state === '') {
            $('.bill-error').text('Fillup billing state.').css('color', 'red');
            return false;
        } else if (billing_zipcode === '') {
            $('.bill-error').text('Fillup biling zipcode.').css('color', 'red');
        } else {

        }
        $.ajax({
            url: '/user/bill-account-pref',
            type: 'POST',
            data: {
                address: billing_address,
                city: billing_city,
                state: billing_state,
                zipcode: billing_zipcode,
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
                            obj_data.state + " " + obj_data.zipcode +
                            '<a href="javascript:void(0)" class="bill-add-delete" data-index="' + obj_data.insert_id + '">Delete</a>' +
                            '</div>'
                            );
                    $('.billing-address,.billing-city,.billing-state,.billing_zipcode').val('');
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
                if (objData.success == 'OK') {
                    if (objData.media_name == 'Facebook') {
                        if (objData.status == 'Y') {
                            $('.fbStatus').addClass('active');
                        } else {
                            $('.fbStatus').removeClass('active');
                        }
                        $('.fbStatus').text(objData.value);
                    } else if (objData.media_name == 'Twitter') {
                        if (objData.status == 'Y') {
                            $('.twitStatus').addClass('active');
                        } else {
                            $('.twitStatus').removeClass('active');
                        }
                        $('.twitStatus').text(objData.value);
                    } else if (objData.media_name == 'Instagram') {
                        if (objData.status == 'Y') {
                            $('.insStatus').addClass('active');
                        } else {
                            $('.insStatus').removeClass('active');
                        }
                        $('.insStatus').text(objData.value);
                    } else {

                    }
                    if (objData.status == 'Y') {
                        status.parents('li').find('.social-status').addClass('active');
                        status.attr("disabled", 'disabled');
                        status.next().attr('disabled', false);
                        status.removeClass('btn-grey');
                        status.addClass('btn-blue');
                        status.next().removeClass('btn-blue');
                        status.next().addClass('btn-grey');
                        status.css("pointer-events", "none");
                        status.next().css("pointer-events", "auto");
                    } else if (objData.status == 'N') {
                        status.parents('li').find('.social-status').removeClass('active');
                        status.prev().removeClass('btn-blue');
                        status.prev().addClass('btn-grey');
                        status.removeClass('btn-grey');
                        status.addClass('btn-blue');
                        status.prev().attr("disabled", false);
                        status.attr("disabled", true);
                        status.css("pointer-events", "none");
                        status.prev().css("pointer-events", "auto");
                    } else {

                    }
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
                                    '</div>').fadeIn('slow');
                        }
//                        $('#showMore').fadeIn(2000);
                    }
                    $('input[name="more_country_news"]').val(limit_news);
                    dotdot();
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
                dotdot();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert("Error");
            }
        })
    });
});
/**
 * Order History 
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
/**
 * User Preference
 */
$(document).ready(function () {
    $('.user-set-pref').on('click', function () {
        $('.edit-settings-content').show();
        $('.user-preference-init').hide();
    });
    $('.edit-settings-content').on('click', '.cancel-btn', function () {
        $(".settings .widget").toggleClass('admin');
        $('.edit-settings-content').hide();
        $('.user-preference-init').show();
        $('.success-msg').text('').removeClass('alert alert-success');
        $('.success-msg').text('').removeClass('alert alert-danger');
        $('.user_name_confirm,.user_pass_confirm,.user_email_confirm').text('');
        $('.curr-password,.new-password,.confirm-password').val('');
    });
    $('.new-username').on('blur', function () {
        var regx = /^[A-Za-z0-9 _.-]+$/;
        var username = $('.new-username').val();
        if (username.length > 0) {
            if (!regx.test(username)) {
                $('.user_name_confirm').html('Username must be alphanumeric.<br>').css('color', 'red');
                $('.new-username').css('border', '1px solid red');
                return false;
            } else if (username.length < 6) {
                $('.user_name_confirm').html('Username at least 6 charcter.<br>').css('color', 'red');
                $('.unew-username').css('border', '1px solid red');
                return false;
            } else {
                $('.new-username').css('border', '1px solid #ccc');
                $('.user_name_confirm').html('').css('color', '');
                $.ajax({
                    url: '/user/check_username',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        username: username,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function (xhr) {

                    },
                    complete: function (jqXHR, textStatus) {

                    },
                    success: function (data, textStatus, jqXHR) {

                        if (data.status == 'FAILED') {
                            $('.new-username').css('border', '1px solid red');
                            $('.username_message').text(data.msg).css('color', 'red');
                            $('.save-user-pref').prop('disabled', true);
                            $('.save-user-pref').css("pointer-events", "none");
                        } else {
                            $('.new-username').css('border', '1px solid #ccc');
                            $('.username_message').text('').css('color', 'none');
                            $('.save-user-pref').prop('disabled', false);
                            $('.save-user-pref').css("pointer-events", "auto");
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                    }
                });
            }
        }
    });
    $('.confirm-username').on('keyup', function () {
        var regx = /^[A-Za-z0-9 _.-]+$/;
        var confirm_username = $(this).val(),
                new_username = $('.new-username').val();
        if (confirm_username.length > 6 && regx.test(confirm_username)) {
            if (confirm_username === new_username) {
                $('.user_name_confirm').html('').css('color', '');
                $('.new-username').css('border', '1px solid #ccc');
                return true;
            } else {
                $('.user_name_confirm').html('Email does not match<br>').css('color', 'red');
                $('.new-username').css('border', '1px solid red');
                return false;
            }
        }
    })
    $('.new-email').on('keyup', function () {
        var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
        var email = $(this).val();
        if (pattern.test(email)) {
            $.ajax({
                url: '/user/check_email',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    email: email,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function (xhr) {

                },
                complete: function (jqXHR, textStatus) {

                },
                success: function (data, textStatus, jqXHR) {
                    if (data.status == 'FAILED') {
                        $('.new-email').css('border', '1px solid red');
                        $('.email_message').text(data.msg).css('color', 'red');
                        $('.save-user-pref').prop('disabled', true);
                        $('.save-user-pref').css("pointer-events", "none");
                    } else {
                        $('.new-email').css('border', '1px solid #ccc');
                        $('.email_message').text('').css('color', 'none');
                        $('.save-user-pref').prop('disabled', false);
                        $('.save-user-pref').css("pointer-events", "auto");
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {

                }

            });
        }
    });
    $('.confirm-email').on('keyup', function () {
        var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i,
                confirm_email = $(this).val(),
                new_email = $('.new-email').val();
        if (pattern.test(confirm_email)) {
            if (confirm_email === new_email) {
                $('.user_email_confirm').text('').css('color', 'none');
                $('.new-email').css('border', '1px solid #ccc');
                return true;
            } else {
                $('.user_email_confirm').html('Email does not match<br>').css('color', 'red');
                $('.new-email').css('border', '1px solid red');
                return false;
            }
        }

    });
    $('.new-password').on('blur', function () {
        var password = $(this).val();
        if (password.length > 0) {
            if (password.length < 6) {
                $('.user_pass_confirm').text('Password at least 6 character.').css('color', 'red');
                $('.new-password').css('border', '1px solid red');
                return false;

            } else {
                $('.user_pass_confirm').text('').css('color', 'red');
                $('.new-password').css('border', '1px solid #ccc');
                return true;
            }
        }
    });
    $('.confirm-password').on('keyup', function () {
        var confirm_pass = $(this).val();
        if (confirm_pass.length >= 6) {
            if (confirm_pass === $('.new-password').val()) {
                $('.user_pass_confirm').text('').css('color', 'red');
                $('.new-password').css('border', '1px solid #ccc');
                return true;
            } else {
                $('.user_pass_confirm').text('Password does not match.').css('color', 'red');
                $('.new-password').css('border', '1px solid red');
                return false;
            }
        }
    });
    $('.user_firstname').on('keyup', function () {
        if ($(this).val().length > 0) {
            $('.user_firstname_msg').removeClass('text-danger').show().html('');
            $('.user_firstname').css('border', '1px solid #ccc');
            return true;
        }
    });
    $('.user_lastname').on('keyup', function () {
        if ($(this).val().length > 0) {
            $('.user_lastname_msg').removeClass('text-danger').show().html('');
            $('.user_lastname').css('border', '1px solid #ccc');
            return true;
        }
    });
    $('.user-gender').on('change', function () {
        if ($(this).val() !== '') {
            $('.user_gender_msg').removeClass('text-danger').show().html('');
            $('.user-gender').css('border', '1px solid #ccc');
            return true;
        } else {
            $('.user_gender_msg').addClass('text-danger').show().html('<i class="fa fa-exclamation-triangle"></i> Gender field is required.<br>');
            $('.user-gender').css('border', '1px solid #a94442');
            return false;
        }
    });
    $('.save-user-pref').on('click', function () {
        $('.success-msg').text('').removeClass('alert alert-danger');
        $('.success-msg').text('').removeClass('alert alert-success');
        var regx = /^[A-Za-z0-9 _.-]+$/;
        var base_url = $('.base_url').val(),
                first_name = $('.user_firstname').val(),
                last_name = $('.user_lastname').val(),
                curr_username = $('.curr-username').val(),
                new_username = $('.new-username').val(),
                conf_username = $('.confirm-username').val(),
                curr_password = $('.curr-password').val(),
                new_password = $('.new-password').val(),
                conf_password = $('.confirm-password').val(),
                curr_email = $('.curr-email').val(),
                new_email = $('.new-email').val(),
                conf_email = $('.confirm-email').val(),
                user_bio = $('.user-bio-info').val(),
                user_gender = $('.user-gender').val(),
                date_of_birth = $('.birthDay').val();

        if (date_of_birth === '') {
            $('.user_dob_msg').addClass('text-danger').show().html('<i class="fa fa-exclamation-triangle"></i> Date of birth field is required.<br>');
            $('.birthdayPicker').css('border', '1px solid #a94442');
            alert("Error");
            return false;
        }

        if (user_gender === '') {
            $('.user_gender_msg').addClass('text-danger').show().html('<i class="fa fa-exclamation-triangle"></i> Gender field is required.<br>');
            $('.user-gender').css('border', '1px solid #a94442');
            return false;
        }
        if (first_name === '') {
            $('.user_firstname_msg').addClass('text-danger').show().html('<i class="fa fa-exclamation-triangle"></i> First name field is required.<br>');
            $('.user_firstname').css('border', '1px solid #a94442');
            return false;
        }
        if (last_name === '') {
            $('.user_lastname_msg').addClass('text-danger').show().html('<i class="fa fa-exclamation-triangle"></i> Last name field is required.<br>');
            $('.user_lastname').css('border', '1px solid #a94442');
            return false;
        }
        if (new_username != '') {
            if (new_username.length < 6) {
                $('.user_name_confirm').html('Username must be 6 charcter.<br>').css('color', 'red');
                return false;
            } else if (!regx.test(new_username)) {
                $('.user_name_confirm').html('Username must be aphanumeric.<br>').css('color', 'red');
                return false;
            } else if (new_username != conf_username) {
                $('.user_name_confirm').html('Username does not match.<br>').css('color', 'red');
                return false;
            } else {

            }
        }
        if (new_password != '') {
            if (new_password != conf_password) {
                $('.user_pass_confirm').html('Password does not match.<br>').css('color', 'red');
                $('.new-password').css('border', '1px solid red');
                return false;
            }
        }

        if (new_email != '') {
            if (new_email != conf_email) {
                $('.user_email_confirm').html('Email does not match.<br>').css('color', 'red');
                return false;
            }
        }

        $.ajax({
            url: base_url + '/user/settings',
            type: 'post',
            data: {
                first_name: first_name,
                last_name: last_name,
                user_gender: user_gender,
                date_of_birth: date_of_birth,
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
                $('.save-user-pref').prop('disabled', true);
                setTimeout(function () {
                    $('.save-user-pref').prop('disabled', false);
                }, 3000);
                $('.success-msg').removeClass('text-danger').addClass('text-success').show().html('<i class="fa fa-spinner fa-spin"></i> Saving. Please wait...');
            },
            complete: function (jqXHR, textStatus) {
//                $('.save-user-pref').text('Save Settings');
            },
            success: function (data, textStatus, jqXHR) {
                var objData = jQuery.parseJSON(JSON.stringify(data));
                if (objData.status == 'success') {
                    $('.success-msg').removeClass('text-danger').addClass('text-success').show().html('<i class="fa fa-check-square fa-fw"></i>' + objData.msg);
                    $('.new-password').val('');
                    $('.confirm-password').val('');
                    $('.new-username,.confirm-username').val('');
                    $('.curr-username').val(new_username == "" ? curr_username : new_username);
                    $('.new-email,.confirm-email').val('');
                    $('.curr-email').val(new_email == "" ? curr_email : new_email);
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                } else {
                    $('.success-msg').removeClass('text-success').addClass('text-danger').show().html('<i class="fa fa-exclamation-triangle"></i> Request failed! ' + data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error Occured');
            }
        });
    });
    function readURL(input) {

    }
    $('.profileImage').on('click', function () {
        $('.upload').click();
    });

    $("#imgInp").on('change', function () {
        var file_data = this.files[0];
        var form_data = new FormData();
        form_data.append('file', file_data);
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
                $('.upload').addClass('disabled').css('pointer-events', 'none');
                setTimeout(function () {
                    $('.upload').removeClass('disabled').css('pointer-events', 'auto');
                }, 3000);
                $('.image-msg').removeClass('text-danger').addClass('text-success').show().html('<i class="fa fa-spinner fa-spin"></i> Uploading. Please wait...');
            },
            complete: function (jqXHR, textStatus) {
                $('.upload-text').text('Upload');
            },
            success: function (data, textStatus, jqXHR) {
                var objData = jQuery.parseJSON(JSON.stringify(data));
                if (objData.status == 'OK') {
                    $('.image-msg').addClass('text-success').show().html('<i class="fa fa-check-square fa-fw" > </i>' + objData.msg);
                    $('.user-image').attr('src', objData.url);
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                } else {
                    $('.image-msg').html(objData.msg).addClass('text-danger');
                }
            },
            error: function () {
                $('.image-msg').text('Upload failed! Try again!').addClass('text-danger');
            }
        });
    });
});
//# sourceMappingURL=user.js.map
