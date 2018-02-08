/**
 * Created by Administrator on 5/18/2016.
 */

jQuery(document).ready(function(){
    var start_checked =  jQuery( ".music_type input:checked" ).val();
    var license_checked =  jQuery( ".music_license input:checked" ).val();
    if(start_checked =='video' && license_checked=='free'){
        jQuery('#acf-song_video').addClass('tzshow');
    }
    if(start_checked =='video' && license_checked=='sale'){
        jQuery('#acf-song_video_cover').addClass('tzshow');
    }
    if(start_checked =='audio' && license_checked=='free'){
        jQuery('#acf-song_audio').addClass('tzshow');
    }
    if(start_checked =='audio' && license_checked=='sale'){
        jQuery('#acf-song_audio_cover').addClass('tzshow');
    }

    jQuery( ".acf-radio-list input" ).on( "click", function() {
        var license_checked =  jQuery( ".music_license input:checked" ).val();
        var type_checked = jQuery( ".music_type input:checked" ).val();
        if(license_checked =='sale' && type_checked =='audio'){
            jQuery('#acf-song_for_sale ').addClass('tzshow');
            jQuery('#acf-song_audio_cover').addClass('tzshow');
            jQuery('#acf-song_audio').removeClass('tzshow');
            jQuery('#acf-song_video').removeClass('tzshow');
            jQuery('#acf-song_video_cover').removeClass('tzshow');
        }
        if(license_checked =='sale' && type_checked =='video'){
            jQuery('#acf-song_for_sale ').addClass('tzshow');
            jQuery('#acf-song_video_cover').addClass('tzshow');
            jQuery('#acf-song_video').removeClass('tzshow');
            jQuery('#acf-song_audio').removeClass('tzshow');
            jQuery('#acf-song_audio_cover').removeClass('tzshow');
        }
        if(license_checked =='free' && type_checked =='audio'){
            jQuery('#acf-song_for_sale ').removeClass('tzshow');
            jQuery('#acf-song_audio_cover').removeClass('tzshow');
            jQuery('#acf-song_video').removeClass('tzshow');
            jQuery('#acf-song_video_cover').removeClass('tzshow');
            jQuery('#acf-song_audio').addClass('tzshow');
        }
        if(license_checked =='free' && type_checked =='video'){
            jQuery('#acf-song_for_sale ').removeClass('tzshow');
            jQuery('#acf-song_video_cover').removeClass('tzshow');
            jQuery('#acf-song_audio').removeClass('tzshow');
            jQuery('#acf-song_audio_cover').removeClass('tzshow');
            jQuery('#acf-song_video').addClass('tzshow');
        }

    })

    jQuery('#album_created').datepicker({
        dateFormat : 'dd-mm-yy'
    });
})