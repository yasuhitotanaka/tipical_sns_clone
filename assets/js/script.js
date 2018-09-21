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
