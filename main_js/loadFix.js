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
            url: "back_end/loadFix.php",
            contentType: "application/x-www-form-urlencoded",
            processData: true,
            success: function (response) {

                response = JSON.parse(response);
                ourData = response;
                GHEMS_flag = ourData.GHEMS_flag;
                compare_timeblock = {
                    
                    local: ourData.local_simulate_timeblock,
                    global: ourData.global_simulate_timeblock
                };
                console.log(ourData);
                insertText_after_breadcrumb(response.database_name, null, null, ourData.dr_mode, ourData.dr_info);
                tableInfo(ourData);
                progessbar(ourData);
                priceVsLoad(ourData);
                SOCVsLoad(ourData)
                loadModel(ourData)
                EMchargingSOC(ourData.EM_start_departure_SOC)
                flag_table(GHEMS_flag)
            }
        });
}

function tableInfo(ourData) {

    const powerUnit = "(kWh)";
    const moneyUnit = "(NTD)";
    const hydrogenUnit = "(g)";
    var tableData = {

        name: [
            "使用總負載",
            "負載花費(表燈電價)",
            "負載花費(三段式電價)",
            "購買市電",
        ],
        value: [
            ourData.total_load_power_sum + powerUnit,           
            ourData.taipower_loads_cost + moneyUnit,
            ourData.three_level_loads_cost + moneyUnit,
            ourData.real_buy_grid_cost + moneyUnit,
        ]
    }
    if (GHEMS_flag[1][GHEMS_flag[0].indexOf(energyType.PublicLoad_flag_name)] == 1) {

        tableData.name.splice(1, 0, "公共負載");
        tableData.name.push("公設花費(三段式電價)");
        tableData.value.splice(1, 0, ourData.total_publicLoad_power + powerUnit);
        tableData.value.push(ourData.total_publicLoad_cost + moneyUnit);
    }

    if (ourData.dr_mode != 0) {

        tableData.name.push("輔助服務回饋");
        tableData.value.push(ourData.dr_feedbackPrice + moneyUnit);
    }

    if (GHEMS_flag[1][GHEMS_flag[0].indexOf(energyType.Pfc_flag_name)] == 1) {
        
        tableData.name.push("燃料電池花費");
        tableData.name.push("氫氣消耗");
        tableData.value.push(ourData.min_FC_cost + moneyUnit,);
        tableData.value.push(ourData.consumption + hydrogenUnit);
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
    
    if (ourData.EM_flag == 1) {
        
        var EMData = {
            name:[],
            value:[]
        }
        EMData.name.push("電動機車花費(三段式電價)");
        EMData.name.push("電動機車充電消耗");
        EMData.name.push("電動機車最小離場SOC");
        EMData.name.push("電動機車平均離場SOC");
        EMData.value.push(ourData.EM_total_power_cost + moneyUnit);
        EMData.value.push(ourData.EM_total_power_sum + powerUnit);
        EMData.value.push(ourData.EM_MIN_departureSOC + " %");
        EMData.value.push(ourData.EM_AVG_departureSOC + " %");
        if (EMData.name.length == EMData.value.length) {
            
            for (let nameNum = 0; nameNum < EMData.name.length; nameNum++) {
                
                var th = document.createElement('th');
                th.appendChild(document.createTextNode(EMData.name[nameNum]));
                th.setAttribute("style", "text-align: center; color:black");
                document.getElementById('table_EMInfo_thead').appendChild(th);
            }
            
            for (let dataNum = 0; dataNum < EMData.value.length; dataNum++) {
                
                var td = document.createElement('td');
                td.appendChild(document.createTextNode(EMData.value[dataNum]));
                td.setAttribute("style", "text-align: center; color:black; font-size: 20px");
                document.getElementById('table_EMInfo_tbody').appendChild(td);
            }
        }
        else {
            
            console.log("Function: " + tableInfo.name + " Wrong length in table 'name' & 'data'")
        }
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
    if (data.dr_mode != 0)
        show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1, data.dr_info[1], data.dr_info[2] - 1);
    else
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
    if (data.dr_mode != 0)
        show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1, data.dr_info[1], data.dr_info[2] - 1);
    else
        show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}

function loadModel(ABC) {
    //parse to get all json data
    var data = ABC;
    //define all needed data array
    var chart_info = ["loadModel", "Load Model", " ", "time", "price(TWD)", "power(kW)"];
    var multi_name = [energyType.HEMS_chart_name, energyType.public1_chart_name, energyType.public2_chart_name, energyType.public3_chart_name, energyType.EM_charging_chart_name];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    if (data.EM_discharge_flag) {
        multi_name.push(energyType.EM_discharging_chart_name)
    }

    set_series_function(0, "line", data.electric_price, energyType.electrice_chart_name, 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(1, "column", data.load_model_seperate, "", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, multi_name);

    /*Show chart*/
    if (data.dr_mode != 0)
        show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1, data.dr_info[1], data.dr_info[2] - 1);
    else
        show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}

function EMchargingSOC(EM_start_departure_SOC) {

    var chart_info = ["EMchargingSOC", "EM users Arrived & Departure SOC", " ", "user number", "SOC(%)", ""];
    var multi_name = ["Departure SOC", "Arrived SOC"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(1, "column", EM_start_departure_SOC, energyType.Pload_chart_name, 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, multi_name);

    /*Show chart*/
    if (EM_start_departure_SOC[0] != null) {
        
        show_chart_with_EM_users(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, EM_start_departure_SOC[0].length-1);
    }
    else {
        document.getElementById('EMchargingSOC').style.display = "none";
        var hint = document.createTextNode("Wait for first departure EM user...")
        document.getElementById('EM_hint').appendChild(hint)
    }
}