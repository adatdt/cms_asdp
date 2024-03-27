<div class="page-content-wrapper">
  <div class="page-content">
    <div class="page-bar">
      <ul class="page-breadcrumb">
        <li>
          <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
          <i class="fa fa-circle"></i>
        </li>
        <li>
          <?php echo '<a href="' . $url_parent . '">' . $parent . '</a>'; ?>
          <i class="fa fa-circle"></i>
        </li>
        <li>
          <?php echo '<a href="' . $url_parent1 . '">' . $parent1 . '</a>'; ?>
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
            <a href="<?php echo site_url('booking') ?>" class="btn btn-sm btn-warning">Kembali</a>
        </div>
      </div>
      <div class="portlet-body">
	  	<div class="row">
			<div class="col-lg-3">
				<table class="table table-striped ">
					<thead>
						<tr class="success">
							<td align="center">Kode Booking</td>
						</tr>
					</thead>
					<tbody>
						<tr class="warning">
							<td align="center"><?php echo $detail->code; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-lg-3">
				<table class="table table-striped">
					<tr class="success">
						<td align="center">Waktu Keberangkatan</td>
					<tr>
					<tr class="warning">
						<td align="center"><?php echo format_date($detail->depart_date) ?></td>
					<tr>
					
				</table>
			</div>
			
			<div class="col-lg-3">
				<table class="table table-striped">
					<tr class="success">
						<td align="center">Jam Keberangkatan</td>
					<tr>
					<tr class="warning">
						<td align="center">Pukul : <?php echo format_time($detail->departure) ?></td>
					<tr>
				</table>
			</div>
			<div class="col-lg-3">
				<table class="table table-striped">
					<tr class="success">
						<td align="center">Nama Kapal</td>
					<tr>
					<tr class="warning">
						<td align="center"><?php echo $detail->ship_name?></td>
					<tr>			
				</table>
			</div>
			
		</div>
		
		<!-- strat row -->
		<div class="row">
			<div class="col-lg-6">
				<table class="table">
					<tbody>
						<tr class="success">
							<td colspan="3">Data Pelanggan</td>
						</tr>
						<tr class="warning">
							<td>Nama </td>
							<td>:</td><td><?php echo $detail->customer_name?></td>
						</tr>
						<tr class="warning">
							<td>No Telepon</td>
							<td>:</td><td><?php echo $detail->phone?></td>
						</tr>
						<tr class="warning">
							<td>No Telepon</td>
							<td>:</td><td><?php echo $detail->email?></td>
						</tr>
						<tr class="warning">
							<td>Service</td>
							<td>:</td><td><?php echo $detail->name?></td>
						</tr>
					</tbody>
				</table>
			</div>
			
			<div class="col-lg-6">
				<table class="table">
					<tbody>
						<tr class="success">
							<td colspan="3">Data Booking</td>
						</tr>
						<tr class="warning">
							<td>Tanggal Booking </td>
							<td>:</td><td><?php echo format_date($detail->created_on); ?></td>
						</tr>
						<tr class="warning">
							<td>Keberangkatan</td>
							<td>:</td><td><?php echo $detail->origin_name?></td>
						</tr>
						<tr class="warning">
							<td>Tujuan</td>
							<td>:</td><td><?php echo $detail->destination_name ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			
		</div>
		<!-- end row -->
		<!--  start row -->
		<div class="row">
			<div class="col-lg-12">
				<table class="table table-striped ">
					<thead>
						<tr style="background:#FFCC00; color:#FFFFFF;">
							<td colspan="5">Data Tarif Kendaraan</td>
						</tr>
						<tr class="success">
							<td>Jenis Penumpang</td><td>Plat No</td><td>Jumlah</td><td>Tarif (Rp)</td><td >Subtotal (Rp)</td>
						</tr>
					</thead> 
					<tbody>
						<?php $grandtotal=0;
						foreach ($booking_vehicle as $class) {?>
						<tr class="warning">
							<td><?php echo $class->vehicle_class_name; ?></td>
							<td><?php echo $class->id_number; ?></td>  
							<td><?php echo $class->count?></td>
							<td align="right"><?php echo idr_currency($class->fare)?></td><td align="right"><?php echo idr_currency($class->sum_fare); ?></td>
						</tr>
						<?php  
							$grandtotal+=$class->sum_fare;
						} ?>
						<tr class="warning">
							<td colspan="4" style="text-align:center; background:#FFCC00; color:#FFFFFF;">Total (Rp)</td> 
							<td align="right"><?php echo idr_currency($grandtotal); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<!-- end row -->
				<!--  start row -->
		<div class="row">
			<div class="col-lg-12">
				<table class="table">
					<thead>
						<tr style="background:#FFCC00; color:#FFFFFF;" >
							<td colspan="5">Nama Penumpang </td>
						</tr>
						<tr class="success">
							<td>Nama</td><td>NO Identitas</td><td>Jenis Kelamin</td><td>Jenis Penumpang</td>
						</tr>
					</thead>
					<tbody>
					<?php 
							foreach($pssgr as $pssgr1){
					?>
						<tr class="warning">
							<td><?php echo $pssgr1->name ?> </td>
							<td><?php echo $pssgr1->id_number ?> </td>
							<td><?php echo $pssgr1->gender=='L'?'Laki-laki':'Prempuan'; ?></td>
							<td><?php echo $pssgr1->class_name?> </td>

						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		<!-- end row -->
 	</div> 
</div>

</div>
