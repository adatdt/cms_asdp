<?php $this->load->helper('nutech_helper'); ?>
<div class="page-content-wrapper">
  <div class="page-content">
    <div class="page-bar">
      <ul class="page-breadcrumb">
        <li>
          <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
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
          <h4><?php echo $title; ?></h4>
        </div>
        <div class="tools">
          <div class="pull-right">
            <?php echo generate_button('schedule', 'view', '<a href="' . site_url('sail') . '" class="btn btn-warning">Kembali</a>'); ?>
          </div>
        </div>
      </div>
      <div class="portlet-body">
		<!-- start -->
		<ul class="nav nav-tabs">
			<li class="<?php echo ($tab == 'passanger') ? 'active' : ''; ?>"><a href="#passanger" data-toggle="tab"><span style="font-size:13px width: 50px text-align: center" class="widget-caption btn ">Boarding Penumpang</span></a></li>
			<li class="<?php echo ($tab == 'vehicle') ? 'active' : ''; ?>"><a href="#vehicle" data-toggle="tab"><span style="font-size:13px width: 140px text-align: center" class="widget-caption btn ">Boarding Kendaraan </span></a></li>
		</ul>
		<!-- end -->
	  
		<!-- Start row -->
		<div class="row">
			<div class="col-lg-12"> 
				<table class="table table-striped">
					<thead>
						<tr style="background:#FFCC00; color:#FFFFFF;" >
							<td colspan="9">Data Penumpang </td>
						</tr>
						<tr style="background:#FFCC00; color:#FFFFFF;" >
							<td>Nama</td><td>NO KTP</td><td>Jenis Kelamin</td><td>Tanggal Lahir</td><td>Kota Asal</td><td>Nomer Tiket</td> 
							<td>Tanggal Boarding</td><td>Tanggal Keberangkatan</td><td>Jam Keberangkatan</td> 
						</tr>
					</thead>
					<tbody>
					<?php foreach ($passanger as $passanger ){?>
					<tr>
						<td > <?php echo $passanger->name ?></td>
						<td > <?php echo $passanger->id_number ?></td>
						<td > <?php echo $passanger->gender=='L'?'Laki-laki':'Perempuan'; ?></td>
						<td > <?php echo format_date($passanger->birth_date) ?></td>
						<td > <?php echo $passanger->city ?></td>
						<td > <?php echo $passanger->ticket_number ?></td>
						<td > <?php echo format_date($passanger->created_on) ?></td>
						<td > <?php echo format_date($passanger->depart_date); ?></td>
						<td > <?php echo format_time($passanger->departure); ?></td>
					</tr>
					<?php }?>


					</tbody>
				</table>
				<?php }?>
			</div>
		</div>
		<!-- end row -->
		
		<!-- end row -->
		<div class="row">
			<div class="col-lg-12"> 
			<?php if (!empty($vehicle)) {?>
				<table class="table table-striped">
					<thead>
						<tr style="background:#FFCC00; color:#FFFFFF;" >
							<td colspan="9">Data Kendaraan </td>
						</tr>
						<tr style="background:#FFCC00; color:#FFFFFF;" >
							<td>Golongan</td><td>Nama</td><td>Plat No</td><td>Nomer Tiket</td> 
							<td>Tanggal Boarding</td><td>Tanggal Keberangkatan</td><td>Jam Keberangkatan</td> 
						</tr>
					</thead>
					<tbody>
	
					<tr>
						<td > </td>
						<td > </td>
						<td > </td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td ></td>
					</tr>
					</tbody>
				</table>
				<?php } ?>
				<?php echo $approve; ?>
			</div>
		</div>
		<!-- end row -->

	</div> 
</div>

</div>
