// =-=-=-=-=-=-=-=-=-=-=-=- function 'show_chart_with_redDashLine' information -=-=-=-=-=-=-=-=-=-=-=-= //
// parameter 'simulate_timeblock' is null, the chart won't show the red dash line                       //
// array 'chart_info' [id, title, sub title, x-axis name, left y-axis name, right y-axis name, color]   //
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=//
function show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, simulate_timeblock, dr_startTime = null, dr_endTime = null) {

    //set all series data
    var series_data = [],
        len = Object.keys(chart_series_name).length,
        i = 0;

    for (i; i < (len); i++) {
        series_data.push({
            type: chart_series_type[i],
            name: chart_series_name[i],
            data: chart_series_data[i],
            stack: chart_series_stack[i],
            yAxis: chart_series_yAxis[i],
            color: chart_info[6+i]
        });
    }
    //set all chart data
    var charts = Highcharts.chart(chart_info[0], {
        title: {
            text: chart_info[1],
            style: {
                fontWeight: 'bold',
                fontSize: '24px'
            }
        },
        subtitle: {
            text: chart_info[2]
        },
        legend: {
            itemStyle: {
                fontWeight: 'bold',
                fontSize: '18px'
            }
        },
        xAxis: {
            plotBands: [{
                color: '#e7e7e7', // Color value
                from: dr_startTime, // Start of the plot band
                to: dr_endTime // End of the plot band
            }],
            max: 95,
            title: {
                text: chart_info[3], style: {
                    fontWeight: 'bold',
                    fontSize: '16px'
                }
            },
            categories: [],
            plotLines: [{
                color: 'red', // Color value
                dashStyle: 'ShortDash', // Style of the plot line. Default to solid
                value: simulate_timeblock, // Value of where the line will appear
                width: 1, // Width of the line   
            }
            ]
        },
        yAxis: [{
            min: 0,
            title: {
                text: chart_info[4], style: {
                    fontWeight: 'bold',
                    fontSize: '16px'
                }
            }
        }, {
            // min: 0,
            // max: 4,
            title: {
                text: chart_info[5],
                rotation: 270,
                style: {
                    fontWeight: 'bold',
                    fontSize: '16px'
                }
            },
            opposite: true
        }]
        ,
        tooltip: {
            //enabled: false
            formatter: function () {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + ': ' + this.y + '<br/>' +
                    'Total: ' + this.point.stackTotal;
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: false
                },
                enableMouseTracking: false,
                marker: {
                    enabled: false
                }
            },
            column: {
                stacking: 'normal'
            }
        },
        series: series_data
    });

}

function set_series_function(multi, series_type, DATA, stack_class, yAxis_locate, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, multi_name = null) {
    if (multi == 1) { var DATA_NUM = Object.keys(DATA).length; } //get data row num       
    else { var DATA_NUM = 1; }//get data row num

    for (var i = 0; i < DATA_NUM; i++) {
        chart_series_type.push(series_type);
        if (multi == 1) {
            if (multi_name == null) { chart_series_name.push(parseInt(Object.keys(DATA)[i]) + 1) }
            else { chart_series_name.push(multi_name[i]) }
            chart_series_data.push(DATA[Object.keys(DATA)[i]]);
        }
        else {
            chart_series_name.push(stack_class);  //same as stack name
            chart_series_data.push(DATA);
        }
        chart_series_stack.push(stack_class);
        chart_series_yAxis.push(yAxis_locate);
    }
}

function show_chart_with_pinkAreaOrComforLevel(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, chart_lowband, chart_upband, comfort_lowband = null, comfort_upband = null, comfortLevel_flag = false) {
    
    var plotBandsArray = [];
    if (comfortLevel_flag) {
        
        plotBandsArray.push({
            
            color: '#dddddd',
            from: chart_lowband,
            to: chart_upband
        });

        // 綠 黃 粉 橘 
        var color_name = ['#B7FF68', '#FFFF93', 'pink', '#ff0000a1'];
        // var color_name = ['green', 'yellow', 'pink', 'red'];
        for (let i = 0; i < comfort_lowband.length; i++) {
                
            for (let j = 0; j < comfort_lowband[i].length; j++) {

                plotBandsArray.push({

                    color: color_name[i],
                    from: comfort_lowband[i][j],
                    to: comfort_upband[i][j],
                });    
            }
        }
    }
    else {

        plotBandsArray.push({
            
            color: 'pink',
            from: chart_lowband,
            to: chart_upband
        })
    }

    //set all series data
    var series_data = [];
    var len = Object.keys(chart_series_name).length;
    var i = 0;

    for (i; i < (len); i++) {
        series_data.push({
            type: chart_series_type[i],
            name: chart_series_name[i],
            data: chart_series_data[i],
            stack: chart_series_stack[i],
            yAxis: chart_series_yAxis[i]
        });
    }
    //set all chart data
    var charts = Highcharts.chart(chart_info[0], {
        title: {
            text: chart_info[1]
        },
        subtitle: {
            text: chart_info[2]
        },
        xAxis: {
            plotBands: plotBandsArray,
            max: 95,
            title: { text: chart_info[3] },
            categories: []
        },
        yAxis: [{
            min: 0,
            title: {
                text: chart_info[4]
            }
        }, {
            // min: -4,
            // max: 4,   
            title: {
                text: chart_info[5]
            },
            opposite: true
        }]
        ,
        tooltip: {
            //enabled: false
            formatter: function () {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + ': ' + this.y + '<br/>' +
                    'Total: ' + this.point.stackTotal;
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: false
                },
                enableMouseTracking: false,
                marker: {
                    enabled: false
                }
            },
            column: {
                stacking: 'normal'
            }
        },
        series: series_data
    });

}

function show_chart_with_EM_users(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, xAxis_max) {

    //set all series data
    var series_data = [],
        len = Object.keys(chart_series_name).length,
        i = 0;

    for (i; i < (len); i++) {
        series_data.push({
            type: chart_series_type[i],
            name: chart_series_name[i],
            data: chart_series_data[i],
            stack: chart_series_stack[i],
            yAxis: chart_series_yAxis[i],
            color: chart_info[6+i]
        });
    }
    //set all chart data
    var charts = Highcharts.chart(chart_info[0], {
        title: {
            text: chart_info[1],
            style: {
                fontWeight: 'bold',
                fontSize: '24px'
            }
        },
        subtitle: {
            text: chart_info[2]
        },
        legend: {
            itemStyle: {
                fontWeight: 'bold',
                fontSize: '18px'
            }
        },
        xAxis: {
            max: xAxis_max,
            title: {
                text: chart_info[3], style: {
                    fontWeight: 'bold',
                    fontSize: '16px'
                }
            },
            categories: [],
        },
        yAxis: [{
            min: 0,
            max:100,
            title: {
                text: chart_info[4], style: {
                    fontWeight: 'bold',
                    fontSize: '16px'
                }
            }
        }, {
            // min: 0,
            // max: 4,
            title: {
                text: chart_info[5],
                rotation: 270,
                style: {
                    fontWeight: 'bold',
                    fontSize: '16px'
                }
            },
            opposite: true
        }]
        ,
        tooltip: {
            //enabled: false
            formatter: function () {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + ': ' + this.y + '<br/>' +
                    'Total: ' + this.point.stackTotal;
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: false
                },
                enableMouseTracking: false,
                marker: {
                    enabled: false
                }
            },
            column: {
                stacking: 'normal'
            }
        },
        series: series_data
    });

}

function set_each_load_function(multi, series_type, DATA, ID, stack_class, yAxis_locate, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis) {
    if (multi == 1) { var DATA_NUM = Object.keys(DATA).length; } //get data row num       
    else { var DATA_NUM = 1; }//get data row num

    for (var i = 0; i < DATA_NUM; i++) {
        chart_series_type.push(series_type);
        if (multi == 1) {
            chart_series_name.push(ID[Object.keys(ID)[i]]);
            chart_series_data.push(DATA[Object.keys(DATA)[i]]);
        }
        else {
            chart_series_name.push(stack_class);  //same as stack name
            chart_series_data.push(DATA);
        }
        chart_series_stack.push(stack_class);
        chart_series_yAxis.push(yAxis_locate);
    }

}

function insertText_after_breadcrumb(database, weather, initSOC, dr_mode = 0, dr_info = null) {
    
    var insert_target = document.getElementById('breadcrumb').getElementsByTagName('li')[1];
    if (database != null)
        insert_target.innerHTML += " (DB: " + database;
    if (weather != null)
        insert_target.innerHTML += ", weather: " + weather;
    if (initSOC != null)
        insert_target.innerHTML += ", initSOC: " + initSOC;
    if (dr_mode != 0) {
        insert_target.innerHTML += ", dr_startTime: " + dr_info[1];
        insert_target.innerHTML += ", dr_endTime: " + dr_info[2];
    }
    
    if (database != null)
        insert_target.innerHTML += ")";
}

var energyType = {

    interrupt_flag_name: "interrupt",
    uninterrupt_flag_name: "uninterrupt",
    varying_flag_name: "varying",
    Pgrid_flag_name: "Pgrid",
    mu_grid_flag_name: "mu_grid",
    Pess_flag_name: "Pess",
    Psell_flag_name: "Psell",
    SOC_change_flag_name: "SOC_change",
    Pfc_flag_name: "Pfc",
    PublicLoad_flag_name: "publicLoad",

    Pgrid_chart_name: "pwr-buy",
    Pess_chart_name: "pwr-battery",
    Psolar_chart_name: "pwr-solar",
    Psell_chart_name: "pwr-sell",
    Pload_chart_name: "pwr-load",
    Pfc_chart_name: "pwr-FC",
    SOC_chart_name: "SOC",
    electrice_chart_name: "price",
    
    // load model chart name
    HEMS_chart_name: "pwr-HEMS",
    public1_chart_name: "pwr-public_1",
    public2_chart_name: "pwr-public_2",
    public3_chart_name: "pwr-public_3",
    EM_charging_chart_name: "pwr-charging-EM",
    EM_discharging_chart_name: "pwr-discharging-EM",

}

var compare_timeblock = {};
setInterval(() => {
    
    $.ajax
        ({
            type: "POST",
            url: "back_end/reload_windows_compare.php",
            data: { compare_timeblock: compare_timeblock },
            success: function (response) {

                response = JSON.parse(response);
                
                if (response.status == "reload") {
                                        
                    Swal.fire({
                        icon: 'info',
                        title: '時刻更新了',
                        timerProgressBar: true,
                        timer: 3000,
                        didOpen: () => {
                            Swal.showLoading()
                            timerInterval = setInterval(() => {
                                const content = Swal.getHtmlContainer()
                                if (content) {
                                  const b = content.querySelector('b')
                                  if (b) {
                                    b.textContent = Swal.getTimerLeft()
                                  }
                                }
                              }, 100)
                            },
                            willClose: () => {
                              clearInterval(timerInterval)
                            }
                    })
                    .then(() => {

                        location.reload("")
                    });
                }
            }
        });

}, 1000*10);