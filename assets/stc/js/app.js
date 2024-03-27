alertify.defaults = {
  // dialogs defaults
  autoReset: true,
  basic: false,
  closable: true,
  closableByDimmer: true,
  invokeOnCloseOff: false,
  frameless: false,
  defaultFocusOff: false,
  maintainFocus: true, // <== global default not per instance, applies to all dialogs
  maximizable: true,
  modal: true,
  movable: false,
  moveBounded: false,
  overflow: true,
  padding: true,
  pinnable: true,
  pinned: true,
  preventBodyShift: false, // <== global default not per instance, applies to all dialogs
  resizable: true,
  startMaximized: false,
  transition: 'pulse',
  transitionOff: false,
  tabbable: 'button:not(:disabled):not(.ajs-reset),[href]:not(:disabled):not(.ajs-reset),input:not(:disabled):not(.ajs-reset),select:not(:disabled):not(.ajs-reset),textarea:not(:disabled):not(.ajs-reset),[tabindex]:not([tabindex^="-"]):not(:disabled):not(.ajs-reset)',  // <== global default not per instance, applies to all dialogs

  // notifier defaults
  notifier: {
    // auto-dismiss wait time (in seconds)  
    delay: 5,
    // default position
    position: 'top-right',
    // adds a close button to notifier messages
    closeButton: false,
    // provides the ability to rename notifier classes
    classes: {
      base: 'alertify-notifier',
      prefix: 'ajs-',
      message: 'ajs-message',
      top: 'ajs-top',
      right: 'ajs-right',
      bottom: 'ajs-bottom',
      left: 'ajs-left',
      center: 'ajs-center',
      visible: 'ajs-visible',
      hidden: 'ajs-hidden',
      close: 'ajs-close'
    }
  },

  // language resources 
  glossary: {
    // dialogs default title
    title: 'Perhatian!',
    // ok button text
    ok: 'Ya',
    // cancel button text
    cancel: 'Batal'
  },

  // theme settings
  theme: {
    // class name attached to prompt dialog input textbox.
    input: 'ajs-input',
    // class name attached to ok button
    ok: 'ajs-ok',
    // class name attached to cancel button 
    cancel: 'ajs-cancel'
  },
  // global hooks
  hooks: {
    // invoked before initializing any dialog
    preinit: function (instance) { },
    // invoked after initializing any dialog
    postinit: function (instance) { },
  },
};

function toggleFullScreen() {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen();
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    }
  }
}

function textInfo(data, conjunction = '') {
  var timestamp = new Date(data.created_on);
  var text = $('<strong>').html(data.dock_name);
  var t = $('<span>');
  t.append('Di ');
  t.append(text);
  t.append(` ${data.message} <br>  ${conjunction} (${data.created_by})`);
  t.append($('<span class="toast-time">')
    .data('time', timestamp.getTime())
    .text(pretty(timestamp.getTime()) || 'Baru saja'));

  return t;
}

function capitalize(s) {
  if (typeof s !== 'string') return ''
  return s.charAt(0).toUpperCase() + s.slice(1)
}

// from ejohn.org/blog/javascript-pretty-date/
function pretty(timestamp) {
  var date = new Date(parseInt(timestamp, 10)),
    diff = (((new Date()).getTime() - date.getTime()) / 1000),
    day_diff = Math.floor(diff / 86400);
  if (isNaN(day_diff) || day_diff < 0 || day_diff >= 31) return;
  return day_diff == 0 && (
    diff < 60 && "Baru saja" ||
    diff < 120 && "1 mnt" ||
    diff < 3600 && Math.floor(diff / 60) + "mnt" ||
    diff < 7200 && "1 jam" ||
    diff < 86400 && Math.floor(diff / 3600) + "jam") ||
    day_diff == 1 && "1 hr" ||
    day_diff < 7 && day_diff + "hr" ||
    day_diff < 31 && Math.ceil(day_diff / 7) + "mgg" ||
    date;
}

function formatTime(str, plusHour = 0) {
  myTime = new Date(str);
  strTime =
    ("0" + parseInt(myTime.getHours() + plusHour)).slice(-2) +
    ":" +
    ("0" + myTime.getMinutes()).slice(-2) +
    ":" +
    ("0" + myTime.getSeconds()).slice(-2);

  return strTime;
}

function formatDateId(str) {
  var hari = [
    "Minggu",
    "Senin",
    "Selasa",
    "Rabu",
    "Kamis",
    "Jum&#39;at",
    "Sabtu",
  ];
  var bulan = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
  ];

  splitDate = str.split("-");
  myDate = new Date(splitDate[0], splitDate[1] - 1, splitDate[2]);

  var hari = hari[myDate.getDay()];
  var tanggal = myDate.getDate();
  var bulan = bulan[myDate.getMonth()];
  var tahun =
    myDate.getFullYear() < 1000
      ? myDate.getFullYear() + 1900
      : myDate.getFullYear();

  return hari + ", " + tanggal + " " + bulan + " " + tahun;
}