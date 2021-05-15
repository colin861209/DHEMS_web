window.onload = function () {

    get_backEnd_data();

}

function get_backEnd_data() {

    save_target = {

        modify_target: ["SOCmin", "SOCmax", "SOCthres", "real_time", "Global_real_time", "dr_mode", "uncontrollable_load_flag", "ini_SOC", "hydrogen_price", "simulate_weather"],
        fix_target: ["now_SOC", "next_simulate_timeblock", "Global_next_simulate_timeblock", "household_id"]
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
                tableInfo = removeParameter(response, save_target);
                flag_table(tableInfo, save_target);
                console.log(tableInfo);
                gauge(tableInfo, response);
            }
        });
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

function gauge(data, fullInfo) {
    
    var fullInfo = {
        
        name: fullInfo.baseParameter[0],
        value: fullInfo.baseParameter[1],
    }

    var baseParameter = {

        name: data[0],
        value: data[1],
    }

    var next_simulate_timeblock = new JustGage({
        
        id: save_target.fix_target[1] + "_gauge",
        value: baseParameter.value[baseParameter.name.indexOf(save_target.fix_target[1])],
        min: 0,
        max: fullInfo.value[fullInfo.name.indexOf(fullInfo.name[0])],
        decimals: 0,
        symbol: '',
        label: "住戶時刻",
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

        id: save_target.fix_target[2]+ "_gauge",
        value: baseParameter.value[baseParameter.name.indexOf(save_target.fix_target[2])],
        min: 0,
        max: fullInfo.value[fullInfo.name.indexOf(fullInfo.name[0])],
        decimals: 0, //小數點
        symbol: '',
        label: "社區時刻",
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

        id: save_target.fix_target[0] + "_gauge",
        value: baseParameter.value[baseParameter.name.indexOf(save_target.fix_target[0])],
        min: 0,
        max: 1,
        decimals: 3,
        symbol: '',
        label: "now SOC",
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

        id: save_target.fix_target[3]+ "_gauge",
        value: baseParameter.value[baseParameter.name.indexOf(save_target.fix_target[3])],
        min: 1,
        max: fullInfo.value[fullInfo.name.indexOf(fullInfo.name[1])],
        decimals: 0,
        symbol: '',
        label: "排程中住戶",
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
        
        id: save_target.modify_target[5] + "_gauge",
        value: baseParameter.value[baseParameter.name.indexOf(save_target.modify_target[5])],
        min: 0,
        max: 2,
        decimals: 0,
        symbol: '',
        label: "需量模式",
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
}
