<div class="page-content-wrapper">
  <div class="page-content">
    <div class="page-bar">
      <ul class="page-breadcrumb">
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
    <div style="padding-top: 10px;">
      <div class="portlet box blue-madison" style="border: none;">
        <div class="portlet-body">
          <div id="myCarousel" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
              <?php 
              $foto = array('slider1.png');
              foreach($foto as $key => $val) { 
                $active = '';
                if($key == 0){
                  $active = 'class="active"';
                }
                ?>
                <li data-target="#myCarousel" data-slide-to="<?php echo $key ?>" $active></li>
              <?php } ?>
            </ol>

            <div class="carousel-inner">
              <?php
              foreach($foto as $key => $val) { 
                $active = '';
                if($key == 0){
                  $active = 'active';
                }
                ?>
                <div class="item <?php echo $active ?>">
                  <img src="<?php echo base_url() ?>assets/img/<?php echo $val ?>" class="img-carousel">
                </div>              
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>