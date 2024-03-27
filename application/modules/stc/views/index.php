<div class="page-content-wrapper">
	<div class="page-content">
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<li>
					<?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
					<i class="fa fa-circle"></i>
				</li>
				<li>
					<span><?php echo $title; ?></span>
				</li>
			</ul>
			<div class="page-toolbar">
				<div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
					<span class="thin hidden-xs" id="datetime"></span>
					<script type="text/javascript">
						window.onload = date_time('datetime');
					</script>
				</div>
			</div>
		</div>
		<h1 class="page-title"></h1>
		<div class="my-div-body">
			<div id="box-portlet" class="portlet bg-white">
				<div class="portlet-title">
					<div class="caption">
						<?php echo $title ?> <b>(<?php echo $port_name ?>)</b>
					</div>
					<div class="actions">
						<!-- <a id="allow-desktop-notification" class="btn btn-circle btn-icon-only btn-default enable-desktop-notification tooltips" data-container="body" data-placement="top" data-original-title="Desktop Notification" href="javascript:;">
							<i class="icon-bell"></i>
						</a> -->
						<a class="btn btn-circle btn-icon-only btn-default reload-dock tooltips" data-container="body" data-placement="top" data-original-title="Reload" href="javascript:;"></a>
						<a class="btn btn-circle btn-icon-only btn-default fullscreen fullscreen-browser" href="javascript:;" data-original-title="" title=""> </a>
					</div>
				</div>
				<div class="portlet-body" id="box-body" style="padding: 1px 15px;min-height:468px">
					<div class="row">
						<button type="button" class="btn btn-small btn-primary hidden" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" title="Cari" id="searching" disabled style="padding: 6px 12px; font-size: 14px">Cari</button>

						<div id="padd-dock" class="col-md-12 pad-left hidden">
							<div class="row padding-10">
								<div class="col-md-3 col-sm-4 padding-10 pad-left">
									<div class="table-scrollable margin-0" style="height: 220px;">
										<table class="table table-bordered table-legend">
											<thead>
												<tr>
													<th> Warna </th>
													<th> Keterangan </th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td style="background-color: #F1C40F"></td>
													<td> Masuk Alur </td>
												</tr>
												<tr>
													<td style="background-color: #E87E04"></td>
													<td> Sandar </td>
												</tr>
												<tr>
													<td style="background-color: #337ab7"></td>
													<td> Mulai Pelayanan </td>
												</tr>
												<tr>
													<td style="background-color: #9A12B3"></td>
													<td> Selesai Pelayanan </td>
												</tr>
												<tr>
													<td style="background-color: #36c6d3"></td>
													<td> Tutup Rampdoor </td>
												</tr>
												<tr>
													<td style="background-color: #26C281"></td>
													<td> Berlayar </td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div id="listDock"></div>
							</div>
						</div>
					</div>

					<div id="listProblem" class="row hidden">
						<div class="col-md-12 pad-left" style="border-top: 1px solid #ebedf3;">
							<div class="row padding-10">
								<?php foreach ($problem as $key => $row) { ?>
									<div class="col-md-3 col-sm-4 padding-10 pad-left">
										<div class="portlet box red">
											<div class="portlet-title">
												<div class="caption">
													<?php echo $row['title'] ?>
												</div>
												<div class="tools">
													<a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
													<a href="" class="fullscreen full-view" data-original-title="" title=""> </a>
												</div>
											</div>

											<div class="portlet-body form">
												<div class="mt-element-list">
													<div class="mt-list-container list-simple max-height">
														<ul class="sortable problem" data-problem="<?php echo $row['problem'] ?>" id="<?php echo $row['id'] ?>">
														</ul>
													</div>
												</div>
											</div>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<audio id="notification">
	<source src="<?php echo base_url('assets/stc/sounds/quite-impressed-565.ogg') ?>" type="audio/ogg">
	<source src="<?php echo base_url('assets/stc/sounds/quite-impressed-565.mp3') ?>" type="audio/mpeg">
	Your browser does not support the audio element.
</audio>
<script src="<?php echo base_url(); ?>assets/stc/js/socket.io/2.4.0/socket.io.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/stc/js/notifyMe.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/stc/js/crypto-js.min.js" type="text/javascript"></script>
<script type="text/javascript">
	var sound = document.getElementById("notification");
	var socket;
	var deviceName;
	var timeout, counter;
	var toastApproveOpt = {
		"iconClass": 'approve-manifest',
		"closeButton": true,
		"newestOnTop": true,
		"positionClass": "toast-top-center",
		"timeOut": "0"
	}

	$(function() {
		const reconnectionDelayMax = 7000;
		const dashboardKey = "<?php echo $dashboard_socket_key ?>";
		const pwd = CryptoJS.MD5(dashboardKey).toString().toUpperCase();
		deviceName = "<?php echo strtoupper($this->session->userdata('username')); ?>";
		const credentials = CryptoJS.enc.Utf8.parse(`${deviceName}:${pwd}`);
		const auth = CryptoJS.enc.Base64.stringify(credentials);
		const transport = '<?php echo $socket_transport ?>';
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
			transports: ['websocket', 'polling'],
			query: {
				auth
			}
		}

		const opt = (transport === "websocket") ? WebsocketOpt : socketOpt;

		// socket 3000
		// socket3000 = io('<?php echo $_SERVER['HTTP_HOST'] . ':3000'; ?>');
		// socket3000.on('connect', function() {
		// 	socket3000.emit("client_id", deviceName);
		// });
		// socket3000.on('approve_manifest', function(data) {
		// 	sound.play();
		// 	var title = 'KMP. ' + data.ship_name.toUpperCase();
		// 	toastr.info(textInfo(data, ' oleh operator '), title, toastApproveOpt);

		// 	notifyMe(title, {
		// 		body: `Di ${data.dock_name} ${data.message} oleh operator ${data.created_by}`,
		// 		tag: `<a href>${title}</a>`,
		// 		timeout: 10
		// 	})
		// });
		// end socket 3000

		try {
			socket = io('<?php echo $socket_protocol . $socket_url ?>', opt);

			socket.on('connect', function() {
				socket.emit("client_id", deviceName);
				toastr.clear();
				clearInterval(counter);
				$('.page-content').unblock();
			});

			socket.on('error', (error) => {
				$('.page-content').block({
					message: `<h3 class="blink" style="margin: 15px;font-family: inherit;font-weight: 400;">${error}</h3>`,
					overlayCSS: {
						opacity: 0.8
					},
					css: {
						border: 'none',
						padding: '8px',
						backgroundColor: '#e43a45',
						'border-radius': '4px !important',
						'-webkit-border-radius': '4px',
						'-moz-border-radius': '4px',
						opacity: .6,
						color: '#fff'
					}
				});
			});

			socket.on('connect_error', (error) => {
				toastr.error(error, 'connect error', {
					"timeOut": 3000,
					"newestOnTop": false,
					"iconClass": "disconnect"
				});
			});

			socket.on('reconnecting', (attemptNumber) => {
				clearInterval(counter);
				toastr.warning('menguhubungkan ke server <span id="countdown"></span>', 'Trying to reach server<span class="loading-dots-fixed"><i></i><i></i><i></i></span>', {
					"iconClass": "reconnecting",
					"preventDuplicates": true,
					"timeOut": 0,
					"tapToDismiss": false,
					"onclick": false,
					"closeOnHover": false,
				});
				timeout = (reconnectionDelayMax / 1000) - 1;
				counter = setInterval(timer, 1000);
				console.log('trying reconnect...(' + attemptNumber + ')');
			});

			socket.on('disconnect', function() {
				toastr.error('Tidak ada koneksi ke server, hubungi teknisi!', 'Computer not connected', {
					"timeOut": 3000,
					"iconClass": "disconnect",
					"preventDuplicates": true
				});

				$('.status').each(function(i, obj) {
					$(this).removeClass('font-green').addClass('font-red');
					$(this).parents().siblings('.status-label').addClass('blink');
					$(this).parents().siblings('.connection').val('0');
				});
			});

			setInterval(function() {
				$('.toast-time').each(function() {
					$(this).text(pretty($(this).data('time')));
				});

				$('.problem-ago').each(function() {
					$(this).text(timeAgo($(this).data('created')));
				});
			}, 10 * 1000);

			socket.on('approve_manifest', function(data) {
				sound.play();
				var title = 'KMP. ' + data.ship_name.toUpperCase();
				toastr.info(textInfo(data, ' oleh operator '), title, toastApproveOpt);

				notifyMe(title, {
					body: `Di ${data.dock_name} ${data.message} oleh operator ${data.created_by}`,
					tag: `<a href>${title}</a>`,
					timeout: 10
				})
			});

			// $('.ahref-problem').trigger('click');

			var listDermaga = function() {
				$.ajax({
					url: 'stc/list_dock',
					data: {
						date: '<?php echo date('Y-m-d'); ?>',
						port: '<?php echo $port_id; ?>',
					},
					type: 'POST',
					dataType: 'json',

					beforeSend: function() {
						$('#box-body').block({
							message: '<h4>Memuat data<span class="loading-dots-fixed"><i></i><i></i><i></i></span></h4>',
							overlayCSS: {
								opacity: 0.4
							},
							css: {
								border: 'none',
								padding: '8px',
								backgroundColor: '#000',
								'border-radius': '4px !important',
								'-webkit-border-radius': '4px',
								'-moz-border-radius': '4px',
								opacity: .6,
								color: '#fff'
							}
						});
					},

					success: function(d) {
						if (d.code === 1) {
							$('#listProblem').removeClass('hidden');
							dataUL = d.data.schedule;
							anchor = d.data.anchor;
							docking = d.data.docking;
							broken = d.data.broken;
							html_dock = '';

							for (i in dataUL) {
								dataList = {
									data: dataUL[i].schedule,
									terminal_code: dataUL[i].gate,
									name: i,
									type: 'dock',
									action: d.data.action,
									identity_app: d.data.identity_app
								}

								html_dock += listDock(dataList);
							}

							if (anchor.length) {
								html_a = ulLi({
									data: anchor,
								});
							} else {
								html_a = '<li class="mt-list-item uppercase grid-center" style="grid-template-rows: 165px">Data tidak ditemukan</li>';
							}

							if (docking.length) {
								html_d = ulLi({
									data: docking,
								});
							} else {
								html_d = '<li class="mt-list-item uppercase grid-center" style="grid-template-rows: 165px">Data tidak ditemukan</li>';
							}

							if (broken.length) {
								html_b = ulLi({
									data: broken,
								});
							} else {
								html_b = '<li class="mt-list-item uppercase grid-center" style="grid-template-rows: 165px">Data tidak ditemukan</li>';
							}

							$('#list-anchor').html(html_a);
							$('#list-docking').html(html_d);
							$('#list-broken').html(html_b);

							$("#padd-dock").removeClass("hidden");
							$('#listProblem').removeClass('hidden');
							$('#listDock').html(html_dock);
						} else if (d.code === 2) {
							$('#listProblem').addClass('hidden');
							App.alert({
								place: 'append', // append or prepent in container 
								type: 'warning', // alert's type 
								message: d.message, // alert's message
								close: false, // make alert closable 
								reset: true, // close all previouse alerts first 
								focus: true, // auto scroll to the alert after shown 
								icon: 'fa fa-warning' // put icon class before the message 
							});
						} else {
							$('#listProblem').addClass('hidden');
							$('#listDock').html('');
							$('#box-portlet').remove();
							App.alert({
								place: 'append', // append or prepent in container 
								type: 'danger', // alert's type 
								message: d.message, // alert's message
								close: false, // make alert closable 
								reset: true, // close all previouse alerts first 
								focus: true, // auto scroll to the alert after shown 
								icon: 'fa fa-warning' // put icon class before the message 
							});
						}
					},

					error: function(xhr, status, error) {
						toastr.error(`(${xhr.status}) ${xhr.statusText}`, 'Gagal');
					},

					complete: function() {
						$('#box-body').unblock();
					}
				}).done(function() {
					$('.ahref').trigger('click');

					// responsive font size ship name
					setTimeout(() => {
						$('.padd').each(function() {
							let paddTime = $(this).find('.padd-time');
							let paddShip = $(this).find('.padd-ship');
							let diff = $(this).width() - paddTime.width() - paddShip.width();
							paddShip.css('font-size', getFontSize(diff));
						});
					}, 1);
				})
			}

			$('#searching').click(function() {
				listDermaga();
			})

			$('.reload-dock').click(function() {
				listDermaga();
			})

			$('.fullscreen-browser').click(function() {
				toggleFullScreen();
			})

			listDermaga();

			setInterval(function() {
				var today = new Date();
				var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
				if (time == '0:0:0') {
					listDermaga();
				}
			}, 1000);

		} catch (error) {
			$('#box-portlet').block({
				message: `<h3 class="blink" style="margin: 15px;font-family: inherit;font-weight: 400;">${error}</h3>`,
				overlayCSS: {
					opacity: 0.8
				},
				css: {
					border: 'none',
					padding: '8px',
					backgroundColor: '#e43a45',
					'border-radius': '4px !important',
					'-webkit-border-radius': '4px',
					'-moz-border-radius': '4px',
					opacity: .6,
					color: '#fff'
				}
			});
		}

		$('#allow-desktop-notification').on('click', function() {
			requestPermissionNotification();
		});

		if (notificationPermission != 1) {
			$('.enable-desktop-notification').removeClass('hidden');
		}

		$(document).on("click", ".full-view", function() {
			$(this).parent().parent().parent().find('.mt-list-container').toggleClass('max-height');
			$(this).parent().parent().parent().find('.padd').each(function() {
				let paddTime = $(this).find('.padd-time');
				let paddShip = $(this).find('.padd-ship');
				let diff = $(this).width() - paddTime.width() - paddShip.width();
				paddShip.css('font-size', getFontSize(diff));
			});
		})
	});

	function getFontSize(diffLength) {
		fontSize = 1;
		if (diffLength < 15) {
			if ($(window).innerWidth() < 1026) {
				fontSize = .75;
			} else {
				fontSize = .85;
			}
		}
		return `${fontSize}em`;
	};

	function replaceAll(str, find, replace) {
		return str.replace(new RegExp(find, 'g'), replace);
	}

	function listDock(z) {
		terminal = z.terminal_code || 0;
		str = replaceAll(z.name, ' ', '_');
		html = '<div class="col-md-3 col-sm-4 padding-10 pad-left">\
            <div class="portlet box grey-cascade">\
                <div class="portlet-title">\
                    <div class="caption"> ' + z.name + '</div>\
										<div class="tools">\
											<a href="" class="fullscreen full-view" data-original-title="" title="Fullscreen"> </a>\
										</div>\
                </div>\
                <div class="portlet-body form">\
                    <div class="mt-element-list">\
                        <div class="mt-list-container list-simple max-height" id="' + str + '">\
                            <ul class="sortable drag">\
                                ' + ulLi(z) + '\
                            </ul>\
                        </div>\
                    </div>\
                </div>\
            </div>\
        </div>';
		return html;
	}

	function ulLi(x) {
		html = '';
		data = x.data;
		if (data.length > 0) {
			for (a in data) {
				ship_name = data[a].ship_name == null ? '' : data[a].ship_name;
				if (x.type == 'dock') {
					if (data[a].real_plot_date == null && data[a].real_dock_date == null && data[a].real_open_boarding_date == null) {
						classBtn = 'class="btn btn-default padd-btn uppercase"';
						classShip = 'class="padd padd-default uppercase"';
						dataPlot = 'data-ploting="1"'
					} else if (data[a].real_plot_date != null && data[a].real_dock_date == null)

					{
						classBtn = 'class="btn btn-warning padd-btn uppercase"';
						classShip = 'class="padd padd-plot uppercase"';
						dataPlot = 'data-ploting="2"'
					} else if ((data[a].real_plot_date != null && data[a].real_dock_date != null && data[a].real_open_boarding_date == null) || (data[a].real_plot_date == null && data[a].real_dock_date != null && data[a].real_open_boarding_date == null))

					{
						classBtn = 'class="btn yellow-gold padd-btn uppercase"';
						classShip = 'class="padd padd-docking uppercase"';
						dataPlot = 'data-ploting="3"'
					} else if ((data[a].real_open_boarding_date != null && data[a].real_plot_date == null) || (data[a].real_plot_date != null && data[a].real_dock_date != null && data[a].real_open_boarding_date != null && data[a].real_close_boarding_date == null))

					{
						classBtn = 'class="btn btn-primary padd-btn uppercase"';
						classShip = 'class="padd padd-boarding uppercase"';
						dataPlot = 'data-ploting="4"'
					} else if (data[a].real_plot_date != null && data[a].real_dock_date != null && data[a].real_open_boarding_date != null && data[a].real_close_boarding_date != null && data[a].real_ramp_door_date == null)

					{
						classBtn = 'class="btn purple-seance padd-btn uppercase"';
						classShip = 'class="padd padd-close uppercase"';
						dataPlot = 'data-ploting="5"'
					} else if (data[a].real_plot_date != null && data[a].real_dock_date != null && data[a].real_open_boarding_date != null && data[a].real_close_boarding_date != null && data[a].real_ramp_door_date != null && data[a].real_sail_close_date == null)

					{
						classBtn = 'class="btn btn-success padd-btn uppercase"';
						classShip = 'class="padd padd-rampdoor uppercase"';
						dataPlot = 'data-ploting="6"'
					} else {
						classBtn = 'class="btn green-jungle padd-btn uppercase"';
						classShip = 'class="padd padd-sail uppercase"';
						dataPlot = 'data-ploting="7"'
					}

					if (data[a].real_sail_close_date == null) {
						icon = '<span class="icon-note" title="Edit"></span>';
					} else {
						icon = '<span class="fa fa-check-square-o" title="Detail"></span>';
					}

					btn_edit = (x.action && x.identity_app) ? '<button type="button" ' + classBtn + ' onClick="showModal(\'' + data[a].url + '\')" ' + data[a].disabled + '>' + icon + '</button>' : '';
					ship_display_name = ship_name || '-';

					html += '<li class="mt-list-item" ' + dataPlot + ' data-param="' + data[a].param + '" title="' + ship_name + '">\
                <div ' + classShip + '><span class="padd-time">' + data[a].docking_time + '</span><span class="padd-ship ellipsis">' + ship_display_name + '</span></div>' + btn_edit + '\
                </li>';
				} else {
					html += '<li class="mt-list-item" data-param="' + data[a].param + '">\
								<div class="padd padd-default" title="' + ship_name + '"><span class="padd-time problem-ago text-danger" data-created="' + Date.parse(data[a].created_on) + '">' + timeAgo(Date.parse(data[a].created_on)) + '</span><span class="padd-ship uppercase ellipsis">' + ship_name + '</span></div>\
                </li>';
				}
			}
		} else {
			html += '<li class="mt-list-item uppercase grid-center" style="grid-template-rows: 165px;grid-template-columns: auto;">Jadwal tidak tersedia</li>';
		}

		return html
	}

	// countdown toasts connecting to server
	function timer() {
		timeout -= 1;
		if (timeout <= 0) {
			clearInterval(counter);
			document.getElementById("countdown").innerHTML = "..."
			return;
		}
		document.getElementById("countdown").innerHTML = " dalam " + timeout + " detik";
	}

	function timeAgo(date) {

		var seconds = Math.floor((new Date() - date) / 1000);

		var interval = seconds / 31536000;

		if (interval > 1) {
			return Math.floor(interval) + " years";
		}
		interval = seconds / 2592000;
		if (interval > 1) {
			return Math.floor(interval) + " months";
		}
		interval = seconds / 86400;
		if (interval > 1) {
			return Math.floor(interval) + " days";
		}
		interval = seconds / 3600;
		if (interval > 1) {
			return Math.floor(interval) + " hours";
		}
		interval = seconds / 60;
		if (interval > 1) {
			return Math.floor(interval) + " minutes";
		}
		return Math.floor(seconds) + " seconds";
	}
</script>