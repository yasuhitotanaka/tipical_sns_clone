$(document).ready(function(){

  // extend search bar when focusing
  $('#search_text_input').focus(function(){
    if(window.matchMedia("(min-width: 800px)").matches) {
      $(this).animate({width: '250px'}, 500);
    }
  });

  $('.button_holder').on('click', function(){
    document.search_form.submit();
  });

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

$(document).click(function(e){
  if(e.target.class != "search_results" && e.target.id != "search_text_input") {
    $(".search_results").html("");
    $('.search_results_footer').html("");
    $('.search_results_footer').toggleClass("search_results_footer_empty");
    $('.search_results_footer').toggleClass("search_results_footer");
  }

  if(e.target.class != "dropdown_data_window") {
    $(".dropdown_data_window").html("");
    $(".dropdown_data_window").css({"padding": "0px", "height": "0px"});
  }


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
      pageName = "load_notifications.php";
      $("span").remove("#unread_notification");

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

function getLiveSearchUsers(value, user) {
  $.post("includes/handlers/ajax/search.php",
    {query:value, userLoggedIn: user}, function(data) {
      if($(".search_results_footer_empty")[0]) {
        $(".search_results_footer_empty").toggleClass("search_results_footer");
        $(".search_results_footer_empty").toggleClass("search_results_footer_empty");
      }

      $('.search_results').html(data);
      $('.search_results_footer').html("<a href='search.php?q='" + value + "'>See all results</a>");

      if(data = "") {
        $('.search_results_footer').html("");
        $('.search_results_footer').toggleClass("search_results_footer_empty");
        $('.search_results_footer').toggleClass("search_results_footer");

      }
    });
}
