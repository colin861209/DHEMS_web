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
    const data_ponitOne = [705.2, 4438.39209, 664.396301, 587.484131, 99.516205, 61.248016, 612.416016];
    const data_ponitOneTwo = [705.2, 4438.39209, 664.390259, 561.81073, 7.26496, 0, 0];

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
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

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
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

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
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

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
    set_series_function(0, "areaspline", data.eight_sell_power, "pwr-sell", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

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
    set_series_function(0, "areaspline", data.one_sell_power, "pwr-sell", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

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
    set_series_function(0, "areaspline", data.oneTwo_sell_power, "pwr-sell", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

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
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}