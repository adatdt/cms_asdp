<div class="page-content-wrapper">
  <div class="page-content">
    <div class="page-bar">
      <ul class="page-breadcrumb">
        <li>
          <?php echo '<a href="' . $url_home . '">' . $home; ?></a>
          <i class="fa fa-circle"></i>
        </li>
        <li>
          <span><?php echo $title; ?></span>
        </li>
      </ul>
      <div class="page-toolbar">
        <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
          <span class="thin uppercase hidden-xs" id="datetime"></span>
          <script type="text/javascript">
            window.onload = date_time('datetime');
          </script>
        </div>
      </div>
    </div>
    <div class="my-div-body">
      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="input-group">
            <div class="input-group-addon">Start Date</div>
            <input type="text" class="form-control" id="date" value="<?php echo date('Y-m-d') ?>" autocomplete="off" readonly>
          </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="input-group">
            <div class="input-group-addon">End Date</div>
            <input type="text" class="form-control" id="date2" value="<?php echo date('Y-m-d') ?>" autocomplete="off" readonly>
          </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon">Pelabuhan</span>
              <?php echo $port; ?>
              <span class="input-group-btn">
                <button class="btn btn-success" id="searching" type="button" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" title="Search">
                  <i class="fa fa-search"></i>
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <a class="dashboard-stat dashboard-stat-v2 blue" href="javascript:;">
            <div class="visual">
              <i class="fa fa-users"></i>
            </div>
            <div class="details">
              <div class="number">
                <span data-counter="counterup" id="total_passenger">
                  0
                </span>
              </div>
              <div class="desc"> Total Pejalan Kaki </div>
            </div>
          </a>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <a class="dashboard-stat dashboard-stat-v2 red" href="javascript:;">
            <div class="visual">
              <i class="fa fa-car"></i>
            </div>
            <div class="details">
              <div class="number">
                <span data-counter="counterup" id="total_vehicle">
                  0
                </span>
              </div>
              <div class="desc"> Total Kendaraan </div>
            </div>
          </a>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <a class="dashboard-stat dashboard-stat-v2 yellow" href="javascript:;">
            <div class="visual">
              <i class="fa fa-users"></i>
            </div>
            <div class="details">
              <div class="number">
                <span data-counter="counterup" id="boarding_passenger">
                  0
                </span>
              </div>
              <div class="desc"> Penumpang Boarding </div>
            </div>
          </a>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <a class="dashboard-stat dashboard-stat-v2 green" href="javascript:;">
            <div class="visual">
              <i class="fa fa-car"></i>
            </div>
            <div class="details">
              <div class="number">
                <span data-counter="counterup" id="boarding_vehicle">
                  0
                </span>
              </div>
              <div class="desc"> Kendaraan Boarding </div>
            </div>
          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <div class="portlet light portlet-fit bordered block-ui">
            <div class="portlet-title padding-title-chart">
              <div class="caption">
                <i class=" fa fa-pie-chart font-green"></i>
                <span class="caption-subject font-green bold uppercase">Penjualan Tiket Go Show & Online</span>
              </div>
            </div>

            <div class="portlet-body padding-body">
              <div id="volTicket" class="height-chart"></div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <div class="portlet light portlet-fit bordered block-ui">
            <div class="portlet-title padding-title-chart">
              <div class="caption">
                <i class=" fa fa-pie-chart font-green"></i>
                <span class="caption-subject font-green bold uppercase">Pendapatan Tiket Go Show & Online</span>
              </div>
            </div>

            <div class="portlet-body padding-body">
              <div id="revTicket" class="height-chart"></div>
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="portlet light portlet-fit bordered block-ui">
            <div class="portlet-title padding-title-chart">
              <div class="caption">
                <i class=" fa fa-line-chart font-green"></i>
                <span class="caption-subject font-green bold uppercase">Penjualan Tiket</span>
              </div>
            </div>

            <div class="portlet-body padding-body">
              <div id="daysChart" class="height-chart"></div>
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="portlet light portlet-fit bordered block-ui" style="height:380px;">
            <div class="portlet-title padding-title-chart">
              <div class="caption">
                <i class=" fa fa-line-chart font-green"></i>
                <span class="caption-subject font-green bold uppercase">LINE METER</span>
              </div>
            </div>

            <div class="portlet-body padding-body">
              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="input-group">
                  <div class="input-group-addon">Date</div>
                  <input type="text" class="form-control" id="dates" value="<?php echo date('Y-m-d') ?>" autocomplete="off" readonly>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon">Ship Class</span>
                    <?php echo $ship; ?>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon">Pelabuhan</span>
                    <?php echo $port; ?>
                    <span class="input-group-btn">
                      <button class="btn btn-success" id="searchings" type="button" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" title="Search">
                        <i class="fa fa-search"></i>
                      </button>
                    </span>
                  </div>
                </div>
              </div>

              <div id="pcmChart" class="height-chart" ></div>
            </div>
          </div>
        </div>
        <br>
      </div>
    </div>
  </div>
</div>
<style type="text/css">
  .padding-title-chart {
    padding: 5px 10px 0px 5px !important;
  }

  .padding-body {
    padding: 0px 5px 0px 20px !important;
  }
</style>
<script type="text/javascript">
  function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
      x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }

    return x1 + x2;
  }

  var ticket_chart;

  function Tickets(data) {
    require.config({
      paths: {
        echarts: 'assets/global/plugins/echarts/',
      }
    });

    require(
      [
        'echarts',
        'echarts/chart/pie',
      ],

      function(ec) {
        var name = 'Type'
        ticket_chart = ec.init(document.getElementById(data.id));
        var ticket_chart_options = {
          tooltip: {
            trigger: 'item',
            formatter: function(params, ticket, callback) {
              html = params[0] + '<br>';
              html += params[1] + ' :<br>' + addCommas(params[2]);

              return html;
            }
          },
          legend: {
            show: true,
            x: 'left',
            y: 'center',
            orient: 'vertical',
            data: data.data.ticket
          },
          color: data.color,
          toolbox: {
            show: true,
            feature: {
              mark: {
                show: false
              },
              dataView: {
                show: false,
                readOnly: false
              },
              restore: {
                show: false
              },
              saveAsImage: {
                show: true
              }
            }
          },
          calculable: false,
          series: [{
            name: name,
            type: 'pie',
            center: ['50%', '50%'],
            radius: '80%',
            itemStyle: {
              normal: {
                label: {
                  formatter: function(params) {
                    return params.name
                  }
                },
              },
            },
            data: data.data.total
          }]
        };

        ticket_chart.setOption(ticket_chart_options);

        $(".menu-toggler").click(function() {
          ticket_chart.resize();
        });

        $(window).resize(function() {
          ticket_chart.resize();
        });
      }
    );
  }

  var daysChart;

  function daysCharts(data) {
    require.config({
      paths: {
        echarts: 'assets/global/plugins/echarts/',
      }
    });

    require(
      [
        'echarts',
        'echarts/chart/bar',
      ],
      function(ec) {
        daysChart = ec.init(document.getElementById('daysChart'));
        var daysChart_options = {
          tooltip: {
            trigger: 'axis',
            formatter: function(params, ticket, callback) {
              html = params[0][1] + ' :<br>' + addCommas(params[0].data);

              return html;
            }
          },
          title: {
            text: 'Go Show & Online',
            subtext: '',
            x: 'center'
          },
          grid: {
            x: 50,
            x2: 25,
            y: 45,
            y2: 60,
          },
          toolbox: {
            show: true,
            feature: {
              mark: {
                show: false
              },
              dataView: {
                show: false,
                readOnly: false
              },
              magicType: {
                show: false,
                type: ['line', 'bar']
              },
              restore: {
                show: false
              },
              saveAsImage: {
                show: true
              }
            }
          },
          calculable: false,
          xAxis: [{
            type: 'category',
            data: data.date,
            // axisLabel:{
            //   rotate:16
            // }
          }],
          yAxis: [{
            type: 'value',
            name: 'Total',
            splitArea: {
              show: true
            },
            axisLabel: {
              formatter: function(params) {
                return addCommas(params)
              }
            },
          }],
          series: [{
            type: 'bar',
            data: data.total,
            // itemStyle: {
            //   normal: {
            //     color: function(params) {
            //       var colorList = [
            //       '#4B77BE','#32C5D2','#26C281','#E7505A','#C49F47',
            //       '#E87E04','#C8D046','#9A12B3','#F3C200','#95A5A6',
            //       '#D7504B','#C6E579','#F4E001','#F0805A','#26C0C0'
            //       ];
            //       return colorList[params.dataIndex]
            //     },
            //     label: {
            //       show: true,
            //       position: 'top',
            //       formatter: function(params){
            //         return addCommas(params.data)
            //       }
            //     }
            //   }
            // },
          }]
        };

        daysChart.setOption(daysChart_options);

        $(".menu-toggler").click(function() {
          daysChart.resize();
        });

        $(window).resize(function() {
          daysChart.resize();
        });
      }
    );
  }

  $(document).ready(function() {
    setTimeout(function() {
      $('.menu-toggler').trigger('click');
      $('.select2').select2();
    }, 1);

    $(".menu-toggler").click(function() {
      $('.select2').css('width', '100%');
    });

    $('#date').datepicker({
      format: 'yyyy-mm-dd',
      changeMonth: true,
      changeYear: true,
      autoclose: true,
      endDate: new Date(),
    }).on('changeDate', function(e) {

    });
    $('#dates').datepicker({
      format: 'yyyy-mm-dd',
      changeMonth: true,
      changeYear: true,
      autoclose: true,
      endDate: new Date(),
    }).on('changeDate', function(e) {

    });

    $('#date2').datepicker({
      format: 'yyyy-mm-dd',
      changeMonth: true,
      changeYear: true,
      autoclose: true,
      endDate: new Date(),
    }).on('changeDate', function(e) {
      $('#date').datepicker('setEndDate', e.date);
    });

    point = 3;
    height = window.screen.availHeight / point;

    $('.height-chart').css('height', height + 'px');

    $(window).resize(function() {
      height = window.screen.availHeight / point;
      $('.height-chart').css('height', height + 'px');
    });
    var gateCardChart;


    //Line Meters pcm

    function PcmCharts(data) {

      var dataX = data.date;
      console.log(dataX)
      // var dataship = data.ship;
      var datatotal = data.total_lm;
      var sudahdigunain = data.sudahdigunain;
      var ketersediaan = data.ketersediaan;
      
      $(function() {
        require.config({
          paths: {
            echarts: 'assets/global/plugins/echarts/',
          }
        });

        require(
          [
            'echarts',
            'echarts/chart/line',
          ],

          function(ec) {
            gateCardChart = ec.init(document.getElementById('pcmChart'));

            // var ship = 'Ship';
            var total = 'total';
            var sudah = 'Sudah Digunakan';
            var sedia = 'Ketersedian';
            var markShip = [];
            var markTotal = [];
            var markSudah = [];
            var markSedia = [];

            // for (i in ship) {
            //   markShip[i] = {
            //     name: dataX[i],
            //     value: number_format(ship[i]),
            //     xAxis: dataX[i],
            //     yAxis: dataship[i]
            //   };
            // }

            for (i in datatotal) {
              markTotal[i] = {
                name: dataX[i],
                value: number_format(total[i]),
                xAxis: dataX[i],
                yAxis: datatotal[i]
              };
            }
            for (i in sudahdigunain) {
              markSudah[i] = {
                name: dataX[i],
                value: number_format(sudah[i]),
                xAxis: dataX[i],
                yAxis: sudahdigunain[i]
              };
            }
            for (i in ketersediaan) {
              markSedia[i] = {
                name: dataX[i],
                value: number_format(sedia[i]),
                xAxis: dataX[i],
                yAxis: ketersediaan[i]
              };
            }

            gateCardChart_options = {
              grid: {
                x: 50,
                x2: 35,
                y: 25,
                y2: 20,
              },

              tooltip: {
                trigger: 'axis',
                // formatter: function(params, ticket, callback) {
                //   html = params[0][1] + ':00<br>';
                //   html += params[0].seriesName + ' : ' + number_format(params[0].data) + '<br>';
                //   html += params[1].seriesName + ' : ' + number_format(params[1].data) + '<br>';
                //   return html;
                // }
              },

              legend: {
                data: [total, sudah, sedia]
              },

              color: ['#FF0000', '#036dbf', '#FFA500'],

              toolbox: {
                show: true,
                feature: {
                  mark: {
                    show: false
                  },
                  dataView: {
                    show: false,
                    readOnly: false
                  },
                  magicType: {
                    show: false,
                    type: ['line', 'bar']
                  },
                  restore: {
                    show: false
                  },
                  saveAsImage: {
                    show: true
                  }
                }
              },
              calculable: true,
              xAxis: [{
                type: 'category',
                name: 'Time',
                boundaryGap: false,
                data: dataX
              }],
              yAxis: [{
                type: 'value',
                name: 'Total',
                splitArea: {
                  show: true
                },
                axisLabel: {
                  formatter: function(params) {
                    return number_format(params)
                  }
                },
              }],
              series: [{
                name: total,
                type: 'line',
                data: datatotal,

              }, {
                name: sudah,
                type: 'line',
                data: sudahdigunain,

              }, {
                name: sedia,
                type: 'line',
                data: ketersediaan,

              }]
            };

            gateCardChart.setOption(gateCardChart_options);

            $(".menu-toggler").click(function() {
              gateCardChart.resize()
            });

            $(window).resize(function() {
              gateCardChart.resize()
            });
          }
        );
      })
    }

    var listDashboard = function() {
      $.ajax({
        url: 'dashboard/listDashboard',
        data: {
          date: $('#date').val(),
          date2: $('#date2').val(),
          origin: $('#origin').val(),

        },
        type: 'POST',
        dataType: 'json',

        beforeSend: function() {
          $('.block-ui').block({
            message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>',
            css: {
              padding: 0,
              margin: 0,
              width: '120px',
              top: '40%',
              left: '40%',
              textAlign: 'center',
              color: '#000',
              border: '0px solid #aaa',
              backgroundColor: '#fff',
              cursor: 'wait'
            },
            centerX: false,
            centerY: false,
            overlayCSS: {
              backgroundColor: '#000',
              opacity: 0.2,
              cursor: 'wait'
            },
          });

          $('#searching').button('loading');
        },

        success: function(data) {
          // console.log(data)
          if (data.code == 1) {
            d = data.data;

            $('#total_passenger').html(addCommas(d.total_passenger));
            $('#total_vehicle').html(addCommas(d.total_vehicle));
            $('#boarding_passenger').html(addCommas(d.boarding_passenger));
            $('#boarding_vehicle').html(addCommas(d.boarding_vehicle));

            Tickets({
              id: 'volTicket',
              data: d.volume_ticket,
              color: ['#FFD700', '#0073c8']
            });

            Tickets({
              id: 'revTicket',
              data: d.revenue_ticket,
              color: ['#FFA500', '#000080']
            });

            daysCharts(d.days);
          } else {
            toastr.error(d.message, 'Gagal')
          }
        },

        error: function() {
          console.log('Please contact the administrator');
        },

        complete: function() {
          $('.block-ui').unblock();
          $('#searching').button('reset');
        }
      })

      // .done(

      // setTime = setTimeout(function(){
      //   listDashboard()
      // }, 60000)

      // );  
    }
    var listPCM= function() {
      $.ajax({
        url: 'dashboard/listPCM',
        data: {
          dates: $('#dates').val(),
          origins:  $('#origins').val(),
          ship:$('#ship').val()

        },
        type: 'POST',
        dataType: 'json',

        beforeSend: function() {
          $('.block-ui').block({
            message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>',
            css: {
              padding: 0,
              margin: 0,
              width: '120px',
              top: '40%',
              left: '40%',
              textAlign: 'center',
              color: '#000',
              border: '0px solid #aaa',
              backgroundColor: '#fff',
              cursor: 'wait'
            },
            centerX: false,
            centerY: false,
            overlayCSS: {
              backgroundColor: '#000',
              opacity: 0.2,
              cursor: 'wait'
            },
          });

          $('#searchings').button('loading');
        },

        success: function(data) {
          console.log(data)
          if (data.code == 1) {
            d = data.data;
            PcmCharts(d.PCM);
          } else {
            toastr.error(data.message, 'Gagal')
          }
        },

        error: function() {
          console.log('Please contact the administrator');
        },

        complete: function() {
          $('.block-ui').unblock();
          $('#searchings').button('reset');
        }
      })

      // .done(

      // setTime = setTimeout(function(){
      //   listDashboard()
      // }, 60000)

      // );  
    }
    
    listDashboard();
    listPCM();
    // $('#date').change(function(){
    //   clearTimeout(setTime)
    //   listDashboard();
    // });

    // $('#date2').change(function(){
    //   clearTimeout(setTime)
    //   listDashboard();
    // });

    // $('#origin').change(function(){
    //   clearTimeout(setTime)
    //   listDashboard();
    // })

    $('#searching').click(function() {
      // clearTimeout(setTime)
      listDashboard();
    });
    $('#searchings').click(function() {
      // clearTimeout(setTime)
      listDashboard();
     listPCM();
    });
  });
</script>