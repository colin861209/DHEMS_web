# commonSQL_data.php

Class `BP` extend `SQLQuery`
All information fetch from DB table `BaseParameter`
## Class BP
### __construct
+ Get some base parameter from DB table `BaseParameter`
### getChartInfo
+ Get chart limit from DB table `BaseParameter`
### getHEMS_BP
+ Get only HEMS page need information
### getCEMS_BP
+ Get only CEMS page need information

Class `CommonData` extend `BP`
## Class CommonData
### __construct
+ Get DR information & Flag setting
### HEMS Function
+ getHEMS_LoadListSelect
+ getHEMS_PowerOfLoadGridEss
+ getHEMS_UCLoad
+ getHEMS_LoadListArray
+ getHEMS_DRParticipateAndCBL
+ getHEMS_ComLvTime
+ getHEMS_Cost
### CEMS Function
+ getCEMS_LoadModel
    + Contain function ->
    + getEachPowerSupplyArray
    + calculateCEMSLoadModel
+ getCEMS_EMInfo
+ getCEMS_EVInfo
+ getCEMS_DRCBL


Class `HEMS` extend `CommonData`
## Class HEMS
+ Get All HEMS information through above for `localHouseholdLoadDeployment.php` and `backup_LHEMS.php`

Class `CEMS` extend `CommonData`
## Class CEMS
+ Get All CEMS information through above for `loadFix.php` and `backup_GHEMS.php`

Class `BPSetting` extend `CommonData`
## Class BPSetting
+ Get All BaseParameter information through above for `baseParameter.php`
 
Class `EMEVSetting` extend `CommonData`
## Class BPSetting
+ Get All EMEV information through above for `emevParameter.php`