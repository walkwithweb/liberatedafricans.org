$(document).ready(function(){
    /*
    * Change link colours based on page url visited
    */
    var sectionID = document.getElementsByTagName("section")[0].id;
    if(sectionID=="home"){
        /* Make navbar collapsible button dark bg */
        $("nav").removeClass("navbar-light").addClass("navbar-dark");
        $("#hLogo").attr("src","../assets/img/wordmark_white.png");
    }
    
});