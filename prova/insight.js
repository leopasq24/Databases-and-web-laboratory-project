$(document).ready(function(){
    $("p.nome_blog").on("click", function(){
        var idBlog = $(this).data("blog-id");
        window.location.href = "singolo_blog.php?id=" + idBlog;
    });
    $("img.img_blog").on("click", function(){
        var idBlog = $(this).data("blog-id");
        window.location.href = "singolo_blog.php?id=" + idBlog;
    });
    $(".tuo_blog_pop").on("click", function(){
        var idBlog = $(this).data("blog-id");
        window.location.href = "singolo_blog.php?id=" + idBlog;
    });

    $("input#cerca_like").on("click", function() {
        $("label#cerca_like").addClass("selected");
        $("label#cerca_dislike").removeClass("selected");
        $("label#cerca_commenti").removeClass("selected");
        $(this).closest(".filtri_interazione").siblings(".results_interazione").html(utenti_attivi_positivi);
    })
    $("input#cerca_dislike").on("click", function() {
        $("label#cerca_like").removeClass("selected");
        $("label#cerca_dislike").addClass("selected");
        $("label#cerca_commenti").removeClass("selected");
        $(this).closest(".filtri_interazione").siblings(".results_interazione").html(utenti_attivi_negativi);
    })
    $("input#cerca_commenti").on("click", function() {
        $("label#cerca_like").removeClass("selected");
        $("label#cerca_dislike").removeClass("selected");
        $("label#cerca_commenti").addClass("selected");
        $(this).closest(".filtri_interazione").siblings(".results_interazione").html(utenti_attivi_commenti);
    })

});