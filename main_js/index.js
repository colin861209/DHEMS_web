//get time block
var dt = new Date();
var now_t = Math.floor(dt.getHours() * 4 + dt.getMinutes() / 15);

var ourData = [];
var function_run;
var household_num;

window.onload = function () {

    get_backEnd_data();
}

function get_backEnd_data() {

    $.ajax
        ({
            type: "GET",
            url: "back_end/localHouseholdLoadDeployment.php",
            contentType: "application/x-www-form-urlencoded",
            processData: true,
            success: function (response) {

                response = JSON.parse(response);
                console.log(response);
                ourData = response;
                household_num = 0;
                householdsLoadSum(ourData);
                muti_divs(ourData);
                run_household_eachLoad(ourData, 0)
                autoRun(ourData, household_num)
            }
        });
}

$(document).ready(function () {

    $('input[type="checkbox"]').click(function () {
        if ($(this).prop("checked") == true) {
            console.log("Checkbox is checked.")
            autoRun(ourData, household_num)
        }
        else if ($(this).prop("checked") == false) {
            console.log("Checkbox is unchecked.")
            clearInterval(function_run)

        }
    });
});

function choose_singleHousehold(element) {

    $('input[type="checkbox"]').prop("checked", false);
    clearInterval(function_run);
    run_household_eachLoad(ourData, element.id - 1);

}

function autoRun(ourData, household_num) {

    function_run = setInterval(function () {

        householdsLoadSum(ourData);
        if (household_num + 1 == ourData.household_num)
            household_num = 0
        else
            household_num++
        run_household_eachLoad(ourData, household_num)

    }, 7000);

}

function run_household_eachLoad(ourData, household_num) {

    document.getElementById("household_id").innerHTML = "住戶 " + (household_num + 1) + " 負載使用情況"
    var i = 0;
    for (i = 0; i < ourData.app_counts; i++) {
        each_load(ourData, i, household_num)
    }
}

function set_series_function(multi, series_type, DATA, stack_class, yAxis_locate, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis) {
    if (multi == 1) { var DATA_NUM = Object.keys(DATA).length; } //get data row num       
    else { var DATA_NUM = 1; }//get data row num

    for (i = 0; i < DATA_NUM; i++) {
        chart_series_type.push(series_type);
        if (multi == 1) {
            chart_series_name.push(parseInt(Object.keys(DATA)[i]) + 1)
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

function set_each_load_function(multi, series_type, DATA, ID, stack_class, yAxis_locate, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis) {
    if (multi == 1) { var DATA_NUM = Object.keys(DATA).length; } //get data row num       
    else { var DATA_NUM = 1; }//get data row num

    for (i = 0; i < DATA_NUM; i++) {
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

function show_chart(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, simulate_timeblock) {

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
            yAxis: chart_series_yAxis[i]
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
            min: 0,
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

function show_each_load(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, chart_upband, chart_lowband) {
    //set all series data
    var series_data = [];
    len = Object.keys(chart_series_name).length;
    i = 0;

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
            plotBands: [{
                color: 'pink', // Color value
                from: chart_upband, // Start of the plot band
                to: chart_lowband // End of the plot band
            }],
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

function householdsLoadSum(data) {
    //parse to get all json data

    var chart_info = ["households_loadsSum", "Households' Loads Consuption", " ", "time", "price(TWD)", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "line", data.electric_price, "price", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0, "line", data.limit_capability, "limit-power", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(1, "column", data.load_power_sum, "household_", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    /*Show chart*/
    show_chart(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock);

}

function each_load(data, num, household_num) {
    //parse to get all json data
    var this_load = data.load_power[household_num];
    var this_ID = data.number
    var this_name = data.equip_name;
    var this_s_time = data.start[household_num];
    var this_e_time = data.end[household_num];
    var start = this_s_time[num];
    var end = this_e_time[num] - 1;
    //define all needed data array
    var chart_info = ["con_" + num, this_name[num], "模擬值(simulation)", "時間(區間)", "電價(TWD)", "功率(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    /*DATA SET*/
    set_each_load_function(0, "line", data.electric_price, null, "price", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_each_load_function(0, "column", this_load[num], ((household_num + 1) + "-" + this_ID[num]), ((household_num + 1) + "-" + this_ID[num]), 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    /*Show chart*/
    show_each_load(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, start, end);

}

function muti_divs(data) {

    var LOAD_NUM = data.app_counts;
    var i;
    var htmlElements = "";
    for (i = 0; i < LOAD_NUM; i++) {
        htmlElements += '<div id=con_' + i + ' style="min-width: 310px; height: 420px; margin: 0 auto"> </div>';
    }
    // console.log(htmlElements);
    var container = document.getElementById("containers");
    container.innerHTML = htmlElements;
}
