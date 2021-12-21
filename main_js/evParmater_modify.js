// Motor parameter setting 
function evParameter_change(thead_id) {

    switch (thead_id.id) {
        case "evParm_thead":
            document.getElementById('btn_evParameterModify').style.display = "block";
            break;
        case "evESS_thead":
            document.getElementById('btn_evESSModify').style.display = "block";
            break;
        case "evRand_thead":
            document.getElementById('btn_evRandModify').style.display = "block";
            break;
        default:
            break;
    }
}

function sendNewEVParameter(btn_id) {

    var new_flag = [], name = [], table;
    
    if (btn_id == "btn_evParameterModify") {
        table = "EV_Parameter";
        name = evParm_save_target.modify_target;
    }
    else if (btn_id == "btn_evESSModify") {
        table = "EV_Parameter_of_ESS";
        name = evESS_save_target.modify_target;
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
            url: "back_end/send_newEV_parameterOrType.php",
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
    $("#evESS_table").click(function () {
        $("#evESS_flags").fadeToggle();
    });
});
$(document).ready(function () {
    $("#evRand_table").click(function () {
        $("#evRand_flags").fadeToggle();
    });
});

// Motor type setting  
function evPercent_change() {
    
    document.getElementById('btn_evPercentModify').style.display = "block";
}

function sendNewEVPercent() {
    
    var new_flag = [], id = []
    var new_typeData = {

        table: "EV_motor_type",
        id: id,
        percent_value: new_flag
    }
    for (let index = 0; index < document.getElementsByName('motor_type').length; index++) {

        new_flag[index] = document.getElementById('type'+index).value
        id[index] = index
    }
    console.log(new_typeData)
    $.ajax
        ({
            type: "POST",
            url: "back_end/send_newEV_parameterOrType.php",
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

// update simulation_LP_parameter
// TRUNCATE simulation_percent_number & SELECT simulation_user_number 2 INSERT simulation_percent_number
// TRUNCATE & INSERT simulation_Pole