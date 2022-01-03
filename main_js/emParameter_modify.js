// Motor parameter setting 
function emParameter_change(thead_id) {

    switch (thead_id.id) {
        case "emParm_thead":
            document.getElementById('btn_emParameterModify').style.display = "block";
            break;
        case "emESS_thead":
            document.getElementById('btn_emESSModify').style.display = "block";
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
    else if (btn_id == "btn_emESSModify") {
        table = "EM_Parameter_of_ESS";
        name = emESS_save_target.modify_target;
    }
    else if (btn_id == "btn_emRandModify") {
        table = "EM_Parameter_of_randomResult";
        name = emRand_save_target.modify_target;
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
            url: "back_end/send_newEM_parameterOrType.php",
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
    $("#emParm_table").click(function () {
        $("#emParm_flags").fadeToggle();
    });
});
$(document).ready(function () {
    $("#emESS_table").click(function () {
        $("#emESS_flags").fadeToggle();
    });
});
$(document).ready(function () {
    $("#emRand_table").click(function () {
        $("#emRand_flags").fadeToggle();
    });
});

// Motor type setting  
function emPercent_change() {
    
    document.getElementById('btn_emPercentModify').style.display = "block";
}

function sendNewEMPercent() {
    
    var new_flag = [], id = []
    var new_typeData = {

        table: "EM_motor_type",
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
            url: "back_end/send_newEM_parameterOrType.php",
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