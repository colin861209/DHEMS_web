<?php
require 'fetch_mysql.php';

class BP extends SQLQuery {
    
    private $table_BP = '';
    private $col_value = 'value';
    private $col_parmName = 'parameter_name';

    // string price & weather
    private $str_target_price;
    private $str_target_solar;
    public $electric_price = array();
    public $simulate_solar = array();
    // time
    public $local_simulate_timeblock;
    public $global_simulate_timeblock;
    public $time_block;
    // grid power limit
    public $limit_capability;
    public $community_limit_capability;
    // demand response
    public $dr_mode;
    // hems uc flag
    public $uncontrollable_load_flag;
    // chart flag & related info
    public $chart_upperLowerLimit_flag;
    public $electric_price_upper_limit = null;
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

    // HEMS flag
    public $comfortLevel_flag;
    // index.html base parameter
    public $app_counts;
    public $household_num;
    public $simulate_timeblock;
    public function getHEMS_BP() {
        
        $this->comfortLevel_flag = boolval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'comfortLevel_flag' ", $this->oneValue));
        $this->app_counts = $this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'app_counts' ", $this->oneValue);
        $this->household_num = intval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'householdAmount' ", $this->oneValue));
        $this->simulate_timeblock = intval($this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `". $this->table_BP ."` WHERE `". $this->col_parmName ."` = 'next_simulate_timeblock' ", $this->oneValue));
    }
}   

class CommonData extends BP {
    
    // Fix table
    private $table_DR = 'demand_response';
    private $table_LoadSelect = 'load_list_select';
    private $table_Load = 'load_list';
    private $table_DRParticipate = 'LHEMS_demand_response_participation';
    private $table_DRCBL = 'LHEMS_demand_response_CBL';
    private $table_ComLv = 'LHEMS_comfort_level';
    private $table_CS = '';
    // HEMS 
    private $table_LHEMSCost = '';
    private $table_LHEMS_UCLoad = '';
    // CEMS

    public $dr_count;
    public $dr_info;
    public $GHEMS_flag;
    public $LHEMS_flag;
    function __construct($table_BP, $table_CS, $table_LHEMS_UCLoad = null, $table_Cost = null, $table_GHEMS_UCLoad = null, $table_TotalLoad = null) {
        
        parent::__construct($table_BP);
        $this->table_CS = $table_CS;
        $this->table_LHEMSCost = $table_Cost;
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
    public $arr_GridPower = array();
    public $arr_EssPower = array();
    public $arr_EssSOC = array();
    // Must call function 'getHEMS_LoadListSelect' first
    public function getHEMS_PowerOfLoadGridEss() {

        for ($i=0; $i < $this->household_num; $i++) { 
    
            $interrupt_status = $this->sqlFetchRow("SELECT * FROM `". $this->table_CS ."` WHERE (equip_name LIKE '%interrupt%' OR equip_name LIKE 'varyingPsi%') AND household_id =" .($i+1). " ORDER BY `household_id`, `control_id` ASC ", $this->controlStatusResult);
            array_push($this->load_power_tmp, $interrupt_status);
            
            $grid_power_tmp = $this->sqlFetchRow("SELECT * FROM `". $this->table_CS ."` WHERE equip_name = 'Pgrid' AND household_id =" .($i+1). " ORDER BY `household_id`, `control_id` ASC ", $this->aRow);
            array_splice($grid_power_tmp, 0, 1);
            array_splice($grid_power_tmp, 96, count($grid_power_tmp)-1);
            array_push($this->arr_GridPower, array_map('floatval', $grid_power_tmp));

            if (boolval($this->LHEMS_flag[2][array_search("Pess", $this->LHEMS_flag[0], true)])) {

                $battery_power_tmp = $this->sqlFetchRow("SELECT * FROM `". $this->table_CS ."` WHERE equip_name = 'Pess' AND household_id =" .($i+1). " ORDER BY `household_id`, `control_id` ASC ", $this->aRow);
                array_splice($battery_power_tmp, 0, 1);
                array_splice($battery_power_tmp, 96, count($battery_power_tmp)-1);
                array_push($this->arr_EssPower, array_map('floatval', $battery_power_tmp));
                
                $SOC_tmp = $this->sqlFetchRow("SELECT * FROM `". $this->table_CS ."` WHERE equip_name = 'arr_EssSOC' AND household_id =" .($i+1). " ORDER BY `household_id`, `control_id` ASC ", $this->aRow);
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
    // hems dr
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
                FROM `". $this->table_DRCBL ."` WHERE `time_block` BETWEEN ".$this->dr_info[1]." AND ".($this->dr_info[2]-1)." AND `comfort_level_flag` = ".$this->comfortLevel_flag, $this->aRow));

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
}

class HEMS extends CommonData {

    function __construct($table_BP, $table_CS, $table_LHEMS_UCLoad = null, $table_Cost = null, $table_GHEMS_UCLoad = null, $table_TotalLoad = null) {
        
        parent::__construct($table_BP, $table_CS, $table_LHEMS_UCLoad, $table_Cost);
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

// class ElectricVehicle extends BaseParameter {
//     $EM_discharge_flag = $this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `backup_EM_Parameter` WHERE `". $this->col_parmName ."` = 'Motor_can_discharge' ", $this->oneValue);
//     $EV_discharge_flag = $this->sqlFetchRow("SELECT `". $this->col_value ."` FROM `backup_EV_Parameter` WHERE `". $this->col_parmName ."` = 'Vehicle_can_discharge' ", $this->oneValue);

// }

?>