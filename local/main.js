V = {
    init: function(){
        V.Form.init();
        V.Timer.init();
        V.Video.init();
        V.Popup.init();
        V.mobileCheck.init();
        V.openingVideo.init();
        if (typeof window.orientation !== 'undefined') { V.orient(); }
    },
    orient: function(){
       var landscapePlayer = null;
       videojs($('.mobile-loop-video video')[0],
           {preload: 'auto', muted: true, loop: true, playsinline: true, loadingSpinner: false, 'webkit-playsinline': true},
           function(){
               landscapePlayer = this;
               $(window).trigger('orientationchange');
           }
       );

       var userAgent = navigator.userAgent || navigator.vendor || window.opera;

       $(window).on('orientationchange', function(event){
           if(window.orientation == 0){
               $('.mobile-landscape').css({'display':'none'});
               if (/android/i.test(userAgent)) {
                   $('.androidPoster').hide();
               } else {
                   landscapePlayer.pause();
               }
           }else{
               $('.mobile-landscape').css({'display':'block'});
               if (/android/i.test(userAgent)) {
                    $('.androidPoster').show();
               } else {
                   landscapePlayer.play();
               }
           }
       });
    },

    Popup: {
        init:function(){
            $('.popup-close').on('click', function(){
                V.Popup.close();
            });

            $(document).on('click','.popup-inner .approve',function(e) {
                e.preventDefault();
                var target = $(this).parents('.popup-wrap').attr('id');

                $.ajax({
                    type: "POST",
                    url: $('input[data-target="' + target + '"]').attr('data-url'),
                    data: {name:$('input[data-target="' + target + '"]').val()},
                    success: function(data){
                        $(this).parents('.popup-wrap').find('.popup-inner > *').addClass('hidden').siblings('.success-screen').removeClass('hidden');
                        setTimeout(function(){
                            V.Popup.close(true);
                        },1500);
                    },
                    error: function(){
                        $('.popup-wrap.reveal').find('.error').removeClass('hidden')
                    }
                });
            });
            $(document).on('click','.popup-inner .approve-deactive',function(e) {
                e.preventDefault();
                var target = $(this).parents('.popup-wrap').attr('id');

                $.ajax({
                    type: "POST",
                    url: $('input[data-target="' + target + '"]').attr('data-url'),
                    data: {name:$('input[data-target="' + target + '"]').val()},
                    success: function(data){
                        $(this).parents('.popup-wrap').find('.popup-inner > *').addClass('hidden').siblings('.deactive-success-screen').removeClass('hidden');
                        setTimeout(function(){
                            V.Popup.close(true);
                        },1500)
                    },
                    error: function(){
                        $('.popup-wrap.reveal').find('.error').removeClass('hidden')
                    }
                });
            });
        },
        open:function(el){
            $('.popup-wrap#' + el).addClass('reveal');
            //$('body').addClass('unflow');
        },  
        close:function(checkControl){
            var parent = $('.popup-wrap.reveal').attr('id');
            $('.popup-wrap.reveal').removeClass('reveal');

            if(!checkControl) {
                if($('input[data-target="' + parent + '"]').prop('checked')) 
                    $('input[data-target="' + parent + '"]').prop('checked', false)
                else 
                    $('input[data-target="' + parent + '"]').prop('checked', true)
            }
        }
    },
    Video:{
		videoStarted:false,
        videoRequestComplete:false,
        countMax:20,
        countMinValue:3, 
        countMaxValue:-1, 
        decrementBy:5,
        count:0,
        videoLoadPercent : 100,
        isVideoFinished:false,
        loading : $('.loading'),
        video : $('.mobile-video video'),
        isVideoReady:false,
        player:null,
        url:'',
        init:function(){
            if($('.web-video').length > 0){
                V.Video.initWebVideo();
                return;
            }else if(V.Video.video.length == 0) return;
                
            videojs(V.Video.video[0], {'controls':true, preload: 'auto', 'poster':'assets/img/poster.jpg'}, function(){
                V.Video.isVideoReady = true;
                V.Video.player = this;

                if(V.isMobile || V.isTablet){
                    var vjsBtn = videojs.getComponent('Button');
                    var vjsPauseBtn = videojs.extend(vjsBtn, {
                        constructor: function() {
                            vjsBtn.call(this, V.Video.player);
                        },
                        handleClick: function(){
                            V.Video.player.pause();
                            $('#videoScreenPause').addClass('reveal');
                            $('.video-screen-curtain').addClass('reveal');

                            // TweenMax.to($('.rhombus-spec'), .5, {y:'0%', ease:Expo.easeOut});
                            // $('footer').removeClass('hidden');
                            // $('.menu').removeClass('spec3');
                        }
                    });

                    var vjsPauseBtnIns = V.Video.player.controlBar.addChild(new vjsPauseBtn());
                    vjsPauseBtnIns.addClass("vjs-pause-button");
                }

                V.Video.player.on('ended',function(){
                    V.Video.isVideoFinished = true;
                    $('#videoScreenEnd').addClass('reveal');
                    //$('.video-screen-curtain').addClass('reveal');
                    // $('.menu').removeClass('spec3');
                    // $('.rhombus-spec').addClass('white');
                    //$('footer').removeClass('hidden');
                    // TweenMax.to($('.rhombus-spec'), .5, {y:'0%', ease:Expo.easeOut});
                    $(window).trigger('resize');
                    // V.tealiumVideoEnd();
                });

                // V.Video.player.on('pause',function(){
                //     if(!$('.video-wrap').hasClass('hidden')){
                //         $(window).trigger('resize');
                //         $('footer').removeClass('hidden');
                //         // V.tealiumVideoPause();
                //     }
                // });

                $('.video-wrap .curtain').on('click',function(){
                    if(V.Video.player.paused()) V.Video.video[0].play();
                    else V.Video.video[0].pause();
                });

                V.Video.player.controlBar.playToggle.on('click', function(){
                    if($('.vjs-play-control.vjs-playing').length > 0){
                        $('#videoScreenPause').addClass('reveal');
                        $('.video-screen-curtain').addClass('reveal');
                        // TweenMax.to($('.rhombus-spec'), .5, {y:'0%', ease:Expo.easeOut});
                        $('footer').removeClass('hidden');
                        // $('.menu').removeClass('spec3');

                    }
                });

                V.Video.player.controlBar.playToggle.el().addEventListener('touchend', function(){
                    if($('.vjs-play-control.vjs-playing').length > 0){
                        $('#videoScreenPause').addClass('reveal');
                        $('.video-screen-curtain').addClass('reveal');
                        // TweenMax.to($('.rhombus-spec'), .5, {y:'0%', ease:Expo.easeOut});
                        $('footer').removeClass('hidden');
                        $('.logo').css("display", "block");
                        // $('.menu').removeClass('spec3');
                        var utag_data = {
                                page_name: 'Welcome:yanimda:video ara ekran',
                        };
                        
                        utag.view( utag_data );
                    }
                });

                V.Video.player.on('play',function(){
                    if($('.video-wrap').hasClass('hidden')) return;
                    V.Video.videoStarted = true;
                    // $('.rhombus-spec').removeClass('white');
                    $('footer').addClass('hidden');
                    // TweenMax.to($('.rhombus-spec'), .5, {y:'-100%', ease:Expo.easeOut});
                    $('.logo').css("display", "none");
                });

                V.Video.player.on('click', function(){
                    if($('.vjs-play-control.vjs-playing').length == 0){
                        $('#videoScreenPause').addClass('reveal');
                        $('.video-screen-curtain').addClass('reveal');
                        // TweenMax.to($('.rhombus-spec'), .5, {y:'0%', ease:Expo.easeOut});
                        $('footer').removeClass('hidden');
                        // $('.menu').removeClass('spec3');
                    }
                });

                $('.videoScreen .play').on('click',function(){
                    $('#videoScreenPause').removeClass('reveal');
                    $('.video-screen-curtain').removeClass('reveal');
                    // $('.menu').addClass('spec3');
                    V.Video.player.play();
                });

                $('.videoScreen .rewatch').on('click',function(){
                    V.Video.isVideoFinished = false;
                    $('#videoScreenEnd').removeClass('reveal');
                    $('.video-screen-curtain').removeClass('reveal');
                    // $('.menu').addClass('spec3');
                    V.Video.player.play();
                });
            });

            requestAnimationFrame(V.Video.checkVideo);
        },
        checkVideo:function(){
            var clear = false;
            var currentCount = parseInt(V.Video.loading.find('.loading-count').text());
            var loaded = 0;

            V.Video.count++;
            if(V.Video.count == parseInt(V.Video.countMax)){
                if(V.Video.countMax > V.Video.countMinValue){
                    V.Video.countMax -= V.Video.decrementBy;
                    if(V.Video.countMax < V.Video.countMinValue) V.Video.countMax = V.Video.countMinValue;
                }
                V.Video.count = 0;
                if(currentCount <= V.Video.videoLoadPercent) currentCount++;

                var countStr = Math.min(currentCount, 99);
                if(countStr < 10) countStr = '0' + countStr;
                V.Video.loading.find('.loading-count').text(countStr);
                V.Video.loading.find('.progress-bar').css('width', (countStr - 1) + '%')
            }

            if(currentCount == V.Video.videoLoadPercent){
                clear = true;
                $(window).trigger('resize');
                //$('.menu').addClass('spec2 spec3');
                V.Video.loading.remove();
                $('.mobile-video').removeClass('hidden');
                // if(V.Video.player.currentSrc() == ""){
                //     V.Video.player.src(V.Video.url);
                //     V.Video.player.preload(true);
                //     V.Video.player.play();
                //     V.Video.player.pause();
                // }
                V.Video.player.play();
                $('body').addClass('loaded');
                // V.initFullScreen();
            }

            if(!clear) requestAnimationFrame(V.Video.checkVideo);
        },
        initWebVideo: function(){
            videojs($('.web-video video')[0],
                {'controls':false, preload: 'auto', playsinline: true, loadingSpinner: false, 'webkit-playsinline': true},
                function(){
                    this.play();
                }
            );
        }
	},
    Form:{
        init:function(){
            V.Form.phoneFormVal();
            V.Form.codeFormVal();
            V.Form.serviceFormVal();
            V.Form.popup();
        },
        phoneFormVal:function(){
            $('#phoneForm').find('input[name="phone"]').mask("0599 999 99 99");
            $('#phoneForm').validate({
                rules:{
                    phone: {required: true},
                    captcha: {required: true, minlength: 6}
                },
                messages:{
                    phone: {required: ''},
                    captcha: {required: '', minlength: ''}
                }
            });
        },
        serviceFormVal:function(){
            $('.activateService').validate({
                rules:{
                    mail: {required: true, email:true}
                },
                messages:{
                    mail: {required: '', email:''}
                },
                submitHandler:function(form) {
                    form.reset();
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {email:$('input[name=mail]').val()},
                        success: function(){
                            $('.popup-wrap.reveal').find('.first-screen, .popup-close').addClass('hidden').siblings('.success-screen').removeClass('hidden');
                            setTimeout(function(){
                                V.Popup.close(true);
                            },1500)
                        }
                    });
                }
            }); 
        },
        codeFormVal:function(){
            $('#codeForm').validate({
                rules:{
                    code: {required: true, minlength: 6}
                },
                messages:{
                    code: {required: '', minlength: ''}
                }
            });
        },
        popup:function(){
            $('.toggleSwitchForm input[type="checkbox"]').change(function() {
                var target = $(this).data('target');
                var on = $(this).prop('checked');
                $('.popup-wrap .error').addClass('hidden');
                setTimeout(function(){
                    V.Popup.open(target);
                    if(!on) { // Kapamaya zorla
                        $('.popup-wrap#' + target).find('.deactive-screen, .popup-close').removeClass('hidden').siblings('.success-screen, .first-screen, .deactive-success-screen').addClass('hidden');
                    } else { // Açmaya Zorla
                        //V.Popup.close();
                        $('.popup-wrap#' + target).find('.first-screen, .popup-close').removeClass('hidden').siblings('.success-screen, .deactive-screen, .deactive-success-screen').addClass('hidden');
                    }
                },280)
            });
        }
    },
    Timer:{
        timerInterval:null,
	    timer:0,
        init:function(){
            clearInterval(V.Timer.timerInterval);
           // var minutes = 60 * 2,
            var minutes = 5 * 2,
                display = $('.timer b');
            if(display.length != 0 ) V.Timer.startTimer(minutes, display);
        },
        startTimer:function(duration, display) {
            V.Timer.timer = duration;
            V.Timer.getTimer(display);
            V.Timer.timerInterval = setInterval(function () {
                V.Timer.getTimer(display);
            }, 1000);
        },
        getTimer:function(display){
            var minutes, seconds;
            minutes = parseInt(V.Timer.timer / 60, 10)
            seconds = parseInt(V.Timer.timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.text(minutes + ":" + seconds);

            if (--V.Timer.timer < 0) {
                clearInterval(V.Timer.timerInterval);
                $('.timer').addClass('hidden');
                $('.againSend').addClass('reveal');
                $('.submitBtn').addClass('disabled');

                $('document').on('click', '.disabled', function(){
                    return false;
                });

                $('.againSend').on('click', function(){
                    $('.timer').removeClass('hidden');
                    $('.againSend').removeClass('reveal');
                    $('.submitBtn').removeClass('disabled');
                    V.Timer.init();
                    return false;
                });
            }
        }
    },
    mobileCheck: {
        init:function(){
            isMobile = {
                Android: function () {
                    return navigator.userAgent.match(/Android/i);
                },
                BlackBerry: function () {
                    return navigator.userAgent.match(/BlackBerry/i);
                },
                iOS: function () {
                    return navigator.userAgent.match(/iPhone|iPad|iPod/i);
                },
                Opera: function () {
                    return navigator.userAgent.match(/Opera Mini/i);
                },
                Windows: function () {
                    return navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/WPDesktop/i);
                },
                any: function () {
                    return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
                }
            };
            V.mobileCheck.detectMobile();
        },
        detectMobile:function(){
            if(isMobile.iOS()){
                $(".appstore").css("display", "block");
                $(".google").css("display", "none");
            }
            if(isMobile.Android()){
                $(".appstore").css("display", "none");
                $(".google").css("display", "block");
            }
        }
    },
    openingVideo: {
        init:function(){
            $('.videoOverlay').click(function(){
                var video = $('#deskVid')[0];
                video.play();
                $(this).hide();
            });
            V.openingVideo.playVideo();
        },
        playVideo:function(){
          var userAgent = navigator.userAgent || navigator.vendor || window.opera;

          $(window).on('orientationchange', function(event){
             if(window.orientation == 0){
                 $('.mobile-landscape').css({'display':'none'});
                 if (/android/i.test(userAgent)) {
                     $('.androidPoster').hide();
                 } else {
                     //landscapePlayer.pause();
                 }
             } else{
                 $('.mobile-landscape').css({'display':'block'});
                 if (/android/i.test(userAgent)) {
                      $('.androidPoster').show();
                 } else {
                     //landscapePlayer.play();
                 }
             }
          });

        }
    }
}

$(document).ready(function(){
	V.init();

    $('.menuMobile').on('click', function (event) {
        event.preventDefault();
        $(".menuOpen").fadeIn();
        setTimeout(function(){ 
           $(".menuMobile").fadeOut();
        }, 400);
        
    });

    $('.menuClose').on('click', function (event) {
        event.preventDefault();
        $(".menuOpen").fadeOut();
        setTimeout(function(){ 
           $(".menuMobile").fadeIn();
        }, 250);
    });

    $('.toggleSwitchForm input').each(function(index) {
        //setTimeout(function(){ 
            $(this).click(function() {
                var $el = $(this);
                    if ($el.hasClass('passive')){
                        $el.removeClass('passive');
                        $el.addClass('active');
                        $el.attr('checked', true);
                        $el.attr("disabled", true);
                        $el.closest('.switch').children('.slider').addClass('activeL');
                        if($el.hasClass("active")){
                            setTimeout(function(){ 
                                $el.attr("disabled", false);
                                $el.closest('.switch').children('.slider').removeClass('activeL');
                            },3000);
                        }
                        
                    } 
                    else {
                        $el.removeClass('active');
                        $el.addClass('passive');
                        $el.attr('checked', false);
                        //$el.attr("disabled", false);
                        $el.closest('.switch').children('.slider').removeClass('activeL')
                    }
            });
        //}, 3000);
    });







    var minimized_elements = $('p.infoText');
    
    minimized_elements.each(function(){    
        var t = $(this).text();        
        if(t.length < 150) return;
        
        $(this).html(
            t.slice(0,150)+'<span>... </span><a href="#" class="more">Daha fazla</a>'+
            '<span style="display:none;">'+ t.slice(150,t.length)+' <a href="#" class="less">Daha az</a></span>'
        );
        
    }); 
    
    $('a.more', minimized_elements).click(function(event){
        event.preventDefault();
        $(this).hide().prev().hide();
        $(this).next().show();        
    });
    
    $('a.less', minimized_elements).click(function(event){
        event.preventDefault();
        $(this).parent().hide().prev().show().prev().show();    
    });

})