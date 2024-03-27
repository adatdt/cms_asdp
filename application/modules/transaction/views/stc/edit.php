<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style>
	.form .form-bordered .form-group {
		margin: 0;
		border-bottom: 1px solid #efefef;
	}

	.btn-done {
		background-color: #11c211 !important;
		border-color: #11c211 !important;
		color: white !important;
	}

	.modal {
		left: 50%;
		bottom: auto;
		right: auto;
		padding: 0;
		width: 300px;
		margin-left: -10%;
		background-color: #ffffff;
		border: 1px solid #999999;
		border: 1px solid rgba(0, 0, 0, 0.2);
		border-radius: 6px;
		-webkit-box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
		box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
		background-clip: padding-box;
	}

	.modal-header {
		padding: 7px;
		border-bottom: 1px solid #e5e5e5;
	}

	.modal-footer {
		padding: 7px;
		text-align: right;
		border-top: 1px solid #e5e5e5;
	}

	.portlet.light>.portlet-title>.caption>i::before {
		font-size: 32px;
	}

	.portlet>.portlet-title>.caption>i {
		margin-top: 2px;
	}

	/* .input-switch i::before {
		color: #e7505a;
	} */
	.font-green {
		color: #11c211 !important;
	}

	.fa::before {
		font-size: 16px;
		line-height: 16px;
	}

	[class^="flaticon-"]::before,
	[class*=" flaticon-"]::before,
	[class^="flaticon-"]::after,
	[class*=" flaticon-"]::after {
		margin-left: 0;
	}

	.blink {
		animation: blinker 1s alternate infinite ease-in-out;
	}

	@keyframes blinker {
		50% {
			color: #e7505a;
		}
	}

	.alertify-message {
		font-family: 'Open Sans';
	}
</style>
<div class="col-md-10 col-md-offset-1">
	<div class="portlet box blue box-edit">
		<div class="portlet-title">
			<div class="caption"><?php echo $title ?></div>
			<div class="tools">
				<button id="close" type="button" class="btn btn-box-tool btn-xs btn-primary"><i class="fa fa-times"></i></button>
			</div>
		</div>
		<div class="portlet-body">
			<div class="box-body">
				<div class="form-group">
					<div class="row">
						<div class="col-md-3 form-group">
							<label>Nama Kapal</label>
							<?php $disabled = ($schedule->ploting_date || $schedule->docking_date || $schedule->open_boarding_date) ? 'disabled' : ''; ?>
							<select class="form-control select2" data-placeholder="Pilih Kapal" id="ship" <?php echo $disabled ?>>
								<option></option>
								<?php foreach ($ship as $value) { ?>
									<option value="<?php echo $this->enc->encode($value->id); ?>" <?php echo $value->id == $id_ship ? "selected" : "" ?>><?php echo strtoupper($value->name); ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-3 col-md-offset-6 form-group">
							<div style="font-size: 20pt; font-weight: bold; padding-top: 0px"><?php echo $dock_name; ?></div>
							<div style="color: green; font-weight: bold;"><?php echo $status_boarding ?></div>
						</div>

						<div class="col-md-12"></div>
						<?php $cek_kapal = 'Cek kapal yang anda pilih sudah benar?';
						if ($schedule) {
							//1 
							if ($schedule->ploting_date) {
								$dis1 = "disabled";
								$btn1 = "btn-done";
							} else {
								$dis1 = "data-ploting='{$this->enc->encode(1)}'";
								$btn1 = "btn-primary";
							}

							//2
							if (($schedule->ploting_date && $schedule->docking_date) || ($schedule->docking_date)) {
								$dis2 = "disabled";
								$btn2 = "btn-done";
							}

							// elseif(empty($schedule->ploting_date) && empty($schedule->docking_date)){
							//     $dis2 = "disabled";
							//     $btn2 = "";
							// }

							elseif (empty($schedule->ploting_date) && empty($schedule->docking_date) && $schedule->open_boarding_date) {
								$dis2 = "disabled";
								$btn2 = "";
							} else {
								$dis2 = "data-ploting='{$this->enc->encode(2)}'";
								$btn2 = "btn-primary";
							}

							//3
							if (($schedule->ploting_date && $schedule->docking_date && $schedule->open_boarding_date) || (empty($schedule->ploting_date) && empty($schedule->docking_date) && $schedule->open_boarding_date) || ($schedule->ploting_date && empty($schedule->docking_date) && $schedule->open_boarding_date)) {
								$dis3 = "disabled";
								$btn3 = "btn-done";
							}

							// elseif(empty($schedule->docking_date) && $schedule->ploting_date && empty($schedule->open_boarding_date) || ($schedule->docking_date && empty($schedule->ploting_date) && empty($schedule->open_boarding_date))){
							//     $dis3 = "disabled";
							//     $btn3 = "";
							// }

							else {
								$dis3 = "data-ploting='{$this->enc->encode(3)}'";
								$btn3 = "btn-primary";
							}

							//4
							// if($schedule->ploting_date && $schedule->docking_date && $schedule->open_boarding_date && $schedule->close_boarding_date)
							// {
							//     $dis4 = "disabled";
							//     $btn4 = "btn-done";
							// }

							// elseif((empty($schedule->open_boarding_date) && empty($schedule->close_boarding_date)) || ($schedule->open_boarding_date && empty($schedule->ploting_date) && empty($schedule->docking_date)) || ($schedule->open_boarding_date && $schedule->ploting_date && empty($schedule->docking_date))){
							//     $dis4 = "disabled";
							//     $btn4 = "";
							// }

							if ($schedule->open_boarding_date && $schedule->close_boarding_date) {
								$dis4 = "disabled";
								$btn4 = "btn-done";
							} elseif ((empty($schedule->open_boarding_date) && empty($schedule->close_boarding_date)) || (empty($schedule->open_boarding_date) && empty($schedule->ploting_date) && empty($schedule->docking_date)) || (empty($schedule->open_boarding_date) && ($schedule->ploting_date) && empty($schedule->docking_date)) || ($schedule->open_boarding_date && empty($schedule->ploting_date) && empty($schedule->docking_date))) {
								$dis4 = "disabled";
								$btn4 = "";
							} else {
								$dis4 = "data-ploting='{$this->enc->encode(4)}'";
								$btn4 = "btn-primary";
							}

							//5
							if ($schedule->ploting_date && $schedule->docking_date && $schedule->open_boarding_date && $schedule->close_boarding_date && $schedule->close_ramp_door_date) {
								$dis5 = "disabled";
								$btn5 = "btn-done";
							} elseif (empty($schedule->close_boarding_date) && empty($schedule->close_ramp_door_date) || empty($schedule->docking_date) || empty($schedule->ploting_date)) {
								$dis5 = "disabled";
								$btn5 = "";
							} else {
								$dis5 = "data-ploting='{$this->enc->encode(5)}'";
								$btn5 = "btn-primary";
							}

							//6
							if ($schedule->ploting_date && $schedule->docking_date && $schedule->open_boarding_date && $schedule->close_boarding_date && $schedule->close_ramp_door_date && $schedule->sail_date) {
								$dis6 = "disabled";
								$btn6 = "btn-done";
							} elseif (empty($schedule->close_ramp_door_date) && empty($schedule->sail_date)) {
								$dis6 = "disabled";
								$btn6 = "";
							} else {
								$dis6 = "data-ploting='{$this->enc->encode(6)}'";
								$btn6 = "btn-primary";
							}
						?>
							<div id="btnAction">
								<div class="col-md-2 form-group">
									<label>Waktu : <br /> <?php echo $schedule->ploting_date ? $schedule->ploting_date : '&nbsp' ?></label>
									<button data-info="1" id="btn-ploting" data-sail="0" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(1) ?>" data-code="101" data-messageptc="Masuk Alur" type="button" class="btn btn-default form-control btn-schedule <?php echo $btn1 ?>" <?php echo $dis1 ?> data-message="Kapal sudah masuk alur? cek kembali kapal yang anda pilih, apakah sudah sesuai?">Masuk Alur</button>
								</div>
								<div class="col-md-2 form-group">
									<label>Waktu : <br /> <?php echo $schedule->docking_date ? $schedule->docking_date : '&nbsp' ?></label>
									<button data-info="1" data-sail="0" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(2) ?>" data-code="102" data-messageptc="Sandar" type="button" class="btn btn-default form-control btn-schedule <?php echo $btn2 ?>" <?php echo $dis2 ?> data-message="Kapal sudah sandar?">Sandar</button>
								</div>
								<div class="col-md-2 form-group">
									<label>Waktu : <br /> <?php echo $schedule->open_boarding_date ? $schedule->open_boarding_date : '&nbsp' ?></label>
									<button data-info="1" id="btn-ob" data-sail="0" data-boarding="1" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(3) ?>" data-code="103" data-messageptc="Mulai Pelayanan" type="button" class="btn btn-default form-control btn-schedule <?php echo $btn3 ?>" <?php echo $dis3 ?> data-message="Kapal sudah mulai pelayanan?">Mulai Pelayanan</button>
								</div>
								<div class="col-md-2 form-group">
									<label>Waktu : <br /> <?php echo $schedule->close_boarding_date ? $schedule->close_boarding_date : '&nbsp' ?></label>
									<button data-info="0" data-sail="0" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(4) ?>" data-code="104" data-messageptc="Selesai Pelayanan" type="button" class="btn btn-default form-control btn-schedule <?php echo $btn4 ?>" <?php echo $dis4 ?> data-message="Kapal sudah selesai pelayanan?">Selesai Pelayanan</button>
								</div>
								<div class="col-md-2 form-group">
									<label>Waktu : <br /> <?php echo $schedule->close_ramp_door_date ? $schedule->close_ramp_door_date : '&nbsp' ?></label>
									<button data-info="0" data-sail="0" data-boarding="0" data-rampdoor="1" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(5) ?>" data-code="105" data-messageptc="Belum ada layanan" type="button" class="btn btn-default form-control btn-schedule <?php echo $btn5 ?>" <?php echo $dis5 ?> data-message="Tanyakan kepada operator kapal, Apakah sudah selesai scan tiket??">Tutup Ramp door</button>
								</div>
								<div class="col-md-2 form-group">
									<label>Waktu : <br /> <?php echo $schedule->sail_date ? $schedule->sail_date : '&nbsp' ?></label>
									<button data-info="2" data-sail="1" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(6) ?>" data-code="106" data-messageptc="Belum ada layanan" type="button" class="btn btn-default form-control btn-schedule <?php echo $btn6 ?>" <?php echo $dis6 ?> data-message="Tanyakan kepada operator kapal, Apakah manifest kapal sudah approve?"> Berlayar</button>
								</div>
							</div>

						<?php } else { ?>
							<div class="col-md-2 form-group">
								<label>Waktu :</label>
								<button data-info="1" id="btn-ploting" data-sail="0" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(1) ?>" data-code="101" data-messageptc="Masuk Alur" type="button" class="btn btn-primary form-control btn-schedule" data-message="Kapal sudah masuk alur?">Masuk Alur</button>
							</div>
							<div class="col-md-2 form-group">
								<label>Waktu :</label>
								<button data-info="0" data-sail="0" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(2) ?>" data-code="102" data-messageptc="Sandar" type="button" class="btn btn-default form-control btn-schedule" disabled data-message="Kapal sudah sadar?">Sandar</button>
							</div>
							<div class="col-md-2 form-group">
								<label>Waktu :</label>
								<button data-info="2" id="btn-ob" data-sail="0" data-boarding="1" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(3) ?>" data-code="103" data-messageptc="Mulai Pelayanan" type="button" class="btn btn-default form-control btn-schedule" disabled data-message="Kapal sudah mulai pelayanan?">Mulai Pelayanan</button>
							</div>
							<div class="col-md-2 form-group">
								<label>Waktu :</label>
								<button data-info="0" data-sail="0" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(4) ?>" data-code="104" data-messageptc="Selesai Pelayanan" type="button" class="btn btn-default form-control btn-schedule" disabled data-message="Kapal sudah selesai pelayanan?">Selesai Pelayanan</button>
							</div>
							<div class="col-md-2 form-group">
								<label>Waktu :</label>
								<button data-info="0" data-sail="0" data-boarding="0" data-rampdoor="1" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(5) ?>" data-code="105" data-messageptc="Belum ada layanan" type="button" class="btn btn-default form-control btn-schedule" disabled data-message="Tanyakan kepada operator kapal, Apakah sudah selesai scan tiket??">Tutup Ramp door</button>
							</div>
							<div class="col-md-2 form-group">
								<label>Waktu :</label>
								<button data-info="2" data-sail="1" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(6) ?>" data-code="106" data-messageptc="Belum ada layanan" type="button" class="btn btn-default form-control btn-schedule" disabled data-message="Tanyakan kepada operator kapal, Apakah manifest kapal sudah approve?">Berlayar</button>
							</div>
						<?php } ?>
					</div>
					<hr>
					<div class="row problem">
						<div class="col-md-4 form-group">
							<div class="input-group">
								<span class="input-group-addon">Terjadi masalah?</span>
								<select class="form-control select2" data-placeholder="Tipe Masalah" id="problem">
									<option value=""></option>
									<option value="0" data-code="106" data-messageptc="Belum ada layanan">Tidak Masalah</option>
									<option value="<?php echo $this->enc->encode(2) ?>" data-code="106" data-messageptc="Belum ada layanan">Anchor</option>
									<option value="<?php echo $this->enc->encode(3) ?>" data-code="106" data-messageptc="Belum ada layanan">Docking</option>
									<option value="<?php echo $this->enc->encode(4) ?>" data-code="106" data-messageptc="Belum ada layanan">Broken</option>
								</select>
								<span class="input-group-btn">
									<button class="btn btn-success" id="set_probelm" type="button" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" title="Search">
										Ya
									</button>
								</span>
							</div>
						</div>
						<?php $disabledGate = ($schedule->ploting_date || $schedule->docking_date || $schedule->open_boarding_date) ? '' : 'disabled'; ?>
						<div class="col-md-8 form-group">
							<!-- <div class="input-group">
							<span class="input-group-addon">Aksi Gate</span>
							<?php echo form_dropdown('', $gate, '', 'class="form-control select2" id="gate" data-placeholder="Pilih Gate" ' . $disabledGate . ''); ?>
							</div> -->

							<div class="portlet light form-fit bordered">
								<div class="portlet-title">
									<div class="caption" style="padding:0">
										<!-- <i class="icon-settings font-red"></i> -->
										<i class="flaticon-barrier font-red"></i>
										<span class="caption-subject font-red sbold uppercase">Barrier Gate</span>
									</div>
									<div class="actions">

									</div>
								</div>
								<div class="portlet-body form">
									<!-- BEGIN FORM-->
									<form action="#" class="form-horizontal form-bordered">
										<div class="form-body">
											<?php foreach ($manless as $key => $value) {
												$checked = $value['status'] == 1 ? "checked" : "";
											?>
												<div class="form-group">
													<label class="control-label col-md-4 status-label" id="label-<?php echo $value['terminal_code'] ?>"><?php echo $value['name'] ?></label>
													<div class="col-md-8 input-switch">
														<input type="checkbox" id="switch-<?php echo $value['terminal_code'] ?>" class="switch  mt-ladda-btn ladda-button" name="checkbox" data-toggle="switch" data-on-text="OPENED" data-off-text="CLOSED" data-on-color="success" data-off-color="danger" data-size="small" data-name="<?php echo $value['name'] ?>" data-terminal="<?php echo $value['terminal_code'] ?>" data-status="<?php echo $value['status'] ?>" data-param="<?php echo $param ?>" data-label-text="<i class='fa fa-power-off status font-red' id='icon-<?php echo $value['terminal_code'] ?>' data-terminal='<?php echo $value['terminal_code'] ?>'></i>" <?php echo $checked . " " . $disabledGate ?>>
														<span class="reconnect" id="reconnect-<?php echo $value['terminal_code'] ?>" style="font-size:12px;color:#aaa;display:none"> connecting....</span>
														<input type="hidden" class="connection" id="connection-<?php echo $value['terminal_code'] ?>" value="0" data-terminal="<?php echo $value['terminal_code'] ?>">
														<?php if ($value['last_update']) { ?>
															<span class="text-info last-update" style="font-style: italic;margin-left:15px">last update gate: <span id="updated-<?php echo $value['terminal_code'] ?>"><?php echo $value['last_update']; ?></span></span>
														<?php } ?>
													</div>
												</div>
											<?php } ?>
										</div>
									</form>
									<!-- END FORM-->
								</div>
							</div>
						</div>
					</div>
					<!-- <div class="clearfix"></div> -->
				</div>
			</div>
		</div>
	</div>
</div>
<!-- <div class="modal fade" id="myModal" role="dialog"> -->
<div class="modal-dialog modal modal-sm box-edit" id="myModal" role="dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Call</h4>
		</div>
		<div class="modal-body">
			<div class="form-group">
				<label>Call Sandar</label>
				<input type="number" id="call" placeholder="Call Sandar" class="form-control" min="0" required value="1">
			</div>

			<!--  <div class="form-group">
			<label>Call Anchor</label>
			<input type="number" id="call_anchor" placeholder="Call Anchor" class="form-control" min="0" required>
			</div> -->
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary btn-sm" id="save_sail">Simpan</button>
		</div>
	</div>
</div>
<!-- </div> -->

<!-- </div> -->



<script type="text/javascript">
	var client = new ClientJS(),
		browser_id = client.getFingerprint().toString(); // Get Client's Fingerprint
	var socket = io.connect('<?php echo $socket_url ?>');
	var dock = '<?php echo $dock_id ?>',
		open_boarding_dt = '<?php echo $schedule->open_boarding_date ?>',
		close_boarding_dt = '<?php echo $schedule->close_boarding_date ?>';

	$(document).ready(function() {
		// console.log(socket)
		socket.on('connect', function() {
			socket.emit("client_id", "PTCE" + browser_id + uid);
		});
		$('.box-edit').block({
			message: '<h4><i class="fa fa-spinner fa-spin"></i> Menunggu</h4>',
		});

		socket.on('status', function(data) {
			if (data.connected) {
				$('.box-edit').unblock();
			}
		});

		$('.mfp-wrap').removeAttr('tabindex');

		$('.select2').select2();

		$('.btn-schedule').click(function() {
			if ($('#ship').val() == '') {
				alertify.alert('Silahkan pilih kapal terlebih dahulu!');
			} else {
				var param = $(this).data('param'),
					code = $(this).data('code'),
					type = $(this).data('type'),
					sail = $(this).data('sail'),
					message = $(this).data('message'),
					messageptc = $(this).data('messageptc'),
					info = $(this).data('info'),
					rampdoor = $(this).data('rampdoor');
				boarding = $(this).data('boarding');

				alertify.confirm(message, function(e) {
					if (e) {
						if (sail == 1) {
							if ($('.bootstrap-switch-on').length > 0) {
								alertify.alert('<span class="blink"><strong>Perhatian:</strong> Masih ada Barrier Gate yang terbuka (OPEN), mohon tutup terlebih dahulu!</span>')
							} else {
								$("#myModal").modal({
									backdrop: 'static',
									keyboard: false
								});

								$('#save_sail').click(function() {
									stc_action({
										code: code,
										param: param,
										type: type,
										ship: $('#ship').val(),
										call: $('#call').val(),
										call_anchor: $('#call_anchor').val(),
										info: info,
										rampdoor: rampdoor,
										boarding: boarding,
										ship_name: $("#ship option:selected").text(),
										message: messageptc
									});
								});
							}
						} else {
							stc_action({
								code: code,
								param: param,
								type: type,
								ship: $('#ship').val(),
								info: info,
								rampdoor: rampdoor,
								boarding: boarding,
								ship_name: $("#ship option:selected").text(),
								message: messageptc
							});
						}
					}
				});
			}
		});

		p = $('#btnAction').find('.btn-primary');
		if (p.data('ploting')) {
			plot = p.data('ploting');
		} else {
			plot = '<?php echo $this->enc->encode(7); ?>';
			$('.problem').addClass('hidden');
		}

		$('#set_probelm').click(function() {
			var idProblem = $('#problem').val(),
				btn = $(this),
				problem = $("#problem option:selected").text(),
				problem_msg = $("#problem option:selected").data('messageptc'),
				problem_code = $("#problem option:selected").data('code'),
				ship_name = $("#ship option:selected").text();

			if (idProblem == '' || idProblem == 0) {
				toastr.error('Harus pilih dulu tipe masalahnya.!', 'Gagal');
				$(this).button('reset');
			} else {
				alertify.confirm('Apakah yakin kapal mengalami masalah', function(e) {
					if (e) {
						$.ajax({
							url: 'stc/set_problem',
							data: {
								type: $('#problem').val(),
								ploting: plot,
								ship: $('#ship').val(),
								param: '<?php echo $param ?>'
							},
							type: 'POST',
							dataType: 'json',

							beforeSend: function() {
								btn.button('loading');
							},

							success: function(json) {
								console.log(json.code);
								if (json.code == 1) {
									socket.emit('ptc_info', {
										code: problem_code,
										message: problem_msg,
										data: {
											ship_name: ship_name + " (" + problem + ")",
											dock_id: dock
										},
									});

									closeModal();
									socket.disconnect();
									$("#myModal").modal('hide')
									toastr.success(json.message, 'Sukses');
									$('#searching').trigger('click');
								} else {
									toastr.error(json.message, 'Gagal');
								}
							},

							error: function() {
								toastr.error('Silahkan Hubungi Administrator', 'Gagal');
							},

							complete: function() {
								btn.button('reset');
								return false;
							}
						});
					}
				})
			}
		});

		var getConfirm = function(data) {
			alertify.confirm(data.confirm, function(e) {
				if (e) {
					$('.box-edit').block({
						message: '<h4><i class="fa fa-spinner fa-spin"></i> Menunggu Respon dari Gate</h4>',
					});

					socket.emit('web', {
						code: data.code,
						message: data.message,
						data: {
							channel_type: 1,
							terminal_code: data.terminal_code,
							username: browser_id
						}
					});
				}
			});
		}

		var paramGate = null;
		var setTimeoutResponse = null;

		$(".switch").bootstrapSwitch();
		var options = {
			onSwitchChange: function(event, state) {
				return false;
			}
		};
		$(".switch").bootstrapSwitch(options);
		$('.switch').on('switchChange.bootstrapSwitch', function(e) {
			var _this = $(this);
			// console.log(_this.parents().siblings('.connection').val())
			var currentDiv = $("#" + e.currentTarget.id).bootstrapSwitch('state');
			if (_this.parents().siblings('.connection').val() == 1) {
				if (currentDiv == false) {
					alertify.confirm('Apakah anda yakin akan <b>menutup</b> ' + _this.data('name') + ' ?', function(event) {

						if (event) {
							// console.log(_this.data('on-text') + ' : ' + _this.data('terminal'));
							$('.box-edit').block({
								message: '<h4><i class="fa fa-spinner fa-spin"></i> Menunggu Respon dari Gate</h4>',
							});
							socket.emit('web', {
								code: 0,
								message: 'TUTUP',
								data: {
									channel_type: 1,
									terminal_code: _this.data('terminal'),
									username: browser_id
								}
							});
							paramGate = _this.data('param');
							setTimeoutResponse = setTimeout(function() {
								$('.box-edit').unblock();
								$("#" + e.currentTarget.id).bootstrapSwitch('toggleState', true);
								toastr.error('Tidak ada respon dari gate', 'Gagal');
							}, 20000);
						} else {
							$("#" + e.currentTarget.id).bootstrapSwitch('toggleState', true);
						}


					});
				} else {
					alertify.confirm('Apakah anda yakin akan <b>membuka</b> ' + _this.data('name') + ' ?', function(event) {
						if (event) {
							// console.log(_this.data('on-text') + ' : ' + _this.data('terminal'));
							$('.box-edit').block({
								message: '<h4><i class="fa fa-spinner fa-spin"></i> Menunggu Respon dari Gate</h4>',
							});
							socket.emit('web', {
								code: 1,
								message: 'BUKA',
								data: {
									channel_type: 1,
									terminal_code: _this.data('terminal'),
									username: browser_id
								}
							});
							paramGate = _this.data('param');
							setTimeoutResponse = setTimeout(function() {
								$('.box-edit').unblock();
								$("#" + e.currentTarget.id).bootstrapSwitch('toggleState', true);
								toastr.error('Tidak ada respon dari gate', 'Gagal');
							}, 20000);
						} else {
							$("#" + e.currentTarget.id).bootstrapSwitch('toggleState', true);
						}
					});


				}
			} else {
				alertify.alert('<span class="blink"><strong>Perhatian:</strong> Barrier Gate tidak terkoneksi dengan server!</span>', function() {
					socket.emit('web', {
						code: 2,
						message: 'CHECK CONNECTION! PING!',
						data: {
							channel_type: 1,
							terminal_code: _this.data('terminal'),
							username: browser_id
						}
					});
					$('#reconnect-' + _this.data('terminal')).show();
					$("#" + e.currentTarget.id).bootstrapSwitch('toggleState', true);
					setTimeout(function() {
						$('#reconnect-' + _this.data('terminal')).hide();
					}, 5000);
				});
			}

		});

		socket.on('gate', function(dataGate) {
			var d = JSON.parse(dataGate);
			if ((d.code == 0 || d.code == 1) && (d.data.channel_type == 3) && (d.data.username == browser_id) && paramGate && (paramGate != null)) {
				$.ajax({
					url: 'stc/action_gate',
					data: {
						param: paramGate,
						code: d.code,
						terminal_code: d.data.terminal_code
					},
					type: 'POST',
					dataType: 'json',

					beforeSend: function() {

					},

					success: function(json) {
						if (json.code == 1) {
							//  closeModal();
							let gateName = $('#label-' + d.data.terminal_code).text();
							toastr.success(`${gateName} ${d.message}`, 'Sukses');
							let data = json.data.map(updateData);

							function updateData(value, index, array) {
								let html = $('#updated-' + value.terminal_code).html(value.last_update);
								return html;
							}
						} else {
							toastr.error(json.message, 'Gagal');
						}
					},

					error: function() {
						toastr.error('Silahkan Hubungi Administrator', 'Gagal');
					},

					complete: function() {
						clearTimeout(setTimeoutResponse)
						$('.box-edit').unblock();
						return false;
					}
				});
			} else if (d.code == 2) { // check koneksi barrier
				// $('#switch-' + d.data.terminal_code).attr('data-connection', '1');
				$('#connection-' + d.data.terminal_code).val('1');
				$('#icon-' + d.data.terminal_code).removeClass('font-red').addClass('font-green');
				$('#label-' + d.data.terminal_code).removeClass('blink');
				$('#reconnect-' + d.data.terminal_code).hide();
			} else {
				console.warn('Gagal melakukan aksi barrier gate');
			}
		});

		socket.on('client_id', function(data) {
			if (data.status == 1) {
				$('.status').each(function(i, obj) {
					if ($(this).data('terminal') == data.terminal_code) {
						$(this).removeClass('font-red').addClass('font-green');
						$(this).parents().siblings('.status-label').removeClass('blink');
						$(this).parents().siblings('.connection').val('1');
						$(this).parents().siblings('.reconnect').hide();
					}
				});
			} else {
				$('.status').each(function(i, obj) {
					if ($(this).data('terminal') == data.terminal_code) {
						$(this).removeClass('font-green').addClass('font-red');
						$(this).parents().siblings('.status-label').addClass('blink');
						$(this).parents().siblings('.connection').val('0');
					}
				});
			}
		});

		socket.on('clients_status', function(data) {
			for (let key in data.terminal_code) {
				$('.status').each(function(i, obj) {
					if ($(this).data('terminal') == data.terminal_code[key]) {
						$(this).removeClass('font-red').addClass('font-green');
						$(this).parents().siblings('.status-label').removeClass('blink');
						$(this).parents().siblings('.connection').val('1');
						$(this).parents().siblings('.reconnect').hide();
					}
				});
			}
		});

		setTimeout(function() {
			$('.connection').each(function(i, obj) {
				let _this = $(this);
				if (_this.val() == 0) {
					_this.siblings('.reconnect').show();
					socket.emit('web', {
						code: 2,
						message: 'CHECK CONNECTION! PING!',
						data: {
							channel_type: 1,
							terminal_code: _this.data('terminal'),
							username: browser_id
						}
					});

					setTimeout(function() {
						$('#reconnect-' + _this.data('terminal')).hide();
					}, 5000);
				}
			});
		}, 3000);



		$('#close').click(function() {
			closeModal();
			socket.disconnect();
		})

		// bootstrap-switch-on
		$('.uppercase').click(function() {
			console.log()
		})
	});

	function stc_action(data) {
		$.ajax({
			url: 'stc/action_edit',
			data: data,
			type: 'POST',
			dataType: 'json',

			beforeSend: function() {
				$('.box-edit').block({
					message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>',
				});
			},

			success: function(json) {
				if (json.code == 1) {
					if (json.data.post.info == 1 || json.data.post.info == 2) {
						if (json.data.post.info == 1) {
							dataSend = {
								ship_name: json.data.post.ship_name,
								dock_id: '<?php echo $dock_id ?>'
							}
						} else {
							dataSend = {
								ship_name: '',
								dock_id: '<?php echo $dock_id ?>'
							}
						}

						socket.emit('ship_info', dataSend);
					}

					if (json.data.post.boarding == 1) {
						dataSend = {
							"ship_id": '<?php echo $id_ship ?>',
							"ship_name": json.data.post.ship_name,
							"dock_name": '<?php echo $dock_name ?>',
							"message": "Silahkan memulai pelayanan!",
							"sn_validator": '<?php echo json_encode($serial_number) ?>',
							"created_on": new Date(),
							"created_by": '<?php echo $this->session->userdata('username') ?>'
						}
						socket.emit('open_boarding', dataSend);
					}

					if (json.data.post.rampdoor == 1) {
						dataSend = {
							"ship_name": json.data.post.ship_name,
							"dock_name": '<?php echo $dock_name ?>',
							"message": "Telah Tutup Rampdoor",
							"ship_id": '<?php echo $id_ship ?>',
							"boarding_code": '<?php echo $boarding_code ?>',
							"schedule_code": '<?php echo $schedule_code ?>',
							"created_on": new Date(),
							"created_by": '<?php echo $this->session->userdata('username') ?>'
						}
						socket.emit('close_ramp_door', dataSend);
					}

					socket.emit('pids', json.data.pids);


					if (data.code == 101 && open_boarding_dt) {
						data.code = 103;
						data.message = 'Mulai Pelayanan';
						json.data.post.ship_name = json.data.post.ship_name + ' (Masuk Alur)';
					}

					if (data.code == 102 && open_boarding_dt && close_boarding_dt) {
						data.code = 104;
						data.message = 'Selesai Pelayanan';
						json.data.post.ship_name = json.data.post.ship_name + ' (Sandar)';
					}

					if (data.code == 102 && open_boarding_dt) {
						data.code = 103;
						data.message = 'Mulai Pelayanan';
						json.data.post.ship_name = json.data.post.ship_name + ' (Sandar)';
					}

					if (data.code == 105 || data.code == 106) {
						json.data.post.ship_name = "";
					}

					socket.emit('ptc_info', {
						code: data.code,
						message: data.message,
						data: {
							ship_name: json.data.post.ship_name,
							dock_id: dock
						},
					});

					closeModal();
					socket.disconnect();
					$("#myModal").modal('hide')
					toastr.success(json.message, 'Sukses');
					$('#searching').trigger('click');

				} else {
					toastr.error(json.message, 'Gagal');
				}
			},

			error: function() {
				toastr.error('Silahkan Hubungi Administrator', 'Gagal');
			},

			complete: function() {
				$('.box-edit').unblock();
				return false;
			}
		});
	}
</script>