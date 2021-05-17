function flag_table(flag) {

    var tableData = {

        name: ["旗標名", "狀態"]
    }

    for (let nameNum = 0; nameNum < tableData.name.length; nameNum++) {

        var th = document.createElement('th');
        th.appendChild(document.createTextNode(tableData.name[nameNum]));
        th.setAttribute("style", "text-align: center; color:black");
        document.getElementById('flag_thead').appendChild(th);
    }

    for (let typeNum = 0; typeNum < flag[0].length; typeNum++) {

        var tr = document.createElement('tr');
        tr.setAttribute("style", "text-align: center; color:black; font-size: 20px");

        for (let dataNum = 0; dataNum < flag.length; dataNum++) {

            var td = document.createElement('td');
            switch (dataNum) {
                case 1:

                    var input = document.createElement('input');
                    input.setAttribute("type", "text");
                    input.setAttribute("id", flag[0][typeNum]);
                    input.setAttribute("style", "text-align: center; background-color: #ABFFFF;");
                    input.setAttribute("size", "5");
                    input.setAttribute("value", flag[dataNum][typeNum]);
                    input.setAttribute("onchange", "flag_change()");
                    td.appendChild(input);
                    break;

                default:
                    td.appendChild(document.createTextNode(flag[dataNum][typeNum]));
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

function sendNewFlag(element) {

    var get_Flag = []
    var new_flag = []

    switch (element.name) {
        case "LHEMS":
            get_Flag = LHEMS_flag;
            break;

        case "GHEMS":
            get_Flag = GHEMS_flag;
            break;

        default:
            alert("Wrong button name in Html")
            break;
    }
    var new_flagData = {

        table: element.name + "_flag",
        name: get_Flag[0],
        flag: new_flag
    }
    for (let index = 0; index < get_Flag[0].length; index++) {

        new_flag[index] = document.getElementById(get_Flag[0][index]).value
    }
    console.log(new_flagData)
    $.ajax
        ({
            type: "POST",
            url: "back_end/send_newFlag.php",
            data: { phpReceive: new_flagData },
            // contentType: "application/x-www-form-urlencoded",
            // processData: true,
            success: function (response) {

                response = JSON.parse(response);
                console.log(response)
                if (response.status == "success") {
                    
                    // alert("旗 標 修 改 完 成")
                    // location.reload("/loadFix.html")
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

$(document).ready(function () {
    $("#flag_table").click(function () {
        $("#flags").fadeToggle();
    });
});