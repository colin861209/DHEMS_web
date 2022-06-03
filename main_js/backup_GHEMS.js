window.onload = function () {

    get_backEnd_data();
}

function get_backEnd_data() {

    $.ajax
        ({
            type: "GET",
            url: "back_end/backup_GHEMS.php",
            contentType: "application/x-www-form-urlencoded",
            processData: true,
            success: function (response) {

                response = JSON.parse(response);
                ourData = response;
                GHEMS_flag = ourData.GHEMS_flag;
                console.log(ourData);
                compare_timeblock = {
                    
                    page_name: path,
                    local: ourData.local_simulate_timeblock,
                    global: ourData.global_simulate_timeblock
                };
                console.log("compare_timeblock:", compare_timeblock);
                insertText_after_breadcrumb(response.database_name, null, null, ourData.dr_mode, ourData.dr_info);
                tableInfo(ourData);
                progessbar(ourData);
                priceVsLoad(ourData);
                SOCVsLoad(ourData)
                loadModel(ourData)
                EMchargingSOC(ourData.EM_start_departure_SOC)
                EVchargingSOC(ourData.EV_start_departure_SOC)
            }
        });
}