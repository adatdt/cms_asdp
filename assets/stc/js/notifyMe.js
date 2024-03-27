var notificationPermission = Notification.permission === "granted" ? 1 : 0;

function requestPermissionNotification() {
  Notification.requestPermission().then(function (permission) {
    // If the user accepts, let's create a notification
    if (permission === "granted") {
      notificationPermission = 1;
      $('.enable-desktop-notification').hide();
    }
  });
}

function notifyMe(title, option) {
  // Let's check if the browser supports notifications
  if (!("Notification" in window)) {
    alert("This browser does not support desktop notification");
  }

  // Let's check whether notification permissions have already been granted
  else if (Notification.permission === "granted") {
    // If it's okay let's create a notification
    var notification = new Notification(title, option);
  }

  // Otherwise, we need to ask the user for permission
  else if (Notification.permission !== "denied") {
    requestPermissionNotification()
  }

  // At last, if the user has denied notifications, and you
  // want to be respectful there is no need to bother them any more.
}