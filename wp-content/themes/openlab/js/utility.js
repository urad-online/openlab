(function ($) {

    var related_links_count,
            $add_new_related_link,
            $cloned_related_link_fields;

    $(document).ready(function () {

        //fixing hover issues on mobile
        if (isBreakpoint('xs') || isBreakpoint('sm')) {
            $('.mobile-no-hover').bind('touchend', function () {
                console.log('touchend triggered');
                fixHoverOnMobile($(this));
            })
        }

        // Workshop fields on Contact Us
        function toggle_workshop_meeting_items() {
            var contact_us_topic = document.getElementById('contact-us-topic');

            if (!!contact_us_topic) {
                if ('Request a Workshop / Meeting' == contact_us_topic.value) {
                    jQuery('#workshop-meeting-items').slideDown('fast');
                } else {
                    jQuery('#workshop-meeting-items').slideUp('fast');
                }
            }
        }

        // + button on Related Links List Settings
        $add_new_related_link = $('#add-new-related-link');
        $add_new_related_link.css('display', 'inline-block');
        $add_new_related_link.on('click', function () {
            create_new_related_link_field();
        });

        jQuery('#contact-us-topic').on('change', function () {
            toggle_workshop_meeting_items();
        });
        toggle_workshop_meeting_items();

        jQuery('#wds-accordion-slider').easyAccordion({
            autoStart: true,
            slideInterval: 6000,
            slideNum: false
        });

        jQuery("#header #menu-item-40 ul li ul li a").prepend("+ ");

        //search
        if ($('.search-trigger-wrapper').length) {
            var select = $('.search-form-wrapper .hidden-custom-select select');
            var adminBar = $('#wpadminbar');

            $('.search-form-wrapper').each(function () {
                var searchFormDim = invisibleDimensions($(this));
                $(this).data('thisheight', searchFormDim.height);
            });

            $('.search-trigger').on('click', function () {
                var searchTrigger = $(this);
                var mode = searchTrigger.data('mode');
                var searchForm = $('.search-form-wrapper.search-mode-' + mode);
                if (searchTrigger.parent().hasClass('search-live')) {

                    searchForm.slideUp(700, function () {
                        searchTrigger.parent().toggleClass('search-live');
                    });

                    if (searchTrigger.data('mode') == 'mobile') {
                        adminBar.animate({
                            top: "-=" + searchForm.data('thisheight')
                        }, 700);
                    }

                } else {
                    searchTrigger.parent().toggleClass('search-live');
                    searchForm.slideDown(700);
                    if (searchTrigger.data('mode') == 'mobile') {
                        adminBar.animate({
                            top: "+=" + searchForm.data('thisheight')
                        }, 700);
                    }

                }
                select.customSelect();
            })
        }

        // this add an onclick event to the "New Topic" button while preserving 
        // the original event; this is so "New Topic" can have a "current" class
        $('.show-hide-new').click(function () {
            var origOnClick = $('.show-hide-new').onclick;
            return function (e) {
                if (origOnClick != null && !origOnClick()) {
                    return false;
                }
                return true;
            }
        });

        window.new_topic_is_visible = $('#new-topic-post').is(":visible");
        $('.show-hide-new').click(function () {
            if (window.new_topic_is_visible) {
                $('.single-forum #message').slideUp(300);
                window.new_topic_is_visible = false;
            } else {
                $('.single-forum #message').slideDown(300);
                window.new_topic_is_visible = true;
            }
        });

        //custom select arrows
        $('.custom-select select').customSelect();

        //printing page
        if ($('.print-page').length) {
            $('.print-page').on('click', function (e) {
                e.preventDefault();
                window.print();
            });
        }

        //ajax functionality for courses archive
        $('#school-select').change(function () {
            var school = $(this).val();
            var nonce = $('#nonce-value').text();

            //disable the dept dropdown
            $('#dept-select').attr('disabled', 'disabled');
            $('#dept-select').addClass('processing');
            $('#dept-select').html('<option value=""></option>');

            if (school == "") {
                document.getElementById("dept-select").innerHTML = "";
                return;
            }

            $.ajax({
                type: 'GET',
                url: ajaxurl,
                data:
                        {
                            action: 'openlab_ajax_return_course_list',
                            school: school,
                            nonce: nonce
                        },
                success: function (data, textStatus, XMLHttpRequest)
                {
                    $('#dept-select').removeAttr('disabled');
                    $('#dept-select').removeClass('processing');
                    $('#dept-select').html(data);
                    $('.custom-select select').trigger('render');
                },
                error: function (MLHttpRequest, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        });
        function clear_form() {
            document.getElementById('group_seq_form').reset();
        }

        //member profile friend/cancel friend hover fx
        if ($('.btn.is_friend.friendship-button').length) {
            var allButtons = $('.btn.is_friend.friendship-button');
            allButtons.each(function () {
                var thisButton = $(this);
                var thisButtonHTML = $(this).html();
                thisButton.hover(function () {
                    thisButton.html('<span class="pull-left"><i class="fa fa-user"></i> Cancel Friend</span><i class="fa fa-minus-circle pull-right"></i>');
                }, function () {
                    thisButton.html(thisButtonHTML);
                });
            });
        }

        //member notificatoins page - injecting Bootstrap classes
        if ($('table.notification-settings').length) {
            $('table.notification-settings').each(function () {
                $(this).addClass('table');
            });
        }

        //clear login form
        if ($('#user-login').length) {
            $('#sidebar-user-login, #sidebar-user-pass').on('focus', function () {
                $(this).attr('placeholder', '');
            });
        }

    });//end document.ready

    $(window).resize(function () {

        //fixing hover issues on mobile
        if (isBreakpoint('xs') || isBreakpoint('sm')) {
            $('.mobile-no-hover').bind('touchend', function () {
                fixHoverOnMobile($(this));
            })
        }

        //resetting the search on resize
        if ($('.search-trigger-wrapper').length) {
            var adminBar = $('#wpadminbar');

            $('.search-trigger-wrapper').each(function () {
                var searchTriggerWrapper = $(this);
                var searchTrigger = $(this).find('.search-trigger');

                if (!searchTrigger.parent().hasClass('sliding-active')) {
                    if (searchTriggerWrapper.hasClass('search-live')) {
                        searchTrigger.parent().addClass('sliding-active');
                        var mode = searchTrigger.data('mode');
                        var searchForm = $('.search-form-wrapper.search-mode-' + mode);
                        searchForm.slideUp(700, function () {
                            searchTrigger.parent().toggleClass('search-live');
                            searchTrigger.parent().removeClass('sliding-active');
                        });

                        if (searchTrigger.data('mode') == 'mobile') {
                            adminBar.animate({
                                top: "-=" + searchForm.data('thisheight')
                            }, 700);
                        }
                    }
                }
            });
        }

    });

    $(window).load(function () {

        //setting equal rows on homepage group list
        equal_row_height();

        //camera js slider on home
        if ($('.camera_wrap').length) {
            $('.camera_wrap').camera({
                autoAdvance: false,
                fx: 'simpleFade',
                playPause: false,
                height: '400px',
                navigationHover: false
            });
        }

        var newMembers = jQuery("#home-new-member-wrap");

        //this is for the new OpenLab members slider on the homepage
        newMembers.jCarouselLite({
            btnNext: ".next",
            btnPrev: ".prev",
            vertical: false,
            visible: 2,
            auto: 4000,
            speed: 200,
        });

        $('#home-new-member-wrap').css('visibility', 'visible').hide().fadeIn(700);

    });

    $(document).ajaxComplete(function () {

        if ($('.wpcf7').length && !$('.wpcf7-mail-sent-ok').length) {
            $('.wpcf7-form-control-wrap').each(function () {
                var thisElem = $(this);
                if (thisElem.find('.wpcf7-not-valid-tip').text()) {

                    thisElem.remove('.wpcf7-not-valid-tip');

                    var thisText = 'Please enter your ' + thisElem.find('.wpcf7-form-control').attr('name');
                    var newValidTip = '<div class="bp-template-notice error" style="display: none;"><p>' + thisText + '</p></div>';

                    thisElem.prepend(newValidTip);
                    thisElem.find('.bp-template-notice.error').css('visiblity', 'visible').hide().fadeIn(550);

                }
            });
        }
        if ($('.wpcf7').length && $('.wpcf7-mail-sent-ok').length) {
            $('.wpcf7-form-control-wrap').each(function () {
                var thisElem = $(this);
                if (thisElem.find('.bp-template-notice.error')) {
                    thisElem.remove('.bp-template-notice.error');
                }
            });
        }

    });

    function create_new_related_link_field() {
        $cloned_related_link_fields = $add_new_related_link.closest('li').clone();

        // Get count of existing link fields for the iterator
        related_links_count = $('.related-links-edit-items li').length + 1;

        // Swap label:for and input:id attributes
        $cloned_related_link_fields.html(function (i, old_html) {
            return old_html.replace(/(related\-links\-)[0-9]+\-(name|url)/g, '$1' + related_links_count + '-$2');
        });

        // Swap name iterator
        $cloned_related_link_fields.html(function (i, old_html) {
            return old_html.replace(/(related\-links\[)[0-9]+(\])/g, '$1' + related_links_count + '$2');
        });

        // Remove current button from the DOM, as the cloned fields contain the new one
        $add_new_related_link.remove();

        // Add new fields to the DOM
        $('.related-links-edit-items').append($cloned_related_link_fields);

        // Remove values
        $('#related-links-' + related_links_count + '-name').val('');
        $('#related-links-' + related_links_count + '-url').val('');

        // Reindex new Add button and bind click event
        $add_new_related_link = $('#add-new-related-link');
        $add_new_related_link.on('click', function () {
            create_new_related_link_field();
        });
    }

    /*this is for the homepage group list, so that cells in each row all have the same height 
     - there is a possiblity of doing this template-side, but requires extensive restructuring of the group list function*/
    function equal_row_height() {
        /*first we get the number of rows by finding the column with the greatest number of rows*/
        var $row_num = 0;
        $('.activity-list').each(function () {
            var $row_check = $(this).find('.activity-item').length;

            if ($row_check > $row_num) {
                $row_num = $row_check;
            }
        });

        //build a loop to iterate through each row
        var $i = 1;

        while ($i <= $row_num) {
            //check each cell in the row - find the one with the greatest height
            var $greatest_height = 0;

            $('.row-' + $i).each(function () {
                var $cell_height = $(this).outerHeight();

                if ($cell_height > $greatest_height) {
                    $greatest_height = $cell_height;
                }

            });

            //now apply that height to the other cells in the row
            $('.row-' + $i).css('height', $greatest_height + 'px');

            //iterate to next row
            $i++;
        }

        //there is an inline script that hides the lists from the user on load (just so the adjusment isn't jarring) - this will show the lists
        $('.activity-list').css('visibility', 'visible').hide().fadeIn(700);

    }

    function invisibleDimensions(el) {

        $(el).css({
            'display': 'block',
            'visibility': 'hidden'
        });
        var dim = {
            height: $(el).outerHeight(),
            width: $(el).outerWidth()
        };
        $(el).css({
            'display': 'none',
            'visibility': ''
        });
        return dim;
    }

    //detection of bootstrap breakpoints
    function isBreakpoint(alias) {
        return $('.device-' + alias).is(':visible');
    }

    //fixing hover on mobile
    function fixHoverOnMobile(el)
    {
        console.log(el);
        var par = el.parentNode;
        var next = el.nextSibling;
        par.removeChild(el);
        setTimeout(function () {
            par.insertBefore(el, next);
        }, 0)
    }

})(jQuery);
