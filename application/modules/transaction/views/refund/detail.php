<?php $this->load->helper('nutech_helper'); ?>
<div class="page-content-wrapper">
	<div class="page-content">
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<li>
					<?php echo '<a href="' . $url_home . '">' . $home; ?></a>
					<i class="fa fa-circle"></i>
				</li>
				<li>
					<?php echo '<a href="' . $url_parent1 . '">' . $parent1; ?></a>
					<i class="fa fa-circle"></i>
				</li>
				<li>
					<?php echo '<a href="' . $url_parent2 . '">' . $parent2; ?></a>
					<i class="fa fa-circle"></i>
				</li>
				<li>
					<span><?php echo $title; ?></span>
				</li>
			</ul>
			<div class="page-toolbar">
				<div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
					<span class="thin uppercase hidden-xs" id="datetime"></span>
					<script type="text/javascript">window.onload = date_time('datetime');</script>
				</div>
			</div>
		</div>
		<br />
		<div class="portlet box blue-madison">
			<div class="portlet-title">
				<div class="caption">
					<?php echo $title; ?>
				</div>
				<div class="pull-right btn-add-padding">
                    <a href="<?php echo site_url('refund') ?>" class="btn btn-sm btn-warning">Kembali</a>
                </div>
			</div>
			<div class="portlet-body">		
				<!-- strat row -->
				<div class="row">
					<div class="col-lg-4">
						<table class="table">
							<tbody>
								<tr style="background:#FFCC00; color:#FFFFFF;">
									<td colspan="3">Data Refund</td>
								</tr>
								<tr class="warning">
									<td>Nama </td>
									<td>:</td><td><?php echo $header->name ?></td>
								</tr>
								<tr class="warning">
									<td>No Telepon</td>
									<td>:</td><td><?php echo $header->phone ?></td>
								</tr>
								<tr class="warning">
									<td>Pelabuhan</td>
									<td>:</td><td><?php echo $header->port ?></td>
								</tr>
								<tr class="warning">
									<td>Tanggal Refund</td>
									<td>:</td><td><?php echo format_date($header->created_on) ?></td>
								</tr>
								<tr class="warning">
									<td>Tanggal Pengembalian</td>
									<td>:</td><td><?php echo format_date($header->date_collection) ?></td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<div class="col-lg-8">
						<table class="table table-striped">
							<thead>
								<tr style="background:#FFCC00; color:#FFFFFF;">
									<td colspan="6">Detail Refund </td>
								</tr>
								<tr style="background:#FFCC00; color:#FFFFFF;">
									<th width="50px" align="center">No</th>
									<?php if($service == 1){ ?>
										<th>Nama</th>
									<?php }else{ ?>
										<th>Jenis Kendaraan</th>
									<?php } ?> 
									<th>Nomor Tiket</th>
									<th>Tarif (Rp)</th>
									<th>Pengembalian (Rp)</th>
									<th>Status</th>
								</tr>
							</thead> 
							<?php 
								$fare = 0; 
								$fee = 0; 
								foreach($detail as $k => $row){ 
									$fare += $row->fare;
									$fee += $row->fee;
									$bea = $row->fare - $row->fee;
							?>
								<tr class="warning">
									<td align="center"><?php echo $k+1 ?> </td>
									<?php if($service == 1){ ?>
										<td><?php echo $row->name ?> </td>
									<?php }else{ ?>
										<td><?php echo $row->vehicle ?> </td>
									<?php } ?> 
									<td><?php echo $row->ticket_number ?> </td>
									<td align="right"><?php echo number_format($row->fare,0,',','.') ?> </td>
									<td align="right"><?php echo number_format($bea,0,',','.') ?> </td>
									<td><span style="color: red">Menunggu Verifikasi</span></td>
								</tr>
							<?php } $jbea = $fare - $fee; ?>
						<tr style="background:#FFCC00; color:#FFFFFF;">
							<td colspan="3" align="center">Jumlah</td>
							<td align="right"><?php echo number_format($fare,0,',','.') ?></td>
							<td align="right"><?php echo number_format($jbea,0,',','.') ?></td>
							<td></td>
						</tr>
						</table>
					</div>
				</div>
			</div> 
		</div>

	</div>
