$(function () {
  $(".follow-btn").click(function () {
    let followID = $(this).data("follow");
    $button = $(this);
    if ($(this).hasClass("follow")) {
      $.post(
        BASE_URL + "follow.php",
        {
          followID: followID,
        },
        function (data) {
          $button.removeClass("follow").addClass("unfollow");
          $button.text("Following");
        }
      );
    } else {
      $.post(
        BASE_URL + "follow.php",
        {
          unfollowID: followID,
        },
        function (data) {
          $button.removeClass("unfollow").addClass("follow");
          $button.text("Follow");
        }
      );
    }
  });
});
