window.onload = function () {

    get_backEnd_data();
}

function get_backEnd_data() {

    $.ajax
        ({
            type: "GET",
            url: "back_end/backup_LHEMS.php",
            contentType: "application/x-www-form-urlencoded",
            processData: true,
            success: function (response) {

                response = JSON.parse(response);
                console.log(response);
                ourData = response;
                LHEMS_flag = ourData.LHEMS_flag;
                compare_timeblock = {
                    
                    page_name: path,
                    local: ourData.local_simulate_timeblock,
                    global: ourData.global_simulate_timeblock
                };
                console.log("compare_timeblock:", compare_timeblock);
                household_num = 0;
                hide_household_button(ourData.database_name == "DHEMS_fiftyHousehold");
                increase_chartHeight('households_loadsSum', ourData.database_name == "DHEMS_fiftyHousehold");
                insertText_after_breadcrumb(response.database_name, null, null, ourData.dr_mode, ourData.dr_info)
                householdsLoadSum(ourData);
                householdsLoadSelect(ourData);
                uncontrollable_loadSum(ourData);
                muti_divs(ourData);
                each_household_status(ourData, 0)
                each_household_status_SOC(ourData, household_num)
                run_household_eachLoad(ourData, 0)
                progessbar(ourData);
                autoRun(ourData, household_num)
                cost_table(ourData.origin_grid_price, ourData.total_origin_grid_price, ourData.real_grid_price, ourData.public_price, ourData.origin_pay_price, ourData.final_pay_price, ourData.saving_efficiency, household_num);
                participate_table(ourData.dr_mode, ourData.dr_info, ourData.dr_participation, ourData.household_CBL, household_num);
            }
        });
}