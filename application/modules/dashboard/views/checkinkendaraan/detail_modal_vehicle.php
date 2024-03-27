<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-10 col-md-offset-1">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>KODE BOOKING</th>
                    <th>NO TIKET</th>
                    <th>NO TELEPON</th>
                    <th>NIK</th>
                    <th>NAMA PENUMPANG</th>
                    <th>UMUR</th>
                    <th>JENIS KELAMIN</th>
                    <th>TIPE PENUMPANG</th>
                    <th>LAYANAN</th>
                    <th>SERVIS</th>
                </tr>
            </thead>            
            <tbody>
                <?php $no=1; foreach ($detail as $key => $value) { ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= $value->booking_code ?></td>
                    <td><?= $value->ticket_number ?></td>
                    <td><?= $value->phone_number ?></td>
                    <td><?= $value->id_number ?></td>
                    <td><?= $value->name ?></td>
                    <td><?= $value->age ?></td>
                    <td><?= $value->gender ?></td>
                    <td><?= $value->tipe_penumpang ?></td>
                    <td><?= $value->layanan ?></td>
                    <td><?= $value->service ?></td>
                </tr>
                <?php $no++; }  ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">
</script>