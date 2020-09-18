$(function() {
    $('input[type="file"]').on('change', function() {
        // C:\fakepath\bb.jpg => renvoi bb.jpg
        var filename = $(this).val().split('\\').pop();
        console.log(filename);

        //le next() est le label après le input
        $(this).next().text(filename);

        $('#product img').remove(); // on cleane les anciennes images

        var img = $('<img class="img-fluid" width="250" />');
        
        //on ajoute l'image dans la div qui contient le input file
        $(this).parent().parent().append(img);

        var file = this.files[0];
        //avec un filereader, on peut lire un fichier en JS
        var reader = new FileReader();
        
        reader.addEventListener('load', function(file) {
            //une fois qu'on a chargé l'image, on l'affiche dans le src
            //de la balise img
            img.attr('src', file.target.result);
        });

        reader.readAsDataURL(file);
    });
});