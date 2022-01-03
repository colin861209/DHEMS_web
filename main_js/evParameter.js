var now_database_name = '';

window.onload = function () {

    get_backEnd_data();

}

function get_backEnd_data() {

    evParm_save_target = {

        modify_target: ["Normal_Charging_Pole", "Fast_Charging_Pole", "Super_Fast_Charging_Pole", "EV_Upper_SOC","EV_Lower_SOC", "EV_threshold_SOC", "charging_pole_charging_efficiency", "charging_pole_discharging_efficiency",  "Pgrid_Upper_limit"],
        fix_target: ["Total_Charging_Pole",  "Total_Num_of_EM", "Normal_Charging_power","Fast_Charging_power","Super_Fast_Charging_power"]
    }
    evESS_save_target = {
        modify_target:["ESS_capacity", "ESS_Upper_SOC", "ESS_lower_SOC", "ESS_SOC_threshold", "ESS_efficiency"],
        fix_target:["ESS_now_SOC"]
    }
    evRand_save_target = {
        modify_target:["normal_soc_mean", "normal_soc_variance", "normal_time_mean", "normal_time_variance", "normal_wait_mean", "normal_wait_variance", "fast_soc_mean", "fast_soc_variance", "fast_time_mean", "fast_time_variance", "fast_wait_mean", "fast_wait_variance", "super_fast_soc_mean", "super_fast_soc_variance", "super_fast_time_mean", "super_fast_time_variance", "super_fast_wait_mean", "super_fast_wait_variance"],
        fix_target:[]
    }
    $.ajax
        ({
            type: "GET",
            url: "back_end/evParameter.php",
            contentType: "application/x-www-form-urlencoded",
            processData: true,
            success: function (response) {

                response = JSON.parse(response);
                console.log(response);
                now_database_name = response.database_name;
                
                compare_timeblock = {
                
                    local: response.local_simulate_timeblock,
                    global: response.global_simulate_timeblock,
                };

                var evParm_tableInfo = removeParameter(response.evParameter, evParm_save_target);
                evParm_tableInfo = removeParameter(response.evParameter, evParm_save_target);
                show_motorParameter(evParm_tableInfo, evParm_save_target, "evParm_thead", "evParm_tbody");
               
                var evESS_tableInfo = removeParameter(response.evParameter_of_ESS, evESS_save_target);
                evESS_tableInfo = removeParameter(response.evParameter_of_ESS, evESS_save_target);
                show_motorParameter(evESS_tableInfo, evESS_save_target, "evESS_thead", "evESS_tbody");
               
                var evRand_tableInfo = removeParameter(response.evParameter_of_randomResult, evRand_save_target);
                evRand_tableInfo = removeParameter(response.evParameter_of_randomResult, evRand_save_target);
                show_motorParameter(evRand_tableInfo, evRand_save_target, "evRand_thead", "evRand_tbody");
                
                insertText_after_breadcrumb(now_database_name, null, null);
                show_motorType_percent(response.ev_motor_type);
                show_wholeDay_chargingUser_nums(response.n_chargingUser_nums, response.f_chargingUser_nums, response.sf_chargingUser_nums)
            }
        });
}

function removeParameter(baseParameter, save_target) {

    var modify_index = [];
    var fix_index = [];
    var remainParameter = [];

    for (let i = 0; i < save_target.modify_target.length; i++) {

        modify_index[i] = baseParameter[0].indexOf(save_target.modify_target[i]);
    }
    for (let i = 0; i < save_target.fix_target.length; i++) {

        fix_index[i] = baseParameter[0].indexOf(save_target.fix_target[i]);
    }
    save_index = modify_index.concat(fix_index);

    for (let row = 0; row < baseParameter.length; row++) {
        remain_content = [];
        for (let i = 0; i < save_index.length; i++) {

            remain_content.push(baseParameter[row][save_index[i]]);
        }
        remainParameter.push(remain_content);
    }

    return remainParameter;
}

function show_motorParameter(baseParameter, save_target, thead_id, tbody_id) {
    
    var tableData = {

        name: ["參數名", "數值"]
    }
    switch (thead_id) {
        case "evParm_thead":
            onchage_function_name = "evParameter_change("+thead_id+")"
            break;
        case "evESS_thead":
            onchage_function_name = "evParameter_change("+thead_id+")"
            break;
        case "evRand_thead":
            onchage_function_name = "evParameter_change("+thead_id+")"
            break;
        default:
            break;
    }
    for (let nameNum = 0; nameNum < tableData.name.length; nameNum++) {

        var th = document.createElement('th');
        th.appendChild(document.createTextNode(tableData.name[nameNum]));
        th.setAttribute("style", "text-align: center; color:black");
        document.getElementById(thead_id).appendChild(th);
    }

    for (let typeNum = 0; typeNum < baseParameter[0].length; typeNum++) {

        var tr = document.createElement('tr');
        tr.setAttribute("style", "text-align: center; color:black; font-size: 17px");
        var fix = 0;
        if (save_target.fix_target.includes(baseParameter[0][typeNum])) {
            fix = 1;
        }
        for (let dataNum = 0; dataNum < baseParameter.length; dataNum++) {
            
            var td = document.createElement('td');
            switch (dataNum == 1 && fix == 0) {
                case true:

                    var input = document.createElement('input');
                    input.setAttribute("type", "text");
                    input.setAttribute("id", baseParameter[0][typeNum]);
                    input.setAttribute("style", "text-align: center; background-color: #ABFFFF;");
                    input.setAttribute("size", "15");
                    input.setAttribute("value", baseParameter[dataNum][typeNum]);
                    input.setAttribute("onchange", onchage_function_name);
                    td.appendChild(input);
                    break;

                default:
                    td.appendChild(document.createTextNode(baseParameter[dataNum][typeNum]));
                    break;
            }
            
            tr.appendChild(td);
        }
        document.getElementById(tbody_id).appendChild(tr);
    }
}

function show_motorType_percent(type) {
    
    motor = {
        img: [
            "images/motor/iE125.png",
            "images/motor/iE125B.png",
            "images/motor/emoving.png",
            "images/motor/emoving_Shine.png",
            "images/motor/emoving_Super.png",
            "images/motor/emoving_bobe.png",
        ],
        name: type[0],
        capacity: type[1],
        voltage: type[2],
        power: type[3],
        percent: type[4],
    }
    motor_key = ["單位", " ", "電池容量", "電池電壓", "電池功率", "設定人數"];
    
    // 單位
    for (let index = 0; index < motor.name.length + 1; index++) {

        var th = document.createElement('th');
        if (index == 0) {
            th.appendChild(document.createTextNode(motor_key[0]));
            th.setAttribute("class", "text-center");
            document.getElementById('motor_name').appendChild(th);
        }
        else {
            if (motor.name[index-1] != motor.name[index]) {
                th.appendChild(document.createTextNode(motor.name[index-1]));
                th.setAttribute("class", "text-center");
                document.getElementById('motor_name').appendChild(th);
            }
        }
    }
    
    // 圖片
    var tr = document.createElement('tr');
    for (let imgNum = 0; imgNum < motor.img.length + 1; imgNum++) {
        
        var td = document.createElement('td');
        if (imgNum == 0) {
            td.appendChild(document.createTextNode(motor_key[1]));
            tr.appendChild(td);
        }
        else {
            var img = document.createElement('img');
            img.src = motor.img[imgNum-1];
            img.width = 200;
            tr.appendChild(td).appendChild(img); 
        }
    }
    document.getElementById('motor_info').appendChild(tr);
    
    // 容量
    var tr = document.createElement('tr');
    for (let index = 0; index < motor.capacity.length + 1; index++) {

        var td = document.createElement('td');
        if (index == 0) {
            td.appendChild(document.createTextNode(motor_key[2]));
            td.setAttribute("style", "text-align: center");
            tr.appendChild(td);
        }
        else {
            if (motor.name[index-1] != motor.name[index]) {
                td.appendChild(document.createTextNode(motor.capacity[index-1]+"(Ah)"));
                td.setAttribute("style", "text-align: center");
                tr.appendChild(td);
            }
        }
    }
    document.getElementById('motor_info').appendChild(tr);

    // 電壓
    var tr = document.createElement('tr');
    for (let index = 0; index < motor.voltage.length + 1; index++) {

        var td = document.createElement('td');
        if (index == 0) {
            td.appendChild(document.createTextNode(motor_key[3]));
            td.setAttribute("style", "text-align: center");
            tr.appendChild(td);
        }
        else {
            if (motor.name[index-1] != motor.name[index]) {
                td.appendChild(document.createTextNode(motor.voltage[index-1]+"(V)"));
                td.setAttribute("style", "text-align: center");
                tr.appendChild(td);
            }
        }
    }
    document.getElementById('motor_info').appendChild(tr);

    // 功率
    var tr = document.createElement('tr');
    var word="";
    for (let index = 0; index < motor.power.length + 1; index++) {

        var td = document.createElement('td');
        if (index == 0) {
            td.appendChild(document.createTextNode(motor_key[4]));
            td.setAttribute("style", "text-align: center");
            tr.appendChild(td);
        }
        else {
            if (motor.name[index-1] != motor.name[index]) {
                word += motor.power[index-1] + "(kW)";
                td.appendChild(document.createTextNode(word));
                td.setAttribute("style", "text-align: center");
                tr.appendChild(td);
                word="";
            }
            else {
                word += motor.power[index-1] + "(kW) / ";
            }
        }
    }
    document.getElementById('motor_info').appendChild(tr);

    // 設定人數
    var tr = document.createElement('tr');
    var input = document.createElement('td');
    for (let index = 0; index < motor.power.length + 1; index++) {

        var td = document.createElement('td');
        if (index == 0) {
            td.appendChild(document.createTextNode(motor_key[5]));
            td.setAttribute("style", "text-align: center");
            tr.appendChild(td);
        }
        else {
            var input_tmp = document.createElement('input');
            input_tmp.setAttribute("type", "text");
            input_tmp.setAttribute("name", "motor_type");
            input_tmp.setAttribute("id", "type"+(index-1));
            input_tmp.setAttribute("style", "text-align: center; background-color: #ABFFFF;");
            input_tmp.setAttribute("size", "2");
            input_tmp.setAttribute("value", motor.percent[index-1]);
            input.setAttribute("onchange", "evPercent_change()");
            
            if (motor.name[index-1] != motor.name[index]) {
                input.appendChild(input_tmp);
                input.appendChild(document.createTextNode(" (%)"));
                tr.appendChild(input);
                input = document.createElement('td')
            }
            else {
                input.appendChild(input_tmp);
                input.appendChild(document.createTextNode(" (%) / "));
            }
        }
    }
    document.getElementById('motor_info').appendChild(tr);
}

function show_wholeDay_chargingUser_nums(n_chargingUser_nums, f_chargingUser_nums, sf_chargingUser_nums) {
    
    chargingNums_dic = {
        id: ["normal_chargingUser_nums", "fast_chargingUser_nums", "superFast_chargingUser_nums"],
        title: ["Normal Charging User Numbers", "Fast Charging User Numbers", "Super Fast Charging User Numbers"],
        data: [n_chargingUser_nums, f_chargingUser_nums, sf_chargingUser_nums]
    }
    
    for (let index = 0; index < chargingNums_dic.id.length; index++) {
        
        var chart_info = [chargingNums_dic.id[index], chargingNums_dic.title[index], "", "time", "個數", null];
        var chart_series_type = [];
        var chart_series_name = [];
        var chart_series_data = [];
        var chart_series_stack = [];
        var chart_series_yAxis = [];
        
        set_series_function(1, "column", chargingNums_dic.data[index], "", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);
        show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, null);
    }
}