
(function() {
    tinymce.PluginManager.add('music_press_shortcode_btn', function( editor, url ) {
        editor.addButton( 'music_press_shortcode_btn', {
            text: 'Music Press',
            type: 'menubutton',
            icon: 'icon',
            image: url + '/icon.png',
            menu: [
                {
                    text: 'Add Album',
                    value: 'Add Album',
                    onclick: function() {
                        editor.windowManager.open( {
                            title: 'Insert Album',
                            width:380,
                            height:250,
                            html :
                            '<div id="albums-select" class="album-popup" tabindex="-1">' +
                            jQuery('.album_select').html()+
                            '</div>',

                            buttons: [
                                {
                                    text: 'Cancel',
                                    onclick: 'close'
                                },
                                {
                                    text: 'Save Album',
                                    onclick: function(){
                                        //some code here that modifies the selected node in TinyMCE
                                        tinyMCE.activeEditor.setContent('[music_press_album album_id="'+ jQuery('#albums-select .albums').val()+'"  autoplay="'+ jQuery('#albums-select .album_autoplay').val()+'"]');
                                        tinymce.activeEditor.windowManager.close();
                                    }
                                }
                            ]
                        });
                    }
                }
                ]

        });
    });
})();