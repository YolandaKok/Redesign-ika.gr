function openChoice(evt, choice) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace("active", "");
    }
    document.getElementById(choice).style.display = "block";
    evt.currentTarget.className += "active";
}


$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();

    $('#click').click(function(){
        $('#progressBar').val(60);
    });
    // Get the element with id="defaultOpen" and click on it
});
