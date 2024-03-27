<script src="../assets/global/plugins/echarts4/echarts.js"></script>
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
            <div class="row" id="grafik-checkin">

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
<script>
    String.prototype.ucwords = function() {
        var str = this.toLowerCase();
        return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
            function($1) {
                return $1.toUpperCase();
            });
    }

    function reservasiCharts(data, id_item) {
        // console.log(data)
        // based on prepared DOM, initialize echarts instance
        // var colors = ['#FFD700', '#0073c8', '#FFA500', '#999', '#f71717', ' #9933ff',' #cc0000'];

        var colors = [ '#FFD700','#0073c8', '#FFA500', '#999',' #9933ff',' #cc0000'];
        var myChart = echarts.init(document.getElementById(id_item));

        // specify chart configuration item and data
        var option;

        var yearCount = 5;
        var categoryCount = data.date.length;

        var xAxisData = [];
        var customData = [];
        var legendData = [];
        var dataList = [];

        let dataQuota=[];
        /*
        legendData.push('Kuota');
        legendData.push('Roda 2');
        legendData.push('Roda 4');
        legendData.push('Roda 4+');
        legendData.push('Akumulasi');
        legendData.push('Total boarding');
        legendData.push('Belum boarding');
        */
        legendData.push('Kuota');
        legendData.push('Jumlah Kend. Reservasi'); // nama harus sama data header detail yang dikirim
        legendData.push('Jumlah Kend. Telah Check In');
        legendData.push('Sisa Kend. Belum Check In');
        legendData.push('Total boarding');
        legendData.push('Belum boarding');

        var encodeY = [];
        // console.log(data)
        // 10
        //5
        for (var i = 0; i < 7; i++) {
            dataList.push([]);
            encodeY.push(1 + i);
        }

        for (var i = 0; i < categoryCount; i++) {

            xAxisData.push(data.date[i]);

            const {
                date,
                ...newobject
            } = data;
            
            var key;
            var index;
            for (var j = 0; j < dataList.length; j++) {


                key = Object.keys(newobject)[j];

                var valuew ="";
                if (key === 'total_reservasi') {
                    index = 1;
                    valuew = newobject[key]
                    dataList[index].push(valuew[i]);
                } else if (key === 'total_checkin') {
                    index = 2;
                    valuew = newobject[key]
                    dataList[index].push(valuew[i]);

                } else if (key === 'total_reservation_not_checkin') {
                    index = 3;
                    valuew = newobject[key]
                    dataList[index].push(valuew[i]);
                } else if (key === 'total_boarding') {
                    index = 4;
                    valuew = newobject[key]
                    dataList[index].push(valuew[i]);
                }
                else if (key === 'total_belum_boarding') {
                    index = 5;
                    valuew = newobject[key]
                    dataList[index].push(valuew[i]);
                }
                else if(key === 'kuota'){
                    index = 0;
                    customData.push(data.kuota[i]);

                    valuew = newobject[key]
                    dataList[index].push(valuew[i]);
                }

            }

            // console.log(dataList);
            // console.log(customData)

        }
        


        function renderItem(params, api) {
            var xValue = api.value(0);

            var currentSeriesIndices = api.currentSeriesIndices();
            var barLayout = api.barLayout({
                barGap: '30%',
                barCategoryGap: '20%',
                count: currentSeriesIndices.length - 1
            });

            var points = [];
            var l = 0;

            for (var i = 0; i < currentSeriesIndices.length; i++) {

                var seriesIndex = currentSeriesIndices[i];

                if (seriesIndex !== params.seriesIndex) {
                    var point = api.coord([xValue, data.kuota[xValue]]);
                    // var point = api.coord([xValue, api.value(seriesIndex)]);
                    point[0] += barLayout[i - 1].offsetCenter;
                    point[1] -= 2;
                    points.push(point);
                }

            }
            var style = api.style({
                stroke: api.visual('color'),
                fill: null
            });



            return {
                type: 'polyline',
                shape: {
                    points: points
                },
                style: style
            };
        }

        var labelOption = {
            show: true,
            position: 'top',
            distance: '15',
            align: 'center',
            verticalAlign: 'top',
            rotate: 1,
            fontSize: 14,
            rich: {
                name: {}
            }
        };

        option = {
            color: colors,
            tooltip: {
                trigger: 'axis',
                enterable:true,
                triggerOn : 'click',
                axisPointer: {
                    type: 'shadow'
                },
                formatter: function(params, c) {

                    // console.log(params)
                    let html =  `
                    <h5 style="font-weight:bold; text-align: center;">${params[0].axisValue}</h5>`

                    let totalData = "";
                    let listReservasi = "";
                    let classNameDiv = "";

                    for( let idx = 0 ; idx < data.date.length; idx++ )
                    {                        
                        if(data.date[idx] === params[0].axisValue){
                            let paramDetail = `portId=${data.detail[idx].portId}&shipClass=${data.detail[idx].shipClass}&departDate=${data.date[idx]} `;
                            params.forEach(x => {
                            switch (x.seriesName.toUpperCase()) {
                            
                                case "JUMLAH KEND. RESERVASI":
                                    classNameDiv ="divReservasi"
                                    totalData = data.total_reservasi[idx];
                                    let detailRes = data.detail[idx].detailReservasi
                                    
                                    listReservasi = `
                                    <div style="margin-bottom:0; display:none; " class="totalReservasi" >
                                        ${x.marker}
                                        Telah Reservasi : ${totalData}
                                        </br>
                                        <ul >
                                            <li>Roda 2 : ${detailRes.dua} </li>
                                            <li>Roda 4 : ${detailRes.empat} </li>
                                            <li>Roda 4+ : ${detailRes.empatplus} </li>
                                        </ul>
                                        <?php if ($detail_url != 1) { ?>
                                            <p></p><div align="center" ><a href="<?= site_url()?>dashboard/detail_reservasi?${paramDetail}&detail=reservasi" target="_blank" style="color:white" >Detail</a></div>
                                        <?php } ?>
                                       
                                    </div>
                                    `
                                    break;
                                case "JUMLAH KEND. TELAH CHECK IN":
                                    totalData = data.total_checkin[idx];
                                    let detailCheckin = data.detail[idx].detailCheckin
                                    listReservasi = `
                                    <div style="margin-bottom:0; display:none;" class="totalCheckin" >
                                        ${x.marker}
                                        Telah Check In : ${totalData}
                                        </br>
                                        <ul>
                                            <li>Roda 2 : ${detailCheckin.dua} </li>
                                            <li>Roda 4 : ${detailCheckin.empat} </li>
                                            <li>Roda 4+ : ${detailCheckin.empatplus} </li>
                                        </ul>
                                        <?php if ($detail_url != 1) { ?>
                                            <p></p><div align="center" ><a href="<?= site_url()?>dashboard/detail_reservasi?${paramDetail}&detail=checkin" target="_blank" style="color:white" >Detail</a></div>                                       
                                        <?php } ?>
                                    </div>
                                    `                                    
                                    break;
                                case "SISA KEND. BELUM CHECK IN":
                                    totalData = data.total_reservation_not_checkin[idx];
                                    let detailNotCheckin = data.detail[idx].detailNotCheckin                                    
                                    listReservasi = `
                                    <div style="margin-bottom:0; display:none;" class="totalNotCheckin" >
                                        ${x.marker}
                                        Belum Check In : ${totalData}
                                        </br>
                                        <ul>
                                            <li>Roda 2 : ${detailNotCheckin.dua} </li>
                                            <li>Roda 4 : ${detailNotCheckin.empat} </li>
                                            <li>Roda 4+ : ${detailNotCheckin.empatplus} </li>
                                        </ul>
                                        <?php if ($detail_url != 1) { ?>
                                            <p></p><div align="center" ><a href="<?= site_url()?>dashboard/detail_reservasi?${paramDetail}&detail=notCheckin" target="_blank" style="color:white" >Detail</a></div>                                       
                                        <?php } ?>
                                    </div>
                                    `
                                    break;
                                case "TOTAL BOARDING":
                                    totalData = data.total_boarding[idx];
                                    let detailBoarding = data.detail[idx].detailBoarding
                                    listReservasi = `
                                    <div style="margin-bottom:0; display:none;" class="totalBoarding" >
                                        ${x.marker}
                                        Telah Boarding : ${totalData}
                                        </br>
                                        <ul>
                                            <li>Roda 2 : ${detailBoarding.dua} </li>
                                            <li>Roda 4 : ${detailBoarding.empat} </li>
                                            <li>Roda 4+ : ${detailBoarding.empatplus} </li>
                                        </ul>
                                        <?php if ($detail_url != 1) { ?>
                                            <p></p><div align="center" ><a href="<?= site_url()?>dashboard/detail_reservasi?${paramDetail}&detail=boarding" target="_blank" style="color:white" >Detail</a></div>
                                        <?php } ?>
                                    </div>
                                    `
                                break;
                                case "BELUM BOARDING":
                                    totalData = data.total_belum_boarding[idx];
                                    let detailNotBoarding = data.detail[idx].detailNotBoarding
                                    listReservasi = `
                                    <div style="margin-bottom:0; display:none;" class="totalNotBoarding" >
                                        ${x.marker}
                                        Belum Boarding : ${totalData}
                                        </br>
                                        <ul>
                                            <li>Roda 2 : ${detailNotBoarding.dua} </li>
                                            <li>Roda 4 : ${detailNotBoarding.empat} </li>
                                            <li>Roda 4+ : ${detailNotBoarding.empatplus} </li>
                                        </ul>
                                        <?php if ($detail_url != 1) { ?>
                                            <p></p><div align="center" ><a href="<?= site_url()?>dashboard/detail_reservasi?${paramDetail}&detail=notBoarding" target="_blank" style="color:white" >Detail</a></div>
                                        <?php } ?>
                                    </div>
                                    `
                                break;
                                default:
                                    totalData = data.kuota[idx];
                                    listReservasi ="";
                                    break;

                            }
                            
                            // console.log(data.detail[idx].detailCheckin)
                            // x.marker untuk warna
                            html += `
                                    <div style="margin-bottom:0;" class="divHeader" >
                                    ${x.marker}
                                    ${x.seriesName} : ${totalData}
                                                                        
                                    </div>
                                    ${listReservasi}

                                    `});
                        }
                        
                    }


                    return html;
                }
            },
            legend: {
                selectedMode: true,
                data: legendData
            },
            grid: {
                bottom: 90
            },
            dataZoom: [{
                type: 'slider',
                start: 1,
                end: 70,
                startValue: 0,
                endValue: 1
            }, {
                type: 'inside'
            }],
            xAxis: {
                data: xAxisData,
                axisLabel: {
                    interval: 0,
                    rotate: 30
                }
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
                        title: 'Save Image',

                    }
                },
                right: "10%"
            },

            yAxis: {
                type: 'value',
            },
            series: [{
                type: 'custom',
                name: 'Kuota',
                renderItem: renderItem,
                itemStyle: {
                    borderWidth: 2
                },
                label: labelOption,
                encode: {
                    x: 0,
                    y: encodeY
                },
                data: customData,
                // data: dataList,
                z: 100
            }].concat(dataList.map(function(data, index) {                
                // console.log(data)
                if (index > 0 ) {
                    
                    return {
                        type: 'bar',
                        animation: false,
                        name: legendData[index],
                        itemStyle: {
                            opacity: 0.5
                        },
                        label: labelOption,
                        data: data,

                    };
                } else {
                    return '';
                }

            }))
        };

        option && myChart.setOption(option);
        myChart.on('click', data, function(params) {

            let classElement = "";
            let valueData= ""
            switch (params.seriesName.toUpperCase()) {
                case "JUMLAH KEND. RESERVASI":
                    classElement = "totalReservasi";
                    break;
                case "JUMLAH KEND. TELAH CHECK IN":
                    classElement = "totalCheckin";
                    break;
                case "TOTAL BOARDING":
                    classElement = "totalBoarding";
                    break;
                case "BELUM BOARDING":
                    classElement = "totalNotBoarding";
                    break;                    
                default:
                    classElement = "totalNotCheckin";
                    break;
            }

            $(`.${classElement}`).css({display:""});
            $(`.divHeader`).css({display:"none"});


        })

        $(window).resize(function() {
            myChart.resize()
        });

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
            // startDate: "-1m",
            todayHighlight: true,
        });

        $('#date2').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            endDate: new Date(),
            startDate: new Date()
        });


        $("#date").change(function() {

            var startDate = $(this).val();
            var someDate = new Date(startDate);
            someDate.setDate(someDate.getDate() + 7)

            // destroy ini firts setting
            $('#date2').datepicker('remove');

            // Re-int with new options
            $('#date2').datepicker({
                format: 'yyyy-mm-dd',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                endDate: someDate,
                startDate: startDate
            });

            $('#date2').val(startDate).datepicker("update")
        });

        function formatTanggal(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            return [year, month, day].join('-');
        }

        function formatTanggalDay(date) {
            var d = new Date(date),
                month = '' + d.getMonth() ,
                day = '' + d.getDate(),
                year = d.getFullYear();

                

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            const returnData = [year, month, day].join('-');            

            return returnData
        }

        var listDashboard = function() {
            $.ajax({
                url: '<?php echo base_url() ?>dashboard/listReservasi',
                type: 'POST',
                data: {
                    date: $('#date').val(),
                    date2: $('#date2').val(),
                    origin: $('#origin').val()
                },
                dataType: 'json',
                beforeSend: function() {
                    $('.block-ui').block({
                        message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>',
                        css: {
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

                    if (data.code == 1) {
                        d = data.data;
                        var date = new Date($('#date').val());
                        var date2 = new Date($('#date2').val());
                        const diffTime = Math.abs(parseInt(date2 - date));
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        var dat = '';
                        for (key in data.data) {
                            for (subkey in data.data[key]) {
                                for (subkeys in data.data[key][subkey]) {
                                    var e = subkey.ucwords();
                                    var title = `${e}Layanan ${subkeys}`;
                                    if (diffDays > 0) {

                                        dat = `<h4 class="bold">Layanan ${subkeys} Tanggal ${tanggal_(date)} s/d ${tanggal_(date2)}</h4>`;
                                    } else {
                                        dat = `<h4 class="bold">Layanan ${subkeys} Tanggal ${tanggal_(date)}</h4>`;
                                    }
                                    var htmlContent = `
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-bottom-40">
                                    <div class="portlet light portlet-fit bordered block-ui">
                                        <div class="portlet-title padding-title-chart text-center">
                                            <h4 class="bold">Grafik Produksi Jumlah Kendaraan Reservasi</h4>
                                            <h4 class="bold">Pelabuhan Penyeberangan ${e}</h4>
                                           ${dat}
                                            <h4 class="bold"> Waktu Akses ${tanggal_()}</h4>
                                        </div>
                                     
                                        <div class="portlet-body padding-body">
                                        
                                            <div id="${subkey+'_'+subkeys+date}" class="height-chart" style="height:400px;width:100%;" >
                                            
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                                    $('#grafik-checkin').append(htmlContent);
                                    reservasiCharts(data.data[key][subkey][subkeys], subkey + '_' + subkeys + date)
                                }


                            }
                        }

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

    function tanggal_(tgl) {
        var date = tgl ? new Date(tgl) : new Date(),
            year = date.getFullYear(),
            month = date.getMonth(),
            months = new Array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'),
            sort_months = new Array('Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'),

            d = date.getDate(),
            day = date.getDay(),

            h = date.getHours(),
            m = date.getMinutes();
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
</script>