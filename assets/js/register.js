$(document).ready(function() {

  $("#signup").on('click', function() {
    $("#first").slideUp("slow", function(){
      $("#second").slideDown("slow");
    });
  });

  $("#signin").on('click', function() {
    $("#second").slideUp("slow", function(){
      $("#first").slideDown("slow");
    });
  });

});
