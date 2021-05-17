//get time block
var dt = new Date();
var now_t = Math.floor(dt.getHours() * 4 + dt.getMinutes() / 15);

var ourData = [];
var LHEMS_flag = [];
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
                LHEMS_flag = ourData.LHEMS_flag;
                household_num = 0;
                insertText_after_breadcrumb(response.database_name, null, null)
                householdsLoadSum(ourData);
                uncontrollable_loadSum(ourData);
                muti_divs(ourData);
                each_household_status(ourData, 0)
                each_household_status_SOC(ourData, household_num)
                run_household_eachLoad(ourData, 0)
                progessbar(ourData);
                autoRun(ourData, household_num)
                flag_table(LHEMS_flag)
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
    each_household_status(ourData, element.id - 1)
    each_household_status_SOC(ourData, element.id - 1)
    run_household_eachLoad(ourData, element.id - 1);

}

function autoRun(ourData, household_num) {

    function_run = setInterval(function () {

        householdsLoadSum(ourData);
        if (household_num + 1 == ourData.household_num)
            household_num = 0
        else
            household_num++
        each_household_status(ourData, household_num)
        each_household_status_SOC(ourData, household_num)
        run_household_eachLoad(ourData, household_num)
        progessbar(ourData);
    }, 7000);

}

function run_household_eachLoad(ourData, household_num) {

    document.getElementById("household_id").innerHTML = "住戶 " + (household_num + 1) + " 負載使用情況"
    var i = 0;
    for (i = 0; i < ourData.app_counts; i++) {
        each_load(ourData, i, household_num)
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

function each_household_status(data, household_id) {

    var chart_info = ["each_household_status", "Household " + (household_id + 1) + " Status", " ", "time", "price(TWD)", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    var load_power_sum_with_UCLoad = [];
    for (let index = 0; index < data.load_power_sum[household_id].length; index++) {
        load_power_sum_with_UCLoad[index] = data.load_power_sum[household_id][index] + data.uncontrollable_load[household_id][index];
    }

    set_series_function(0, "line", data.electric_price, energyType.electrice_chart_name, 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "column", load_power_sum_with_UCLoad, "household_" + (household_id + 1), 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(0, "areaspline", data.grid_power[household_id], energyType.Pgrid_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    if (LHEMS_flag[0].indexOf(energyType.Pess_flag_name) !== -1 && LHEMS_flag[1][LHEMS_flag[0].findIndex(flag => flag === energyType.Pess_flag_name)] == 1)
        set_series_function(0, "spline", data.battery_power[household_id], energyType.Pess_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    /*Show chart*/
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock);
}

function each_household_status_SOC(data, household_id) {
    
    if (LHEMS_flag[0].indexOf(energyType.Pess_flag_name) !== -1 && LHEMS_flag[1][LHEMS_flag[0].findIndex(flag => flag === energyType.Pess_flag_name)] == 1) {

        var chart_info = ["each_household_status_SOC", "", " ", "time", "SOC", "power(kW)"];
        var chart_series_type = [];
        var chart_series_name = [];
        var chart_series_data = [];
        var chart_series_stack = [];
        var chart_series_yAxis = [];

        var load_power_sum_with_UCLoad = [];
        for (let index = 0; index < data.load_power_sum[household_id].length; index++) {
            load_power_sum_with_UCLoad[index] = data.load_power_sum[household_id][index] + data.uncontrollable_load[household_id][index];
        }

        set_series_function(0, "spline", data.SOC[household_id], energyType.SOC_chart_name, 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
        set_series_function(0, "column", load_power_sum_with_UCLoad, "household_" + (household_id + 1), 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
        set_series_function(0, "areaspline", data.grid_power[household_id], energyType.Pgrid_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

        /*Show chart*/
        show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock);
    }
    else {

        document.getElementById('each_household_status_SOC').style.display = "none";
    }
}

function householdsLoadSum(data) {
    //parse to get all json data

    var chart_info = ["households_loadsSum", "Households' Loads Comsuption", " ", "time", "price(TWD)", "power(kW)"];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "line", data.electric_price, energyType.electrice_chart_name, 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    // set_series_function(0, "line", data.limit_capability, "limit-power", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_series_function(1, "column", data.load_power_sum, "household_", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    /*Show chart*/
    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock);

}

function uncontrollable_loadSum(data) {

    if (data.uncontrollable_flag) {

        var chart_info = ["uncontrollable_loadSum", "Households' Uncontrllable Loads", " ", "time", "price(TWD)", "power(kW)"];
        var chart_series_type = [];
        var chart_series_name = [];
        var chart_series_data = [];
        var chart_series_stack = [];
        var chart_series_yAxis = [];
        
        set_series_function(0, "line", data.electric_price, energyType.electrice_chart_name, 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
        // set_series_function(0, "line", data.limit_capability, "limit-power", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
        set_series_function(1, "column", data.uncontrollable_load, "household_", 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
        
        /*Show chart*/
        show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock);
    }
    else {

        document.getElementById('uncontrollable_loadSum').style.display = "none";
    }
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
    set_each_load_function(0, "line", data.electric_price, null, energyType.electrice_chart_name, 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    set_each_load_function(0, "column", this_load[num], ((household_num + 1) + "-" + this_ID[num]), ((household_num + 1) + "-" + this_ID[num]), 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
    /*Show chart*/
    show_chart_with_pinkArea(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, start, end);

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
