//get time block
var dt = new Date();
var now_t = Math.floor(dt.getHours() * 4 + dt.getMinutes() / 15);
console.log(now_t);
var ourData = []


window.onload = function () {

    get_backEnd_data();
}

function get_backEnd_data() {

    $.ajax
        ({
            type: "GET",
            url: "back_end/loadFix.php",
            contentType: "application/x-www-form-urlencoded",
            processData: true,
            success: function (response) {

                response = JSON.parse(response);
                ourData = response;
                console.log(ourData);
                tableInfo(ourData);
                priceVsLoad(ourData);
                SOCVsLoad(ourData)
                loadModel(ourData)
            }
        });
}

function tableInfo(ourData) {

    const powerUnit = "(kWh)";
    const moneyUnit = "(NTD)";
    const hydrogenUnit = "(g)";
    const name = ["使用總負載", "負載花費(表燈電價)", "負載花費(三段式電價)", "購買市電", "賣電回饋", "燃料電池花費", "氫氣消耗"];
    var data = [
        ourData.total_load_power_sum + powerUnit,
        ourData.taipower_loads_cost + moneyUnit,
        ourData.three_level_loads_cost + moneyUnit,
        ourData.real_buy_grid_cost + moneyUnit,
        ourData.max_sell_price + moneyUnit,
        ourData.min_FC_cost + moneyUnit,
        ourData.consumption + hydrogenUnit
    ];
    if (name.length == data.length) {

        for (let nameNum = 0; nameNum < name.length; nameNum++) {

            var th = document.createElement('th');
            th.appendChild(document.createTextNode(name[nameNum]));
            th.setAttribute("style", "text-align: center; color:black");
            document.getElementById('table_costInfo_thead').appendChild(th);
        }

        for (let dataNum = 0; dataNum < data.length; dataNum++) {

            var td = document.createElement('td');
            td.appendChild(document.createTextNode(data[dataNum]));
            td.setAttribute("style", "text-align: center; color:black; font-size: 20px");
            document.getElementById('table_costInfo_tbody').appendChild(td);
        }
    }
    else {

        console.log("Function: " + tableInfo.name + " Wrong length in table 'name' & 'data'")
    }
}

function priceVsLoad(ABC) {
    //parse to get all json data
    var data = ABC;
    //define all needed data array
    var chart_info = ["priceVsLoad", "Price vs Load", " ", "time", "price(TWD)", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "line", data.electric_price, "price", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "column", data.load_model, "pwr-load", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.battery_power, "pwr-battery", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.simulate_solar, "pwr-solar", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.grid_power, "pwr-buy", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.FC_power, "pwr-FC", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.sell_power, "pwr-sell", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}

function SOCVsLoad(ABC) {
    //parse to get all json data
    var data = ABC;
    //define all needed data array
    var chart_info = ["SOCVsLoad", "SOC vs Load", " ", "time", "SOC", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "spline", data.SOC_value, "SOC", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "column", data.load_model, "pwr-load", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.battery_power, "pwr-battery", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.simulate_solar, "pwr-solar", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.grid_power, "pwr-buy", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "spline", data.FC_power, "pwr-FC", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.sell_power, "pwr-sell", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}

function loadModel(ABC) {
    //parse to get all json data
    var data = ABC;
    //define all needed data array
    var chart_info = ["loadModel", "Load Mdoel", " ", "time", "price(TWD)", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "line", data.electric_price, "price", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(1, "column", data.load_model_seperate, "pwr-load", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    /*Show chart*/
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}
