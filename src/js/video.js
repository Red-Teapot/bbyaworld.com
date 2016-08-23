$(function() {
    if($(document).width() > 769
       && $('body').hasClass('carousel-video')) {

        var video = $(document.createElement('video'));
        video.hide();
        video.attr('playsinline', true);
        video.attr('autoplay', true);
        video.attr('muted', true);
        video.attr('loop', true);

        var source_webm = $(document.createElement('source'));
        source_webm.attr('src', '/assets/videos/bg_video.webm');
        source_webm.attr('type', 'video/webm');

        var source_mp4 = $(document.createElement('source'));
        source_mp4.attr('src', '/assets/videos/bg_video.mp4');
        source_mp4.attr('type', 'video/mp4');

        video.append(source_webm);
        video.append(source_mp4);
        $('#carousel-background').append(video);
        video.fadeIn(1000);
    }
});
