//get time block
var dt = new Date();
var now_t = Math.floor(dt.getHours() * 4 + dt.getMinutes() / 15);
console.log(now_t);

var request = new XMLHttpRequest();
request.open('GET', 'back_end/control_status.php');
request.onload = function () {
    // var ourData = JSON.parse(request.responseText);
    // var LOOP_NUM = JSON.parse(this.responseText).load_power.length;
    console.log(JSON.parse(this.responseText));
    tableInfo(this.responseText);
    pointZeroEight_price(this.responseText);
    pointOne_price(this.responseText);
    pointOneTwo_price(this.responseText);
    pointZeroEight_SOC(this.responseText);
    pointOne_SOC(this.responseText);
    pointOneTwo_SOC(this.responseText);
    // SOCVsLoad(this.responseText)
    // real_status(this.responseText)
    // muti_divs(this.responseText);
    // for (var i = 0; i < LOOP_NUM; i++) { each_load(this.responseText, i); }
}
request.send();

function tableInfo(ourData) {

    const powerUnit = "(kWh)";
    const moneyUnit = "(NTD)";
    const hydrogenUnit = "(g)";
    const unitArray = [powerUnit, moneyUnit, moneyUnit, moneyUnit, moneyUnit, moneyUnit, hydrogenUnit];
    const name = ["使用總負載", "負載花費(表燈電價)", "負載花費(三段式電價)", "購買市電", "賣電回饋", "燃料電池花費", "氫氣消耗"];
    const data_ponitZeroEight = [705.2, 4438.39209, 664.396301, 541.415344, 166.673996, 145.120056, 1813.792603];
    const data_ponitOne = [705.2, 4438.39209, 664.396301, 580.569092, 93.268204, 61.099018, 610.99018];
    const data_ponitOneTwo = [705.2, 4438.39209, 664.396301, 577.577942, 18.035212, 0, 0];

    // 0.08
    var tr_head = document.createElement('tr');
    tr_head.setAttribute("class", "well");
    for (let nameNum = 0; nameNum < name.length; nameNum++) {

        var th = document.createElement('th');
        th.appendChild(document.createTextNode(name[nameNum] + unitArray[nameNum]));
        th.setAttribute("style", "text-align: center; color:black");
        tr_head.appendChild(th);
    }
    var thead = document.createElement('thead');
    thead.appendChild(tr_head);

    var tr_body = document.createElement('tr');
    tr_body.setAttribute("class", "alert alert-danger");
    tr_body.setAttribute("style", "text-align: center");
    for (let dataNum = 0; dataNum < data_ponitZeroEight.length; dataNum++) {

        var td = document.createElement('td');
        td.appendChild(document.createTextNode(data_ponitZeroEight[dataNum]));
        td.setAttribute("style", "text-align: center; color:black; font-size: 20px; font-weight: bolder");
        tr_body.appendChild(td);
    }
    var tbody = document.createElement('tbody');
    tbody.appendChild(tr_body);
    document.getElementById('Price0.08_table').appendChild(thead);
    document.getElementById('Price0.08_table').appendChild(tbody);

    // 0.1
    var tr_head = document.createElement('tr');
    tr_head.setAttribute("class", "well");
    for (let nameNum = 0; nameNum < name.length; nameNum++) {

        var th = document.createElement('th');
        th.appendChild(document.createTextNode(name[nameNum] + unitArray[nameNum]));
        th.setAttribute("style", "text-align: center; color:black");
        tr_head.appendChild(th);
    }
    var thead = document.createElement('thead');
    thead.appendChild(tr_head);

    var tr_body = document.createElement('tr');
    tr_body.setAttribute("class", "alert alert-danger");
    tr_body.setAttribute("style", "text-align: center");
    for (let dataNum = 0; dataNum < data_ponitOne.length; dataNum++) {

        var td = document.createElement('td');
        td.appendChild(document.createTextNode(data_ponitOne[dataNum]));
        td.setAttribute("style", "text-align: center; color:black; font-size: 20px; font-weight: bolder");
        tr_body.appendChild(td);
    }
    var tbody = document.createElement('tbody');
    tbody.appendChild(tr_body);
    document.getElementById('Price0.1_table').appendChild(thead);
    document.getElementById('Price0.1_table').appendChild(tbody);

    // 0.12
    var tr_head = document.createElement('tr');
    tr_head.setAttribute("class", "well");
    for (let nameNum = 0; nameNum < name.length; nameNum++) {

        var th = document.createElement('th');
        th.appendChild(document.createTextNode(name[nameNum] + unitArray[nameNum]));
        th.setAttribute("style", "text-align: center; color:black");
        tr_head.appendChild(th);
    }
    var thead = document.createElement('thead');
    thead.appendChild(tr_head);

    var tr_body = document.createElement('tr');
    tr_body.setAttribute("class", "alert alert-danger");
    tr_body.setAttribute("style", "text-align: center");
    for (let dataNum = 0; dataNum < data_ponitOneTwo.length; dataNum++) {

        var td = document.createElement('td');
        td.appendChild(document.createTextNode(data_ponitOneTwo[dataNum]));
        td.setAttribute("style", "text-align: center; color:black; font-size: 20px; font-weight: bolder");
        tr_body.appendChild(td);
    }
    var tbody = document.createElement('tbody');
    tbody.appendChild(tr_body);
    document.getElementById('Price0.12_table').appendChild(thead);
    document.getElementById('Price0.12_table').appendChild(tbody);

}

function set_series_function(multi, series_type, DATA, stack_class, yAxis_locate, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis) {

    if (multi == 1) { var DATA_NUM = Object.keys(DATA).length; } //get data row num       
    else { var DATA_NUM = 1; }//get data row num

    for (i = 0; i < DATA_NUM; i++) {
        chart_series_type.push(series_type);
        if (multi == 1) {

            chart_series_name.push(Object.keys(DATA)[i]);
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

function pointZeroEight_price(ABC) {
    //parse to get all json data
    var data = JSON.parse(ABC);
    //define all needed data array
    var chart_info = ["FCPrice0.08", "Price ~ 0.08(NTD/g)", " ", "time", "price(TWD)", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "line", data.electric_price, "price", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "column", data.load_model, "pwr-load", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.eight_battery_power, "pwr-battery", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.simulate_solar, "pwr-solar", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.eight_FC_power, "pwr-FC", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.eight_grid_power, "pwr-buy", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.eight_sell_power, "pwr-sell", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}

function pointOne_price(ABC) {
    //parse to get all json data
    var data = JSON.parse(ABC);
    //define all needed data array
    var chart_info = ["FCPrice0.1", "Price ~ 0.1(NTD/g)", " ", "time", "price(TWD)", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "line", data.electric_price, "price", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "column", data.load_model, "pwr-load", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.one_battery_power, "pwr-battery", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.simulate_solar, "pwr-solar", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.one_FC_power, "pwr-FC", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.one_grid_power, "pwr-buy", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.one_sell_power, "pwr-sell", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}

function pointOneTwo_price(ABC) {
    //parse to get all json data
    var data = JSON.parse(ABC);
    //define all needed data array
    var chart_info = ["FCPrice0.12", "Price ~ 0.12(NTD/g)", " ", "time", "price(TWD)", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "line", data.electric_price, "price", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "column", data.load_model, "pwr-load", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.oneTwo_battery_power, "pwr-battery", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.simulate_solar, "pwr-solar", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.oneTwo_FC_power, "pwr-FC", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.oneTwo_grid_power, "pwr-buy", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.oneTwo_sell_power, "pwr-sell", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}

function pointZeroEight_SOC(ABC) {
    //parse to get all json data
    var data = JSON.parse(ABC);
    //define all needed data array
    var chart_info = ["SOC0.08", "SOC ~ 0.08(NTD/g)", " ", "time", "SOC", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "spline", data.eight_SOC_value, "SOC", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "column", data.load_model, "pwr-load", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.eight_battery_power, "pwr-battery", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.simulate_solar, "pwr-solar", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.eight_FC_power, "pwr-FC", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.eight_grid_power, "pwr-buy", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}

function pointOne_SOC(ABC) {
    //parse to get all json data
    var data = JSON.parse(ABC);
    //define all needed data array
    var chart_info = ["SOC0.1", "SOC ~ 0.1(NTD/g)", " ", "time", "SOC", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "spline", data.one_SOC_value, "SOC", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "column", data.load_model, "pwr-load", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.one_battery_power, "pwr-battery", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.simulate_solar, "pwr-solar", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.one_FC_power, "pwr-FC", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.one_grid_power, "pwr-buy", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}

function pointOneTwo_SOC(ABC) {
    //parse to get all json data
    var data = JSON.parse(ABC);
    //define all needed data array
    var chart_info = ["SOC0.12", "SOC ~ 0.12(NTD/g)", " ", "time", "SOC", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "spline", data.oneTwo_SOC_value, "SOC", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "column", data.load_model, "pwr-load", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.oneTwo_battery_power, "pwr-battery", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.simulate_solar, "pwr-solar", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.oneTwo_FC_power, "pwr-FC", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.oneTwo_grid_power, "pwr-buy", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}
/* =-=-=-=-=-=- below not use right now -=-=-=-=-=-= */
function real_status(ABC) {
    //parse to get all json data
    var data = JSON.parse(ABC);
    //define all needed data array
    var chart_info = ["real_status", "Real Status", " ", "time", "price(TWD)", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];
    for (var i = 0; i < data.simulate_timeblock - 1; i++) {

        for (var j = 0; j < data.load_num.length; j++) {

            data.load_power_sum[i] = 0
            // data.SOC_value[i] = 0
            data.battery_power[i] = 0
            data.grid_power[i] = 0
            data.simulate_solar[i] = 0
        }
    }
    set_series_function(0, "line", data.electric_price, "price", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "column", data.load_power_sum, "pwr-load", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0, "spline", data.SOC_value, "SOC", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.battery_power, "pwr-battery", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.grid_power, "pwr-buy", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.FC_power, "pwr-FC", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.simulate_solar, "pwr-solar", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}

function each_load(ABC, num) {
    //parse to get all json data
    // console.log("each_load"+num)
    var GET_CHART_DATA = JSON.parse(ABC);
    var this_load = GET_CHART_DATA.load_power;
    var this_ID = GET_CHART_DATA.load_num;
    var this_name = GET_CHART_DATA.equip_name;
    var this_s_time = GET_CHART_DATA.start_time;
    var this_e_time = GET_CHART_DATA.end_time;
    var start = this_s_time[num] * 4;
    var end = this_e_time[num] * 4 - 1;

    //define all needed data array
    var chart_info = ["con_" + num, this_name[num], "模擬值(simulation)", "時間(區間)", "電價(TWD)", "功率(kW)"];
    // console.log(chart_info)
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    /*DATA SET*/
    set_each_load_function(0, "line", GET_CHART_DATA.electric_price, null, "price", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_each_load_function(0, "column", this_load[num], this_ID[num], this_ID[num], 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    /*Show chart*/
    show_each_load(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, start, end);

}

function muti_divs(ABC) {
    var GET_LOAD_DATA = JSON.parse(ABC);
    console.log(GET_LOAD_DATA);
    var LOAD_NUM = GET_LOAD_DATA.load_power.length;
    var i;
    var htmlElements = "";
    for (i = 0; i < LOAD_NUM; i++) {
        htmlElements += '<div id=con_' + i + ' style="min-width: 310px; height: 420px; margin: 0 auto"> </div>';
    }
    // console.log(htmlElements);
    var container = document.getElementById("containers");
    container.innerHTML = htmlElements;
}
