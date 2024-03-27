<div class="col-md-12 col-md-offset-0" >
    <div class="portlet box blue" id="box" >
        <?php echo headerForm($title) ?>
        <div class="portlet-body" >

            <div class="row">
                <div class="col-md-12">
                       
                    <div class="portlet-body">
                        <div class="table-toolbar">
                            <div class="row">

                        <div class="col-md-12">
                            <div class="table-scrollable">

		        				<div class="row">
									<div class="col-lg-12">
										<table class="table">
											<tbody>
												<tr style="background:#FFCC00; color:#FFFFFF;">
													<td colspan="3">Data Email</td>
												</tr>
												<tr class="warning">
													<td >
														<table>
															<tr class="warning">
																<td>Tanggal</td>
																<td>&nbsp;:&nbsp; </td>
																<td><?php echo format_datetime($email->created_on); ?></td>
															</tr>
															<tr class="warning">
																<td>Recipient</td>
																<td>&nbsp;:&nbsp; </td>
																<td><?php echo $email->recipient; ?></td>
															</tr>
															<tr class="warning">
																<td>CC</td>
																<td>&nbsp;:&nbsp; </td>
																<td><?php 
																	foreach($cc as $cc)
																	{
																		echo $cc->recipient." ";
																	}?>
																		
																</td>
															</tr>
															<tr class="warning">
																<td>BC</td>
																<td>&nbsp;:&nbsp; </td>
																<td>
																<?php 
																foreach($bc as $bc)
																{
																echo $bc->recipient." ";
																}
																?>
																</td>
															</tr>
															<tr class="warning">
																<td>Subject</td>
																<td>&nbsp;:&nbsp; </td>
																<td><?php echo $email->subject; ?></td>
															</tr>
														</table>							
													</td>
												</tr>
								
												<tr >
											<td>
												<?php echo $email->body; ?>
											</td> 	
										</tr>
									</tbody>
								</table>
							</div>

						</div>

                            </div>
                        </div>
                    </div>
                </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function(){

    })
</script>