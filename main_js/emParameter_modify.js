// Motor parameter setting 
function emevParameter_change(thead_id) {

    switch (thead_id.id) {
        case "evParm_thead":
            document.getElementById('btn_evParameterModify').style.display = "block";
            break;
        case "evRand_thead":
            document.getElementById('btn_evRandModify').style.display = "block";
            break;
        case "emParm_thead":
            document.getElementById('btn_emParameterModify').style.display = "block";
            break;
        case "emRand_thead":
            document.getElementById('btn_emRandModify').style.display = "block";
            break;
        default:
            break;
    }
}

function sendNewEMParameter(btn_id) {

    var new_flag = [], name = [], table;
    
    if (btn_id == "btn_emParameterModify") {
        table = "EM_Parameter";
        name = emParm_save_target.modify_target;
    }
    else if (btn_id == "btn_emRandModify") {
        table = "EM_Parameter_of_randomResult";
        name = emRand_save_target.modify_target;
    }
    else if (btn_id == "btn_evParameterModify") {
        table = "EV_Parameter";
        name = evParm_save_target.modify_target;
    }
    else if (btn_id == "btn_evRandModify") {
        table = "EV_Parameter_of_randomResult";
        name = evRand_save_target.modify_target;
    }

    var new_ParameterData = {
        table: table,
        name: name,
        value: new_flag
    }
    for (let index = 0; index < new_ParameterData.name.length; index++) {

        new_flag[index] = document.getElementById(new_ParameterData.name[index]).value
    }
    console.log(new_ParameterData)
    $.ajax
        ({
            type: "POST",
            url: "back_end/send_newEMEV_parameterOrType.php",
            data: { phpReceive: new_ParameterData },
            // contentType: "application/x-www-form-urlencoded",
            // processData: true,
            success: function (response) {

                response = JSON.parse(response);
                console.log(response)
                if (response.status == "success") {
                    
                    // alert("旗 標 修 改 完 成")
                    // location.reload("/baseParameter.html");
                    Swal.fire({
                        icon: 'success',
                        title: '旗標修改完成',
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

$(document).ready(function () {
    $("#evParm_table").click(function () {
        $("#evParm_flags").fadeToggle();
    });
});
$(document).ready(function () {
    $("#evRand_table").click(function () {
        $("#evRand_flags").fadeToggle();
    });
});
$(document).ready(function () {
    $("#emParm_table").click(function () {
        $("#emParm_flags").fadeToggle();
    });
});
$(document).ready(function () {
    $("#emRand_table").click(function () {
        $("#emRand_flags").fadeToggle();
    });
});

// Motor type setting  
function evPercent_change() {
    
    document.getElementById('btn_evPercentModify').style.display = "block";
}
function emPercent_change() {
    
    document.getElementById('btn_emPercentModify').style.display = "block";
}

function sendNewEMEVPercent(btn_id) {
    
    var new_flag = [], id = [], new_typeData = {};
    switch (btn_id) {
        case 'btn_emPercentModify':
            new_typeData['table'] = "EM_motor_type";
            new_typeData['name'] = "motor_type";
            new_typeData['id'] = id;
            new_typeData['percent_value'] = new_flag;
            break;
        case 'btn_evPercentModify':
            new_typeData['table'] = "EV_motor_type";
            new_typeData['name'] = "vehicle_type";
            new_typeData['id'] = id;
            new_typeData['percent_value'] = new_flag;
            break;
        default:
            break;
    }
    
    for (let index = 0; index < document.getElementsByName(new_typeData.name).length; index++) {

        new_flag[index] = document.getElementsByName(new_typeData.name)[index].value
        id[index] = index
    }
    console.log(new_typeData)
    $.ajax
        ({
            type: "POST",
            url: "back_end/send_newEMEV_parameterOrType.php",
            data: { phpReceive: new_typeData },
            // contentType: "application/x-www-form-urlencoded",
            // processData: true,
            success: function (response) {

                response = JSON.parse(response);
                console.log(response)
                if (response.status == "success") {
                    
                    // alert("旗 標 修 改 完 成")
                    // location.reload("/baseParameter.html");
                    Swal.fire({
                        icon: 'success',
                        title: '旗標修改完成',
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