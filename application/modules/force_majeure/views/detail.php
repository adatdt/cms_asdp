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
                    <a href="<?php echo site_url('force_majeure') ?>"
                           class="btn btn-sm btn-warning">Kembali</a>
                </div>
			</div>
			<div class="portlet-body">		
				<!-- strat row -->
				<div class="row">
					<div class="col-lg-4">
						<table class="table">
							<tbody>
								<tr style="background:#FFCC00; color:#FFFFFF;">
									<td colspan="3">Data Force</td>
								</tr>
								<tr class="warning">
									<td>Tanggal </td>
									<td>:</td><td><?php echo $header->date ?></td>
								</tr>
								<tr class="warning">
									<td>Keterangan</td>
									<td>:</td><td><?php echo $header->remark ?></td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<div class="col-lg-8">
						<table class="table table-striped">
							<thead>
								<tr style="background:#FFCC00; color:#FFFFFF;">
									<td colspan="4">Detail Force </td>
								</tr>
								<tr style="background:#FFCC00; color:#FFFFFF;">
									<th width="50px" align="center">No</th>
									<th>Nama</th>
									<th>Nomor Tiket</th>
									<th style="text-align: right;">Tarif (Rp)</th>
								</tr>
							</thead> 
							<tbody>
					<?php 
						$t_vehicle = 0;
						$t_passenger = 0;
						foreach($detail as $k => $row){
					?>
						<tr class="warning">
							<td align="center"><?php echo $k+1 ?> </td>

							<?php if($row->name == null){ ?>
								<td><?php echo $row->vehicle ?> - Kendaraan</td>
							<?php }else{ ?>
								<td><?php echo $row->name ?> </td>
							<?php } ?>

							<td><?php echo $row->ticket_number ?> </td>
							<?php if($row->name == null){ $t_vehicle += $row->fare_vehicle; ?>
								<td align="right"><?php echo number_format($row->fare_vehicle,0,',','.') ?> </td>
							<?php }else{ $t_passenger += $row->fare; ?>
								<td align="right"><?php echo number_format($row->fare,0,',','.') ?> </td>
							<?php } ?>
						</tr>
					<?php } ?>
						<tr style="background:#FFCC00; color:#FFFFFF;">
							<td colspan="3" align="center">Jumlah</td>
							<td align="right"><?php echo number_format($t_vehicle+$t_passenger,0,',','.') ?></td>
						</tr>
					</tbody>
						</table>
					</div>
				</div>
			</div> 
		</div>

	</div>
