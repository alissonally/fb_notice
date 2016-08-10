//Load Template
(function($){

   var dados = {
        action: 'comments_fb',
        //pag: currentPage,
        //paginas: total_paginas
    }
    $.ajax({
        type: 'GET',
        url: NoticeCont.adminpath+'admin-ajax.php',
        data: dados,
        dataType:'json',
        success: function (cometarios_face) {
            console.log(cometarios_face);
             template = $('#template').html(),

            //tell Mustache.js to iterate through the JSON and insert the data into the HTML template
            output = Mustache.render(template, cometarios_face);

            //append the HTML template to the DOM
            $('#container').append(output);
        }
     }); 
   
})(jQuery);