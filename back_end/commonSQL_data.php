<?php
require 'fetch_mysql.php';

class BP extends SQLQuery {
    
    // BaseParemeter Table
    private $table_BP = '';
    private $col_value = 'value';
    private $col_parmName = 'parameter_name';
    private $table_EM_BP = '';
    private $table_EV_BP = '';

    /**
     * ------ BP Construct ------
     * Price
     * Solar
     * Time Block
     * Grid Limit
     * DR Mode
     * Chart Flag & Info
     * Function: getChartInfo
     * --------------------------
     */

    // String Price & Weather
    private $str_target_price;
    private $str_target_solar;
    public $electric_price = array();
    public $simulate_solar = array();
    // Time
    public $local_simulate_timeblock;
    public $global_simulate_timeblock;
    public $time_block;
    // HEMS & CEMS Grid Power Limit
    public $limit_capability;
    public $community_limit_capability;
    // Demand Response
    public $dr_mode;
    // HEMS UC Flag
    public $uncontrollable_load_flag;
    // Chart Flag & Related Info
    public $chart_upperLowerLimit_flag;
    public $electric_price_upper_limit = null;
    public $weather_upper_limit = null;
    public $ev_chargingUser_nums_upper_limit = null;
    public $em_n_chargingUser_nums_upper_limit = null;
    public $load_model_upper_limit = null;
    public $load_model_lower_limit = null;
    public $load_model_seperate_upper_limit = null;
    public $load_model_seperate_lower_limit = null;
    public $householdsLoadSum_upper_limit = null;
    public $each_household_status_upper_limit = null;
    function __construct($table_BP) {
        
        parent::__construct();
        $this->table_BP = $table_BP;
        $this->str_target_price = $this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'simulate_price' ", $this->oneValue);
        $this->str_target_solar = $this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'simulate_weather' ", $this->oneValue);
        $this->electric_price = array_map('floatval', $this->sqlFetchAssoc("SELECT `" .$this->str_target_price. "` FROM `price` ", array($this->str_target_price)));
        $this->simulate_solar = array_map('floatval', $this->sqlFetchAssoc("SELECT `" .$this->str_target_solar. "` FROM `solar_data` ", array($this->str_target_solar)));
        $this->local_simulate_timeblock = intval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'next_simulate_timeblock' ", $this->oneValue));
        $this->global_simulate_timeblock = intval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'Global_next_simulate_timeblock' ", $this->oneValue));
        $this->time_block = intval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'time_block' ", $this->oneValue));        
        $this->limit_capability = array_fill(0, $this->time_block, floatval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'Pgridmax' ", $this->oneValue)));
        $this->community_limit_capability = array_fill(0, $this->time_block, floatval($this->sqlFetchRow("SELECT `". $this->col_value ."`*(SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'householdAmount') FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'Pgridmax' ", $this->oneValue)));        
        $this->dr_mode = intval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'dr_mode' ", $this->oneValue));        
        $this->uncontrollable_load_flag = boolval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'uncontrollable_load_flag' ", $this->oneValue));
        $this->chart_upperLowerLimit_flag = boolval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'chart_upperLowerLimit_flag' ", $this->oneValue));
        $this->getChartInfo();
    }
    private function getChartInfo() {

        if ($this->chart_upperLowerLimit_flag) {
            $this->electric_price_upper_limit = floatval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'electric_price_upper_limit' ", $this->oneValue));
            $this->weather_upper_limit = floatval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'weather_upper_limit' ", $this->oneValue));
            $this->ev_chargingUser_nums_upper_limit = floatval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'ev_chargingUser_nums_upper_limit' ", $this->oneValue));
            $this->em_n_chargingUser_nums_upper_limit = floatval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'em_n_chargingUser_nums_upper_limit' ", $this->oneValue));
            $this->load_model_upper_limit = floatval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'load_model_upper_limit' ", $this->oneValue));
            $this->load_model_lower_limit = floatval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'load_model_lower_limit' ", $this->oneValue));
            $this->load_model_seperate_upper_limit = floatval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'load_model_seperate_upper_limit' ", $this->oneValue));
            $this->load_model_seperate_lower_limit = floatval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'load_model_seperate_lower_limit' ", $this->oneValue));
            $this->householdsLoadSum_upper_limit = floatval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'householdsLoadSum_upper_limit' ", $this->oneValue));
            $this->each_household_status_upper_limit = floatval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'each_household_status_upper_limit' ", $this->oneValue));
        }
    }

    /**
     * ------  Only BP which HEMS need ------
     * Flag: Comfort Level
     * All Controllable Loads Count
     * All Household Count
     * Simulate TimeBlock <=> local_simulate_timeblock
     * Function: getHEMS_BP
     * --------------------------------------
     */

    public $simulate_timeblock;
    public $comfortLevel_flag;
    public $app_counts;
    public $household_num;
    public function getHEMS_BP() {
        
        $this->comfortLevel_flag = boolval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'comfortLevel_flag' ", $this->oneValue));
        $this->app_counts = $this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'app_counts' ", $this->oneValue);
        $this->household_num = intval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'householdAmount' ", $this->oneValue));
        $this->simulate_timeblock = intval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'next_simulate_timeblock' ", $this->oneValue));
    }

    /**
     * ------  Only BP which CEMS need ------
     * Flag:
     *  UCLoad
     *  EM
     *  EV
     *  EM Discharge
     *  EV Discharge 
     * Total Load Power, Public Load Power (kWh)
     * Calculate Cost by
     *  PublicLoad*ThreeLevel (NTD)
     *  Loads*Taipower (NTD)
     *  Loads*ThreeLevel (NTD)
     *  Grid*ThreeLevel (NTD)
     *  Sell*ThreeLevel (NTD)
     *  FuelCell*ThreeLevel (NTD)
     *  DR Feedback (NTD)
     *  Hydrogen (g)
     * Function: getCEMS_BP
     * --------------------------------------
     */

    public $Global_uncontrollable_load_flag;
    public $EM_flag;
    public $EM_discharge_flag;
    public $EV_flag;
    public $EV_discharge_flag;
    public $total_load_power_sum;
    public $total_publicLoad_power;
    public $total_publicLoad_cost;
    public $taipower_loads_cost;
    public $three_level_loads_cost;
    public $real_buy_grid_cost;
    public $max_sell_price;
    public $min_FC_cost;
    public $consumption;
    public $dr_feedbackPrice;
    public function getCEMS_BP(string $table_EM_BP, string $table_EV_BP) {

        $this->table_EM_BP = $table_EM_BP;
        $this->table_EV_BP = $table_EV_BP;
        $this->Global_uncontrollable_load_flag = boolval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'Global_uncontrollable_load_flag' ", $this->oneValue));
        $this->EM_flag = boolval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'ElectricMotor' ", $this->oneValue));
        $this->EM_discharge_flag = boolval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_EM_BP ."` WHERE `". $this->col_parmName ."` = 'Motor_can_discharge' ", $this->oneValue));
        $this->EV_flag = boolval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'ElectricVehicle' ", $this->oneValue));
        $this->EV_discharge_flag = boolval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_EV_BP ."` WHERE `". $this->col_parmName ."` = 'Vehicle_can_discharge' ", $this->oneValue));
        $this->total_load_power_sum = round($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'totalLoad' ", $this->oneValue), 2);
        $this->total_publicLoad_power = round($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'publicLoad' ", $this->oneValue), 2);
        $this->total_publicLoad_cost = round($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'publicLoadSpend(threeLevelPrice)' ", $this->oneValue), 2);
        $this->taipower_loads_cost = round($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'LoadSpend(taipowerPrice)' ", $this->oneValue), 2);
        $this->three_level_loads_cost = round($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'LoadSpend(threeLevelPrice)' ", $this->oneValue), 2);
        $this->real_buy_grid_cost = round($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'realGridPurchase' ", $this->oneValue), 2);
        $this->max_sell_price = round($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'maximumSell' ", $this->oneValue), 2);
        $this->min_FC_cost = round($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` ='fuelCellSpend' ", $this->oneValue), 2);
        $this->consumption = round($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'hydrogenConsumption(g)' ", $this->oneValue), 2);
        $this->dr_feedbackPrice = round($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'demandResponse_feedbackPrice' ", $this->oneValue), 2);
        $this->simulate_timeblock = intval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'Global_next_simulate_timeblock' ", $this->oneValue));
    }
}   

class CommonData extends BP {
    
    // Fix Table
    private $table_DR = 'demand_response';
    private $table_LoadSelect = 'load_list_select';
    private $table_Load = 'load_list';
    private $table_DRParticipate = 'LHEMS_demand_response_participation';
    private $table_DRCBL = 'LHEMS_demand_response_CBL';
    private $table_ComLv = 'LHEMS_comfort_level';
    // Control Status Table
    private $table_CS = '';
    // HEMS Table
    private $table_LHEMSCost = '';
    private $table_LHEMS_UCLoad = '';
    // CEMS Table
    private $table_GHEMS_UCLoad = '';
    private $table_HouseholdTotalLoad = '';
    // Common
    private $str_searchEss = 'Pess';
    private $str_searchSOC = 'ESS_SOC';
    public $arr_GridPower = array();
    public $arr_EssPower = array();
    public $arr_EssSOC = array();

    /**
     * ------ CommonData Construct ------
     * DR Count
     * DR Info
     * GHEMS Flag Setting
     * LHEMS Flag Setting
     * ----------------------------------
     */

    public $dr_count;
    public $dr_info;
    public $GHEMS_flag;
    public $LHEMS_flag;
    function __construct($table_BP, $table_CS = null, $table_LHEMS_UCLoad = null, $table_LHEMSCost = null, $table_GHEMS_UCLoad = null, $table_TotalLoad = null) {
        
        parent::__construct($table_BP);
        $this->table_CS = $table_CS;
        $this->table_LHEMSCost = $table_LHEMSCost;
        $this->table_LHEMS_UCLoad = $table_LHEMS_UCLoad;
        $this->table_GHEMS_UCLoad = $table_GHEMS_UCLoad;
        $this->table_HouseholdTotalLoad = $table_TotalLoad;
        $this->dr_count = $this->sqlFetchRow("SELECT COUNT(*) FROM `". $this->table_DR ."` ", $this->oneValue);
        if ($this->dr_mode != 0) { $this->dr_info = $this->sqlFetchRow("SELECT * FROM `". $this->table_DR ."` WHERE mode =" .$this->dr_mode , $this->aRow); }
        $this->GHEMS_flag = $this->sqlFetchAssoc("SELECT `variable_name`, `variable_define`, `flag` FROM `GHEMS_flag` WHERE `flag` IS NOT NULL", array("variable_name", "variable_define", "flag"));
        $this->LHEMS_flag = $this->sqlFetchAssoc("SELECT `variable_name`, `variable_define`, `flag` FROM `LHEMS_flag` WHERE `flag` IS NOT NULL", array("variable_name", "variable_define", "flag"));
    }
    
    /**
     * ------ class HEMS Common Function ------ 
     * getHEMS_LoadListSelect
     * getHEMS_PowerOfLoadGridEss
     * getHEMS_UCLoad
     * getHEMS_LoadListArray
     * getHEMS_DRParticipateAndCBL
     * getHEMS_ComLvTime
     * getHEMS_Cost
     * ---------------------------------------- 
     */

    // load list select
    public $arr_LoadSelect_with_IRL = array();
    public $arr_LoadSelect_with_UIRL = array();
    public $arr_LoadSelect_with_VARL = array();
    public $arr_LoadSelect = array();
    public $arr_LoadSelectCount = array();
    public function getHEMS_LoadListSelect() {
        
        for ($i=0; $i < $this->household_num; $i++) { 
            array_push($this->arr_LoadSelect_with_IRL, array_map('intval', $this->sqlFetchAssoc("SELECT `number` FROM `". $this->table_LoadSelect ."` WHERE group_id = 1 AND household".($i+1)." = 1", array("number"))));
            array_push($this->arr_LoadSelect_with_UIRL, array_map('intval', $this->sqlFetchAssoc("SELECT `number` FROM `". $this->table_LoadSelect ."` WHERE group_id = 2 AND household".($i+1)." = 1", array("number"))));
            array_push($this->arr_LoadSelect_with_VARL, array_map('intval', $this->sqlFetchAssoc("SELECT `number` FROM `". $this->table_LoadSelect ."` WHERE group_id = 3 AND household".($i+1)." = 1", array("number"))));
            array_push($this->arr_LoadSelect, array_map('intval', $this->sqlFetchAssoc("SELECT `number` FROM `". $this->table_LoadSelect ."` WHERE household".($i+1)." = 1", array("number"))));
        }
        foreach ($this->arr_LoadSelect_with_IRL as $inner_array) {
            if (count($inner_array) == null) { $count_LoadSelect_tmp[] = 0; }
            else { $count_interrrupt_tmp[] = count($inner_array); }
        }
        foreach ($this->arr_LoadSelect_with_UIRL as $inner_array) {
            if (count($inner_array) == null) { $count_uninterrupt_tmp[] = 0; }
            else { $count_uninterrupt_tmp[] = count($inner_array); }
        }
        foreach ($this->arr_LoadSelect_with_VARL as $inner_array) {
            if (count($inner_array) == null) { $count_varying_tmp[] = 0; }
            else { $count_varying_tmp[] = count($inner_array); }
        }
        array_push($this->arr_LoadSelectCount, $count_interrrupt_tmp);
        array_push($this->arr_LoadSelectCount, $count_uninterrupt_tmp);
        array_push($this->arr_LoadSelectCount, $count_varying_tmp);
    }
    // power load, grid, Ess
    private $load_power_tmp = array();
    public $arr_LoadPower = array();
    public $arr_LoadPowerSum = array();
    // Must call function 'getHEMS_LoadListSelect' first
    public function getHEMS_PowerOfLoadGridEss() {

        for ($i=0; $i < $this->household_num; $i++) { 
    
            $interrupt_status = $this->sqlFetchRow("SELECT * FROM `". $this->table_CS ."` WHERE (equip_name LIKE '%interrupt%' OR equip_name LIKE 'varyingPsi%') AND household_id =" .($i+1). " ORDER BY `household_id`, `control_id` ASC ", $this->controlStatusResult);
            array_push($this->load_power_tmp, $interrupt_status);
            
            $grid_power_tmp = $this->sqlFetchRow("SELECT * FROM `". $this->table_CS ."` WHERE equip_name = 'Pgrid' AND household_id =" .($i+1). " ORDER BY `household_id`, `control_id` ASC ", $this->aRow);
            array_splice($grid_power_tmp, 0, 1);
            array_splice($grid_power_tmp, 96, count($grid_power_tmp)-1);
            array_push($this->arr_GridPower, array_map('floatval', $grid_power_tmp));

            if (boolval($this->LHEMS_flag[2][array_search($this->str_searchEss, $this->LHEMS_flag[0], true)])) {

                $battery_power_tmp = $this->sqlFetchRow("SELECT * FROM `". $this->table_CS ."` WHERE equip_name = '". $this->str_searchEss ."' AND household_id =" .($i+1). " ORDER BY `household_id`, `control_id` ASC ", $this->aRow);
                array_splice($battery_power_tmp, 0, 1);
                array_splice($battery_power_tmp, 96, count($battery_power_tmp)-1);
                array_push($this->arr_EssPower, array_map('floatval', $battery_power_tmp));
                
                $SOC_tmp = $this->sqlFetchRow("SELECT * FROM `". $this->table_CS ."` WHERE equip_name = '". $this->str_searchSOC ."' AND household_id =" .($i+1). " ORDER BY `household_id`, `control_id` ASC ", $this->aRow);
                array_splice($SOC_tmp, 0, 1);
                array_splice($SOC_tmp, 96, count($SOC_tmp)-1);
                array_push($this->arr_EssSOC, array_map('floatval', $SOC_tmp));
            }
        }
        for ($i = 0; $i < $this->household_num; $i++) {

            for ($y = 0; $y < $this->time_block; $y++) {
                
                for ($u = 0; $u < count($this->arr_LoadSelect_with_IRL[$i]); $u++) {

                    $this->arr_LoadPower[$i][$u][$y] = $this->load_power_tmp[$i][$u][$y] * $this->power1[$this->arr_LoadSelect_with_IRL[$i][$u]-1];
                }
                for ($u = 0; $u < count($this->arr_LoadSelect_with_UIRL[$i]); $u++) {

                    $this->arr_LoadPower[$i][$u + count($this->arr_LoadSelect_with_IRL[$i])][$y] = $this->load_power_tmp[$i][$u + count($this->arr_LoadSelect_with_IRL[$i])][$y] * $this->power1[$this->arr_LoadSelect_with_UIRL[$i][$u]-1];
                }
                for ($u = 0; $u < count($this->arr_LoadSelect_with_VARL[$i]); $u++) {

                    $this->arr_LoadPower[$i][$u + count($this->arr_LoadSelect_with_IRL[$i]) + count($this->arr_LoadSelect_with_UIRL[$i])][$y] = $this->load_power_tmp[$i][$u + count($this->arr_LoadSelect_with_IRL[$i]) + count($this->arr_LoadSelect_with_UIRL[$i])][$y];
                }

                for ($u=0; $u < count($this->arr_LoadSelect); $u++) { 
                    
                    $this->arr_LoadPowerSum[$i][$y] += $this->arr_LoadPower[$i][$u][$y];
                }
            }
        }
    }
    // uc load
    public $arr_UCLoad;
    public function getHEMS_UCLoad() {

        if ($this->database_name == "DHEMS_fiftyHousehold") {
            $this->arr_UCLoad = $this->sqlFetchAssoc("SELECT 
            `household1`, `household2`, `household3`, `household4`, `household5`, `household6`, `household7`, `household8`, `household9`, `household10`,
            `household11`, `household12`, `household13`, `household14`, `household15`, `household16`, `household17`, `household18`, `household19`, `household20`,
            `household21`, `household22`, `household23`, `household24`, `household25`, `household26`, `household27`, `household28`, `household29`, `household30`,
            `household31`, `household32`, `household33`, `household34`, `household35`, `household36`, `household37`, `household38`, `household39`, `household40`,
            `household41`, `household42`, `household43`, `household44`, `household45`, `household46`, `household47`, `household48`, `household49`, `household50` 
            FROM `". $this->table_LHEMS_UCLoad ."`", array(
                "household1", "household2", "household3", "household4", "household5", "household6", "household7", "household8", "household9", "household10", 
                "household11", "household12", "household13", "household14", "household15", "household16", "household17", "household18", "household19", "household20", 
                "household21", "household22", "household23", "household24", "household25", "household26", "household27", "household28", "household29", "household30", 
                "household31", "household32", "household33", "household34", "household35", "household36", "household37", "household38", "household39", "household40", 
                "household41", "household42", "household43", "household44", "household45", "household46", "household47", "household48", "household49", "household50" 
            ));
        }
        else {
            $this->arr_UCLoad = $this->sqlFetchAssoc("SELECT 
            `household1`, `household2`, `household3`, `household4`, `household5` FROM `". $this->table_LHEMS_UCLoad ."`", array(
                "household1", "household2", "household3", "household4", "household5"));
        }
        for ($i = 0; $i < count($this->arr_UCLoad); $i++) {

            $this->arr_UCLoad[$i] = array_map('floatval', $this->arr_UCLoad[$i]);
        }
    }
    // load list array
    private $arr_loadList = array();
    public $start = array();
    public $end = array();
    public $operation = array();
    public $power1 = array();
    public $power2 = array();
    public $power3 = array();
    public $number = array();
    public $equip_name = array();
    public function getHEMS_LoadListArray() {

        if ($this->database_name == "DHEMS_fiftyHousehold") {
            $this->arr_loadList = $this->sqlFetchAssoc("SELECT 
            `household1_startEndOperationTime`, `household2_startEndOperationTime`, `household3_startEndOperationTime`, `household4_startEndOperationTime`, `household5_startEndOperationTime`, `household6_startEndOperationTime`, `household7_startEndOperationTime`, `household8_startEndOperationTime`, `household9_startEndOperationTime`, `household10_startEndOperationTime`,
            `household11_startEndOperationTime`, `household12_startEndOperationTime`, `household13_startEndOperationTime`, `household14_startEndOperationTime`, `household15_startEndOperationTime`, `household16_startEndOperationTime`, `household17_startEndOperationTime`, `household18_startEndOperationTime`, `household19_startEndOperationTime`, `household20_startEndOperationTime`, 
            `household21_startEndOperationTime`, `household22_startEndOperationTime`, `household23_startEndOperationTime`, `household24_startEndOperationTime`, `household25_startEndOperationTime`, `household26_startEndOperationTime`, `household27_startEndOperationTime`, `household28_startEndOperationTime`, `household29_startEndOperationTime`, `household30_startEndOperationTime`, 
            `household31_startEndOperationTime`, `household32_startEndOperationTime`, `household33_startEndOperationTime`, `household34_startEndOperationTime`, `household35_startEndOperationTime`, `household36_startEndOperationTime`, `household37_startEndOperationTime`, `household38_startEndOperationTime`, `household39_startEndOperationTime`, `household40_startEndOperationTime`, 
            `household41_startEndOperationTime`, `household42_startEndOperationTime`, `household43_startEndOperationTime`, `household44_startEndOperationTime`, `household45_startEndOperationTime`, `household46_startEndOperationTime`, `household47_startEndOperationTime`, `household48_startEndOperationTime`, `household49_startEndOperationTime`, `household50_startEndOperationTime`, 
            `power1`, `power2`, `power3`, `number`, `equip_name` 
            FROM `". $this->table_Load ."`", array(
                "household1_startEndOperationTime", "household2_startEndOperationTime", "household3_startEndOperationTime", "household4_startEndOperationTime", "household5_startEndOperationTime", "household6_startEndOperationTime", "household7_startEndOperationTime", "household8_startEndOperationTime", "household9_startEndOperationTime", "household10_startEndOperationTime",
                "household11_startEndOperationTime", "household12_startEndOperationTime", "household13_startEndOperationTime", "household14_startEndOperationTime", "household15_startEndOperationTime", "household16_startEndOperationTime", "household17_startEndOperationTime",  "household18_startEndOperationTime", "household19_startEndOperationTime", "household20_startEndOperationTime",
                "household21_startEndOperationTime", "household22_startEndOperationTime", "household23_startEndOperationTime", "household24_startEndOperationTime", "household25_startEndOperationTime", "household26_startEndOperationTime", "household27_startEndOperationTime", "household28_startEndOperationTime", "household29_startEndOperationTime", "household30_startEndOperationTime",
                "household31_startEndOperationTime", "household32_startEndOperationTime", "household33_startEndOperationTime", "household34_startEndOperationTime", "household35_startEndOperationTime", "household36_startEndOperationTime", "household37_startEndOperationTime", "household38_startEndOperationTime", "household39_startEndOperationTime", "household40_startEndOperationTime",
                "household41_startEndOperationTime", "household42_startEndOperationTime", "household43_startEndOperationTime", "household44_startEndOperationTime", "household45_startEndOperationTime", "household46_startEndOperationTime", "household47_startEndOperationTime", "household48_startEndOperationTime", "household49_startEndOperationTime", "household50_startEndOperationTime", 
                "power1", "power2", "power3", "number", "equip_name"
            ));
        }
        else {
            $this->arr_loadList = $this->sqlFetchAssoc("SELECT 
            household1_startEndOperationTime, household2_startEndOperationTime, household3_startEndOperationTime, household4_startEndOperationTime, household5_startEndOperationTime, 
            power1, power2, power3, number, equip_name 
            FROM `". $this->table_Load ."`", array(
                "household1_startEndOperationTime", "household2_startEndOperationTime", "household3_startEndOperationTime", "household4_startEndOperationTime", "household5_startEndOperationTime", 
                "power1", "power2", "power3", "number", "equip_name"));
        }
        for ($i = 0; $i < $this->app_counts; $i++) {

            for ($j = 0; $j < $this->household_num; $j++) {

                list($start_tmp, $end_tmp, $operation_tmp) = explode("~", $this->arr_loadList[$j][$i]);
                $this->start[$j][$i] = intval($start_tmp);
                $this->end[$j][$i] = intval($end_tmp);
                $this->operation[$j][$i] = intval($operation_tmp);
            }
            $this->power1[$i] = floatval($this->arr_loadList[$this->household_num][$i]);
            $this->power2[$i] = floatval($this->arr_loadList[$this->household_num + 1][$i]);
            $this->power3[$i] = floatval($this->arr_loadList[$this->household_num + 2][$i]);
            $this->number[$i] = $this->arr_loadList[$this->household_num + 3][$i];
            $this->equip_name[$i] = $this->arr_loadList[$this->household_num + 4][$i];
        }
    }
    // HEMS DR
    public $arr_HouseholdParticipation = array();
    public $arr_HouseholdCBLMAX = array();
    public $arr_HouseholdCBL = array();
    public function getHEMS_DRParticipateAndCBL() {
    
        if ($this->dr_mode != 0) {
            
            $this->arr_HouseholdParticipation = $this->sqlFetchRow("SELECT * FROM `". $this->table_DRParticipate ."`; ", $this->controlStatusResult);
            $this->arr_HouseholdCBLMAX = array_map('floatval', $this->sqlFetchRow("SELECT 
                MAX(household1), MAX(household2), MAX(household3), MAX(household4), MAX(household5), MAX(household6), MAX(household7), MAX(household8), MAX(household9), MAX(household10), 
                MAX(household11), MAX(household12), MAX(household13), MAX(household14), MAX(household15), MAX(household16), MAX(household17), MAX(household18), MAX(household19), MAX(household20), 
                MAX(household21), MAX(household22), MAX(household23), MAX(household24), MAX(household25), MAX(household26), MAX(household27), MAX(household28), MAX(household29), MAX(household30), 
                MAX(household31), MAX(household32), MAX(household33), MAX(household34), MAX(household35), MAX(household36), MAX(household37), MAX(household38), MAX(household39), MAX(household40), 
                MAX(household41), MAX(household42), MAX(household43), MAX(household44), MAX(household45), MAX(household46), MAX(household47), MAX(household48), MAX(household49), MAX(household50) 
                FROM `". $this->table_DRCBL ."` WHERE `time_block` BETWEEN ".$this->dr_info[1]." AND ".($this->dr_info[2]-1)." AND `comfort_level_flag` = ".intval($this->comfortLevel_flag), $this->aRow));

            for ($i=0; $i < count($this->arr_HouseholdCBLMAX); $i++) { 
                $tmp = $this->limit_capability;
                for ($j=$this->dr_info[1]; $j < $this->dr_info[2]; $j++) { 
                    if ($this->arr_HouseholdParticipation[$i][$j] != 0) {
                        $tmp[$j] = round($this->arr_HouseholdCBLMAX[$i] * ceil($this->arr_HouseholdParticipation[$i][$j]), 2);
                    }
                }
                array_push($this->arr_HouseholdCBL, $tmp);
                empty($tmp);
            }
        }
    }
    // comfort level
    public $arr_HouseholdComLvStart = array();
    public $arr_HouseholdComLvEnd = array();
    public function getHEMS_ComLvTime(int $diff_level_num = 4, int $same_level_num = 3) {
        
        if ($this->comfortLevel_flag) {
    
            $comfortLevel_array = $this->sqlFetchAssoc("SELECT 
            level1_startEndTime1, level1_startEndTime2, level1_startEndTime3, 
            level2_startEndTime1, level2_startEndTime2, level2_startEndTime3, 
            level3_startEndTime1, level3_startEndTime2, level3_startEndTime3, 
            level4_startEndTime1, level4_startEndTime2, level4_startEndTime3 FROM `". $this->table_ComLv ."` ", 
            array("level1_startEndTime1", "level1_startEndTime2", "level1_startEndTime3", 
            "level2_startEndTime1", "level2_startEndTime2", "level2_startEndTime3", 
            "level3_startEndTime1", "level3_startEndTime2", "level3_startEndTime3", 
            "level4_startEndTime1", "level4_startEndTime2", "level4_startEndTime3"));
            
            for ($i=0; $i < $this->household_num; $i++) { 
                
                for ($j=0; $j < $diff_level_num; $j++) { 
                    
                    for ($k=0; $k < $same_level_num; $k++) { 
                        
                        $comfortStart = array(); $comfortEnd = array();
                        for ($z=0; $z < $this->app_counts; $z++) { 
                            
                            if ($comfortLevel_array[$j * $same_level_num + $k][$i * $this->app_counts + $z] != null) {
                                
                                list($start_tmp, $end_tmp) = explode("~", $comfortLevel_array[$j * $same_level_num + $k][$i * $this->app_counts + $z]);
                                array_push($comfortStart, intval($start_tmp));
                                array_push($comfortEnd, intval($end_tmp));
                            }
                            else {
                                
                                array_push($comfortStart, null);
                                array_push($comfortEnd, null);
                            }
                        }
                        $this->arr_HouseholdComLvStart[$i][$j][$k] = $comfortStart;
                        $this->arr_HouseholdComLvEnd[$i][$j][$k] = $comfortEnd;
                    }
                }
            }
        }
    }
    // cost table info
    public $origin_grid_price = array();
    public $real_grid_price = array();
    public $public_price = array();
    public $origin_pay_price = array();
    public $final_pay_price = array();
    public $saving_efficiency = array();
    public $total_origin_grid_price;
    public function getHEMS_Cost() {
        
        $this->origin_grid_price = array_map('floatval', $this->sqlFetchAssoc("SELECT `origin_grid_price` FROM `". $this->table_LHEMSCost ."` ORDER BY `household_id`", array("origin_grid_price")));
        $this->real_grid_price = array_map('floatval', $this->sqlFetchAssoc("SELECT `real_grid_price` FROM `". $this->table_LHEMSCost ."` ORDER BY `household_id`", array("real_grid_price")));
        $this->public_price = array_map('floatval', $this->sqlFetchAssoc("SELECT `public_price` FROM `". $this->table_LHEMSCost ."` ORDER BY `household_id`", array("public_price")));
        $this->origin_pay_price = array_map('floatval', $this->sqlFetchAssoc("SELECT `origin_pay_price` FROM `". $this->table_LHEMSCost ."` ORDER BY `household_id`", array("origin_pay_price")));
        $this->final_pay_price = array_map('floatval', $this->sqlFetchAssoc("SELECT `final_pay_price` FROM `". $this->table_LHEMSCost ."` ORDER BY `household_id`", array("final_pay_price")));
        $this->saving_efficiency = array_map('floatval', $this->sqlFetchAssoc("SELECT `saving_efficiency` FROM `". $this->table_LHEMSCost ."` ORDER BY `household_id`", array("saving_efficiency")));
        $this->total_origin_grid_price = round(floatval($this->sqlFetchRow("SELECT SUM(origin_grid_price) FROM `". $this->table_LHEMSCost ."`", $this->oneValue)), 3);
    }

    /**
     * ------ class CEMS Common Function ------ 
     * getCEMS_LoadModel
     *  -> Contain function
     *  getEachPowerSupplyArray
     *  calculateCEMSLoadModel
     * getCEMS_EMInfo
     * getCEMS_EVInfo
     * getCEMS_DRCBL
     * ----------------------------------------
     */

    // HEMS loads + CEMS Loads (Controllable & Uncontrollable)
    private $HEMS_TotalLoad;
    private $HEMS_UCLoad;
    private $Global_uncontrollable_load_array;
    private $str_PLStop = 'stoppable_publicLoad';
    private $str_PLDefferal = 'deferrable_publicLoad';
    private $PL_StopPower;
    private $PL_DeferralPower;
    // Load Model
    private $str_searchPL = 'publicLoad';
    private $str_searchFC = 'Pfc';
    private $str_searchGrid = 'Pgrid';
    private $str_searchSell = 'Psell';
    private $variable_name;
    private $arr_CS;
    public $arr_FCPower = array();
    public $arr_SellPower = array();
    public $load_model_seperate = array();
    public $load_model = array();
    public function getCEMS_LoadModel() {

        $this->HEMS_TotalLoad = array_map('floatval', $this->sqlFetchAssoc("SELECT `totalLoad` FROM `". $this->table_HouseholdTotalLoad ."` ", array("totalLoad")));
        $this->HEMS_UCLoad = array_map('floatval', $this->sqlFetchAssoc("SELECT `totalLoad` FROM `". $this->table_LHEMS_UCLoad ."` ", array("totalLoad")));
        $this->Global_uncontrollable_load_array = $this->sqlFetchAssoc("SELECT `uncontrollable_load1`, `uncontrollable_load2`, `uncontrollable_load3` FROM `". $this->table_GHEMS_UCLoad ."` ", array("uncontrollable_load1", "uncontrollable_load2", "uncontrollable_load3"));
        $this->PL_StopPower = $this->sqlFetchAssoc("SELECT `power1` FROM `". $this->table_Load ."` WHERE group_id = 5", array("power1"));
        $this->PL_DeferralPower = $this->sqlFetchAssoc("SELECT `power1` FROM `". $this->table_Load ."` WHERE group_id = 6", array("power1"));
        $this->variable_name = $this->sqlFetchAssoc("SELECT `equip_name` FROM `". $this->table_CS ."` ", array("equip_name"));
        $this->arr_CS = $this->sqlFetchRow("SELECT * FROM `". $this->table_CS ."` ", $this->controlStatusResult);
        $this->getEachPowerSupplyArray();
        $this->calculateCEMSLoadModel();
    }
    private function getEachPowerSupplyArray() {

        $this->arr_FCPower = $this->arr_CS[array_search($this->str_searchFC, $this->variable_name, true)];
        $this->arr_GridPower = $this->arr_CS[array_search($this->str_searchGrid, $this->variable_name, true)];
        $this->arr_EssPower = $this->arr_CS[array_search($this->str_searchEss, $this->variable_name, true)];
        $this->arr_EssSOC = $this->arr_CS[array_search($this->str_searchSOC, $this->variable_name, true)];
        $this->arr_SellPower = $this->multiply($this->arr_CS[array_search($this->str_searchSell, $this->variable_name, true)], -1);
    }
    private function calculateCEMSLoadModel() {
        
        // HEMS controllable load
        $this->load_model = $this->HEMS_TotalLoad;
        array_push($this->load_model_seperate, $this->load_model);
        // HEMS uncontrollable load
        if ($this->uncontrollable_load_flag) {
            array_push($this->load_model_seperate, $this->HEMS_UCLoad);
            $this->load_model = array_map(function() { return array_sum(func_get_args()); }, $this->load_model, $this->HEMS_UCLoad);
        }
        if ($this->database_name == 'DHEMS_fiftyHousehold') {
            $publicLoad = array();
            $publicLoad_total = array();
            // CEMS public load
            if ($this->GHEMS_flag[2][array_search($this->str_searchPL, $this->GHEMS_flag[0], true)]) {
                for ($i=0; $i < count($this->PL_StopPower); $i++) { 
                    
                    $name = $this->str_PLStop.($i+1);
                    $publicLoad[$i] = $this->arr_CS[array_search($name, $this->variable_name, true)];
                    for ($y = 0; $y < $this->time_block; $y++) {
                        $publicLoad[$i][$y] *= $this->PL_StopPower[$i];
                        $publicLoad_total[$y] += $publicLoad[$i][$y];
                    }
                    array_push($this->load_model_seperate, $publicLoad[$i]);
                }
                for ($i=0; $i < count($this->PL_DeferralPower); $i++) { 
                    
                    $name = $this->str_PLDefferal.($i+1);
                    $publicLoad[$i] = $this->arr_CS[array_search($name, $this->variable_name, true)];
                    for ($y = 0; $y < $this->time_block; $y++) {
                        $publicLoad[$i][$y] *= $this->PL_DeferralPower[$i];
                        $publicLoad_total[$y] += $publicLoad[$i][$y];
                    }
                    array_push($this->load_model_seperate, $publicLoad[$i]);
                }
                $this->load_model = array_map(function() { return array_sum(func_get_args()); }, $this->load_model, $publicLoad_total);
            }
            // CEMS uncontrollable load
            if ($this->Global_uncontrollable_load_flag) {

                for ($i=0; $i < count($this->Global_uncontrollable_load_array); $i++) { 
                    
                    $Global_uncontrollable_load = array_map('floatval', $this->Global_uncontrollable_load_array[$i]);
                    array_push($this->load_model_seperate, $Global_uncontrollable_load);
                    $this->load_model = array_map(function() { return array_sum(func_get_args()); }, $this->load_model, $Global_uncontrollable_load);
                }
            }
            // CEMS EM charge and discharge power
            if ($this->EM_flag) {
                array_push($this->load_model_seperate, $this->EM_total_power);
                $this->load_model = array_map(function() { return array_sum(func_get_args()); }, $this->load_model, $this->EM_total_power);
                if ($this->EM_discharge_flag) {
                    array_push($this->load_model_seperate, $this->EM_discharge_power);
                    $this->load_model = array_map(function() { return array_sum(func_get_args()); }, $this->load_model, $this->EM_discharge_power);
                }
            }
            // CEMS EV charge and discharge power
            if ($this->EV_flag) {
                array_push($this->load_model_seperate, $this->EV_total_power);
                $this->load_model = array_map(function() { return array_sum(func_get_args()); }, $this->load_model, $this->EV_total_power);
                if ($this->EV_discharge_flag) {
                    array_push($this->load_model_seperate, $this->EV_discharge_power);
                    $this->load_model = array_map(function() { return array_sum(func_get_args()); }, $this->load_model, $this->EV_discharge_power);
                }
            }
        }
    }
    // EM & EV
    private $table_user_number = '';
    private $table_user_result = '';
    // EM
    private $EM_start_departure_SOC_tmp;
    private $EM_total_power;
    private $EM_discharge_power;
    public $EM_total_power_sum;
    public $EM_MIN_departureSOC;
    public $EM_AVG_departureSOC;
    public $EM_total_power_cost;
    public $EM_start_departure_SOC = array();
    public function getCEMS_EMInfo(string $user_number, string $user_result) {
        
        $this->table_user_number = $user_number;
        $this->table_user_result = $user_result;
        $this->EM_total_power = array_map('floatval', $this->sqlFetchAssoc("SELECT `total_power` FROM `". $this->table_user_number ."`", array("total_power")));
        $this->EM_discharge_power = array_map('floatval', $this->sqlFetchAssoc("SELECT `discharge_normal_power` FROM `". $this->table_user_number ."`", array("discharge_normal_power")));
        $this->EM_start_departure_SOC_tmp = $this->sqlFetchAssoc("SELECT `Start_SOC`,`Departure_SOC` FROM `". $this->table_user_result ."` WHERE Real_departure_timeblock IS NOT NULL", array("Start_SOC", "Departure_SOC"));
        $this->EM_total_power_sum = round($this->sqlFetchRow("SELECT SUM(total_power) FROM `". $this->table_user_number ."`", $this->oneValue), 2);
        $this->EM_MIN_departureSOC = round(floatval($this->sqlFetchRow("SELECT MIN(Departure_SOC) FROM `". $this->table_user_result ."` WHERE Departure_SOC IS NOT NULL", $this->oneValue))*100, 2);
        $this->EM_AVG_departureSOC = round(floatval($this->sqlFetchRow("SELECT AVG(Departure_SOC) FROM `". $this->table_user_result ."` WHERE Departure_SOC IS NOT NULL", $this->oneValue))*100, 2);
        $this->EM_total_power_cost = round(array_sum(array_map(function($x, $y) { return $x * $y * 0.25; }, $this->electric_price, $this->EM_total_power)), 2);
        array_push($this->EM_start_departure_SOC, array_map('floatval', $this->EM_start_departure_SOC_tmp[0]));
        array_unshift($this->EM_start_departure_SOC, array_map(function($x, $y) { return $x - $y; }, array_map('floatval', $this->EM_start_departure_SOC_tmp[1]), $this->EM_start_departure_SOC[0]));
        for ($i=0; $i < count($this->EM_start_departure_SOC); $i++) { 
            
            foreach ($this->EM_start_departure_SOC[$i] as $key => $value) {
                $this->EM_start_departure_SOC[$i][$key] = $value * 100;
            }
        }
    }
    // EV
    private $EV_start_departure_SOC_tmp;
    private $EV_total_power;
    private $EV_discharge_power;
    public $EV_total_power_sum;
    public $EV_MIN_departureSOC;
    public $EV_AVG_departureSOC;
    public $EV_total_power_cost;
    public $EV_start_departure_SOC = array();
    public function getCEMS_EVInfo(string $user_number, string $user_result) {

        $this->table_user_number = $user_number;
        $this->table_user_result = $user_result;
        $this->EV_total_power = array_map('floatval', $this->sqlFetchAssoc("SELECT `total_power` FROM `". $this->table_user_number ."`", array("total_power")));
        $this->EV_discharge_power = array_map('floatval', $this->sqlFetchAssoc("SELECT `discharge_normal_power` FROM `". $this->table_user_number ."`", array("discharge_normal_power")));
        $this->EV_start_departure_SOC_tmp = $this->sqlFetchAssoc("SELECT `Start_SOC`,`Departure_SOC` FROM `". $this->table_user_result ."` WHERE Real_departure_timeblock IS NOT NULL", array("Start_SOC", "Departure_SOC"));
        $this->EV_total_power_sum = round($this->sqlFetchRow("SELECT SUM(total_power) FROM `". $this->table_user_number ."`", $this->oneValue), 2);
        $this->EV_MIN_departureSOC = round(floatval($this->sqlFetchRow("SELECT MIN(Departure_SOC) FROM `". $this->table_user_result ."` WHERE Departure_SOC IS NOT NULL", $this->oneValue))*100, 2);
        $this->EV_AVG_departureSOC = round(floatval($this->sqlFetchRow("SELECT AVG(Departure_SOC) FROM `". $this->table_user_result ."` WHERE Departure_SOC IS NOT NULL", $this->oneValue))*100, 2);
        $this->EV_total_power_cost = round(array_sum(array_map(function($x, $y) { return $x * $y * 0.25; }, $this->electric_price, $this->EV_total_power)), 2);
        array_push($this->EV_start_departure_SOC, array_map('floatval', $this->EV_start_departure_SOC_tmp[0]));
        array_unshift($this->EV_start_departure_SOC, array_map(function($x, $y) { return $x - $y; }, array_map('floatval', $this->EV_start_departure_SOC_tmp[1]), $this->EV_start_departure_SOC[0]));
        for ($i=0; $i < count($this->EV_start_departure_SOC); $i++) { 
            
            foreach ($this->EV_start_departure_SOC[$i] as $key => $value) {
                $this->EV_start_departure_SOC[$i][$key] = $value * 100;
            }
        }
    }
    // CEMS DR
    public $arr_CommunityCBL = array();
    public function getCEMS_DRCBL() {

        if ($this->dr_mode != 0) {
            $this->arr_CommunityCBL = $this->community_limit_capability;
            for ($j=$this->dr_info[1]; $j < $this->dr_info[2]; $j++) { $this->arr_CommunityCBL[$j] = intval($this->dr_info[5]); }
        }
    }
}

/**
 * Receive HEMS & BP info which use in (backup)HEMS pages
 */

class HEMS extends CommonData {

    function __construct($table_BP, $table_CS, $table_LHEMS_UCLoad, $table_LHEMSCost) {
        
        parent::__construct($table_BP, $table_CS, $table_LHEMS_UCLoad, $table_LHEMSCost);
        $this->getHEMS_BP();
        $this->getHEMS_Cost();
        $this->getHEMS_LoadListSelect();
        $this->getHEMS_LoadListArray();
        $this->getHEMS_UCLoad();
        $this->getHEMS_PowerOfLoadGridEss();
        $this->getHEMS_DRParticipateAndCBL();
        $this->getHEMS_ComLvTime();
    }
}

/**
 * Receive CEMS & BP info which use in (backup)CEMS pages
 */

class CEMS extends CommonData {

    function __construct($table_BP, $table_CS, $table_LHEMS_UCLoad, $table_GHEMS_UCLoad, $table_TotalLoad, $table_EM_BP, $table_EM_number, $table_EM_result, $table_EV_BP, $table_EV_number, $table_EV_result) {

        parent::__construct($table_BP, $table_CS, $table_LHEMS_UCLoad, null, $table_GHEMS_UCLoad, $table_TotalLoad);
        $this->getCEMS_BP($table_EM_BP, $table_EV_BP);
        $this->getCEMS_EMInfo($table_EM_number, $table_EM_result);
        $this->getCEMS_EVInfo($table_EV_number, $table_EV_result);
        $this->getCEMS_DRCBL();
        $this->getCEMS_LoadModel();
    }
}

/**
 * Receive BP page info
 */

class BPSetting extends CommonData {

    public $baseParameter;
    public $EM_flag;
    public $EM_charging_amount;
    public $EM_sure_charging_amount;

    /**
     * ------ BPSetting Construct ------
     * baseParameter fetch all info from table BP
     * EM_charging_amount & EM_sure_charging_amount is for guage
     * ---------------------------------
     */

    function __construct($table_BP) {

        parent::__construct($table_BP);
        $this->baseParameter = $this->sqlFetchAssoc("SELECT `parameter_name`, `parameter_define`, `value` FROM `". $table_BP ."`", array("parameter_name", "parameter_define", "value"));
        $this->EM_flag = boolval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'ElectricMotor' ", $this->oneValue));
        $this->EM_charging_amount = $this->sqlFetchRow("SELECT COUNT(*) FROM `EM_Pole` WHERE `charging_status`=1 ", $this->oneValue);
        $this->EM_sure_charging_amount = $this->sqlFetchRow("SELECT COUNT(*) FROM `EM_Pole` WHERE `sure`=1 ", $this->oneValue);
    }
}

/**
 * Receive EMEV page info
 */

class EMEVSetting extends CommonData {

    // table type
    private $str_EM = 'EM_';
    private $str_EV = 'EV_';
    // table 
    private $WholeDayUserNum = 'wholeDay_userChargingNumber';
    private $Type = 'motor_type';
    private $Parm = 'Parameter';
    private $RandResult = 'Parameter_of_randomResult';
    private $ChargeStatus = 'chargingOrDischarging_status';
    // 
    private $em_wholeDay_chargingUser_nums;
    private $ev_wholeDay_chargingUser_nums;
    public $em_motor_type = array();
    public $emParameter = array();
    public $emParameter_of_randomResult = array();
    public $em_chargingOrDischargingStatus_array = array();
    public $ev_motor_type = array();
    public $evParameter = array();
    public $evParameter_of_randomResult = array();
    public $ev_chargingOrDischargingStatus_array = array();
    public $sf_chargingUser_nums = array();
    public $f_chargingUser_nums = array();
    public $n_chargingUser_nums = array();
    public $ev_chargingUser_nums = array();

    function __construct($table_BP) {
        
        parent::__construct($table_BP);
        $this->em_wholeDay_chargingUser_nums = $this->sqlFetchAssoc("SELECT `type_0`, `type_1`, `type_2`, `type_3`, `type_4`, `type_5`, `type_6`, `type_7`, `type_8`, `type_9` FROM `". $this->str_EM.$this->WholeDayUserNum ."`", array("type_0", "type_1", "type_2", "type_3", "type_4", "type_5", "type_6", "type_7", "type_8", "type_9"));
        $this->ev_wholeDay_chargingUser_nums = $this->sqlFetchAssoc("SELECT `type_0`, `type_1`, `type_2`, `type_3` FROM `". $this->str_EV.$this->WholeDayUserNum ."`", array("type_0", "type_1", "type_2", "type_3"));
        
        $this->em_motor_type = $this->sqlFetchAssoc("SELECT `type`, `capacity`, `voltage`, `power`, `percent` FROM `". $this->str_EM.$this->Type ."`", array("type", "capacity", "voltage", "power", "percent"));
        $this->emParameter = $this->sqlFetchAssoc("SELECT `parameter_name`, `parameter_define`, `value` FROM `". $this->str_EM.$this->Parm ."`", array("parameter_name", "parameter_define", "value"));
        $this->emParameter_of_randomResult = $this->sqlFetchAssoc("SELECT `parameter_name`, `parameter_define`, `value` FROM `". $this->str_EM.$this->RandResult ."`", array("parameter_name", "parameter_define", "value"));
        $this->em_chargingOrDischargingStatus_array = $this->sqlFetchRow("SELECT * FROM `". $this->str_EM.$this->ChargeStatus ."`", $this->emChargeDischarge);
        $this->ev_motor_type = $this->sqlFetchAssoc("SELECT `type`, `capacity(kWh)`, `power`, `percent` FROM `". $this->str_EV.$this->Type ."`", array("type", "capacity(kWh)", "power", "percent"));
        $this->evParameter = $this->sqlFetchAssoc("SELECT `parameter_name`, `parameter_define`, `value` FROM `". $this->str_EV.$this->Parm ."`", array("parameter_name", "parameter_define", "value"));
        $this->evParameter_of_randomResult = $this->sqlFetchAssoc("SELECT `parameter_name`, `parameter_define`, `value` FROM `". $this->str_EV.$this->RandResult ."`", array("parameter_name", "parameter_define", "value"));
        $this->ev_chargingOrDischargingStatus_array = $this->sqlFetchRow("SELECT * FROM `". $this->str_EV.$this->ChargeStatus ."`", $this->emChargeDischarge);
        array_push($this->n_chargingUser_nums, array_map('intval', $this->em_wholeDay_chargingUser_nums[2]));
        array_push($this->n_chargingUser_nums, array_map('intval', $this->em_wholeDay_chargingUser_nums[5]));
        array_push($this->n_chargingUser_nums, array_map('intval', $this->em_wholeDay_chargingUser_nums[6]));
        array_push($this->n_chargingUser_nums, array_map('intval', $this->em_wholeDay_chargingUser_nums[7]));
        array_push($this->n_chargingUser_nums, array_map('intval', $this->em_wholeDay_chargingUser_nums[8]));
        array_push($this->n_chargingUser_nums, array_map('intval', $this->em_wholeDay_chargingUser_nums[9]));
        array_push($this->f_chargingUser_nums, array_map('intval', $this->em_wholeDay_chargingUser_nums[1]));
        array_push($this->f_chargingUser_nums, array_map('intval', $this->em_wholeDay_chargingUser_nums[4]));
        array_push($this->sf_chargingUser_nums, array_map('intval', $this->em_wholeDay_chargingUser_nums[0]));
        array_push($this->sf_chargingUser_nums, array_map('intval', $this->em_wholeDay_chargingUser_nums[3]));
        for ($i=0; $i < count($this->ev_wholeDay_chargingUser_nums); $i++) { 
            array_push($this->ev_chargingUser_nums, array_map('intval', $this->ev_wholeDay_chargingUser_nums[$i]));
        }
    }
}

?>