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
                compare_timeblock = {
                    
                    local: ourData.local_simulate_timeblock,
                    global: ourData.global_simulate_timeblock
                };
                household_num = 0;
                hide_household_button(ourData.database_name == "DHEMS_fiftyHousehold");
                increase_chartHeight('households_loadsSum', ourData.database_name == "DHEMS_fiftyHousehold");
                insertText_after_breadcrumb(response.database_name, null, null, ourData.dr_mode, ourData.dr_info)
                householdsLoadSum(ourData);
                uncontrollable_loadSum(ourData);
                muti_divs(ourData);
                each_household_status(ourData, 0)
                each_household_status_SOC(ourData, household_num)
                run_household_eachLoad(ourData, 0)
                progessbar(ourData);
                autoRun(ourData, household_num)
                flag_table(LHEMS_flag)
                participate_table(ourData.dr_mode, ourData.dr_info, ourData.dr_participation, household_num);
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

function choose_singleHousehold(household_id) {

    $('input[type="checkbox"]').prop("checked", false);
    clearInterval(function_run);
    each_household_status(ourData, household_id - 1)
    each_household_status_SOC(ourData, household_id - 1)
    run_household_eachLoad(ourData, household_id - 1);
    show_participate_timeblock(ourData.dr_mode, ourData.dr_info, ourData.dr_participation, household_id - 1)
}

function autoRun(ourData, household_num) {

    function_run = setInterval(function () {

        // householdsLoadSum(ourData);
        if (household_num + 1 == ourData.household_num)
            household_num = 0
        else
            household_num++
        each_household_status(ourData, household_num)
        each_household_status_SOC(ourData, household_num)
        run_household_eachLoad(ourData, household_num)
        progessbar(ourData);
        show_participate_timeblock(ourData.dr_mode, ourData.dr_info, ourData.dr_participation, household_num)
    }, 7000);

}

function run_household_eachLoad(ourData, household_num) {

    document.getElementById("household_id").setAttribute("value", household_num + 1)
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
    if (data.dr_mode != 0)
        show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1, data.dr_info[1], data.dr_info[2] - 1);
    else
        show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);
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
        set_series_function(0, "spline", data.battery_power[household_id], energyType.Pess_chart_name, 1, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

        /*Show chart*/
        if (data.dr_mode != 0)
            show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1, data.dr_info[1], data.dr_info[2] - 1);
        else
            show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);
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
    if (data.dr_mode != 0)
        show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1, data.dr_info[1], data.dr_info[2] - 1);
    else
        show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);

}

function uncontrollable_loadSum(data) {

    if (parseInt(data.uncontrollable_load_flag)) {

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
        if (data.dr_mode != 0)
            show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1, data.dr_info[1], data.dr_info[2] - 1);
        else
            show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, data.simulate_timeblock - 1);
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
    if (data.comfortLevel_flag) {

        var this_s_time = data.each_household_startComfortLevel[household_num];
        var this_e_time = data.each_household_endComfortLevel[household_num];
        var start = [], end = [];
        for (let i = 0; i < this_s_time.length; i++) {
            
            var comfort_s_tmp = [], comfort_e_tmp = [];    
            for (let j = 0; j < this_s_time[i].length; j++) {
                
                comfort_s_tmp.push(this_s_time[i][j][num]);
                comfort_e_tmp.push(this_e_time[i][j][num] - 1);
            }
            start.push(comfort_s_tmp);
            end.push(comfort_e_tmp);
        }
    }
    else {

        var this_s_time = data.start[household_num];
        var this_e_time = data.end[household_num];
        var start = this_s_time[num];
        var end = this_e_time[num] - 1;
    }

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
    show_chart_with_pinkAreaOrComforLevel(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, start, end, data.comfortLevel_flag);

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

function hide_household_button(condition) {

    if (condition) {

        button_household_group = document.getElementsByClassName('button_household_group');
        for (let index = 0; index < button_household_group.length; index++)
            button_household_group[index].style.display = 'none';
        document.getElementById('button_household_range').style.display = 'block';
        document.getElementById('button_household_next').style.display = 'block';
    }
}

function increase_chartHeight(chart_id, condition) {

    if (condition) {

        document.getElementById(chart_id).style.height = '1000px';
    }
}

function choose_singleHousehold_by_rangeBar() {

    Swal.fire({
        title: 'Choose number of household',
        confirmButtonText: 'GO <i class="fa fa-arrow-right"></i>',
        icon: 'question',
        input: 'range',
        width: 800,
        inputLabel: '1 ~ '+ ourData.household_num,
        inputAttributes: {
          min: 1,
          max: ourData.household_num,
          step: 1
        },
        inputValue: 1,
        didOpen:() => {

            Swal.getConfirmButton().addEventListener('click', () => {
                choose_singleHousehold(Swal.getInput().value)
            })
        }
    })
}

function nextOrPrevious_singleHousehold(value) {
    
    element = document.getElementById('household_id');
    now_household_id = element.getAttribute('value');
    choose_singleHousehold(parseInt(now_household_id) + parseInt(value))
}

function participate_table(dr_mode, info, participation_status, household_id) {
    
    if (parseInt(dr_mode) != 0) {
    
        document.getElementsByClassName('table table-bordered')[0].style.display = 'revert';
        show_participate_timeblock(dr_mode, info, participation_status, household_id);
    }
}

function show_participate_timeblock(dr_mode, info, participation, household_id) {

    if (parseInt(dr_mode) != 0) {

        $('#table_participate_tbody > td').remove()
        const dr_start = parseInt(info[1]);
        const dr_end = parseInt(info[2]);
        participate_onOff = [[], []];
        try {

            for (let index = dr_start; index < dr_end; index++) {
                if (participation[household_id][index] == 1)
                    participate_onOff[0].push(index)
                else if (participation[household_id][index] == 0)
                    participate_onOff[1].push(index)
            }
                
            for (let array_num = 0; array_num < participate_onOff.length; array_num++) {
                
                var td = document.createElement('td');
                
                if (participate_onOff[array_num].length == 0) {
                    participate_onOff[array_num].push("無")
                    td.appendChild(document.createTextNode(participate_onOff[array_num]));
                }
                else {
                    word = replace_continuously_timeblock(participate_onOff[array_num]);
                    td.appendChild(document.createTextNode(word));
                }
                td.setAttribute("style", "text-align: center; color:black; font-size: 20px; font-weight:bolder");
                document.getElementById('table_participate_tbody').appendChild(td);
            }
        }
        catch(e) {
            
            console.log(" Reason: DB may not have realted table with 'Particpation'\n")
        }
    }
}

function replace_continuously_timeblock(participate_onOff) {
    
    word = String(participate_onOff).replace(/,/g, ' ');
    replace_text = "";

    for (var i = 1; i < participate_onOff.length-1; i++) {
        if (participate_onOff[i] == participate_onOff[i-1] + 1 && participate_onOff[i] == participate_onOff[i+1] - 1)
            replace_text += participate_onOff[i] + " ";
        
        else {
            if (replace_text !== "") {
                
                word = word.replace(replace_text, "~ ");
                replace_text = "";
            }
        }
    }
    if (replace_text !== "")
        word = word.replace(replace_text, "~ ");
    
    word = word.replace(/ /g, ', ');
    word = word.replace(/, ~, /g, ' ~ ');

    return word;
}
