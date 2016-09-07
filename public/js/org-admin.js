
$(function( ){

    /**
    General Information Modal JS Events
    **/

    /** Set up text editor options **/
    var jqteOptions = {
        format: false,
        sub:    false,
        sup:    false,
        ol:     false,
        ul:     false,
        source: false,
        link:   false,
        class: "jqteOverRide", 
    };

    var userId = "";

    /** Check if userId is populated.  If so, populate Above variable **/
    if( $("input[name=userId]").length > 0 ){
        userId = $("input[name=userId]").val( );
    }

    orgId = $("input[name=orgId]").val( );

    /** Set up text editor for Brief Description **/
    //$("textarea[name=brief-description]").jqte(jqteOptions);

    /** Set up text editor for Mission Statement **/
    //$("textarea[name=mission-statement]").jqte(jqteOptions);

    /** Set up text editor for About Us **/
    //$("textarea[name=about-us]").jqte(jqteOptions);    

    /** Set up text editor for Incentive Description **/
    $("textarea[name=incentive-description]").jqte(jqteOptions);    

    /** Set up text editor for Incentive Updated **/
    $("textarea[name=incentive-update]").jqte(jqteOptions);

    /** Set up text editor for Project Story **/
    $("textarea[name=project_story]").jqte(jqteOptions);

    /** Set up cause description **/
    $("textarea[name=cause-description-textarea]").jqte(jqteOptions);    

    /** Set up crowdfunding description **/
    $("textarea[name=update-desc]").jqte(jqteOptions);    

    /** Event for Saving General Info **/
    $(".save-general-info").on("click", function( ){

        var $button = $(this);
        
        var missionStatement    = $("textarea[name=mission-statement]").val( )
        
        var aboutUs             = $("textarea[name=about-us]").val( );

        var briefDescription    = $("textarea[name=brief-description").val( );

        var orgName             = $("input[name=org-name").val( );

        $.ajax({
            url: "/organization/updateGeneralInfo",
            method: "POST",
            data: encodeURI( "missionStatement=" + missionStatement + "&aboutUs=" + aboutUs + "&briefDescription=" + briefDescription + "&orgName=" + orgName + "&orgId=" + orgId),
            dataType: "json",
            beforeSend: function( ){
                $button.html("Updating Information...");
                $(".upload-generalInfo-error").find(".error-list").html("");
            },
            success: function( resp ){

                $button.html("Save Changes");

                if( resp.status ){
                    $(".upload-generalInfo-success").fadeIn( );

                    if( $(".about-us").length > 0 ){
                        $(".org-header").find('.header-content').find('.org-name').html( orgName );
                        $(".about-us").find(".mission-content").html( missionStatement.replace(/(<([^>]+)>)/ig,"") );
                        $(".about-us").find(".aboutUs-content").html( aboutUs.replace(/(<([^>]+)>)/ig,"") );    
                    }

                    setTimeout( function( ){
                        $(".upload-generalInfo-success").fadeOut( );
                        $("#generalInfoModal").modal('hide');
                    }, 5000);

                }else{

                    $(".upload-generalInfo-error").show( );

                    setTimeout( function( ){
                        $(".upload-generalInfo-error").fadeOut( );
                    }, 5000);
                }
            }
        });
    });

    $("#generalInfoModal").on("hide.bs.modal", function( ){

        $(".upload-generalInfo-success").hide( );
        $(".upload-generalInfo-error").find(".error-list").html( "" );
        $(".upload-generalInfo-error").hide( );
        
        if( $(".about-us").length == 0 ){
            location.reload( );
        }
    });


    /**
    Cause Modal
    **/

    var cause = {};
    var subcauses = [];
    var countries = [];
    var xhr = null;
    var action = "";
    var causeChanges = false;

    $("input[name=country-text]").on("keyup", function( ){

        var parent = $(this).parent( );

        var inputCoords = $(this).offset( );

        var inputWidth  = $(this).width( ) + 24;

        var inputHeight = $(this).height( ) + 12;

        var tmpCountry = {};

        if( $(this).val( ).length >= 3 ){
            var query = $(this).val( );

            if( xhr ){
                if( xhr.readyState != 4 ){
                    xhr.abort( );
                }
            }

            xhr = $.ajax({
                url: "/findCountry/" + query,
                dataType: "json",
                beforeSend: function( ){
                    $("input[name=country-text]").css({
                       background: "url(/images/loading1.gif) top right no-repeat",
                       backgroundSize: "contain" 
                    });
                },
                success: function( resp ){

                    $("input[name=country-text]").css("background", "");

                    var countryList = "<div style='top: " + inputHeight + "px; width: " + inputWidth + "px;' class='country-search-container'>";

                    for( x in resp ){
                        countryList += "<div class='country-selection' data-country-id='" + resp[x].country_id + "'>" + resp[x].country_name + "</div>";
                    }
                    countryList += "</div>";

                    parent.append( countryList );
                }
            });
        }
    });

    //event for clicking on a cause (adding)
    $("input[name=cause-option]").on("change", function( ){

        var causeId = $(this).prop("id").split("-");

        causeId = causeId[2];

        console.log( $(".currentCauseList").find("div[data-org-cause=" + causeId + "]").length );

        if( $(".currentCauseList").find("div[data-org-cause=" + causeId + "]").length > 0 ){
            $(".currentCauseList").find("div[data-org-cause=" + causeId + "]").trigger("click");

        }else{
            
            cause       = {};
            subcauses   = [];
            countries   = [];
            action      = "add";

            clearSubCauses( );

            clearOtherCauses( causeId );

            clearCountries( );

            cause.cause_id = causeId;

            $(".availableSubCauseList").find("label").hide( );

            $(".availableSubCauseList").find("label[data-parent-id=" + causeId + "]").show( );

            $(".cause-action").html( "Add New Cause").addClass("btn-primary").removeClass('btn-success');

            $("textarea[name=cause-description-textarea]").jqteVal("");

            if( $(".subCauseWrapper").hasClass("hidden") ){
                $(".subCauseWrapper").removeClass( "hidden" );
            }    
        }
    });

    //event for clicking on an already selected Cause ( Updating )
    $(document).on("click", ".org-cause", function( ){
        console.log( "in org-cause call");
        cause = {};
        subcauses = [];
        countries = [];
        action = "update";

        $(".main-cause-heading").html( "Update " + $(this).find(".cause-title").html( ) );

        clearSubCauses( );

        var causeId = $(this).data("org-cause");
        var orgCauseId = $(this).data("org-cause-id");

        cause.cause_id = causeId;
        cause.org_cause_id = orgCauseId;

        clearOtherCauses( causeId );

        clearCountries( );

        $(".availableCauseList label").each( function( ){
            
            if( $(this).find("input[type=radio]").prop("id") == "cause-option-" + causeId ){

                $(this).find("input[type=radio]").prop("checked", true);

                $(this).find("input[type=radio]").parent( ).addClass('active');

                $(".availableSubCauseList").find("label[data-parent-id=" + causeId + "]").show( );

                if( $(".subCauseWrapper").hasClass("hidden") ){
                    $(".subCauseWrapper").removeClass( "hidden" );
                }

                $("textarea[name=cause-description-textarea]").jqteVal( $(".currentCauseList").find(".org-cause[data-org-cause=" + causeId + "]").find(".cause-text").html( ) );

                $(".cause-action").html( "Update Cause Data ").addClass("btn-success").removeClass('btn-primary');
            }
        });

        $(this).find(".sub-cause-list span").each( function( ){

            var subCauseId = $(this).data('subcause-id');

            $(".availableSubCauseList label").find("input[type=checkbox][id=sub-cause-option-" + subCauseId + "]").prop("checked", true);

            $(".availableSubCauseList label").find("input[type=checkbox][id=sub-cause-option-" + subCauseId + "]").parent( ).addClass('active');
        });

        $(this).find(".cause-country-list span").each( function( ){
            
            var id = $(this).data('cause-country');
            var name = $(this).html( );

            countryPill = "<div class='country-pill'><button class='btn btn-primary' type='button' data-country-id='" + id + "' data-country-name='" + name + "'>" + name + "<span class='badge'>X</span></div>";

            countries.push( {id: id, name: name});

            $(".country-list").append( countryPill ).show( ); 
        });
    });

    $(".org-cause").on({
        mouseover: function( ){
            $(this).find(".cause-remove-button").removeClass('hidden');
        },
        mouseleave: function( ){
            $(this).find(".cause-remove-button").addClass('hidden');
        }
    });

    $(".cause-remove-button").on("click", function( ){

        var removeCauseId = $(this).find("button").data("cause-id");
        var _token = $("input[name=_token]").val( );

        $.ajax({
            url: "/organization/removeCause",
            method: "POST",
            data: "causeId=" + $(this).find("button").data("cause-id") + "&orgId=" + orgId + "&_token=" + _token,
            dataType: "json",
            sucess: function( resp ){

                if( resp.status ){
                    causeChanges = true;
                    $(".org-cause[data-org-cause=" + removeCauseId + "]").remove( );

                    //clear causes
                    clearOtherCauses( 0 );
                    //clear subcauses
                    clearSubCauses( );
                    //clear countries
                    clearCountries( );
                    //clear textarea
                    $("textarea[name=cause-description-textarea]").jqteVal( "" );
                    //update page causes

                }
            }
        });
    });

    //Event for clicking a sub-cause
    $("input[name=sub-cause-option]").on("change", function( ){

        if( $(this).is(":checked") ){
            var subCauseId = $(this).prop("id").split("-")[3];

            subcauses.push( subCauseId );
        }else{
            var index = subcauses.splice( subcauses.indexOf( subCauseId ), 1);
        }
    });

    //event for clicking on a country that is in the country list
    $(document).on("click", ".country-selection", function( ){

        tmpCountry = {};

        tmpCountry = {"name": $(this).text( ), "id": $(this).data('country-id')};
        
        $(".country-search-container").remove( );

        $("input[name=country-text]").val( tmpCountry.name );
    });

    //Add the country chosen from above event
    $("#add-country").on("click", function( ){

        var countryPill = "<div class='country-pill'>";

        countries.push( tmpCountry ); 

        countryPill += "<button class='btn btn-primary' type='button' data-country-id='" + tmpCountry.id + "' data-country-name='" + tmpCountry.name + "'>" + tmpCountry.name + "<span class='badge remove-country'>X</span></div>";

        tmpCountry = {};

        $("input[name=country-text]").val( "" );

        $(".country-list").append( countryPill ).show( );    
        
    });

    $(".cause-action").on("click", function( ){

        var _token = $("input[name=_token").val( );

        cause.sub_causes = subcauses;

        cause.countries = countries;

        cause.desc = $("textarea[name=cause-description-textarea]").val( );

        cause.action = action;

        cause.orgId  = orgId;

        cause._token = _token;

        var url = "";

        if( action == "add" ){
            url = "/organization/addCause";
        }else{

            url = "/organization/updateCause";
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: cause,
            dataType: 'json',
            beforeSend: function( ){
                if( cause.action == "add" ){
                    $(".cause-action").html( "Adding Cause...");
                }else{
                    $(".cause-action").html("Updating Cause...");
                }
                
            },
            success: function( resp ){

                $(".cause-action").html("Add Cause");

                if( resp.status ){

                    causeChanges = true;

                    var subCauseList = "";
                    var countryList = "";

                    if( cause.action == "update" ){
                        //update 'selected causes'
                        $(".availableSubCauseList label").each( function( ){

                            if( $(this).find("input[type=checkbox]").is(":checked") ){

                                var subCauseId = $(this).find("input[type=checkbox]").prop("id").split("-")[3];

                                if( subCauseList == "" ){
                                    subCauseList = "<span class='org-sub-cause' data-subcause-id='" + subCauseId + "'>" + $(this).find(".cause-name").html( ) + "</span>";
                                }else{
                                    subCauseList += ", " + "<span class='org-sub-cause' data-subcause-id='" + subCauseId + "'>" + $(this).find(".cause-name").html( ) + "</span>";
                                }
                            }
                        });

                        $(".org-cause[data-org-cause=" + cause.cause_id + "]")
                        .find(".sub-cause-list")
                        .html( subCauseList );


                        $(".country-list .country-pill").each(function( ){

                            if( countryList == "" ){
                                countryList = "<span class='org-cause-country' data-cause-country='" + $(this).find("button").data("country-id") + "'>" + $(this).find("button").data("country-name") + "</span>";
                            }else{
                                countryList += "<span class='org-cause-country' data-cause-country='" + $(this).find("button").data("country-id") + "'>," + $(this).find("button").data("country-name") + "</span>";
                            }
                        });

                        $(".org-cause[data-org-cause=" + cause.cause_id + "]")
                        .find(".cause-country-list")
                        .html( countryList );

                        
                    }else{

                        var $causeEl = $(".availableCauseList label").find( "input[type=radio]:checked").parent( );

                        var causeName = $causeEl.find(".cause-name").text( );
                        var causeIcon = $causeEl
                                        .find(".cause-icon")
                                        .find("i")
                                        .prop("class")
                                        .split(" ")[0];

                        $(".availableSubCauseList label").each( function( ){

                            if( $(this).find("input[type=checkbox]").is(":checked") ){

                                var subCauseId = $(this).find("input[type=checkbox]").prop("id").split("-")[3];

                                if( subCauseList == "" ){
                                    subCauseList = "<span class='org-sub-cause' data-subcause-id='" + subCauseId + "'>" + $(this).find(".cause-name").html( ) + "</span>";
                                }else{
                                    subCauseList += ", " + "<span class='org-sub-cause' data-subcause-id='" + subCauseId + "'>" + $(this).find(".cause-name").html( ) + "</span>";
                                }
                            }
                        });

                        $(".country-list .country-pill").each(function( ){

                            if( countryList == "" ){
                                countryList = "<span class='org-cause-country' data-cause-country='" + $(this).find("button").data("country-id") + "'>" + $(this).find("button").data("country-name") + "</span>";
                            }else{
                                countryList += "<span class='org-cause-country' data-cause-country='" + $(this).find("button").data("country-id") + "'>," + $(this).find("button").data("country-name") + "</span>";
                            }
                        });

                        var newCause = "<div class='col-lg-3 col-md-3 col-sm-3 org-cause' data-org-cause='" + cause.cause_id + "' data-org-cause-id='" + resp.org_cse_id + "'>";

                        newCause += "<div class='row'>";
                        newCause += "   <div class='col-lg-3 col-md-3 col-sm-3'>";
                        newCause += "       <i class='" + causeIcon + " pwi-icon-2em'></i>";
                        newCause += "   </div>";
                        newCause += "   <div class='col-lg-9 col-lg-9 col-sm-9' >";
                        newCause += "       <div class='cause-title' >" + causeName + "</div>";
                        newCause += "       <div class='sub-cause-list'>" + subCauseList + "</div>";
                        newCause += "       <div class='cause-country-list'>" + countryList + "</div>";
                            
                                        
                        newCause += "    </div>"
                        newCause += "</div>";
                        newCause += "<div class='hidden cause-remove-button text-center row'>";
                        newCause += "   <div class='col-lg-12 col-md-12 col-sm-12'>";
                        newCause += "       <button type='button' class='btn btn-danger padding-2 margin-top-2' data-toggle='button' aria-pressed='false' autocomplete='off' data-cause-id='" + cause.cause_id + "'>Remove Cause</button>";
                        newCause += "   </div>";
                        newCause += "</div>";
                        newCause += " <div class='cause-text' id='org-cause-description'>" + cause.desc +"</div>"
                        newCause += "</div>";

                        $(".currentCauseList").append( newCause );
                    }

                    //clear causes
                    clearOtherCauses( 0 );
                    //clear subcauses
                    clearSubCauses( );
                    //clear countries
                    clearCountries( );
                    //clear textarea
                    $("textarea[name=cause-description-textarea]").jqteVal( "" );

                    cause = {};
                    subcauses = [];
                    countries = [];
                }else{

                }
            }
        });
    });

    $("#orgCauseModal").on("hidden.bs.modal", function( ){

        if( causeChanges ){
            location.reload( );
        }else{
            //clear causes
            clearOtherCauses( 0 );
            //clear subcauses
            clearSubCauses( );
            //clear countries
            clearCountries( );
            //clear textarea
            $("textarea[name=cause-description-textarea]").jqteVal( "" );

            cause       = {};   
            subcauses   = [];
            countries   = [];
        }
    });

    $(document).on("click", ".remove-country", function(){

        var countryId = $(this).parent( ).data('country-id');
        $(this).parent( ).parent( ).remove( );

        for( x in countries ){
            if( countries[x]["id"] == countryId ){
                countries.splice(x, 1);
            }
        }

        if( countries.length == 0 ){
            $(".country-list").hide( );
        }
    });

    /** End Cause Modal **/

    /** Begin Contact Info Modal **/

    $(".save-contact-info").on("click", function( ){

        var queryString = $("form[name=contact-form-update]").serialize( );

        $.ajax({
            url: "/organization/updateContactInfo",
            method: "POST",
            data: queryString,
            dataType: "json",
            beforeSend: function( ){
                $("#contactInfoModal").find(".error").hide( );
            },
            success: function( resp ){

                if( resp.status ){

                    var $form = $("form[name=contact-form-update]");

                    var orgWebUrl = $form.find("input[name=org_web_url]").val( );

                    if( orgWebUrl.length > 30 ){

                        $("#orgWebUrl").find("a").html( orgWebUrl.substring( 0, 29 ) + "...");
                    }else{
                        $("#orgWebUrl").find("a").html( orgWebUrl );
                    }

                    if( $("#orgWebUrl").parent( ).hasClass('hidden') && orgWebUrl != "" ){
                        $("#orgWebUrl").parent( ).removeClass('hidden');
                    }else if( ( ! $("#orgWebUrl").parent( ).hasClass('hidden') ) && orgWebUrl == "" )  {
                        $("#orgWebUrl").parent( ).addClass('hidden');
                    }

                    $("#orgPhone").html( $form.find("input[name=org_phone]").val( ) );

                    if( $("#orgPhone").parent( ).hasClass('hidden') && $form.find("input[name=org_phone]").val( ) != "" ){
                        $("#orgPhone").parent( ).removeClass( 'hidden' );
                    }else if( ( ! $("#orgPhone").parent( ).hasClass('hidden') ) && $form.find("input[name=org_phone]").val( ) == "" ){
                        $("#orgPhone").parent( ).addClass("hidden");
                    } 

                    $("#orgEmail").html( $form.find("input[name=org_email]").val( ) );

                    var address = $form.find("input[name=org_address1]").val( ) + "<br >";

                    if( $form.find("input[name=org_address2]").val( ) != "" ){
                        address += $form.find("input[name=org_address2]").val( ) + "<br />";
                    }

                    address += $form.find("input[name=org_city]").val( ) + ", " + resp.state + " " + $form.find("input[name=org_zip]").val( );

                    $("#orgStreetAddress").html( address );

                    $(".upload-contactInfo-success").show( );

                    setTimeout( function( ){
                        $(".upload-contactInfo-success").fadeOut( );
                    }, 5000);
                }else{

                    for( var x in resp.errors ){
                        $("#contactInfoModal").find(".error-" + x).html( resp.errors[x] ).show( );
                    }

                    $(".upload-contactInfo-error").show( );

                    setTimeout( function( ){
                        $(".upload-contactInfo-error").fadeOut( );
                    }, 5000);
                }
            }
        });
    });

    $(".contactInfoModal").on("hide.bs.modal", function( ){
        $(".upload-contactInfo-success").show( );
        $(".upload-contactInfo-error").show( );
    });

    /** End Contact Info **/
    /** Photo/Video Upload **/

    var hasChangedMedia = false;

    $(".droppable-box").on("drag dragstart dragend dragover dragenter dragleave drop", function( e ){
        e.preventDefault( );
        e.stopPropagation( );
    })
    .on('drop', function( e ){
        var droppedFile = e.originalEvent.dataTransfer.files;

        var form_data = new FormData();
        form_data.append('file', droppedFile[0]);
        form_data.append('type', 'photo');
        form_data.append('id', orgId);
        form_data.append('userId', userId);

        var token = $(document).find("input[name=_token]").val( );

        form_data.append('_token', token);
        
        $.ajax({
            url: "/organization/uploadimage",
            method: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function( ){
                $(".upload-photo-info").fadeIn( );
                $(".upload-photo-success").hide( );
                $(".upload-photo-error").hide( );
            },
            success: function( resp ){

                hasChangedMedia = true;

                $(".upload-photo-info").fadeOut( );
                
                if( resp.status ){
                    
                    $(".upload-photo-success").fadeIn( );

                    var newElement = "<div class='box' style='background: url(" + resp.url + ") top left no-repeat; background-size: cover;'></div>";

                    $(".photo_list").find("div:first").after( newElement );

                    setTimeout( function( ){
                        $(".upload-photo-success").fadeOut( );
                    }, 3000);

                }else{
                    
                    $(".upload-photo-error").fadeIn( );

                    setTimeout( function( ){
                        $(".upload-photo-error").fadeOut( );
                    }, 3000);
                }

            }
        });
    }).on('click', function( e ){
        $("#orgPic").trigger("click");
    });

    $("#orgPic").on("change", function( ){

        var file_data = this.files[0];
        var form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('type', 'photo');
        form_data.append('id', orgId);
        form_data.append('userId', userId);
        
        var token = $(document).find("input[name=_token]").val( );

        form_data.append('_token', token);

        $.ajax({
            url: "/organization/uploadimage",
            method: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function( ){
                $(".upload-photo-info").fadeIn( );
                $(".upload-photo-success").hide( );
                $(".upload-photo-error").hide( );
            },
            success: function( resp ){

                hasChangedMedia = true;

                $(".upload-photo-info").fadeOut( );

                if( resp.status ){
                    
                    $(".upload-photo-success").fadeIn( );

                    var newElement = "<div class='box' style='background: url(" + resp.url + ") top left no-repeat; background-size: cover;'></div>";

                    $(".photo_list").find("div:first").after( newElement );

                    setTimeout( function( ){
                        $(".upload-photo-success").fadeOut( );
                    }, 3000);

                }else{
                    
                    $(".upload-photo-error").fadeIn( );

                    setTimeout( function( ){
                        $(".upload-photo-error").fadeOut( );
                    }, 3000);
                }
            },
            error: function( ){
                $(".upload-photo-error").fadeIn( );

                setTimeout( function( ){
                    $(".upload-photo-error").fadeOut( );
                }, 3000);
            }
        });
    });

    $(".save-video").on("click", function( ){

        $.ajax({
            url: "/organization/saveVideo",
            method: "POST",
            data: "videoUrl=" + $("input[name=video_url]").val( ) + "&_token=" + $(document).find("input[name=_token]").val( ) + "&orgId=" + orgId,
            dataType: "json",
            beforeSend: function( ){
                $(".upload-video-info").show( );
            },
            success: function( resp ){
                
                $(".upload-video-info").hide( );

                if( resp.status ){

                    hasChangedMedia = true;

                    $(".video_list").prepend("<div class='box' style='background: url( " + resp.img + " ) top left no-repeat; background-size: cover;'></div>");

                    $(".upload-video-success").find(".msg").html(" Your video has been successfully uploaded.").fadeIn( );

                    $("input[name=video_url]").val( "" );

                    setTimeout( function( ){
                        $(".upload-video-success").fadeOut( );
                    }, 3000);

                }else{

                    $(".upload-video-error").find(".msg").append( "<br />" + resp.msg );
                    $(".upload-photo-error").fadeIn( );

                    setTimeout( function( ){
                        $(".upload-video-error").find(".msg").html( "There was an error saving your video" );
                        $(".upload-photo-error").fadeOut( );
                    }, 5000);
                }
            }
        });
    });

    $(".box").on({
        mouseenter: function( ){
            $(this).find(".overlay").show( );
        },
        mouseleave: function( ){
            $(this).find(".overlay").hide( );
        }
    });

    $(".remove-photo").on("click", function( ){

        var $parentBox = $(this).parent( ).parent( );

        var fileId = $(this).data('file-id');

        $.ajax({
            url: "/organization/removePhoto",
            method: "POST",
            data: "fileId=" + fileId + "&orgId=" + orgId + "&_token=" + $(document).find("input[name=_token]").val( ),
            dataType: "json",
            success: function( resp ){

                if( resp.status ){
                    hasChangedMedia = true;
                    $parentBox.remove( );    
                }else{
                    $(".upload-photo-error").find(".msg").html("There was an error deleting the photo.");
                    $(".upload-photo-error").fadeIn( );

                    setTimeout( function( ){
                        $(".upload-photo-error").find(".msg").html("There was an error uploading your photo.");
                        $(".upload-photo-error").fadeOut( ) ;
                    }, 3000);
                }
            }
        });
    });

    $(document).on("click", ".remove-video", function( ){

        var $parentBox = $(this).parent( ).parent( );

        var orgVideoId = $(this).data('file-id');

        $.ajax({
            url: "/organization/removeVideo",
            method: "POST",
            data: "orgVideoId=" + orgVideoId + "&_token=" + $(document).find("input[name=_token]").val( ),
            dataType: "json",
            success: function( resp ){
                if( resp.status ){
                    hasChangedMedia = true;
                    $parentBox.remove( );
                }else{
                    $(".upload-video-error").find(".msg").html("There was an error deleting the video.");
                    $(".upload-video-error").fadeIn( );

                    setTimeout( function( ){
                        $(".upload-video-error").find(".msg").html("There was an error uploading your video.");
                        $(".upload-video-error").fadeOut( ) ;
                    }, 3000);
                }
            }


        });
    });

    $("#mediaModal").on("hide.bs.modal", function( ){

        if( hasChangedMedia ){
            location.reload( );    
        }
    });

    $("#mediaModal").on("show.bs.modal", function( e ){

        var openerClasses = e.relatedTarget.classList;
        
        for( var x in openerClasses ){
            if( openerClasses[x] == "open-photos"){
                $("#mediaModal").find(".nav-tabs li:first a").trigger("click");
            }else if( openerClasses[x] == "open-videos"){
                $("#mediaModal").find(".nav-tabs li:last a").trigger("click");
            }    
        }
    });

    /** End Media Modal **/

    /** Start Crowdfunding Modal **/

    var projectObj          = {};
    var projectChosenCauses = [];
    var incentives          = [];
    var projectUploaded     = false;
    var nextProjectModal    = "";
    var updates             = [];
    var editIncentiveId     = "";
    var editUpdateId        = "";

    $("#crowdFundingModal").find(".list-group .edit-project").on("click", function( ){

        nextProjectModal = "#editCrowdFundingProjectModal_" + $(this).parent( ).data("project-id")

        $("#crowdFundingModal").modal("hide");
    });

    $("#crowdFundingModal").find(".add-new-project").on("click", function( ){

        nextProjectModal = "#addCrowdFundingProjectModal";

        $("#crowdFundingModal").modal("hide");
    });

    $("#crowdFundingModal").on("hidden.bs.modal", function( ){

        if( nextProjectModal != "" ){
            $(nextProjectModal).modal("show");        
            nextProjectModal = "";
        }
    });

    $("#crowdFundingModal").find(".remove-project").on('click', function( ){

        var projectId = $(this).parent( ).data('project-id');

        var _token = $(document).find("input[name=_token]").val( );

        $.ajax({
            url: "/organization/deleteProject",
            method: "POST",
            data: "id=" + projectId + "&orgId=" + orgId + "&_token=" + _token,
            dataType: "json",
            success: function( resp ){

                if( resp.status ){
                    projectUploaded = true;
                    $("#crowdFundingModal").find("li[data-project-id=" + projectId + "]").remove( );
                }
            }
        });
    });

    $(".edit-crowdfunding-modal").on("show.bs.modal", function( ){

        var $thisModal = $(this);

        projectObj          = {};
        projectChosenCauses = [];
        incentives          = [];
        updates             = []; 

        editIncentiveId = "";

        if( $thisModal.find(".incentive-list").children( ).length > 0 ){

            $thisModal.find(".incentive-list li").each( function( i ){
                var newIncentive = {};

                newIncentive.needShipping   = 0;
                newIncentive.id             = $(this).find("input[name=incentiveId]").val( ); 
                $(this).find("input[name=incentiveId]").remove( );
                newIncentive.name           = $(this).find("input[name=incentiveName]").val( );
                $(this).find("input[name=incentiveName]").remove( );
                newIncentive.amount         = $(this).find("input[name=incentiveAmt]").val( );
                $(this).find("input[name=incentiveAmt]").remove( );
                newIncentive.available      = $(this).find("input[name=incentiveCnt]").val( );
                $(this).find("input[name=incentiveCnt]").remove( );
                newIncentive.desc           = $(this).find(".incentive-desc").html( );
                $(this).find(".incentive-desc").remove( );

                if( $(this).find("input[name=incentiveShip]").val( ) == "Y" ){
                    newIncentive.needShipping = 1;
                }

                incentives.push( newIncentive );
            });
        }

        editUpdateId = "";

        if( $thisModal.find(".project-updates-list").children( ).length > 0 ){

            $thisModal.find(".project-updates-list li").each( function( i ){

                var newUpdate = {};

                newUpdate.id    = $(this).find("input[name=updateId]").val( );
                $(this).find("input[name=updateId]").remove( );
                newUpdate.title = $(this).find("input[name=updateTitle]").val( );
                $(this).find("input[name=updateTitle]").remove( );
                newUpdate.desc  = $(this).find(".update-desc").html( );
                $(this).find(".update-desc").remove( );

                updates.push( newUpdate );
            });
        }

        $thisModal.find(".project-org-cause .row").each( function( i ){
            if( $(this).hasClass('chosen_project_cause') ){
                projectChosenCauses.push( $(this).parent( ).data("org-cause-id") );
            }
        });  
    });

    $(".new-crowdfunding-modal").on('show.bs.modal', function( ){

        projectObj          = {};
        projectChosenCauses = [];
        incentives          = [];
        updates             = []; 

    });

    $(".project-header-upload").on("click", function( ){
        
        var $parentModal = $( $(this).data('parent-modal') );
        $parentModal.find("input[name=header_photo]").trigger("click");
    });

    $(".project-thumbnail-upload").on("click", function( ){

        var $parentModal = $( $(this).data('parent-modal') );
        $parentModal.find("input[name=thumbnail_photo]").trigger("click");
    });

    $(".viewVideo").on("click", function( e ){

        e.preventDefault( );
        e.stopPropagation( );

        if( $(this).data("status") == "off" ){
            $(this).parent( ).find(".videoContainer").removeClass("hidden");
            $(this).data("status", "on");    
            $(this).html("Hide Video");
        }else{
            $(this).parent( ).find(".videoContainer").addClass("hidden");
            $(this).data("status", "off");
            $(this).html("View Video");
        }
    });

    $("input[name=header_photo]").on("change", function( e ){

        var file_data = this.files[0];

        projectObj.header = file_data;

        var fileName = file_data.name;
        var fileSize = file_data.size.parseFileSize( );

        $(this).parent( ).find(".project_header_holder").hide( );

        $(".project_header_info").find(".file_name").html( fileName );
        $(".project_header_info").find(".file_size").html( fileSize );
    });

    $("input[name=thumbnail_photo]").on("change", function( e ){

        var file_data = this.files[0];

        projectObj.thumbnail = file_data;

        var fileName = file_data.name;
        var fileSize = file_data.size.parseFileSize( );

        $(this).parent( ).find(".project_thumbnail_holder").hide( );

        $(".project_thumbnail_info").find(".file_name").html( fileName );
        $(".project_thumbnail_info").find(".file_size").html( fileSize );

    });

    $(".project-org-cause").on("click", function( e ){

        if( projectChosenCauses.indexOf( $(this).data("org-cause-id") ) == -1 ){
            projectChosenCauses.push( $(this).data("org-cause-id") );
            $(this).find(".row").addClass("chosen_project_cause");
        }else{
            for( var x in projectChosenCauses ){

                if( projectChosenCauses[x] == $(this).data("org-cause-id") ){
                    projectChosenCauses.splice( x, 1 );    
                }
            }
            $(this).find(".row").removeClass("chosen_project_cause");
        }
    });

    $(document).on("click", ".edit-update", function( ){

        editUpdateId = $(this).parent( ).data("update-id").split("_")[1];

        projectId = $(this).parent( ).data('project-id');

        var $modal = $("#editCrowdFundingProjectModal_" + projectId );

        for( var x in updates ){

            if( updates[x].id == editUpdateId ){
                $modal.find("input[name=update-title]").val( updates[x].title );
                $modal.find("textarea[name=update-desc]").jqteVal( updates[x].desc );
            }
        }
    });

    $(document).on("click", ".remove-update", function( ){

        var updateId = $(this).parent( ).data("update-id").split("_")[1];

        for( var x in updates ){
            if( updates[x].id == updateId ){
                updates.splice(x, 1);

                $(this).parent( ).remove( );
            }
        }
    });

    $(".clear-update").on("click", function( ){

        var $container = $(this).parent( );

        clearUpdateForm( $container );

        if( editUpdateId != "" ){
            editUpdateId = "";
        }
    });

    $(".add-update").on("click", function( ){
        
        var newUpdate = {};
        $("#addCFP_UpdatesTab").find(".error").html( "" ).hide( );
        $("#editCFP_UpdatesTab").find(".error").html( "" ).hide( );

        var $container = $(this).parent( );

        var newId = new Date( ).valueOf( );
        var errors = [];

        newUpdate.id    = "update" + newId;
        newUpdate.title = $container.find("input[name=update-title]").val( );
        newUpdate.desc  = $container.find("textarea[name=update-desc]").val( );
        
        if( newUpdate.title == "" ){
            errors.push( {field: "update-title", error: "Please Enter an Updated Title"} );
        }

        if( newUpdate.desc == "" ){
            errors.push( {field: "update-desc", error: "Please Enter an Update"} );
        }

        if( errors.length == 0 ){

            if( editUpdateId == "" ){
                updates.push( newUpdate );

                var newListItem = "<li class='list-group-item' data-incentive-id='update_" + newUpdate.id + "'>";
                    newListItem +=    " <span class='badge remove-update'>";
                    newListItem +=    "     <span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
                    newListItem +=    " </span>";
                    newListItem +=    " <span class='badge edit-update'>";
                    newListItem +=    "     <span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>";
                    newListItem +=    " </span>";
                    newListItem +=    " <small>Just Now</small>";
                    newListItem +=    " &nbsp;";
                    newListItem +=    " <span class='update-list-title'>" + newUpdate.title + "</span>";
                    newListItem +=    "</li>";
                
                if( $container.find(".project-updates-list").children( ).length > 0 ){
                    
                    $container
                    .find(".project-updates-list")
                    .find("ul")
                    .append( newListItem );

                }else{
                    
                    $container
                    .find(".project-updates-list")
                    .append( "<ul class='list-group'>" + newListItem + "</ul>");
                }

                newIncentive = {};
                clearUpdateForm( $container );
            }else{

                for( var x in updates ){

                    if( updates[x].id == editUpdateId ){
                        updates[x].title    = newUpdate.title;
                        updates[x].desc     = newUpdate.desc;
                    }
                }

                $container.find("li[data-update-id=update_" + editUpdateId + "]").find(".update-list-title").html( newUpdate.title);
                $container.find("li[data-update-id=update_" + editUpdateId + "]").find("small").html( "Just Now ");

                editUpdateId = "";
                newUpdate = {};
                clearUpdateForm( $container );   
            }
        }else{
            for( var x in errors ){
                $container.find("." + errors[x].field + "_error").html( errors[x].error ).show( );
                newIncentive = {};
            }
        }

    });

    $(document).on("click", ".edit-incentive", function( ){

        editIncentiveId = $(this).parent( ).data("incentive-id").split("_")[1];

        projectId = $(this).parent( ).data('project-id');

        var $modal = $("#editCrowdFundingProjectModal_" + projectId );

        for( var x in incentives ){

            if( incentives[x].id == editIncentiveId ){
                $modal.find("input[name=incentive-name]").val( incentives[x].name );
                $modal.find("input[name=incentive-donation-amount]").val( incentives[x].amount );        
                $modal.find("input[name=incentive-number-available]").val( incentives[x].available );
                $modal.find("textarea[name=incentive-description]").jqteVal( incentives[x].desc );

                if( incentives[x].needShipping == 1 ){
                    $modal.find("input[name=incentive-has-shipping]").prop("checked", true);
                }
            }
        }
    });

    $(document).on("click", ".remove-incentive", function( ){

        var incentiveId = $(this).parent( ).data("incentive-id").split("_")[1];

        for( var x in incentives ){
            if( incentives[x].id == incentiveId ){
                incentives.splice(x, 1);

                $(this).parent( ).remove( );
            }
        }
    });

    $(".clear-incentive").on("click", function( ){

        var $container = $(this).parent( );

        clearIncentiveForm( $container );

        if( editIncentiveId != "" ){
            editIncentiveId = "";
        }
    });

    $(".add-incentive").on("click", function( ){

        var $container = $(this).parent( );

        var newIncentive = {};
        $("#addCFP_IncentivesTab").find(".error").html( "" ).hide( );
        $("#editCFP_IncentivesTab").find(".error").html( "" ).hide( );

        var newId = new Date( ).valueOf( );

        newIncentive.needShipping   = 0;
        newIncentive.id             = "incentive" + newId;
        newIncentive.name           = $container.find("input[name=incentive-name]").val( );
        newIncentive.amount         = $container.find("input[name=incentive-donation-amount]").val( );
        newIncentive.available      = $container.find("input[name=incentive-number-available]").val( );
        newIncentive.desc           = $container.find("textarea[name=incentive-description]").val( );

        if( $container.find("input[name=incentive-has-shipping]").is(":checked") ){
            newIncentive.needShipping = 1;
        }
        var errors = [];
        
        if( newIncentive.name == "" ){
            errors.push( {field: "incentive-name", error: "Please Enter an Incentive Name"} );
        }

        if( newIncentive.amount == "" ){
            errors.push( {field: "incentive-donation-amount", error: "Please Enter an Incentive Amount"} );   
        }else if( isNaN( newIncentive.amount ) ){
            errors.push( {field: "incentive-donation-amount", error: "Incentive Amount must be a Number."} );   
        }

        if( newIncentive.available != "" ){
            if( isNaN( newIncentive.available ) ){
                errors.push( {field: "incentive-number-available", error: "Incentive Number must be a Number."} );         
            }
        }

        if( errors.length == 0 ){

            if( editIncentiveId == "" ){
                incentives.push( newIncentive );

                var newListItem = "<li class='list-group-item' data-incentive-id='incentive_" + newIncentive.id + "'>";
                    newListItem +=    " <span class='badge remove-incentive'>";
                    newListItem +=    "     <span class='glyphicon glyphicon-remove' aria-hidden='true'></span>";
                    newListItem +=    " </span>";
                    newListItem +=    " <span class='badge edit-incentive'>";
                    newListItem +=    "     <span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>";
                    newListItem +=    " </span>";
                    newListItem +=    "<span class='incentive-list-name'>" + newIncentive.name + "</span>";
                    newListItem +=    "</li>";
                
                if( $container.find(".incentive-list").children( ).length > 0 ){
                    
                    $container
                    .find(".incentive-list")
                    .find("ul")
                    .append( newListItem );

                }else{
                    
                    $container
                    .find(".incentive-list")
                    .append( "<ul class='list-group'>" + newListItem + "</ul>");
                }

                newIncentive = {};
                clearIncentiveForm( $container );
            }else{

                for( var x in incentives ){

                    if( incentives[x].id == editIncentiveId ){

                        incentives[x].name           = newIncentive.name;
                        incentives[x].amount         = newIncentive.amount;
                        incentives[x].available      = newIncentive.available;
                        incentives[x].desc           = newIncentive.desc;
                        incentives[x].needShipping   = newIncentive.needShipping;
                    }
                }

                $container.find("li[data-incentive-id=incentive_" + editIncentiveId + "]").find(".incentive-list-name").html( newIncentive.name);

                editIncentiveId = "";
                newIncentive = {};
                clearIncentiveForm( $container );   
            }
        }else{
            for( var x in errors ){
                $container.find("." + errors[x].field + "_error").html( errors[x].error ).show( );
                newIncentive = {};
            }
        }
    });

    $(".update-project").on("click", function( ){
        var $button = $(this);

        var $page = $("#editCrowdFundingProjectModal_" + $(this).data('project-id') );

        var end_date = $page.find("input[name=project_end_date_month]").val( ) + $page.find("input[name=project_end_date_day]").val( ) + $page.find("input[name=project_end_date_year]").val( );

        var form_data = new FormData();

        if( typeof projectObj.thumbnail !== "undefined") {
            form_data.append('thumbnail',   projectObj.thumbnail );
        }

        if( typeof projectObj.header !== "undefined") {
            form_data.append('header',   projectObj.header );
        }
        form_data.append('id',          $(this).data('project-id') );
        form_data.append('name',        $page.find("input[name=project_name]").val( ) );
        form_data.append('goal',        $page.find("input[name=project_goal]").val( ) );
        form_data.append('day',         $page.find("input[name=project_end_date_day]").val( ) );
        form_data.append('month',       $page.find("input[name=project_end_date_month]").val( ) );
        form_data.append('year',        $page.find("input[name=project_end_date_year]").val( ) );
        form_data.append('end_date',    end_date);
        form_data.append('story',       $page.find("textarea[name=project_story]").val( ) );
        form_data.append('video',       $page.find("input[name=project_video_url]").val( ) );

        for( var x in projectChosenCauses ){
            form_data.append('projectCause[]', projectChosenCauses[x] );
        }

        for( var x in incentives ){
            form_data.append('projectIncentiveName[]',      incentives[x].name );
            form_data.append('projectIncentiveAmt[]',       incentives[x].amount );
            form_data.append('projectIncentiveNumber[]',    incentives[x].available );
            form_data.append('projectIncentiveDesc[]',      incentives[x].desc );
            form_data.append('projectIncentiveShipping[]',  incentives[x].needShipping );
            form_data.append('projectIncentiveId[]',        incentives[x].id);
        }

        for( var x in updates ){
            form_data.append('projectUpdateId[]',   updates[x].id );
            form_data.append('projectUpdateTitle[]',updates[x].title)
            form_data.append('projectUpdate[]',     updates[x].desc);
        }

        form_data.append('_token', $(document).find("input[name=_token]").val( ) );

        form_data.append('orgId', orgId );
        form_data.append('userId', userId);

        $.ajax({
            url: "/organization/updateProject",
            method: "POST",
            data: form_data,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function( ){

                $("#editCrowdFundingProjectModal").find(".error").hide( );

                $button
                //.prop("disabled",true);
                .removeClass("btn-primary")
                .addClass("btn-info")
                .html("Saving Project...");
            },
            success: function( resp ){

                projectUploaded = true;
                
                if( resp.status ){
                    
                    $button
                    .removeClass("btn-info")
                    .addClass("btn-success")
                    .html("Project Successfully Saved");      

                    setTimeout(function( ){
                        $button
                        .prop("disabled",false)
                        .removeClass("btn-success")
                        .addClass("btn-primary")
                        .html("Update Project");      
                    }, 3000);
                }else{

                    $button
                    .removeClass("btn-info")
                    .addClass("btn-danger")
                    .html("Please See Errors");   

                    setTimeout(function( ){
                        $button
                        .prop("disabled",false)
                        .removeClass("btn-danger")
                        .addClass("btn-primary")
                        .html("Update Project");      
                    }, 3000);

                    for( var x in resp.errors ){
                        $("#editCrowdFundingProjectModal").find("." + x + "-error").html( resp.errors[x] ).show( );
                    }
                }
                
            }
        });
    });

    $(".save-new-project").on("click", function( ){

        var $button = $(this);

        var $page = $("#addCrowdFundingProjectModal");

        var end_date = $page.find("input[name=project_end_date_month]").val( ) + $page.find("input[name=project_end_date_day]").val( ) + $page.find("input[name=project_end_date_year]").val( );
        
        var form_data = new FormData();

        if( typeof projectObj.thumbnail !== "undefined") {
            form_data.append('thumbnail',   projectObj.thumbnail );
        }

        if( typeof projectObj.header !== "undefined") {
            form_data.append('header',   projectObj.header );
        }
        
        form_data.append('name',        $page.find("input[name=project_name]").val( ) );
        form_data.append('goal',        $page.find("input[name=project_goal]").val( ) );
        form_data.append('day',         $page.find("input[name=project_end_date_day]").val( ) );
        form_data.append('month',       $page.find("input[name=project_end_date_month]").val( ) );
        form_data.append('year',        $page.find("input[name=project_end_date_year]").val( ) );
        form_data.append('end_date',    end_date);
        form_data.append('story',       $page.find("textarea[name=project_story]").val( ) );
        form_data.append('video',       $page.find("input[name=project_video_url]").val( ) );

        for( var x in projectChosenCauses ){
            form_data.append('projectCause[]', projectChosenCauses[x] );
        }

        for( var x in incentives ){
            form_data.append('projectIncentiveName[]',      incentives[x].name );
            form_data.append('projectIncentiveAmt[]',       incentives[x].amount );
            form_data.append('projectIncentiveNumber[]',    incentives[x].available );
            form_data.append('projectIncentiveDesc[]',      incentives[x].desc );
            form_data.append('projectIncentiveShipping[]',  incentives[x].needShipping );
        }

        form_data.append('_token', $(document).find("input[name=_token]").val( ) );

        form_data.append('orgId', orgId );
        form_data.append('userId', userId);

        $.ajax({
            url: "/organization/addProject",
            method: "POST",
            data: form_data,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function( ){

                $("#addCrowdFundingProjectModal").find(".error").hide( );

                $button
                //.prop("disabled",true);
                .removeClass("btn-primary")
                .addClass("btn-info")
                .html("Saving Project...");
            },
            success: function( resp ){

                projectUploaded = true;
                
                if( resp.status ){

                    $("input[name=project_name]").val("");
                    $("input[name=project_goal]").val("");
                    $("input[name=project_end_date_day]").val("");
                    $("input[name=project_end_date_month]").val("");
                    $("input[name=project_end_date_year]").val("");
                    $("textarea[name=project_story]").jqteVal("");
                    $("input[name=project_video_url]").val("");
                    $(".incentive-list").html( "" );

                    $(".project-org-cause").find(".row").removeClass('chosen_project_cause');

                    $incentives = {};
                    projectChosenCauses = [];

                    $button
                    .removeClass("btn-info")
                    .addClass("btn-success")
                    .html("Project Successfully Saved");      

                    setTimeout(function( ){
                        $button
                        .prop("disabled",false)
                        .removeClass("btn-success")
                        .addClass("btn-primary")
                        .html("Save New Project");      
                    }, 3000);
                }else{

                    $button
                    .removeClass("btn-info")
                    .addClass("btn-danger")
                    .html("Please See Errors");   

                    setTimeout(function( ){
                        $button
                        .prop("disabled",false)
                        .removeClass("btn-danger")
                        .addClass("btn-primary")
                        .html("Save New Project");      
                    }, 3000);

                    for( var x in resp.errors ){
                        $("#addCrowdFundingProjectModal").find("." + x + "-error").html( resp.errors[x] ).show( );
                    }
                }

            }
        });
    });


    $(".admin-crowdfunding-modal").on("hide.bs.modal", function( ){
        if( projectUploaded ){
            location.reload( );
        }
    });

    /** End Project Modal **/

    /** Social Media Modal **/

    $socialMediaUpdated = false;

    $(".save-social-media").on("click", function( ){

        var $button = $(this);

        var $smMediaModal = $("#socialMediaModal");

        var twitter     = $smMediaModal.find("input[name=twitter-handle]").val( );
        var facebook    = $smMediaModal.find("input[name=facebook-handle]").val( );
        var instagram   = $smMediaModal.find("input[name=instagram-handle]").val( );
        var pinterest   = $smMediaModal.find("input[name=pinterest-handle]").val( );

        $.ajax({
            url: "/organization/saveSocialMedia",
            method: "POST",
            data: "twitter=" + twitter + "&facebook=" + facebook + "&instagram=" + instagram + "&pinterest=" + pinterest + "&orgId=" + orgId + "&_token=" + $(document).find("input[name=_token]").val( ),
            dataType: "json",
            beforeSend: function( ){
                $button
                .removeClass('btn-primary')
                .addClass('btn-info')
                .html( 'Saving Social Media' );
            },
            success: function( resp ){

                $socialMediaUpdated = true;

                $button
                .removeClass("btn-info")
                .addClass("btn-success")
                .html("Social Media Saved");      

                setTimeout(function( ){
                    $button
                    .prop("disabled",false)
                    .removeClass("btn-success")
                    .addClass("btn-primary")
                    .html("Save Changes");      
                }, 3000);
            }
        });
    });

    $("#socialMediaModal").on("hidden.bs.modal", function( ){

        if( $socialMediaUpdated ){
            location.reload( );
        }
    });

    /** End Social Media Modal **/


    /** Start Product Modal **/
	
	
	/** Set up newProductObj **/
    var newProductObj = {};
    var productChange = false;
    var openedProductModal = "";
    var nextProductModal   = "";
    newProductObj.orgId     = orgId;
    newProductObj.userId    = userId;

    $("#productAdminModal").find(".list-group-item .edit-product").on("click", function( ){
        alert('hi event');
        nextProductModal = "#productAdminModal_" + $(this).parent( ).data("product-id")
        alert( nextProductModal );
        $("#productAdminModal").modal("hide");
    });

    $("#productAdminModal").on("hidden.bs.modal", function( ){

        if( nextProductModal != "" ){
            $(nextProductModal).modal("show");        
            nextProductModal = "";
        }
    });

    $(".admin-product-modal").on("show.bs.modal", function( ){
        openedProductModal = $(this).prop("id");
    });

	/** Initialize Current step  **/	
    var newProductStep = 1;
	
	/** Event for upload image to trigger file dialog **/
    $(".upload-photo-image").on("click", function( ){
        $("#productPic").trigger("click");
    });
	
	/** Event for triggering change on file object **/
    $("#productPic").on("change", function( ){
		
		/** Check if the newProductObj has a photos key **/
        if( typeof newProductObj.photos === "undefined" ){
	        /** Create photos **/
            newProductObj.photos = [];
        }
		
		/**  Push file object to photos array **/
        newProductObj.photos.push( this.files[0] );
		
		/** Display current photo uploaded **/
        $(".product_photo_list").prepend("<div class='product_photo_item' data-product-image-line=" + (newProductObj.photos.length - 1) + ">" + this.files[0].name + " ( " + this.files[0].size.parseFileSize( ) + ")" + "<span class='glyphicon glyphicon-remove-circle pull-right' aria-hidden='true'></span>");

    });/** End #productPic.on.change **/
    
    $(".next-step").on("click", function( ){
	    
	    var thisStep = $(this).data('product-next-step');

        switch( newProductStep ){
			case 1:
				if(	processNewProductStepOne( ) ){
					goToProductStep( (newProductStep + 1), newProductStep, "Inventory", "Information", "Modifiers");
					newProductStep++;
				}
			break;
			case 2:
				if( processNewProductStepTwo( ) ){
					goToProductStep( (newProductStep + 1), newProductStep, "Shipping", "Modifiers", "Inventory" );
					newProductStep++;
					setUpInventory( );
				}
			break;
			case 3:
				if( processNewProductStepThree( ) ){
					goToProductStep( (newProductStep + 1), newProductStep, "Causes/Impacts", "Inventory", "Shipping" );
					newProductStep++;
					setUpShippingForModifiers( );
				}
			break;
			case 4:
				if( processNewProductStepFour( ) ){
					goToProductStep( (newProductStep + 1), newProductStep, "Submit Product", "Shipping", "Causes/Impacts");
					newProductStep++;
				}
			break;
			case 5:
                var form_data = new FormData( );
                
                //info
                form_data.append("_token", $(document).find("input[name=_token]").val( ) );

                for( var x in newProductObj ){
                    if( typeof newProductObj[x] !== "array" && typeof newProductObj[x] !== "object" ){
                        form_data.append(x, newProductObj[x]);
                    }
                }
                
                //photos
                for( var x in newProductObj.photos ){
                    form_data.append("photos[]", newProductObj.photos[x] );
                }
                //causes
                for( var x in newProductObj.causes ){
                    form_data.append("orgCause[]", newProductObj.causes[x].orgCause );
                    form_data.append("orgCauseId[]", newProductObj.causes[x].orgCauseId );
                }

                //modifiers
                for( var x in newProductObj.modifiers ){
                    form_data.append("modifierId[]", newProductObj.modifiers[x].id );
                    form_data.append("modifierTitle[]", newProductObj.modifiers[x].title);

                    for( var i in newProductObj.modifiers[x].items ){
                        form_data.append("modifier_" + x + "_item_id[]", newProductObj.modifiers[x].items[i].id );
                        form_data.append("modifier_" + x + "_item_title[]", newProductObj.modifiers[x].items[i].modifierTitle );
                    }
                }

                //modifierInventory
                for( var x in newProductObj.modifierInventory ){
                    form_data.append("modifierInventoryHeight[]",        newProductObj.modifierInventory[x].height );
                    form_data.append("modifierInventoryIdList[]",        newProductObj.modifierInventory[x].idList );
                    form_data.append("modifierInventoryLength[]",        newProductObj.modifierInventory[x].length );
                    form_data.append("modifierInventoryPriceDiff[]",     newProductObj.modifierInventory[x].priceDiff);
                    form_data.append("modifierInventoryQuantity[]",      newProductObj.modifierInventory[x].quantity);
                    form_data.append("modifierInventoryShippingTime[]",  newProductObj.modifierInventory[x].shippingTime);
                    form_data.append("modifierInventoryShippingFee[]",   newProductObj.modifierInventory[x].shippingFee);
                    form_data.append("modifierInventoryWeight[]",        newProductObj.modifierInventory[x].weight );
                    form_data.append("modifierInventoryWidth[]",         newProductObj.modifierInventory[x].width);
                }

                //shipping Methods
                for( var x in newProductObj.shippingMethods ){
                    form_data.append("shippingMethod[]", newProductObj.shippingMethods[x] );
                }

                //Submit Product
                $.ajax({
                    url: "/organization/addProduct",
                    method: "POST",
                    data: form_data,
                    dataType: "json",
                    beforeSend: function( ){
                        /** disable buttons */
                        $(".new-product-actions").find("button").prop("disabled", true);
                        
                        /** change right button to say the 'Adding Product...' */
                        $(".new-product-actions").find(".next-step").html("Adding Product...");

                        $(".new-product-status").find("div").addClass('hidden');
                    },
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function( resp ){

                        /** check if resp.status is TRUE */
                        if( resp.status ){
                            $(".new-product-status").find(".bg-success").removeClass('hidden');
                            setTimeout(function() {
                                location.reload( );
                            }, 5000);
                        }else{
                            $(".new-product-status").find(".bg-success").removeClass('hidden');
                        }
                    }
                });
            break;
		}
	});

    $(".prev-step").on("click", function( ){

       switch( newProductStep ){
			case 2:
				goToProductStep( 1, newProductStep, "Modifiers", "", "");
                newProductStep = 1;
			break;
			case 3:
				goToProductStep( 2, newProductStep, "Inventory", "Information", "" );
				newProductStep = 2;
			break;
			case 4:
				goToProductStep( 3, newProductStep, "Shipping", "Modifiers", "" );
                newProductStep = 3;
			break;
			case 5:
				goToProductStep( 4, newProductStep, "Causes/Impacts", "Inventory", "");
				newProductStep = 4;
			break;
		}
    });
	
	$(".add-modifier").on("click", function( ){
		
		var modifierId = 0;
		/*
		if( typeof newProductObj.modifiers !== "undefined" ){
			modifierId = newProductObj.modifiers[( newProductObj.modifiers.length - 1)].id + 1
		}
        */
        $(".new-product-modifier-list .modifier").each( function( ){
            modifierId = parseInt( $(this).data("modifier-id") ) + 1;
        });
		
		var modifier = 	"<div class='modifier modifier-" + modifierId + " input-group' data-modifier-id='" + modifierId + "'>";
		modifier += 	"	<label for='modifier-title-" + modifierId + "'>Title of Modifier</label>";
		modifier += 	"	<input type='text' name='modifier-title-" + modifierId +"' class='form-control' />";
		modifier += 	" 	<div class='modifier-container-list-" + modifierId + " margin-top-10 margin-bottom-10'>";
		modifier +=		"		<div class='row'>";
		modifier +=		"			<div class='col-lg-12'>";
		modifier +=		"				<div class='input-group margin-bottom-10 margin-top-10'>";
		modifier +=		"					<input type='text' name='tmp-modifier-name' class='form-control' placeholder='Modifier Name...'/>";
		modifier +=		"					<span class='input-group-btn'>";
		modifier +=		"						<button class='btn btn-default add-modifier-item' data-modifier-id='" + modifierId + "' type='button' >Add</button>";
		modifier +=		"					</span>";
		modifier +=		"				</div>";
        modifier += 	"				<div class='error tmp-modifier-name-error'></div>";
		modifier +=		"			</div>";
		modifier +=		"		</div>";
        modifier +=     "       <div class='row modifier-items-list margin-left-0 margin-right-0'></div>";
		modifier += 	"	</div>";
        modifier +=		"</div>";
		
		$(".new-product-modifier-list").append( modifier );
	});
	
	$(document).on("click", ".add-modifier-item", function( ){
		
		var modifierId 		= $(this).data('modifier-id');
		$(".modifier-container-list-" + modifierId).find('.error').html( "" ).hide( );
		var modifierName 	= $(".modifier-container-list-" + modifierId).find("input[name=tmp-modifier-name]").val( );
		
        if( modifierName == "" ){
            $(".modifier-container-list-" + modifierId).find('.tmp-modifier-name-error').html('Please Enter a Modifier Item Name.').show( );
        }else{
            var modifierItemId = 0;
            $(".modifier-container-list-" + modifierId)
            .find(".modifier-items-list")
            .find(".modifier-item")
            .each( function( ){
                modifierItemId = $(this).data('modifier-item-id') + 1;
            });

            $(".modifier-container-list-" + modifierId)
            .find(".modifier-items-list")
            .append("<div class='col-lg-3 modifier-item modifier-item-" + modifierId + "' data-modifier-id='" + modifierId + "' data-modifier-item-id='" + modifierItemId + "'>" + modifierName + " <span class='glyphicon glyphicon-remove-circle glyphicon-align-right pull-right remove-modifier-item' aria-hidden='true'></span></div>");	
            
            $(".modifier-container-list-" + modifierId).find("input[name=tmp-modifier-name]").val( "" );
        }
        
    });

    $(document).on("click", ".modifier-item > .remove-modifier-item", function( ){
        $(this).parent( ).remove( );
    });

	$(".make-standard-product").on("click", function( ){
		
        var $thisButton = $(this);

        if( $(".new-product-modifier-list").children( ).length > 0 ){
            $(".modifier-msg").show( );	
		}else{
			newProductObj.modifiers = [];
			
			if( $(".breadcrumb").find("li[data-breadcrumb-item=3]").length == 0 ){
				$(".breadcrumb").append("<li data-breadcrumb-item='3'><a href='/breadcrumb-product-modifiers'>Inventory</a></li>");
			}

			$(".modifier-msg").hide( );
			goToProductStep( 4, 2, "Causes/Impacts", "Modifiers", "Shipping", true );
				
			newProductStep = 4;
		}
	});

    $(".new-product-causes-impacts").find(".project-org-cause").on("click", function( ){

        var orgCause    = $(this).data("org-cause");
        var orgCauseId  = $(this).data("org-cause-id");

        if( $(this).find(".row").hasClass("chosen_project_cause") ){
            $(this).find(".row").addClass("chosen_project_cause");

            if( typeof newProductObj.causes === "undefined" ){
                newProductObj.causes = [];
            }

            newProductObj.causes.push({orgCause: orgCause, orgCauseId: orgCauseId});
        }else{
            $(this).find(".row").removeClass("chosen_project_cause");

            $.each( newProductObj.causes, function( i ){
                if( newProductObj.causes[i].orgCause == orgCause && newProductObj.causes[i].orgCauseId == orgCauseId ){
                    newProductObj.causes.splice(i, 1);
                }
            });
        }
    });

    $(document).on("click", ".modifier-shipping-row input[type=checkbox]", function( ){

        var $parent             = $(this).parent( );
        var $grandParent        = $parent.parent( );
        var $greatGrandParent   = $grandParent.parent( );

        if( ! $(this).is(":checked") ){
            $grandParent.css("border-bottom", "1px solid #000");
            $greatGrandParent.find(".modifier-item-shipping-info").show( );
        }else{
            
            $grandParent.css("border", "none");
            $greatGrandParent.find(".modifier-item-shipping-info").hide( );
            $greatGrandParent.find(".modifier-item-shipping-info").find("input[type=text]").val( );
        }
    });

    $(".continue-with-standard-product").on("click", function( ){

        newProductObj.modifiers = [];

        $(".new-product-modifier-list").find(".modifier").remove( );

        if( $(".breadcrumb").find("li[data-breadcrumb-item=3]").length == 0 ){
            $(".breadcrumb").append("<li data-breadcrumb-item='3'><a href='/breadcrumb-product-modifiers'>Inventory</a></li>");
        }
        
        $(".modifier-msg").hide( );

        goToProductStep( 4, 2, "Causes/Impacts", "Modifiers", "Shipping", true );
            
        newProductStep = 4;
    });

    $(".dismiss-this-warning").on("click", function( ){
        $(this).parent( ).hide( );
    });

    $(".remove-product").on("click", function( ){
        var self = $(this);
        var productId = $(this).parent( ).data("product-id");
        var token = $(document).find("input[name=_token]").val( );

        $.ajax({
            url: "/organization/removeProduct",
            method: "POST",
            data: "productId=" + productId + "&_token=" + token,
            success: function( ){
                self.parent( ).remove( );
                productChange = true;
            }
        });
    });

    $("#productAdminModal").on("hide.bs.modal", function( ){
        if( productChange ){
            location.reload( );
        }
    });
    
    /** End Product Modal **/
    
    function processNewProductStepOne( ){
	    
	    var $parent = $("#productAdminModal");
        
        $(".new-product-info")
	    .find(".errors")
	    .html("")
	    .hide( );
	    
	    var productName 		= $parent.find("input[name=product_name]").val( );
	    var productSKU			= $parent.find("input[name=product_sku]").val( );
	    var productPrice		= $parent.find("input[name=product_price]").val( );
	    var productQuantity 	= $parent.find("input[name=product_quantity]").val( );
	    var productCategoryVal 	= $parent.find("select[name=product_category]").find("option:selected").val( );
	    var productCategoryText = $parent.find("select[name=product_category]").find("option:selected").text( );
	    var sProductDesc		= $parent.find("textarea[name=short_product_description]").val( );
	    var lProductDesc		= $parent.find("textarea[name=long_product_description]").val( );
	    var errors 				= {};
	    
	    if( productName == "" ){
		    errors.product_name = "Please Enter a Product Name.";
	    }
	    
	    if( productPrice == "" ){
		    errors.product_price = "Please Enter a Product Price.";
	    }else{
		    if( isNaN( parseInt( productPrice ) ) ){
			    errors.product_price = "Please Enter a Number for the Price.";
		    }
	    }
	    
	    if( productQuantity == "" ){
		    errors.product_quantity = "Please Enter a Product Quantity.";
	    }else{
		    if( isNaN( parseInt(productQuantity ) ) ){
			    errors.product_quantity = "Please Enter a Number for the Quantity.";
		    }
	    }
	    
	    if( productSKU == "" ){
		    errors.product_sku = "Please Enter a Product SKU.";
	    }
	    
	    if( productCategoryVal == 0 ){
		    errors.product_category = "Please Select a Product Category.";
	    }

	    if( getObjectSize( errors ) == 0 ){
			newProductObj.productName 			= productName;
			newProductObj.productShortDesc 		= sProductDesc;
			newProductObj.productLongDesc 		= lProductDesc;    
			newProductObj.productPrice			= productPrice;
			newProductObj.productQuantity		= productQuantity;
			newProductObj.productSKU 			= productSKU;
			newProductObj.productCategoryVal	= productCategoryVal;
			newProductObj.productCategoryText 	= productCategoryText;
			
			return true;
	    }else{
			
			for( var x in errors ){
				if( x == "product_quantity"){
					$(".new-product-info")
					.find("." + x + "-error")
					.html( errors[x] )
					.css("display","inline-block");		
				}else{
					$(".new-product-info")
					.find("." + x + "-error")
					.html( errors[x] )
					.show( );	
				}
			}
			
			return false;	    
		}
	}
	
	function processNewProductStepTwo( ){
		
		var $modifierList =  $("#productAdminModal").find(".new-product-modifier-list");
		
		newProductObj.modifiers = [];
		
		var errors = {};
		
		var modifierList = [];

        var largestId = 0;
		
		$modifierList.find(".modifier").each( function( i ){
			
			var modifier = {};
			
			//errors.
			
			modifier.id = $(this).data('modifier-id');

            modifier.title = $(this).find("input[name=modifier-title-" + modifier.id + "]").val( );
			
			if( modifier.title == "" ){
				//set error for this title
			}

            var modifierItemIncrement = 0;
			
			if( $(this).find(".modifier-container-list-" + modifier.id ).find(".modifier-item").length == 0 ){
				// set error indicating no modifier items have been added
			}else{
				modifier.items = [];	
				
                $(this).find(".modifier-container-list-" + modifier.id ).find(".modifier-item").each( function( i ){
					modifier.items.push( { id: i, modifierTitle : $(this).text( ) } );

                    modifierItemIncrement = i;
				});
			}

            var tmpInputValue = $(".modifier-" + modifier.id).find("input[name=tmp-modifier-name]").val( ) 

            if( tmpInputValue != "" ){

                modifierItemIncrement++;

                modifier.items.push({ id: modifierItemIncrement, modifierTitle : tmpInputValue} );
            }
			
			modifierList.push( modifier );
		});
		
		newProductObj.modifiers = modifierList;
		
		return true;
	}
	
	function setUpInventory( ){
		
		var $inventory = $("#productAdminModal").find(".new-product-inventory");

        $inventory.html( "" );
		
        var topRow  = "<div class='row'>";
        topRow      += " <div class='col-lg-6' style='font-weight: bold;'>Product Modifiers</div>";
        topRow      += " <div class='col-lg-3' style='font-weight: bold;'>Quantity</div>";
        topRow      += " <div class='col-lg-3' style='font-weight: bold;'>Price Diff</div>";
        topRow      += "</div>";

        $inventory.append( topRow );

        if( newProductObj.modifiers.length > 0 ){
            var modifierDisplays = [];

            for( var i = 0 ; i < newProductObj.modifiers.length ; i++ ){
                for( var j = 0 ; j < newProductObj.modifiers[i].items ; j++ ){
                    modifierDisplays[i]
                }
            }

            if( newProductObj.modifiers.length == 1 ){
                $.each( newProductObj.modifiers[0].items, function( i ){
                    
                    var newRow = "<div class='row modifier-inventory-row margin-0' data-ids='" + newProductObj.modifiers[0].items[i].id + "'>";
                    newRow += " <div class='col-lg-6 padding-top-10' style='vertical-align: middle;'>" + newProductObj.modifiers[0].items[i].modifierTitle + "</div>";
                    newRow += "	<div class='col-lg-3'><input type='text' name='quantity' class='form-control' placeholder='0' /></div>";
                    newRow += " <div class='col-lg-3'><input type='text' name='priceDiff' class='form-control' placeholder='0' /></div>";
                    newRow += "</div>";

                    $inventory.append( newRow );
                });
            }else{
                $.each( newProductObj.modifiers[0].items, function( i ){
                    var display = newProductObj.modifiers[0].items[i].modifierTitle;
                    var ids     = newProductObj.modifiers[0].items[i].id;

                    for( var j = 1 ; newProductObj.modifiers[j] ; j++ ){
                        $.each( newProductObj.modifiers[j].items, function( k ){
                            
                            var newRow = "<div class='row modifier-inventory-row margin-0' data-ids='" + ids + "|" + newProductObj.modifiers[j].items[k].id + "'>";
                            newRow += " <div class='col-lg-6 padding-top-10' style='vertical-align: middle;'>" + display + "-> " + newProductObj.modifiers[j].items[k].modifierTitle + "</div>";
                            newRow += "	<div class='col-lg-3'><input type='text' name='quantity' class='form-control' placeholder='0' /></div>";
                            newRow += " <div class='col-lg-3'><input type='text' name='priceDiff' class='form-control' placeholder='0' /></div>";
                            newRow += "</div>";

                            $inventory.append( newRow );
                        });
                    }
                });
            }
        }
    }
    
    function processNewProductStepThree( ){
		
		var $inventory = $("#productAdminModal").find(".new-product-inventory");
		
        newProductObj.modifierInventory = [];

		$inventory.find(".modifier-inventory-row").each( function( ){
			
			var modifierId = $(this).data('modifier-id');
			var modifierItemId = $(this).data('modifier-item-id');

            var ids = $(this).data("ids");

            newProductObj.modifierInventory.push({ idList: ids, quantity: $(this).find("input[name=quantity]").val( ), priceDiff: $(this).find("input[name=priceDiff]").val( ) });
		});

        return true;
	}
	
	function setUpShippingForModifiers( ){
		
		var $shipping = $("#productAdminModal").find(".new-product-shipping").find(".modifier-shipping-items");
        $shipping.html("");

        var modifierDisplays = [];
        /**
        for( var i = 0 ; i < newProductObj.modifiers.length ; i++ ){
            for( var j = 0 ; j < newProductObj.modifiers[i].items ; j++ ){
                modifierDisplays[i]
            }
        }*/

        if( newProductObj.modifiers.length == 1 ){
            $.each( newProductObj.modifiers[0].items, function( i ){
                var newRow  = "<div class='row modifier-shipping-row' data-modifier-ids='" + newProductObj.modifiers[0].items[i].id + "' >";
                newRow      += "	<div class='col-lg-12'>";
                newRow      += "		<div class='row padding-bottom-5'>";
                newRow      += "			<div class='col-lg-6 text-left'>";
                newRow      +=				newProductObj.modifiers[0].items[i].modifierTitle;
                newRow      += "			</div>";
                newRow      += "			<div class='col-lg-6 text-right'>";
                newRow      += "				Same as Above <input type='checkbox' name='save-as-above' value='" + newProductObj.modifiers[0].items[i].id +"' checked />";
                newRow      += "			</div>";
                newRow      += "		</div>";
                newRow      += "		<div class='row modifier-item-shipping-info'>";
                newRow      += "			<div class='col-lg-12'>";
                newRow      += "				<div class='row'>";
                
                /** Retrieve the HTML from "productGeneralShippingLine1" DIV Block */
                var productGeneralShippingLine1 = $("#productAdminModal").find("#productGeneralShippingLine1").html( );

                /** Replace Error classes with classes for modifiers */
                productGeneralShippingLine1.replace("product_flat_rate_shipping_fee-error", "product_flat_rate_shipping_fee_modifier_" + newProductObj.modifiers[0].items[i].id + "-error");
                productGeneralShippingLine1.replace("product_shipping_time-error", "product_shipping_time_modifier_" + newProductObj.modifiers[0].items[i].id + "-error");

                /** Retrieve the HTML from "productGeneralShippingLine2" DIV Block */
                var productGeneralShippingLine2 = $("#productAdminModal").find("#productGeneralShippingLine2").html( );

                /** Replace Error classes with classes for modifiers */
                productGeneralShippingLine2.replace("product_weight-error", "product_weight_modifier_" + newProductObj.modifiers[0].items[i].id + "-error");
                productGeneralShippingLine2.replace("product_dimensions-error", "product_dimensions_modifier_" + newProductObj.modifiers[0].items[i].id + "-error");

                newRow      +=				    productGeneralShippingLine1;
                newRow      += "				</div>";
                newRow      += "				<div class='row'>";
                newRow      +=					productGeneralShippingLine2;
                newRow      += "				</div>";
                newRow      += "			</div>";
                newRow      += "		</div>";
                newRow      += "	</div>";
                newRow      += "</div>";

                $shipping.append( newRow );
            });
        }else{
            $.each( newProductObj.modifiers[0].items, function( i ){
                var display = newProductObj.modifiers[0].items[i].modifierTitle;
                var ids     = newProductObj.modifiers[0].items[i].id;

                for( var j = 1 ; newProductObj.modifiers[j] ; j++ ){
                    $.each( newProductObj.modifiers[j].items, function( k ){
                        
                        var newRow  = "<div class='row modifier-shipping-row' data-modifier-ids='" + ids + "|" + newProductObj.modifiers[j].items[k].id + "' >";
                        newRow      += "	<div class='col-lg-12'>";
                        newRow      += "		<div class='row padding-bottom-5'>";
                        newRow      += "			<div class='col-lg-6 text-left'>";
                        newRow      +=				display + "->" + newProductObj.modifiers[j].items[k].modifierTitle;
                        newRow      += "			</div>";
                        newRow      += "			<div class='col-lg-6 text-right'>";
                        newRow      += "				Same as Above <input type='checkbox' name='save-as-above' value='" + ids + "|" + newProductObj.modifiers[j].items[k].id +"' checked />";
                        newRow      += "			</div>";
                        newRow      += "		</div>";
                        newRow      += "		<div class='row modifier-item-shipping-info'>";
                        newRow      += "			<div class='col-lg-12'>";
                        newRow      += "				<div class='row'>";
                        
                        /** Retrieve the HTML from "productGeneralShippingLine1" DIV Block */
                        var productGeneralShippingLine1 = $("#productAdminModal").find("#productGeneralShippingLine1").html( );

                        /** Replace Error classes with classes for modifiers */
                        productGeneralShippingLine1.replace("product_flat_rate_shipping_fee-error", "product_flat_rate_shipping_fee_modifier_" + ids + "-error");
                        productGeneralShippingLine1.replace("product_shipping_time-error", "product_shipping_time_modifier_" + ids + "-error");

                        /** Retrieve the HTML from "productGeneralShippingLine2" DIV Block */
                        var productGeneralShippingLine2 = $("#productAdminModal").find("#productGeneralShippingLine2").html( );

                        /** Replace Error classes with classes for modifiers */
                        productGeneralShippingLine2.replace("product_weight-error", "product_weight_modifier_" + ids + "-error");
                        productGeneralShippingLine2.replace("product_dimensions-error", "product_dimensions_modifier_" + ids + "-error");

                        newRow      +=				    productGeneralShippingLine1;
                        newRow      += "				</div>";
                        newRow      += "				<div class='row'>";
                        newRow      +=					productGeneralShippingLine2;
                        newRow      += "				</div>";
                        newRow      += "			</div>";
                        newRow      += "		</div>";
                        newRow      += "	</div>";
                        newRow      += "</div>";

                        $shipping.append( newRow );
                    });
                }
            });
        }
		
		$(".modifier-item-shipping-info").find("input[type=text]").val("");
	}

    function processNewProductStepFour( ){
		
		var product_shipping_fee 	= $("#productGeneralShippingLine1").find("input[name=product_flat_rate_shipping_fee]").val( );
		var product_shipping_time 	= $("#productGeneralShippingLine1").find("input[name=product_shipping_time]").val( );	
		var product_weight 			= $("#productGeneralShippingLine2").find("input[name=product_weight]").val( );
		var product_width			= $("#productGeneralShippingLine2").find("input[name=product_dimensions_width]").val( );
		var product_length 			= $("#productGeneralShippingLine2").find("input[name=product_dimensions_length]").val( );
		var product_height 			= $("#productGeneralShippingLine2").find("input[name=product_dimensions_height]").val( );		
		
		errors = {};
		
		if( product_shipping_fee == "" ){
			errors.product_flat_rate_shipping_fee = "Please Enter Shipping Fee.";
		}else{
			if( isNaN( parseInt( product_shipping_fee ) ) ){
				errors.product_flat_rate_shipping_fee = "The Shipping Fee must be a Number.";
			}
		}
		
		if( product_shipping_time == "" ){
			errors.product_shipping_time = "Please Enter a Shipping Time.";
		}else{
			if( isNaN( parseInt( product_shipping_time ) ) ){
				errors.product_shipping_time = "The Shipping Time must be a Number.";
			}
		}
		
		if( ( product_weight != "" ) && isNaN( parseInt( product_weight ) ) ){
			errors.product_weight = "The Product Weight Must be a Number.";
		}
		
		if( ( product_width != "" ) && isNaN( parseInt( product_width ) ) ){
			errors.product_width = "The Product Width Must be a Number.<br />";
		}
		
		if( ( product_length != "" ) && isNaN( parseInt( product_length ) ) ){
			errors.product_length = "The Product Length Must be a Number.<br />";
		}
		
		if( ( product_height != "" ) && isNaN( parseInt( product_height ) ) ){
			errors.product_height = "The Product Height Must be a Number.<br />";
		}
		
		var shippingMethods = [];
		
		$("#productAdminModal").find(".product-shipping-methods-container").find("input[type=checkbox]").each( function( i ){
			if( $(this).parent( ).hasClass('active') ){
				shippingMethods.push( $(this).val( ) );
			}
		});
		
		if( shippingMethods.length == 0 ){
			errors.shipping_method = "Please Select a Shipping Method.";
		}
		
		var $modifierShippingMethods = $("#productAdminModal").find(".new-product-shipping").find(".modifier-shipping-items");
		
		$modifierShippingMethods.find(".modifier-shipping-row").each( function( ){

            var $modifierShippingRow = $(this);
            var ids = $modifierShippingRow.data('modifier-ids');
            
            if( $(this).find("input[name=same-as-above]").not(":checked") ){
				/** Check for each product shipping modifier dimensions */

                $.each( newProductObj.modifierInventory, function( i ){
                    
                    if( newProductObj.modifierInventory[i].idList == ids ){
                        newProductObj.modifierInventory[i].shippingFee  = $modifierShippingRow.find('input[name=product_flat_rate_shipping_fee]').val( );
                        newProductObj.modifierInventory[i].shippingTime = $modifierShippingRow.find("input[name=product_shipping_time]").val( );
                        newProductObj.modifierInventory[i].weight       = $modifierShippingRow.find("input[name=product_weight]").val( );
                        newProductObj.modifierInventory[i].length       = $modifierShippingRow.find("input[name=product_dimensions_length]").val( );
                        newProductObj.modifierInventory[i].width        = $modifierShippingRow.find("input[name=product_dimensions_width]").val( );
                        newProductObj.modifierInventory[i].height       = $modifierShippingRow.find("input[name=product_dimensions_height]").val( );
                    }
                });
            }
		});
		
		if( getObjectSize( errors ) == 0 ){
			newProductObj.shipping_fee 		= product_shipping_fee;
			newProductObj.shipping_time 	= product_shipping_time;	
			newProductObj.weight 			= product_weight;
			newProductObj.width				= product_width;
			newProductObj.length 			= product_length;
			newProductObj.height 			= product_height;
			newProductObj.shippingMethods	= shippingMethods;

            /** Loop through modifier object and append shipping data, if any */

            return true;
		}else{
			for( var x in errors ){
				if( x == "product_height" || x == "product_length" || x == "product_width" ){
					$(".product_dimenstions-error").append( errors[x] ).show( );
				}else if( x == "shipping_method" ){
					$(".product-shipping-method-errors").html( errors[x] ).show( );
				}else{
					$("." + x + "-error").html( errors[x] ).show( );
				}
			}
			return false;
		}
	}

	function goToProductStep( stepIn, stepOut, nextButtonText, prevButtonText, breadCrumbName, fastForward = false ){
		
        //console.log( stepIn + " " + stepOut + " " + nextButtonText + " " + prevButtonText + " " + breadCrumbName );

        $(".new-product-content").find("div[data-product-step=" + stepOut + "]").fadeOut("slow", function(){
			
			$(".new-product-content").find("div[data-product-step=" + stepIn + "]").fadeIn( );
			
			/** Check if breadcrumbs have the next step **/
			if( $(".breadcrumb").find("li[data-breadcrumb-item=" + stepIn + "]").length == 0 && breadCrumbName != ""){
                if( $(".breadcrumb").find("li[data-breadcrumb-item=" + stepIn + "]").length == 0 ){
                    $(".breadcrumb").append("<li data-breadcrumb-item='" + stepIn + "'><a href='/breadcrumb-product-modifiers'>" + breadCrumbName + "</a></li>");
                }
			}
			
			var $modal = $("#productAdminModal");
			
			/** Set Right Action button **/
			$modal
			.find(".right-action > button")
			.data("product-next-step", ( stepIn + 1 ) )
			.html(nextButtonText + " >>");
			/** Set Left Action button **/
            if( prevButtonText == "" ){
                $modal
                .find(".left-action > button")
                .data("product-prev-step", ( stepOut ) )
                .html("<< " + prevButtonText)
                .addClass('hidden');
            }else{
                $modal
                .find(".left-action > button")
                .data("product-prev-step", ( stepOut ) )
                .html("<< " + prevButtonText)
                .removeClass('hidden');
            }
		});	
	}

    function clearIncentiveForm( $parentEl ){
        $parentEl.find("input[name=incentive-name]").val("");
        $parentEl.find("input[name=incentive-donation-amount]").val("");
        $parentEl.find("input[name=incentive-number-available]").val("");
        $parentEl.find("textarea[name=incentive-description]").jqteVal("");
        $parentEl.find("input[name=incentive-has-shipping]").prop("checked", false);
    }

    function clearUpdateForm( $parentEl ){
        $parentEl.find("input[name=update-title]").val("");
        $parentEl.find("textarea[name=update-desc]").jqteVal("");
    }

    function clearSubCauses( ){
        $(".availableSubCauseList label").each( function( ){
            $(this).find("input[type=radio]").prop("checked", false);
            $(this).removeClass("active");
            $(this).hide( );
        });
    }

    function clearOtherCauses( id ){
        $(".availableCauseList label").each( function( ){
            if( id > 0 ){
                var thisCauseId = $(this).find("input[type=radio]").prop("id").split("-")[2];

                if( thisCauseId != id ){
                    $(this).find("input[type=radio]").prop("checked", false);
                    $(this).find("input[type=radio]").parent( ).removeClass('active');
                }
            }else{
                $(this).find("input[type=radio]").prop("checked", false);
                $(this).find("input[type=radio]").parent( ).removeClass('active');
            }
        });
    }

    function clearCountries( ){
        $(".country-list").html("").hide( );
    }
    
    function getObjectSize( obj ){
	    
	    var len = 0;
	    
	    for( var o in obj ){
		    len++;
	    }
	    
	    return len;
    }
});

function navigateToProducts( userId, token ){
    location.href = '/passthru?id=' + userId + "&_token=" + token;
}

//# sourceMappingURL=org-admin.js.map
