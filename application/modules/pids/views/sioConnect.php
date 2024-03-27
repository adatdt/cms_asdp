
<script src="<?php echo base_url(); ?>assets/stc/js/socket.io/2.4.0/socket.io.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/stc/js/crypto-js.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/js/client.min.js" type="text/javascript"></script>

<script>
  // SOCKET.IO

  var socket;
  var deviceName;
  var client = new ClientJS();
  var browser_id = client.getFingerprint().toString();
  $(function() {
    const reconnectionDelayMax = 7000;
    const dashboardKey = "<?php echo $this->config->item('dashboard_socket_key') ?>";
    const pwd = CryptoJS.MD5(dashboardKey).toString().toUpperCase();
    deviceName = "<?php echo strtoupper($this->session->userdata('username')); ?>_" + browser_id;
    const credentials = CryptoJS.enc.Utf8.parse(`${deviceName}:${pwd}`);
    const auth = CryptoJS.enc.Base64.stringify(credentials);
    const transport = '<?php echo $this->config->item('socket_transport') ?>';
    const socketOpt = {
      reconnectionDelayMax: reconnectionDelayMax,
      randomizationFactor: 0,
      reconnectionDelay: 1000,
      withCredentials: true,
      "transportOptions": {
        "polling": {
          "extraHeaders": {
            "Authorization": auth,
          },
        },
      },
    }

    const WebsocketOpt = {
      reconnectionDelayMax: reconnectionDelayMax,
      randomizationFactor: 0,
      reconnectionDelay: 1000,
      transports: ["websocket"],
      query: {
        auth
      }
    }

    const opt = (transport === "websocket") ? WebsocketOpt : socketOpt;

    try {
      socket = io('<?php echo $this->config->item('socket_protocol') . $this->config->item('socket_url') ?>', opt);

      socket.on('connect', function() {
        socket.emit("client_id", deviceName);
        toastr.clear();
      });

      socket.on('error', (error) => {
        toastr.error(error, 'Computer not connected');
      });

      socket.on('connect_error', (error) => {
        toastr.error(error, 'connect error', {
          "timeOut": 5000,
        });
      });

      socket.on('reconnecting', (attemptNumber) => {
        toastr.warning('menguhubungkan ke server <span id="countdown"></span>', 'Trying to reach server<span class="loading_dots"><i></i><i></i><i></i></span>', {
          "preventDuplicates": true,
          "timeOut": 0,
          "tapToDismiss": false,
          "onclick": false,
          "closeOnHover": false,
        });
      });

      socket.on('disconnect', function() {
        setTimeout(function() {
          toastr.error('Tidak ada koneksi ke server, hubungi teknisi!', 'Computer not connected', {
            "timeOut": 3000,
          });
        }, 5000);
      });


    } catch (error) {
      toastr.error(error, 'Computer not connected');
    }
  });
</script>