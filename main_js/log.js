var data = [];
window.onload = function () {

    get_backEnd_data(logFile = 'shell', default_timer = 1000);
}
function get_backEnd_data(logFile, manual_timer) {

    Swal.fire({
        icon: 'info',
        title: '讀取中 > ' + logFile,
        timerProgressBar: true,
        timer: manual_timer,
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
            $.ajax
                ({
                    type: "POST",
                    url: "back_end/log.php",
                    data: { sendtoPHP: logFile },
                    contentType: "application/x-www-form-urlencoded",
                    processData: true,
                    success: function (response) {

                        data = JSON.parse(response);
                    }
                });
        },
        willClose: () => {
            clearInterval(timerInterval)
        }

    }).then(() => {

        var content = data.log_content;
        var name = data.log_name;
        var path = data.log_path;
        var insert_target = document.getElementById('breadcrumb').getElementsByTagName('li')[1];
        insert_target.textContent = "Log檔 (name: " + name + ")";
        showLogContent(content);
        showLogSelect(data.file_name_array, logFile);
    });
}

function showLogContent(content) {

    $('#log > dt').remove();
    $('#log > dd').remove();
    $('#log > br').remove();
    var log = document.getElementById('log');
    for (let index = 0; index < content.length; index++) {

        if (content[index].includes('\t')) {

            var dd = document.createElement('dd');
            dd.setAttribute("style", "font-size: larger;");
            dd.appendChild(document.createTextNode(content[index]));
            log.appendChild(dd);
        }
        else if (content[index] === "") {

            var br = document.createElement('br');
            log.appendChild(br);
        }
        else {
            var dt = document.createElement('dt');
            dt.setAttribute("style", "font-size: larger;");
            dt.appendChild(document.createTextNode(content[index]));
            log.appendChild(dt);
        }
    }
    log.scrollTop = log.scrollHeight;
}

function showLogSelect(file_name_array, selectedFile) {

    var logSelect = document.getElementsByTagName('select')[0];
    $('select > option').remove();

    for (let index = 0; index < file_name_array.length; index++) {

        var option = document.createElement("option");
        option.setAttribute('style', 'color: black');
        option.value = file_name_array[index];
        option.text = file_name_array[index];
        if (file_name_array[index] == selectedFile)
            option.setAttribute('selected', 'true');

        logSelect.appendChild(option);
    }
}

function logChange() {

    var logSelect = document.getElementsByTagName('select')[0];
    var newFile = logSelect.options[logSelect.selectedIndex].value;
    get_backEnd_data(newFile, 1500);
}
