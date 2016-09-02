
//Instruções do Facebook
if (NoticeCont.is_singular) {
    jQuery(document).ready(function( $ ) {
        $.ajaxSetup({
            cache: true
        });
        $.getScript('//connect.facebook.net/pt_BR/all.js', function() {
            
            FB.init({
                appId: '104968829952230',
                status: false,
                xfbml: true,
                version:'v2.7'
            });
            $('#loginbutton,#feedbutton').removeAttr('disabled');
            FB.Event.subscribe('comment.remove', function(response) {
                console.log(response);
                callback_comment_face_remove(response);
            });
            FB.Event.subscribe('comment.create', function(response) {
            	//console.log(response);
                callback_comment_face_create(response);
            });
        });

        function callback_comment_face_create(comment) {
          	console.log(comment);
          	FB.api('/'+comment.href, function(response) {
				console.log(response);
		    });
          // FB.api(
          //   '/'+comment.commentID +'?access_token=EAACEdEose0cBABp2ZCWAIjs7YaZBfFxpwmwtMosmKdX958uoDjVp1jdO76lZBZBs8IZAMGjvPWCseZAAR98N8hgqXwxyeW88r9lCZBmLacpEt2ahMMa9QdtmZBB8g4jKW8RYnYiWFyG5aCkV2hXPjFtayvbgLkHT8VhhDb2i8aKtDgZDZD',
          //   function(response) {
          //        console.log(response);
          //        var ajaxurl = NoticeCont.adminpath + 'admin-ajax.php',
          //         dados = {
          //             action: 'FB_Notice_comment',
          //             comentario: response.message,
          //             comentarioID: response.id,
          //             post: NoticeCont.post,
          //             autor: response.from.name,
          //             // autorEmail: userRow.email,
          //             autorID: response.from.id
          //             // autorImg: userRow.pic_small,
          //             // autorUrl: userRow.profile_url,
          //         };
          //       $.ajax({
          //           type: 'POST',
          //           url: ajaxurl,
          //           data: dados,
          //       });
          //   }
          // );
          // FB.api(
          //   '/fql',
          //   'GET',
          //   {},
          //   function(rr) {
          //       console.log(rr);
          //   }
          // );
          // var commentQuery = FB.Data.query("SELECT text, fromid FROM comment WHERE post_fbid='" + response.commentID + "' AND object_id IN (SELECT comments_fbid FROM link_stat WHERE url='" + response.href + "')");
          // var userQuery = FB.Data.query("SELECT name, uid, pic_small, email, profile_url FROM user WHERE uid in (select fromid from {0})", commentQuery);

          // FB.Data.waitOn([commentQuery, userQuery], function() {
          //     var commentRow = commentQuery.value[0];
          //     console.log(commentRow);
          //     var userRow = userQuery.value[0];
          //     var ajaxurl = NoticeCont.adminpath + 'admin-ajax.php',
          //         dados = {
          //             action: 'FB_Notice_comment',
          //             comentario: commentRow.text,
          //             comentarioID: response.commentID,
          //             post: NoticeCont.post,
          //             autor: userRow.name,
          //             autorEmail: userRow.email,
          //             autorID: userRow.uid,
          //             autorImg: userRow.pic_small,
          //             autorUrl: userRow.profile_url,
          //         };
          //     $.ajax({
          //         type: 'POST',
          //         url: ajaxurl,
          //         data: dados,
          //     });
          // });
      }

      function callback_comment_face_remove(response) {
          var ajaxurl = NoticeCont.adminpath + 'admin-ajax.php',
              dados = {
                  action: 'FB_notice_comment_delete',
                  comentarioID: response.commentID,
                  post_id_fb: NoticeCont.post,
                  nonce_del_fb: NoticeCont.nonce_del_fb,
              }
          $.ajax({
              type: 'POST',
              url: ajaxurl,
              data: dados,
              success: function(res) {
                  //console.log(res);
              }
          });
      }
    });
}

jQuery(document).ready(function( $ ) {
    $(document).on('click', '.delete a', function() {
        var tr = $(this).parent().parent().parent().parent();
        var frase = $(this).data('message') + " ? " + "\n" + "Clique em OK para confirmar.";
        if (confirm(frase)) {
            $(tr).css({
                'background': '#F7CECE'
            });
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: "FB_notice_comment_delete_admin",
                    post_id_fb: $(this).data('idpost'),
                    nonce_del_fb: NoticeCont.nonce_del_fb,
                },
                success: function(data) {
                    $(tr).fadeOut('slow', function() {
                        $(tr).remove();
                    });

                    console.log(data);
                    $('#message').html(data);
                }
            });
        }
    });
});