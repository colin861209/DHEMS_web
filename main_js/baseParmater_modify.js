function flag_table(baseParameter, save_target) {
    
    var tableData = {

        name: ["參數名", "數值"]
    }

    for (let nameNum = 0; nameNum < tableData.name.length; nameNum++) {

        var th = document.createElement('th');
        th.appendChild(document.createTextNode(tableData.name[nameNum]));
        th.setAttribute("style", "text-align: center; color:black");
        document.getElementById('flag_thead').appendChild(th);
    }

    for (let typeNum = 0; typeNum < baseParameter[0].length; typeNum++) {

        var tr = document.createElement('tr');
        tr.setAttribute("style", "text-align: center; color:black; font-size: 20px");
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
                    input.setAttribute("size", "5");
                    input.setAttribute("value", baseParameter[dataNum][typeNum]);
                    input.setAttribute("onchange", "flag_change()");
                    td.appendChild(input);
                    break;

                default:
                    td.appendChild(document.createTextNode(baseParameter[dataNum][typeNum]));
                    break;
            }
            
            tr.appendChild(td);
        }
        document.getElementById('flag_tbody').appendChild(tr);
    }
}

function flag_change() {

    document.getElementById('btn_flagModify').style.display = "block";
}

function sendNewParameter() {

    var new_flag = []
    var new_parameterData = {

        table: "BaseParameter",
        name: save_target.modify_target,
        baseParameter: new_flag
    }
    for (let index = 0; index < save_target.modify_target.length; index++) {

        new_flag[index] = document.getElementById(save_target.modify_target[index]).value
    }
    console.log(new_parameterData)
    $.ajax
        ({
            type: "POST",
            url: "back_end/send_newBaseParameter.php",
            data: { phpReceive: new_parameterData },
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
    $("#flag_table").click(function () {
        $("#flags").fadeToggle();
    });
});