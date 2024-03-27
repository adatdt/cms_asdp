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

            <!-- Search -->
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="input-group">
                        <div class="input-group-addon">Tanggal</div>
                        <input type="text" class="form-control" id="date" value="<?php echo date('Y-m-d') ?>" autocomplete="off" readonly>
                    </div>
                </div>
                <!-- <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="input-group">
                        <div class="input-group-addon">End Date</div>
                        <input type="text" class="form-control" id="date2" value="<?php echo date('Y-m-d') ?>" autocomplete="off" readonly>
                    </div>
                </div> -->

                <!-- <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="input-group">
                        <div class="input-group-addon">Kelas Layanan</div>
                        <?php //echo $shipClass 
                        ?>
                    </div>
                </div> -->

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">Pelabuhan</span>
                            <!-- <?php //echo $port; 
                                    ?> -->
                            <?php echo form_dropdown('', $port, '', 'id="origin" class="form-control select2"') ?>
                            <span class="input-group-btn">
                                <button class="btn btn-success" id="searching" type="button" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" title="Search">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>


            </div>
            <!-- End search -->

            <div class="row" id="grafik-checkin"></div>

        </div>

    </div>
</div>

<script src="<?php echo base_url('assets/global/plugins/echarts4/echarts.js') ?>"></script>

<script>
    function chartLine(data, id_item) {

        var labelOption = {
            show: true,
            position: 'top',
            distance: '10',
            align: 'insideBottom',
            verticalAlign: 'middle',
            rotate: 90,
            fontSize: 10,
            rich: {
                name: {}
            }
        };

        // console.log(data.jam);

        // var jam = [];
        // for (var h = 0; h < 24; h++) {
        //     jam.push((h < 9) ? `0${h}:00` : `${h}:00`);
        // }
        // console.log(data.data);

        // require.config({
        //     paths: {
        //         echarts: '<?php echo base_url() ?>assets/global/plugins/echarts',
        //     }
        // });

        // require(
        //     [
        //         'echarts',
        //         'echarts/chart/bar',
        //         'echarts/chart/line'
        //     ],
        // function() {
        var mycharts = echarts.init(document.getElementById(id_item));
        var colors = ['#0073c8', '#FFA500', '#999', '#FFD700'];

        var option = {
            color: colors,
            tooltip: {
                trigger: 'axis',
                enterable:true,
                // padding: [55, 200],
                triggerOn : 'click',
                // transitionDuration : 10,
                // showDelay:40,
                // hideDelay:200,
                axisPointer: {
                    type: 'shadow',
                },
                formatter: function(params, c) {
                    console.log(params, c)
                    // console.log(params, typeof data.reservasi_grup);
                    let rezReservasi = '',
                        rezCheckIn = '',
                        rezBCheckIn = '';
                    Object.keys(data.reservasi_grup).forEach(function(key) {
                        let dataRG = data.reservasi_grup[key];
                        rezReservasi += `<li>- ${dataRG.name} : ${dataRG.data[params[0].dataIndex]} </li>`
                    })
                    Object.keys(data.checkin_grup).forEach(function(key) {
                        let dataRG = data.checkin_grup[key];
                        rezCheckIn += `<li>- ${dataRG.name} : ${dataRG.data[params[1].dataIndex]}</li>`
                    })
                    Object.keys(data.sisa_quota_grup).forEach(function(key) {
                        let dataRG = data.sisa_quota_grup[key];
                        rezBCheckIn += `<li>- ${dataRG.name} : ${dataRG.data[params[2].dataIndex]}</li>`
                    })
                    var colorSpan = color => '<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background-color:' + color + '"></span>';
                    let rez = '<h5 style="font-weight:bold; text-align: center;">Pukul ' + params[0].name + '</h5>';

                    rez += `<div style="margin-bottom:0;" class="tltpkuota">
                            ${colorSpan(colors[3])} Kuota : ${params[3].value}
                            </div>`;

                    rez += `<div style="margin-bottom:0;" class="tltpReservasi">
                            ${colorSpan(colors[0])} Telah Reservasi : ${params[0].value}
                            <ul style="padding:0; padding-left:18px;list-style-type:none; display:none" class="tltpDetailReservasi">
                            ${rezReservasi}

                            <p></p>
                                <br><div style="margin-bottom:0;" ><a href="http://localhost:8080/asdp-admin" target="_blank">Detail</a></div>

                            </ul>
                            </div>`;

                    rez += `<div style="margin-bottom:0;" class="tltpCheckin">
                            ${colorSpan(colors[1])} Telah Check In : ${params[1].value}
                            <ul style="padding:0; padding-left:18px;list-style-type:none; display:none" class="tltpDetailCheckin">
                            ${rezCheckIn}

                            <p></p>
                                <br><div style="margin-bottom:0;" ><a href="http://localhost:8080/asdp-admin" target="_blank">Detail</a></div>

                            </ul>
                            </div>`;
                    rez += `<div style="margin-bottom:0;" class="tltpNCheckin">
                            ${colorSpan(colors[2])} 
                            Belum Check In : ${params[2].value}
                            <ul style="padding:0; padding-left:18px;list-style-type:none; display:none" class="tltpDetailNCheckin">
                            ${rezBCheckIn}

                                <p></p>
                                <br><div style="margin-bottom:0;" ><a href="http://localhost:8080/asdp-admin" target="_blank">Detail</a></div>
                            </ul>
                            </div>`;

                    return rez;
                }
            },
            // title: {
            //     text: 'Go Show & Online',
            //     subtext: '',
            //     x: 'center'
            // },
            xAxis: {
                splitLine: {
                    show: true
                },
                type: 'category',
                data: data.jam,
            },
            yAxis: {
                type: 'value',
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
                        show: true,
                        title: 'Save Image'
                    }
                }
            },
            legend: {
                // left: 10,
                selectedMode: true,
                // data: ['Reservasi keberangkatan', 'Jumlah telah Chekin', 'Sisa', 'Kuota']
                data: ['Jumlah Kend. Reservasi', 'Jumlah Kend. Telah Check In', 'Sisa Kend. Belum Check In', 'Kuota']
            },
            series: [{
                    name: 'Jumlah Kend. Reservasi',
                    data: data.reservasi,
                    type: 'bar',
                    label: labelOption,
                },
                {
                    name: 'Jumlah Kend. Telah Check In',
                    data: data.checkin,
                    type: 'bar',
                    label: labelOption,
                },
                {
                    name: 'Sisa Kend. Belum Check In',
                    data: data.sisa_quota,
                    type: 'bar',
                    label: labelOption,
                },
                {
                    name: 'Kuota',
                    // data: [82, 93, 90, 93, 120, 133, 132, 150, 100],
                    data: data.quota,
                    type: 'line',
                    label: {
                        show: true,
                    }

                },
            ]
        };

        mycharts.setOption(option);
        mycharts.on('click', data, function(params) {
            // console.log(params);
            let arrClass = [{
                    value: 'tltpReservasi',
                    detail: 'tltpDetailReservasi'
                },
                {
                    value: 'tltpCheckin',
                    detail: 'tltpDetailCheckin'
                },
                {
                    value: 'tltpNCheckin',
                    detail: 'tltpDetailNCheckin'
                }
            ];

            let filterData = '';

            if (params.seriesName === 'Jumlah Kend. Reservasi') {
                filterData = 'tltpReservasi';
            } else if (params.seriesName === 'Jumlah Kend. Telah Check In') {
                filterData = 'tltpCheckin';
            } else if (params.seriesName === 'Sisa Kend. Belum Check In') {
                filterData = 'tltpNCheckin';
            }

            // console.log(arrClass); 

            if (filterData !== '') {
                $('.tltpkuota').css({
                    display: 'none'
                })

                arrClass.map((i, v) => {
                    if (filterData !== i.value) {
                        $(`.${i.value}`).css({
                            display: 'none'
                        })
                    } else {
                        $(`.${i.detail}`).css({
                            display: ''
                        })
                    }
                });
            }
        })

        // window.onresize = function() {
        // mycharts.resize();
        // }
        // }
        // )
    }

    function tanggal_(tgl) {
        var date = tgl ? new Date(tgl) : new Date(),
            year = date.getFullYear(),
            month = date.getMonth(),
            months = new Array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'),
            sort_months = new Array('Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'),

            d = date.getDate(),
            day = date.getDay(),

            h = date.getHours(),
            m = date.getMinutes(),
            s = date.getSeconds();

        if (h < 10) {
            h = "0" + h;
        }

        if (m < 10) {
            m = "0" + m;
        }

        if (s < 10) {
            s = "0" + s;
        }

        // var result = `${d} ${tgl ? months[month] : sort_months[month]} ${year}`;
        var result = `${d} ${tgl ? months[month] : months[month]} ${year}`;

        if (!tgl) {
            result += ` Pukul ${h}:${m}:${s}`;
        }

        return result;
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
            // endDate: new Date(),
            endDate: '+60d',
        }).on('changeDate', function(e) {

        });

        // $('#date2').datepicker({
        //     format: 'yyyy-mm-dd',
        //     changeMonth: true,
        //     changeYear: true,
        //     autoclose: true,
        //     endDate: new Date(),
        // }).on('changeDate', function(e) {
        //     $('#date').datepicker('setEndDate', e.date);
        // });

        var listDashboard = function() {
            $.ajax({
                url: '<?php echo base_url() ?>dashboard/checkinkendaraan/list_grafik',
                type: 'POST',
                data: {
                    start_date: $('#date').val(),
                    // end_date: $('#date2').val(),
                    // tanggal: '2020-02-05',
                    origin: $('#origin').val(),
                    // ship_class: $('#ship_class').val(),
                    port: $('#origin option:selected').text(),
                    sc: $('#ship_class option:selected').text()
                },
                dataType: 'json',
                beforeSend: function() {
                    $('.block-ui').block({
                        message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>',
                        css: {
                            // padding: 0,
                            // margin: 0,
                            // width: '120px',
                            // top: '40%',
                            // left: '40%',
                            // textAlign: 'center',
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
                    $('#grafik-checkin').empty();

                    // console.log(data)
                    if (data.code == 1) {
                        // var htmlContent = '';
                        Object.keys(data.data).forEach(function(item, key) {
                            // console.log(key, data.data[item]);
                            var crackTitle = item.split('_');
                            // var titleGrafik = crackTitle[1] ? `${crackTitle[0]} (${crackTitle[1]})` : crackTitle[0];
                            var titleGrafik = crackTitle[0];
                            var titleClass = crackTitle[1] ? crackTitle[1] : 'Reguler';
                            var htmlContent = `
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-bottom-40">
                                    <div class="portlet light portlet-fit bordered block-ui">
                                        <div class="portlet-title padding-title-chart text-center">
                                            <h4 class="bold">Grafik Produksi Jumlah Kendaraan Reservasi, Jumlah</h4>
                                            <h4 class="bold">Kendaraan Telah Check In, dan Sisa Kendaraan Belum Check In</h4>
                                            <h4 class="bold">Pelabuhan Penyeberangan ${titleGrafik}</h4>
                                            <h4 class="bold">Layanan ${titleClass} Tanggal ${tanggal_($('#date').val())}</h4>
                                            <h4 class="bold">Waktu Akses ${tanggal_()}</h4>
                                        </div>
                                        <div class="portlet-body padding-body">
                                            <div id="${item}" class="height-chart" style="width: 100%; height: 350px;"></div>
                                        </div>
                                    </div>
                                </div>`;
                            // var htmlContent = `
                            //     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-bottom-40">
                            //         <div class="portlet light portlet-fit bordered block-ui">
                            //             <div class="portlet-title padding-title-chart text-center">
                            //                 <h4 class="bold">Selisih antara Reservasi dan Kendaraan yang telah Check-in</h4>
                            //                 <h4 class="bold">Seluruh Kendaraan, Pelabuhan ${titleGrafik} Tanggal ${tanggal_($('#date').val())}</h4>
                            //                 <h4 class="bold">Waktu Akses ${tanggal_()}</h4>
                            //             </div>
                            //             <div class="portlet-body padding-body">
                            //                 <div id="${item}" class="height-chart" style="width: 100%; height: 350px;"></div>
                            //             </div>
                            //         </div>
                            //     </div>`;
                            $('#grafik-checkin').append(htmlContent);
                            // .hide()
                            // .fadeIn(500);


                            chartLine(data.data[item], item);
                        })
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
        }

        listDashboard();

        $('#searching').click(function() {
            listDashboard();
        })

    })
</script>