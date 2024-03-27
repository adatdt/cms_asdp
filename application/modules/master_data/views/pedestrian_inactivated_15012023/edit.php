<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/pedestrian_inactivated/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-3 form-group">
                            <label>Tanggal Mulai</label>
                            <input type="text" class="form-control date" id="start_date" name="start_date" value="<?php echo $detail->start_date; ?>" readonly placeholder="Masukan tanggal mulai">
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Tanggal Selesai</label>
                            <input type="text" class="form-control date" id="end_date" name="end_date" value="<?php echo $detail->end_date; ?>" readonly placeholder="Masukan tanggal selesai">
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Pelabuhan</label>
                            <select class="form-control select2" required name="port_id">
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->port_id == $value->id?"selected":""; ?> > <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Kelas Layanan</label>
                            <select class="form-control select2" required name="ship_class_id">
                                <option value="">Pilih</option>
                                <?php foreach($ship_class as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->ship_class == $value->id?"selected":""; ?> > <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
											<div class="col-sm-2 form-group">
													<label></label>
													<div class="input-group">
															<div class="icheck">
																	<label>
																			<input type="checkbox" class="allow" name='web' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->web == "t" ?"checked":""; ?> > Web
																	</label>
															</div>
													</div>
											</div>

											<div class="col-sm-2 form-group">
													<label></label>
													<div class="input-group">
															<div class="icheck">
																	<label><input type="checkbox" class="allow" name='mobile' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->mobile == "t" ?"checked":""; ?> > Mobile</label>
															</div>
													</div>
											</div>

											<div class="col-sm-2 form-group">
													<label></label>
													<div class="input-group">
															<div class="icheck">
																	<label>
																			<input type="checkbox" class="allow" name='b2b' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->b2b == "t" ?"checked":""; ?> > B2B
																	</label>
															</div>
													</div>
											</div>
											
											<div class="col-sm-2 form-group">
													<label></label>
													<div class="input-group">
															<div class="icheck">
																	<label>
																			<input type="checkbox" class="allow" name='ifcs' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->ifcs == "t" ?"checked":""; ?> > IFCS
																	</label>
															</div>
													</div>
											</div>

											<div class="col-sm-2 form-group">
													<label></label>
													<div class="input-group">
															<div class="icheck">
																	<label>
																			<input type="checkbox" class="allow" name='web_cs' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->web_cs == "t" ?"checked":""; ?> > WEB CS
																	</label>
															</div>
													</div>
											</div>

											<input type="hidden" name="id" value="<?=$this->enc->encode($detail->id) ?>">
									</div>
                </div>
            </div>
            <?php echo createBtnForm('Update') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
						startDate: new Date()
        });

				$("#start_date").change(function() {            
            var startDate = $(this).val();
						console.log(startDate);
            var someDate = new Date(startDate);

						$('#end_date').datetimepicker('remove');
              // Re-int with new options
            $('#end_date').datetimepicker({
                format: 'yyyy-mm-dd hh:ii',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                startDate: someDate
            });
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });
    })
</script>