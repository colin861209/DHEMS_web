//get time block
var dt = new Date();
var now_t = Math.floor(dt.getHours() * 4 + dt.getMinutes() / 15);
console.log(now_t);

var ourData = []
var GHEMS_flag = []


window.onload = function () {

    get_backEnd_data();
}

function get_backEnd_data() {

    $.ajax
        ({
            type: "GET",
            url: "back_end/backup_GHEMS.php",
            contentType: "application/x-www-form-urlencoded",
            processData: true,
            success: function (response) {

                response = JSON.parse(response);
                ourData = response;
                GHEMS_flag = ourData.GHEMS_flag;
                console.log(ourData);
                insertText_after_breadcrumb(response.database_name, null, null, response.dr_mode, response.dr_info);
                tableInfo(ourData);
                progessbar(ourData);
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
    var tableData = {

        name: ["使用總負載", "負載花費(表燈電價)", "負載花費(三段式電價)", "購買市電", "賣電回饋", "燃料電池花費", "氫氣消耗"],
        value: [
            ourData.total_load_power_sum + powerUnit,
            ourData.taipower_loads_cost + moneyUnit,
            ourData.three_level_loads_cost + moneyUnit,
            ourData.real_buy_grid_cost + moneyUnit,
            ourData.max_sell_price + moneyUnit,
            ourData.min_FC_cost + moneyUnit,
            ourData.consumption + hydrogenUnit
        ]
    }

    if (tableData.name.length == tableData.value.length) {

        for (let nameNum = 0; nameNum < tableData.name.length; nameNum++) {

            var th = document.createElement('th');
            th.appendChild(document.createTextNode(tableData.name[nameNum]));
            th.setAttribute("style", "text-align: center; color:black");
            document.getElementById('table_costInfo_thead').appendChild(th);
        }

        for (let dataNum = 0; dataNum < tableData.value.length; dataNum++) {

            var td = document.createElement('td');
            td.appendChild(document.createTextNode(tableData.value[dataNum]));
            td.setAttribute("style", "text-align: center; color:black; font-size: 20px");
            document.getElementById('table_costInfo_tbody').appendChild(td);
        }
    }
    else {

        console.log("Function: " + tableInfo.name + " Wrong length in table 'name' & 'data'")
    }
}

function progessbar(ourData) {

    var finish_rate = ourData.simulate_timeblock / 96 * 100;
    document.getElementById('percent_print').innerHTML = "進度 : " + ourData.simulate_timeblock + " / 96";
    document.getElementById("percent_width").style.width = finish_rate + "%";
    if (finish_rate < 20) { document.getElementById("percent_width").style.backgroundColor = "red"; }
    if (finish_rate >= 20 && finish_rate < 40) { document.getElementById("percent_width").style.backgroundColor = "orange"; }
    if (finish_rate >= 40 && finish_rate < 60) { document.getElementById("percent_width").style.backgroundColor = "yellow"; }
    if (finish_rate >= 60 && finish_rate < 80) { document.getElementById("percent_width").style.backgroundColor = "green"; }
    if (finish_rate >= 80 && finish_rate < 99) { document.getElementById("percent_width").style.backgroundColor = "#4F4FFF"; }
    if (finish_rate == 100) { document.getElementById("percent_width").style.backgroundColor = "blue"; }
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

    set_series_function(0, "line", data.electric_price, energyType.electrice_chart_name, 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "column", data.load_model, energyType.Pload_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    if (GHEMS_flag[0].indexOf(energyType.Pess_flag_name) !== -1 && GHEMS_flag[1][GHEMS_flag[0].findIndex(flag => flag === energyType.Pess_flag_name)] == 1)
        set_series_function(0, "spline", data.battery_power, energyType.Pess_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    set_series_function(0, "spline", data.simulate_solar, energyType.Psolar_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.grid_power, energyType.Pgrid_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    if (GHEMS_flag[0].indexOf(energyType.Pfc_flag_name) !== -1 && GHEMS_flag[1][GHEMS_flag[0].findIndex(flag => flag === energyType.Pfc_flag_name)] == 1)
        set_series_function(0, "spline", data.FC_power, energyType.Pfc_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    if (GHEMS_flag[0].indexOf(energyType.Psell_flag_name) !== -1 && GHEMS_flag[1][GHEMS_flag[0].findIndex(flag => flag === energyType.Psell_flag_name)] == 1)
        set_series_function(0, "areaspline", data.sell_power, energyType.Psell_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1, data.dr_info[1], data.dr_info[2] - 1);

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

    set_series_function(0, "spline", data.SOC_value, energyType.SOC_chart_name, 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "column", data.load_model, energyType.Pload_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    if (GHEMS_flag[0].indexOf(energyType.Pess_flag_name) !== -1 && GHEMS_flag[1][GHEMS_flag[0].findIndex(flag => flag === energyType.Pess_flag_name)] == 1)
        set_series_function(0, "spline", data.battery_power, energyType.Pess_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    set_series_function(0, "spline", data.simulate_solar, energyType.Psolar_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.grid_power, energyType.Pgrid_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    if (GHEMS_flag[0].indexOf(energyType.Pfc_flag_name) !== -1 && GHEMS_flag[1][GHEMS_flag[0].findIndex(flag => flag === energyType.Pfc_flag_name)] == 1)
        set_series_function(0, "spline", data.FC_power, energyType.Pfc_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    if (GHEMS_flag[0].indexOf(energyType.Psell_flag_name) !== -1 && GHEMS_flag[1][GHEMS_flag[0].findIndex(flag => flag === energyType.Psell_flag_name)] == 1)
        set_series_function(0, "areaspline", data.sell_power, energyType.Psell_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[1],"load-2",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);
    // set_series_function(0,"spline",data.load_power[2],"load-3",1,chart_series_type,chart_series_name,chart_series_data,chart_series_stack,chart_series_yAxis);

    /*Show chart*/
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1, data.dr_info[1], data.dr_info[2] - 1);

}

function loadModel(ABC) {
    //parse to get all json data
    var data = ABC;
    //define all needed data array
    var chart_info = ["loadModel", "Load Model", " ", "time", "price(TWD)", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "line", data.electric_price, energyType.electrice_chart_name, 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(1, "column", data.load_model_seperate, energyType.Pload_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    /*Show chart*/
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1, data.dr_info[1], data.dr_info[2] - 1);

}
