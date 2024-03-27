<link href="<?php echo base_url('assets/global/plugins/icheck/skins/all.css'); ?>" rel="stylesheet" type="text/css" />

<div class="col-md-10 col-md-offset-1">
	<div class="portlet box grey-cascade box-edit">

		<div class="portlet-title">
			<div class="uppercase caption"><?php echo $title . " " . $dock_name ?></div>
			<div class="tools">
				<button id="close" type="button" class="btn btn-box-tool btn-xs bg-grey-mint bg-hover-grey-cascade"><i class="fa fa-times"></i></button>
			</div>
		</div>
		<div class="portlet-body box-gate">
			<div class="box-body">
				<div class="form-group">
					<div class="row">
						<div class="col-md-4 col-sm-4 form-group">
							<!-- <label>Kode Jadwal</label>
							<h1 style="font-family: inherit;font-weight: 500;font-size: 16px;margin: 0 0 15px;">S0125125125</h1> -->
							<label>Nama Kapal</label>
							<?php $disabled = ($schedule->ploting_date || $schedule->docking_date || $schedule->open_boarding_date) ? 'disabled' : ''; ?>
							<select class="form-control select2-ship" data-placeholder="Pilih Kapal" id="ship" <?php echo $disabled ?>>
								<option></option>
								<?php foreach ($ship as $value) { ?>
									<option value="<?php echo $this->enc->encode($value->id); ?>" <?php echo $value->id == $id_ship ? "selected" : "" ?>><?php echo strtoupper($value->name)." (".$value->ship_class_name.")"; ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-4 col-md-offset-4 col-sm-4 col-sm-offset-4 form-group">
							<div class="mt-element-ribbon bg-grey-steel">
								<div class="uppercase ribbon ribbon-right ribbon-color-success ribbon-round" style="font-size:16px;font-weight:600;padding:.8rem 1rem"><?php echo $dock_name ?></div>
								<div class="ribbon-content">
									<dl>
										<dt>Kode Jadwal</dt>
										<dd><?php echo $schedule_code ?></dd>
										<!-- <dt>Jadwal</dt>
										<dd><?php echo $schedule->schedule_date ?></dd> -->
										<dt>Status Layanan</dt>
										<dd class="text-uppercase <?php echo strtoupper($status_boarding) === "SUDAH DISETUJUI" ? "font-green-jungle" : "font-blue-madison" ?>"><?php echo $status_boarding ? $status_boarding : "-" ?></dd>
									</dl>
								</div>
							</div>
							<!-- <div style="font-size: 20pt; font-weight: bold; padding-top: 0px"><?php echo $dock_name; ?></div>
							<div style="color: green; font-weight: bold;"><?php echo $status_boarding ?></div> -->
						</div>

					</div>
					<div class="row">
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
							if (($schedule->ploting_date && $schedule->docking_date && $schedule->open_boarding_date) || (empty($schedule->ploting_date) && empty($schedule->docking_date) && $schedule->open_boarding_date) || ($schedule->ploting_date && empty($schedule->docking_date) && $schedule->open_boarding_date)
								|| (empty($schedule->ploting_date) && $schedule->docking_date && $schedule->open_boarding_date)
							) {
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
							} elseif ((empty($schedule->open_boarding_date) && empty($schedule->close_boarding_date)) || (empty($schedule->open_boarding_date) && empty($schedule->ploting_date) && empty($schedule->docking_date)) || (empty($schedule->open_boarding_date) && ($schedule->ploting_date) && empty($schedule->docking_date)) || ($schedule->open_boarding_date && empty($schedule->ploting_date) && empty($schedule->docking_date)) || ($schedule->open_boarding_date && empty($schedule->ploting_date) && $schedule->docking_date) ) {
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
								<div class="col-md-2 col-sm-2 form-group">
									<button data-info="1" id="btn-ploting" data-sail="0" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(1) ?>" data-code="101" data-messageptc="Masuk Alur" type="button" class="btn btn-default ellipsis form-control btn-schedule <?php echo $btn1 ?>" <?php echo $dis1 ?> data-message="Kapal sudah masuk alur? cek kembali kapal yang anda pilih, apakah sudah sesuai?" title="Masuk Alur">Masuk Alur</button>
									<label class="text-muted" style="margin: 10px 0; display:flex;align-items:center;justify-content:center"><?php echo $schedule->ploting_date ? '<span aria-hidden="true" class="flaticon-wall-clock" style="margin-right:5px"></span>' . $schedule->ploting_date : null ?></label>
								</div>
								<div class="col-md-2 col-sm-2 form-group">
									<button data-info="1" data-sail="0" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(2) ?>" data-code="102" data-messageptc="Sandar" type="button" class="btn btn-default ellipsis form-control btn-schedule <?php echo $btn2 ?>" <?php echo $dis2 ?> data-message="Kapal sudah sandar?" title="Sandar">Sandar</button>
									<label class="text-muted" style="margin: 10px 0; display:flex;align-items:center;justify-content:center"><?php echo $schedule->docking_date ? '<span aria-hidden="true" class="flaticon-wall-clock" style="margin-right:5px"></span>' . $schedule->docking_date : null ?></label>
								</div>
								<div class="col-md-2 col-sm-2 form-group">
									<button data-info="1" id="btn-ob" data-sail="0" data-boarding="1" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(3) ?>" data-code="103" data-messageptc="Mulai Pelayanan" type="button" class="btn btn-default ellipsis form-control btn-schedule <?php echo $btn3 ?>" <?php echo $dis3 ?> data-message="Kapal sudah mulai pelayanan?" title="Mulai Pelayanan">Mulai Pelayanan</button>
									<label class="text-muted" style="margin: 10px 0; display:flex;align-items:center;justify-content:center"><?php echo $schedule->open_boarding_date ? '<span aria-hidden="true" class="flaticon-wall-clock" style="margin-right:5px"></span>' . $schedule->open_boarding_date : null ?></label>
								</div>
								<div class="col-md-2 col-sm-2 form-group">
									<button data-info="0" data-sail="0" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(4) ?>" data-code="104" data-messageptc="Selesai Pelayanan" type="button" class="btn btn-default ellipsis form-control btn-schedule <?php echo $btn4 ?>" <?php echo $dis4 ?> data-message="Kapal sudah selesai pelayanan?" title="Selesai Pelayanan">Selesai Pelayanan</button>
									<label class="text-muted" style="margin: 10px 0; display:flex;align-items:center;justify-content:center"><?php echo $schedule->close_boarding_date ? '<span aria-hidden="true" class="flaticon-wall-clock" style="margin-right:5px"></span>' . $schedule->close_boarding_date : null ?></label>
								</div>
								<div class="col-md-2 col-sm-2 form-group">
									<button data-info="0" data-sail="0" data-boarding="0" data-rampdoor="1" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(5) ?>" data-code="105" data-messageptc="Belum ada layanan" type="button" class="btn btn-default ellipsis form-control btn-schedule <?php echo $btn5 ?>" <?php echo $dis5 ?> data-message="Tanyakan kepada operator kapal, Apakah sudah selesai scan tiket?" title="Tutup Ramp door">Tutup Ramp door</button>
									<label class="text-muted" style="margin: 10px 0; display:flex;align-items:center;justify-content:center"><?php echo $schedule->close_ramp_door_date ? '<span aria-hidden="true" class="flaticon-wall-clock" style="margin-right:5px"></span>' . $schedule->close_ramp_door_date : null ?></label>
								</div>
								<div class="col-md-2 col-sm-2 form-group">
									<button data-info="2" data-sail="1" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(6) ?>" data-code="106" data-messageptc="Belum ada layanan" type="button" class="btn btn-default ellipsis form-control btn-schedule <?php echo $btn6 ?>" <?php echo $dis6 ?> data-message="Tanyakan kepada operator kapal, Apakah manifest kapal sudah approve?" title="Berlayar"> Berlayar</button>
									<label class="text-muted" style="margin: 10px 0; display:flex;align-items:center;justify-content:center"><?php echo $schedule->sail_date ? '<span aria-hidden="true" class="flaticon-wall-clock" style="margin-right:5px"></span>' . $schedule->sail_date : null ?></label>
								</div>
							</div>

						<?php } else { ?>
							<div class="col-md-2 col-sm-2 form-group">
								<button data-info="1" id="btn-ploting" data-sail="0" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(1) ?>" data-code="101" data-messageptc="Masuk Alur" type="button" class="btn btn-primary form-control btn-schedule" data-message="Kapal sudah masuk alur?">Masuk Alur</button>
								<label>Waktu :</label>
							</div>
							<div class="col-md-2 col-sm-2 form-group">
								<button data-info="0" data-sail="0" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(2) ?>" data-code="102" data-messageptc="Sandar" type="button" class="btn btn-default form-control btn-schedule" disabled data-message="Kapal sudah sadar?">Sandar</button>
								<label>Waktu :</label>
							</div>
							<div class="col-md-2 col-sm-2 form-group">
								<button data-info="2" id="btn-ob" data-sail="0" data-boarding="1" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(3) ?>" data-code="103" data-messageptc="Mulai Pelayanan" type="button" class="btn btn-default form-control btn-schedule" disabled data-message="Kapal sudah mulai pelayanan?">Mulai Pelayanan</button>
								<label>Waktu :</label>
							</div>
							<div class="col-md-2 col-sm-2 form-group">
								<button data-info="0" data-sail="0" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(4) ?>" data-code="104" data-messageptc="Selesai Pelayanan" type="button" class="btn btn-default form-control btn-schedule" disabled data-message="Kapal sudah selesai pelayanan?">Selesai Pelayanan</button>
								<label>Waktu :</label>
							</div>
							<div class="col-md-2 col-sm-2 form-group">
								<button data-info="0" data-sail="0" data-boarding="0" data-rampdoor="1" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(5) ?>" data-code="105" data-messageptc="Belum ada layanan" type="button" class="btn btn-default form-control btn-schedule" disabled data-message="Tanyakan kepada operator kapal, Apakah sudah selesai scan tiket??">Tutup Ramp door</button>
								<label>Waktu :</label>
							</div>
							<div class="col-md-2 col-sm-2 form-group">
								<button data-info="2" data-sail="1" data-boarding="0" data-rampdoor="0" data-param="<?php echo $param; ?>" data-type="<?php echo $this->enc->encode(6) ?>" data-code="106" data-messageptc="Belum ada layanan" type="button" class="btn btn-default form-control btn-schedule" disabled data-message="Tanyakan kepada operator kapal, Apakah manifest kapal sudah approve?">Berlayar</button>
								<label>Waktu :</label>
							</div>
						<?php } ?>
					</div>

					<div class="row action-problem" style="border-top: 1px solid #ebedf3;padding-top:25px;">
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
									<button class="btn btn-danger" id="set_problem" type="button" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" title="Yes">
										<span aria-hidden="true" class="icon-anchor"></span>
									</button>
								</span>
							</div>
						</div>
						<?php $disabledGate = ($schedule->ploting_date || $schedule->docking_date || $schedule->open_boarding_date) ? '' : 'disabled'; ?>
						<div class="col-md-8 form-group">
							<div id="container-barrier" class="portlet light form-fit bordered">
								<div class="portlet-title">
									<div class="caption" style="padding:0">
										<i class="flaticon-barrier font-red"></i>
										<span class="uppercase caption-subject font-red sbold">Barrier Gate</span>
									</div>
									<div class="actions">
									</div>
								</div>
								<div class="portlet-body form">
									<!-- BEGIN FORM-->
									<form action="#" class="form-horizontal form-bordered">
										<div class="form-body">
											<?php if ($manless) {
												foreach ($manless as $key => $value) {
													$checked = $value['status'] == 1 ? "checked" : "";
											?>
													<div class="form-group">
														<label class="control-label col-md-4 status-label blink" id="label-<?php echo $value['terminal_code'] ?>"><?php echo $value['name'] ?></label>
														<div class="col-md-8 input-switch">
															<div class="tools">
																<input type="checkbox" id="switch-<?php echo $value['terminal_code'] ?>" class="switch mt-ladda-btn ladda-button" name="checkbox" data-toggle="switch" data-on-text="OPENED" data-off-text="CLOSED" data-on-color="success" data-off-color="danger" data-size="small" data-name="<?php echo $value['name'] ?>" data-terminal="<?php echo $value['terminal_code'] ?>" data-status="<?php echo $value['status'] ?>" data-param="<?php echo $param ?>" data-label-text="<i class='fa fa-power-off status font-red' id='icon-<?php echo $value['terminal_code'] ?>' data-terminal='<?php echo $value['terminal_code'] ?>'></i>" <?php echo $checked . " " . $disabledGate ?> />
																<input type="hidden" class="connection" id="connection-<?php echo $value['terminal_code'] ?>" value="0" data-terminal="<?php echo $value['terminal_code'] ?>">
																<?php if ($value['last_update']) { ?>
																	<span class="text-muted last-update" style="margin-left:15px;font-size:12px"><i aria-hidden="true" class="flaticon-wall-clock" style="margin-right:5px"></i><span id="updated-<?php echo $value['terminal_code'] ?>"><?php echo $value['last_update']; ?></span></span>
																<?php } ?>
															</div>
															<div class="reconnect font-red" id="reconnect-<?php echo $value['terminal_code'] ?>" style="color:#95A5A6">Disconnected</div>
														</div>
													</div>
												<?php }
											} else { ?>
												<div class="text-center padding-tb-20">
													<span class="text-muted">Tidak tersedia</span>
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
			<!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
			<h4 class="modal-title">Call</h4>
		</div>
		<div class="modal-body">
			<div class="form-group">
				<label>Call Sandar</label>
				<input type="number" id="call" placeholder="Call Sandar" class="form-control" min="0" required value="1">
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary btn-sm" id="save_sail">Simpan</button>
		</div>
	</div>
</div>
<!-- </div> -->

<script type="text/javascript">
	
	$('.box-edit').block({
		message: '<h4>Menunggu respon server<span class="loading-dots-fixed"><i></i><i></i><i></i></span></h4>',
		overlayCSS: {
			opacity: 0.3
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

	var dock = '<?php echo $dock_id ?>',
		open_boarding_dt = '<?php echo $schedule->open_boarding_date ?>',
		close_boarding_dt = '<?php echo $schedule->close_boarding_date ?>';

	// get all terminal on open modal,
	socket.emit("client_allsocket", deviceName, "Request client all socket");

	// listen client_allsocket set status button gate
	socket.on('client_allsocket', function(data) {
		$('.box-edit').unblock();

		const {
			data: terminals
		} = data;

		for (const x of terminals) {
			$('.status').each(function(i, obj) {
				if ($(this).data('terminal') == x.terminal_code) {
					$(this).removeClass('font-red').addClass('font-green');
					$(this).parents().siblings('.status-label').removeClass('blink');
					$(this).parents().siblings('.connection').val('1');
					$(this).parents().parents().parents().siblings('.reconnect').html('');
				}
			});
		}
	});

	// listen client status and set status button gate
	socket.on('client_status', function(data) {
		if (data.code == 1) {
			$('.status').each(function(i, obj) {
				if ($(this).data('terminal') == data.terminal_code) {
					$(this).removeClass('font-red').addClass('font-green');
					$(this).parents().siblings('.status-label').removeClass('blink');
					$(this).parents().siblings('.connection').val('1');
					$(this).parents().parents().parents().siblings('.reconnect').html('');
				}
			});
		} else {
			$('.status').each(function(i, obj) {
				if ($(this).data('terminal') == data.terminal_code) {
					$(this).removeClass('font-green').addClass('font-red');
					$(this).parents().siblings('.status-label').addClass('blink');
					$(this).parents().siblings('.connection').val('0');
					$(this).parents().parents().parents().siblings('.reconnect').html('Disconnected').addClass('font-red');
				}
			});
		}
	});

	$('.mfp-wrap').removeAttr('tabindex');

	$("#ship").select2({
		containerCssClass: "select-ship"
	});

	$('#problem').select2();

	// button trigger flow kapal
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
				rampdoor = $(this).data('rampdoor'),
				boarding = $(this).data('boarding');

			alertify.confirm(message, function(e) {
				if (e) {
					if (sail == 1) {
						if ($('.bootstrap-switch-on').length > 0) {
							alertify.alert('<span class="blink">Masih ada Barrier Gate yang terbuka (OPEN), mohon tutup terlebih dahulu!</span>')
						} else {
							$("#myModal").modal({
								backdrop: 'static',
								keyboard: false
							});

							$('#save_sail').click(function() {
								$("#myModal").modal('hide');
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
			}, function() {}).set('reverseButtons', true);
		}
	});


	p = $('#btnAction').find('.btn-primary');
	if (p.data('ploting')) {
		plot = p.data('ploting');
	} else {
		plot = '<?php echo $this->enc->encode(7); ?>';
		$('.action-problem').addClass('hidden');
	}

	// button trigger kapal masalah
	$('#set_problem').click(function() {
		var idProblem = $('#problem').val(),
			btn = $(this),
			problem = $("#problem option:selected").text(),
			problem_msg = $("#problem option:selected").data('messageptc'),
			problem_code = $("#problem option:selected").data('code'),
			ship_name = $("#ship option:selected").text();

		if (idProblem == '' || idProblem == 0) {
			toastr.error('Silah pilih tipe masalah!', 'Gagal');
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
							$('.box-edit').block({
								message: '<h4>Memproses<span class="loading-dots-fixed"><i></i><i></i><i></i></span></h4>',
								overlayCSS: {
									opacity: 0.3
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

						success: function(json) {
							if (json.code == 1) {
								socket.emit('ptc_info', {
									code: problem_code,
									message: problem_msg,
									data: {
										ship_name: ship_name + " (" + problem + ")",
										dock_id: dock,
										username: deviceName
									},
								});

								closeModal();
								// socket.disconnect();
								socket.off('res_gate_trigger');
								$("#myModal").modal('hide')
								toastr.success(json.message, 'Sukses');
								$('#searching').trigger('click');
							} else {
								toastr.error(json.message, 'Gagal');
							}
						},

						error: function(xhr, status, error) {
							toastr.error(`(${xhr.status}) ${xhr.statusText}`, 'Gagal');
						},

						complete: function() {
							socket.emit('pidsBoarding', parseInt(<?php echo $this->session->userdata('port_id') ?>), parseInt(<?php echo $ship_class_by_dock; ?>), false);
							btn.button('reset');
							$('.box-edit').unblock();
							return false;
						}
					});
				}
			}, function() {}).set('reverseButtons', true);
		}
	});

	var paramGate = null;
	var setTimeoutResponse = null;

	$(".switch").bootstrapSwitch();
	var options = {
		onSwitchChange: function(event, state) {
			return false;
		}
	};

	// button trigger gate
	$(".switch").bootstrapSwitch(options);
	$('.switch').on('switchChange.bootstrapSwitch', function(e) {
		var _this = $(this);
		var _statusGate = ($("#" + e.currentTarget.id).bootstrapSwitch('state') === true) ? 1 : 0;
		var currentDiv = $("#" + e.currentTarget.id).bootstrapSwitch('state');
		if (_this.parents().siblings('.connection').val() == 1) {
			if (currentDiv == false) {
				alertify.confirm('Apakah anda yakin akan <b>menutup</b> ' + _this.data('name') + ' ?', function(event) {
					if (event) {
						$('.box-gate').block({
							message: '<h4>Menunggu respon dari Barrier Gate<span class="loading-dots-fixed"><i></i><i></i><i></i></span></h4>',
							overlayCSS: {
								opacity: 0.3
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
						socket.emit('gate_trigger', {
							code: 0,
							message: 'TUTUP',
							notif_message: '',
							terminal_code: _this.data('terminal'),
							username: deviceName,
							channel_type: 1
						});

						// private 
						// socket.emit('gate', _this.data('terminal'), {
						// 	code: _statusGate,
						// 	message: 'TUTUP',
						// 	notif_message: '',
						// 	terminal_code: _this.data('terminal'),
						// 	username: deviceName,
						// 	channel_type: 1
						// });
						paramGate = _this.data('param');
						setTimeoutResponse = setTimeout(function() {
							$('.box-gate').unblock();
							$("#" + e.currentTarget.id).bootstrapSwitch('toggleState', true);
							toastr.error('Tidak ada respon dari gate', 'Gagal');
						}, 120000);
					} else {
						$("#" + e.currentTarget.id).bootstrapSwitch('toggleState', true);
					}


				}, function() {
					$("#" + e.currentTarget.id).bootstrapSwitch('toggleState', true);
				}).set('reverseButtons', true);
			} else {
				alertify.confirm('Apakah anda yakin akan <b>membuka</b> ' + _this.data('name') + ' ?', function(event) {
					if (event) {
						$('.box-gate').block({
							message: '<h4>Menunggu respon dari Barrier Gate<span class="loading-dots-fixed"><i></i><i></i><i></i></span></h4>',
							overlayCSS: {
								opacity: 0.3
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
						socket.emit('gate_trigger', {
							code: 1,
							message: 'BUKA',
							notif_message: '<?php echo $notif_gate_trigger ?>',
							terminal_code: _this.data('terminal'),
							username: deviceName,
							channel_type: 1
						});

						// private 
						// socket.emit('gate', _this.data('terminal'), {
						// 	code: _statusGate,
						// 	message: 'BUKA',
						// 	notif_message: '<?php echo $notif_gate_trigger ?>',
						// 	terminal_code: _this.data('terminal'),
						// 	username: deviceName,
						// 	channel_type: 1
						// });
						paramGate = _this.data('param');
						setTimeoutResponse = setTimeout(function() {
							$('.box-gate').unblock();
							$("#" + e.currentTarget.id).bootstrapSwitch('toggleState', true);
							toastr.error('Tidak ada respon dari gate', 'Gagal');
						}, 120000);
					} else {
						$("#" + e.currentTarget.id).bootstrapSwitch('toggleState', true);
					}
				}, function() {
					$("#" + e.currentTarget.id).bootstrapSwitch('toggleState', true);
				}).set('reverseButtons', true);


			}
		} else {
			alertify.alert('<span class="blink">Barrier Gate tidak terkoneksi dengan server!</span>').set('onok', function() {
				socket.emit('client_check', _this.data('terminal'), {
					code: _this.data('status'),
					message: "PING!",
					terminal: _this.data('terminal'),
					username: deviceName
				});
				$('#reconnect-' + _this.data('terminal')).html('<span class="loading-dots">Connecting</span>').removeClass('font-red');
				$("#" + e.currentTarget.id).bootstrapSwitch('toggleState', true);
				setTimeout(function() {
					$('#reconnect-' + _this.data('terminal')).html('Disconnected').addClass('font-red');
				}, 120000);
			}).set('label', 'Hubungkan!');;
		}

	});

	// listen response gate trigger
	socket.on('res_gate_trigger', function(dataGate) {
		if (dataGate.username == deviceName) {

			let gateName = $('#label-' + dataGate.terminal_code).text().toUpperCase();
			let condition = data.code === 1 ? "membuka" : "menutup";

			if (paramGate && (paramGate != null)) {
				try {
					if (dataGate.code != 0 && dataGate.code != 1) throw dataGate.message;

					$.ajax({
						url: 'stc/action_gate',
						data: {
							param: paramGate,
							code: dataGate.code,
							terminal_code: dataGate.terminal_code
						},
						type: 'POST',
						dataType: 'json',

						success: function(json) {
							if (json.code == 1) {
								let messageStatus = dataGate.message; //dataGate.code == 0 ? 'TERTUTUP' : 'TERBUKA';
								toastr.success(capitalize(messageStatus), gateName);
								let data = json.data.map(updateData);

								function updateData(value, index, array) {
									let html = $('#updated-' + value.terminal_code).html(value.last_update);
									return html;
								}
							} else {
								toastr.error(json.message, 'Gagal');
								$("#switch-" + dataGate.terminal_code).bootstrapSwitch('toggleState', true);
							}
						},

						error: function(xhr, status, error) {
							toastr.error(`(${xhr.status}) ${xhr.statusText}`, gateName);
							$("#switch-" + dataGate.terminal_code).bootstrapSwitch('toggleState', true);
						},

						complete: function() {
							clearTimeout(setTimeoutResponse)
							$('.box-gate').unblock();
						}
					});
				} catch (e) {
					toastr.warning(e, gateName);
					clearTimeout(setTimeoutResponse)
					$('.box-gate').unblock();
				}

			} else {
				toastr.error(`Gagal ${condition} barrier gate`, gateName);
				clearTimeout(setTimeoutResponse);
				$("#switch-" + dataGate.terminal_code).bootstrapSwitch('toggleState', true);
				$('.box-gate').unblock();
			}
		}
	});

	setTimeout(function() {
		$('.connection').each(function(i, obj) {
			let _this = $(this);
			if (_this.val() == 0) {
				_this.parents().siblings('.reconnect').html('<span class="loading-dots">Connecting</span>').removeClass('font-red');
				socket.emit('client_check', _this.data('terminal'), {
					code: _this.val(),
					message: "PING!",
					terminal_code: _this.data('terminal'),
					username: deviceName
				});
				setTimeout(function() {
					$('#reconnect-' + _this.data('terminal')).html('Disconnected').addClass('font-red');
				}, 120000);
			}
		});
	}, 3000);


	// button close modal
	$('#close').click(function() {
		closeModal();
		// socket.disconnect();
		socket.off('res_gate_trigger');
		clearTimeout(setTimeoutResponse)
	})

	// action trigger button
	function stc_action(data) {
		$.ajax({
			url: 'stc/action_edit',
			data: data,
			type: 'POST',
			dataType: 'json',

			beforeSend: function() {
				$('.box-edit').block({
					message: '<h4>Memproses<span class="loading-dots-fixed"><i></i><i></i><i></i></span></h4>',
					overlayCSS: {
						opacity: 0.3
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
							"sn_validator": <?php echo json_encode($serial_number) ?>,
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
							"sn_validator": <?php echo json_encode($serial_number) ?>,
							"boarding_code": '<?php echo $boarding_code ?>',
							"schedule_code": '<?php echo $schedule_code ?>',
							"created_on": new Date(),
							"created_by": '<?php echo $this->session->userdata('username') ?>'
						}
						socket.emit('close_ramp_door', dataSend);
					}

					// socket.emit('pids', json.data.pids);
					socket.emit('pidsBoarding', parseInt(json.data.portId), parseInt(<?php echo $ship_class_by_dock; ?>), false);


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
							dock_id: dock,
							username: deviceName
						},
					});

					closeModal();
					// socket.disconnect();
					socket.off('res_gate_trigger');
					$("#myModal").modal('hide')
					toastr.success(json.message, 'Sukses');
					$('#searching').trigger('click');

				} else {
					toastr.error(json.message, 'Gagal');
					// alertify.alert('Gagal!', json.message);
				}
			},

			error: function(xhr, status, error) {
				console.log('xhr: ', xhr);
				toastr.error(`(${xhr.status}) ${xhr.statusText}`, 'Gagal');
			},

			complete: function() {
				$('.box-edit').unblock();
				return false;
			}
		});
	}
</script>