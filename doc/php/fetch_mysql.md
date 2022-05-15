# fetch_mysql.php

Class `SQLQuery` to connect MySQL, DB name receive from `database_name.php`

## Class SQLQuery

### __construct
+ Connect MySQL

### __destruct
+ Disconnect MySQL

### sqlFetchRow
+ Fetch data by row
+ `key`:
1. `oneValue`: Fetch single value from DB
2. `aRow`: Fetch a row data to an array
3. `constrolStatusResult`: Fetch multi dimensional rows data from column 1 to 97 to an array (only can use in DB table like `LHEMS_constrol_status`)
4. `emChargeDischarge`: Fetch multi dimensional rows data from column 0 to 96 to an array (only can use in DB table like `EM_chargingOrDischarging_status`)

### sqlFetchAssoc
+ Fetch data by column
+ `key` should input an array and values must be same as the column name from DB table `ex:array("variable_name", "variable_define", "flag")`
+ If more than one values in array will return multi dimensional array

### multiply
+ Return array which each value in array multiply factor

### UpdateBaseParameter
+ Update and verify each parameters in BP page
+ This function use in `send_newBaseParameter.php`

### UpdateFlags
+ Update and verify each parameters in HEMS & CEMS page
+ This function use in `send_newFlag.php`

### create_wholeDay_userChargingNumber
+ Create amount of users in each timeblock through EMEV type and percentage

### update_EMParameter_totalChargingPole
+ Update EM total pole amount 

### create_chargingPole
+ Create an empty EMEV parking lot

### UpdateEMEV_ParmOrType
+ Update EMEV parmameter or type
+ This function use in `send_newEMEV_parameterOrType.php`