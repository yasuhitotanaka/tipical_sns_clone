$(document).ready(function(){
  $('#submit_profile_post').on('click', function(){
    $.ajax({
      type: "POST",
      url: "includes/handlers/ajax/submit_profile_post.php",
      data: $('form.profile_post').serialize(),
      success: function(msg) {
        $('#post_form').modal('hide');
        location.reload();
      },
      error: function() {
        alert('Faliure');
      },
    });
  });
});

function getUsers(value, user) {
  $.post("includes/handlers/ajax/friend_search.php",
        {query:value, userLoggedIn:user},
        function(data){
          $(".results").html(data);
        });
}

function getDropdownData(user, type) {
  if($(".dropdown_data_window").css("height") == "0px") {
    var pageName;

    if(type == 'notification') {

    } else if (type == 'message') {
      pageName = "load_messages.php";
      $("span").remove("#unread_message");
    }
    var ajaxreq = $.ajax({
      url: "includes/handlers/ajax/" + pageName,
      type: "POST",
      data: "page=1&userLoggedIn=" + user,
      cache: false,

      success: function(response) {
        $(".dropdown_data_window").html(response);
        $(".dropdown_data_window").css({"height": "280px"});
        $("#dropdown_data_type").val(type);
      }
    });
  } else {
    $(".dropdown_data_window").html("");
    $(".dropdown_data_window").css({"height": "0px"});
  }
}
