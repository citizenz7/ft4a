jQuery(document).ready(function ($) {

    /* Alert Messages */
	
    $(".alert-msg .close").click(function () {
        $(this).parent().animate({
            "opacity": "0"
        }, 400).slideUp(400);
        return false;
    });

    /* Accordions */

    $(".accordion-title").click(function () {
        $(".accordion-title").removeClass("active");
        $(".accordion-content").slideUp("normal");
        if ($(this).next().is(":hidden") == true) {
            $(this).addClass("active");
            $(this).next().slideDown("normal");
        }
    });
    $(".accordion-content").hide();

    /* Toggles */

    $(".toggle-title").click(function () {
        $(this).toggleClass("active").next().slideToggle("fast");
        return false;
    });

    /* Tabs */

    $(".tab-wrapper").tabs({
        event: "click"
    })

    /* Vertically Centre Text On Images */

    $.fn.flexVerticalCenter = function (onAttribute) {
        return this.each(function () {
            var $this = $(this);
            var attribute = onAttribute || 'margin-top';
            var resizer = function () {
                $this.css(
                attribute, (($this.parent().height() - $this.height()) / 2));
            };
            resizer();
            $(window).resize(resizer);
        });
    };

    // To run the function:
    $('.viewit').flexVerticalCenter();

    /* Slider */

    $("#rslides").responsiveSlides({
        auto: true, // Boolean: Animate automatically, true or false
        speed: 300, // Integer: Speed of the transition, in milliseconds
        timeout: 4000, // Integer: Time between slide transitions, in milliseconds
        pager: false, // Boolean: Show pager, true or false
        nav: true, // Boolean: Show navigation, true or false
        random: false, // Boolean: Randomize the order of the slides, true or false
        pause: true, // Boolean: Pause on hover, true or false
        pauseControls: true, // Boolean: Pause when hovering controls, true or false
        prevText: "Previous", // String: Text for the "previous" button
        nextText: "Next", // String: Text for the "next" button
        maxwidth: "", // Integer: Max-width of the slideshow, in pixels
        navContainer: "", // Selector: Where controls should be appended to, default is after the 'ul'
        manualControls: "", // Selector: Declare custom pager navigation
        namespace: "centered-btns" // String: Change the default namespace used
    });


});