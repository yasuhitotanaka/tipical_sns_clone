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
