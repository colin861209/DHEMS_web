var now_database_name = '';

window.onload = function () {

    get_backEnd_data();

}

function get_backEnd_data() {

    save_target = {

        modify_target: ["real_time", "Global_real_time", "dr_mode", "ElectricVehicle", "ElectricMotor", "uncontrollable_load_flag", "Global_uncontrollable_load_flag", "EV_generate_random_user_result", "EM_generate_random_user_result", "generate_uncontrollable_load_flag", "generate_Global_uncontrollable_load_flag", "chart_upperLowerLimit_flag", "Pgridmax", "Cbat", "battery_rate", "simulate_weather", "simulate_price", "SOCmin", "SOCmax", "SOCthres", "ini_SOC", "hydrogen_price"],
        fix_target: ["now_SOC", "next_simulate_timeblock", "Global_next_simulate_timeblock", "household_id"]
    }
    chart_limit = {

        modify_target: ["chart_upperLowerLimit_flag", "electric_price_upper_limit", "ev_chargingUser_nums_upper_limit", "em_n_chargingUser_nums_upper_limit", "load_model_upper_limit", "load_model_lower_limit", "load_model_seperate_upper_limit", "load_model_seperate_lower_limit", "each_household_status_upper_limit", "householdsLoadSum_upper_limit"],
        fix_target:[],
        
    }
    $.ajax
        ({
            type: "GET",
            url: "back_end/baseParameter.php",
            contentType: "application/x-www-form-urlencoded",
            processData: true,
            success: function (response) {

                response = JSON.parse(response);
                console.log(response);
                now_database_name = response.database_name;
                if (now_database_name == "DHEMS_fiftyHousehold") {
                    
                    save_target.fix_target.pop();
                    save_target.modify_target.splice(save_target.modify_target.indexOf("generate_Global_uncontrollable_load_flag")+1, 0, "comfortLevel_flag");
                }
                compare_timeblock = {
                
                    local: response.baseParameter[2][response.baseParameter[0].indexOf("next_simulate_timeblock")],
                    global: response.baseParameter[2][response.baseParameter[0].indexOf("Global_next_simulate_timeblock")]
                };
                simulate_solar(response);
                simulate_price(response);
                tableInfo = removeParameter(response, save_target);
                chartLimitInfo = removeParameter(response, chart_limit);
                insertText_after_breadcrumb(now_database_name, tableInfo[1][tableInfo[0].indexOf("simulate_weather")], tableInfo[1][tableInfo[0].indexOf("ini_SOC")])
                flag_table(tableInfo, save_target, 'flag_thead', 'flag_tbody');
                flag_table(chartLimitInfo, chart_limit, 'chart_limit_thead', 'chart_limit_tbody');
                console.log("baseParameter:", tableInfo);
                console.log("chartLimitInfo:", chartLimitInfo);
                console.log("compare_timeblock:", compare_timeblock);
                baseParameter_gauge(tableInfo, response);
                
                setInterval(() => {
                    simulate_solar(response);
                    simulate_price(response);
                }, 5000);
            }
        });
}

function change_databases(element) {

    var target_database_name;
    switch (parseInt(element.value)) {
        case 0:
            target_database_name = 'DHEMS';
            break;
        case 1:
            target_database_name = 'DHEMS_dr' + element.value;
            break;
        case 2:
            target_database_name = 'DHEMS_dr' + element.value;
            break;
        case 50:
            target_database_name = 'DHEMS_fiftyHousehold';
            break;
        default:
            console.log("Wrong database name")
            break;
    }

    if (now_database_name != target_database_name) {

        $.ajax
            ({
                type: "POST",
                url: "back_end/baseParameter.php",
                data: { phpReceive_database_name: target_database_name },
                success: function (response) {

                    response = JSON.parse(response);
                    if (response.database_name == target_database_name) {

                        Swal.fire({
                            icon: 'success',
                            title: '修改連線資料庫',
                            showCloseButton: true,
                            focusConfirm: false,
                            confirmButtonText: '<i class="fa fa-thumbs-up"></i> OK!',
                        })
                        .then(() => {
                            location.reload("")
                        }
                        );
                    }
                }
            });
    }
    else {

        Swal.fire({
            icon: 'warning',
            title: '選到相同資料庫...',
            text: '現在讀取資料庫: ' + now_database_name,
            timerProgressBar: true,
            timer: 1000,
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
        });
    }
}

function removeParameter(data, save_target) {

    baseParameter = data.baseParameter;
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

function baseParameter_gauge(data, fullInfo) {
  
    var fullInfo = {
        
        name: fullInfo.baseParameter[0],
        value: fullInfo.baseParameter[2],
        database_name: fullInfo.database_name,
        dr_count: fullInfo.dr_count,
        EM_flag: fullInfo.EM_flag,
        EM_charging_amount: fullInfo.EM_charging_amount,
        EM_sure_charging_amount: fullInfo.EM_sure_charging_amount

    }
    
    var baseParameter = {
        
        name: data[0],
        value: data[2],
    }
    
    var show = {
        
        label: ["住戶時刻", "社區時刻", "now SOC", "排程中住戶", "需量模式", "機車充電數量"],
        id: [
            "next_simulate_timeblock",
            "Global_next_simulate_timeblock",
            "now_SOC",
            "household_id",
            "dr_mode",
            "EM_charging_amount"
        ],
        value: [
            baseParameter.value[baseParameter.name.indexOf("next_simulate_timeblock")],
            baseParameter.value[baseParameter.name.indexOf("Global_next_simulate_timeblock")],
            baseParameter.value[baseParameter.name.indexOf("now_SOC")],
            baseParameter.value[baseParameter.name.indexOf("household_id")],
            baseParameter.value[baseParameter.name.indexOf("dr_mode")],
            fullInfo.EM_charging_amount
        ],
    }

    var next_simulate_timeblock = new JustGage({

        id: show.id[0] + "_gauge",
        value: show.value[0],
        min: 0,
        max: fullInfo.value[fullInfo.name.indexOf(fullInfo.name[0])],
        decimals: 0,
        symbol: '',
        label: show.label[0],
        pointer: true,

        pointerOptions: {
            toplength: -15,
            bottomlength: 10,
            bottomwidth: 12,
            color: '#8e8e93',
            stroke: '#ffffff',
            stroke_width: 2,
            stroke_linecap: 'round'
        },
        gaugeWidthScale: 0.7,
        counter: true
    });

    var Global_next_simulate_timeblock = new JustGage({

        id: show.id[1] + "_gauge",
        value: show.value[1],
        min: 0,
        max: fullInfo.value[fullInfo.name.indexOf(fullInfo.name[0])],
        decimals: 0, //小數點
        symbol: '',
        label: show.label[1],
        pointer: true,

        pointerOptions: {
            toplength: -15,
            bottomlength: 10,
            bottomwidth: 12,
            color: '#8e8e93',
            stroke: '#ffffff',
            stroke_width: 2,
            stroke_linecap: 'round'
        },
        gaugeWidthScale: 0.7,
        counter: true
    });

    var now_SOC = new JustGage({

        id: show.id[2] + "_gauge",
        value: show.value[2],
        min: 0,
        max: 1,
        decimals: 3,
        symbol: '',
        label: show.label[2],
        pointer: true,

        pointerOptions: {
            toplength: -15,
            bottomlength: 10,
            bottomwidth: 12,
            color: '#8e8e93',
            stroke: '#ffffff',
            stroke_width: 2,
            stroke_linecap: 'round'
        },
        gaugeWidthScale: 0.7,
        counter: true
    });

    var household_id = new JustGage({

        id: show.id[3] + "_gauge",
        value: show.value[3],
        min: 1,
        max: fullInfo.value[fullInfo.name.indexOf(fullInfo.name[1])],
        decimals: 0,
        symbol: '',
        label: show.label[3],
        pointer: true,

        pointerOptions: {
            toplength: -15,
            bottomlength: 10,
            bottomwidth: 12,
            color: '#8e8e93',
            stroke: '#ffffff',
            stroke_width: 2,
            stroke_linecap: 'round'
        },
        gaugeWidthScale: 0.7,
        counter: true
    });
    
    var dr_mode = new JustGage({
        
        id: show.id[4] + "_gauge",
        value: show.value[4],
        min: 0,
        max: fullInfo.dr_count,
        decimals: 0,
        symbol: '',
        label: show.label[4],
        pointer: true,

        pointerOptions: {
            toplength: -15,
            bottomlength: 10,
            bottomwidth: 12,
            color: '#8e8e93',
            stroke: '#ffffff',
            stroke_width: 2,
            stroke_linecap: 'round'
        },
        gaugeWidthScale: 0.7,
        counter: true
    });

    var EM_charging_amount = new JustGage({

        id: show.id[5] + "_gauge",
        value: show.value[5],
        min: 0,
        max: fullInfo.EM_sure_charging_amount,
        decimals: 0, //小數點
        symbol: '',
        label: show.label[5],
        pointer: true,

        pointerOptions: {
            toplength: -15,
            bottomlength: 10,
            bottomwidth: 12,
            color: '#8e8e93',
            stroke: '#ffffff',
            stroke_width: 2,
            stroke_linecap: 'round'
        },
        gaugeWidthScale: 0.7,
        counter: true
    });

    
    if (fullInfo.database_name == "DHEMS_fiftyHousehold") {

        document.getElementById("household_id_gauge").style.display = 'none';
        if (fullInfo.EM_flag)
            document.getElementById("EM_charging_amount_gauge").style.display = 'block';
    }

}

function simulate_solar(data) {
    
    var chart_info = ["simulate_solar_chart", "Solar Power", data.baseParameter[2][data.baseParameter[0].indexOf("simulate_weather")], "time", "power(kW)", null, null, [null, null], 'orange'];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "line", data.simulate_solar, "Weather", 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, null);
}

function simulate_price(data) {
      
    var chart_info = ["simulate_price_chart", "Electric Price", data.baseParameter[2][data.baseParameter[0].indexOf("simulate_price")], "time", "price(NTD)", null, data.electric_price_upper_limit, [null, null]];
    var chart_series_type = [];
    var chart_series_name = [];
    var chart_series_data = [];
    var chart_series_stack = [];
    var chart_series_yAxis = [];

    set_series_function(0, "line", data.electric_price, energyType.electrice_chart_name, 0, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis);

    show_chart_with_redDashLine(chart_info, chart_series_type, chart_series_name, chart_series_data, chart_series_stack, chart_series_yAxis, null);
}