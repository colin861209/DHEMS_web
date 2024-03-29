# DHEMS_web

###### tags: `DHEMS`

* [DHEMS kernel (github)](https://github.com/colin861209/DHEMS)
  * [Mathematical Formula Link (hackmd)](https://hackmd.io/pvujnbJeQf6bXQqIibQXXQ)
  
* [web link ](http://140.124.42.70:3332/how/DHEMS_web/loadFix.html)  (should use school network or connect school VPN)
  * file path : SSH =>  `140.124.42.65 /var/www/html/how/DHEMS_web/`
  
---
### 2021/02/27

+ Commit Link [4dd075b](https://github.com/colin861209/DHEMS_web/commit/4dd075bf710873c4b325f7c8ce930ba8448cad20)

* fetch_mysql.php `doc`

---
### 2021/03/02

+ Commit link [9e4a696](https://github.com/colin861209/DHEMS_web/commit/9e4a696060b4d9690d4daa88a13c32e667f74428)

* Uncontrollable load

---
### 2021/04/14

+ Commit link [b1574b9](https://github.com/colin861209/DHEMS_web/commit/b1574b90fb9eb947e7561e42c1dce07ede0c4726)

* CEMS flag & String value replacement

	1. GHEMS_flag
	2. LHEMS_flag
	3. dr_mode
	4. uncontrollable_load_flag
	
 * Object `energyType`
 
---
### 2021/05/09

+ Commit link [5e8fbe1](https://github.com/colin861209/DHEMS_web/commit/5e8fbe199a186d59400b1c17248b42cf36bf85ae)
+ Commit link [e023c66](https://github.com/colin861209/DHEMS_web/commit/e023c6630a1531bebff71a4e71081a257488481f)

* Flag table Create

	* Html: Create `Flag table` and `modify button` struct
		1. index.html
		2. loadFix.html
	* Js: 
		1. flag_modify.js: Display `flag table` and `modify button`
		2. index.js: Call function from backend
		3. loadFix.js: Call function from backend
		4. chart_style: Create Object
	* php:
		1. send_newFlag.php: Modify `LHEMS` & `GHEMS` flag table when change flag from web page
		2. commonSQL_data.php: Modify SQL statement

---
### 2021/05/09

+ Commit link [2db7e01](https://github.com/colin861209/DHEMS_web/commit/2db7e01a3bc81e9becdec4a898ebdbb1d88a6601)
+ Commit link [b9a5064](https://github.com/colin861209/DHEMS_web/commit/b9a5064295e71885fc1411f78b3d723b1de7add2)
+ Tutorial about [sweetalert2](https://sweetalert2.github.io)

+ Button to change diff databases & alert style

	* Html: import script `sweetalert2` & basic info in id 'breadcrumb'
		1. baseParameter: Button of change DB

	* Js: insert text after 'breadcrumb' in `baseParameter, index, loadFix .js`
		1. baseParameter:  function: change databases, `modify_target` add `simulate_price`
		2. baseParameter, baseParameter_modify, flag_modify: Use `sweetalert2`
		3. baseParameter_modify: Change size of `modify_target` input element

	* PHP: Echo `database_name` in most php files
		1. database_name: Receive DB name from button or session, otherwise default `DHEMS`
		2. fetch_mysql: require `database_name.php`. Change DB name in `$conn`
		3. commonSQL_data: Receive `electric price` by `$target_price` from DB `table BaseParameter`

	* css: breadcrumb, class 'database' button

---
### 2021/06/09

+ Commit link [608106b](https://github.com/colin861209/DHEMS_web/commit/608106bd2d62f76efb5a49f9a3cb328ed40a7108)

- ADD `History weather` represent previous days weather
	- PHP:
		- Get history solar data, **array_map** can 'floatval' array values in one line
	- Js:
		- New gauge & tableinfo & solar chart show two solar data line
		- Show chart color can be multiple after chart_info 6
	- Html:
		- New gauge history weather structure

---
### 2021/06/18

+ Commit link [f22740b](https://github.com/colin861209/DHEMS_web/commit/f22740bf7f96d385161c82e145bfdfb366c6897c)
+ Commit link [55e572f](https://github.com/colin861209/DHEMS_web/commit/55e572f54634da4f65af97f0708721cbb73e8dca)
+ Commit link [9553ea4](https://github.com/colin861209/DHEMS_web/commit/9553ea48942127859ec1efb592ab56ba20eb04ad)

- ADD `Public Load` in loadFix

- **Technique of PHP**
- In [f22740b](https://github.com/colin861209/DHEMS_web/commit/f22740bf7f96d385161c82e145bfdfb366c6897c)
	- array_map: Sum two arrays 
	- ex: `$load_model = array_map(function() {
        return array_sum(func_get_args());
    }, $load_model, $uncontrollable_load_sum);`
- In [55e572f](https://github.com/colin861209/DHEMS_web/commit/55e572f54634da4f65af97f0708721cbb73e8dca)
	- array_push: Can push an array into another two dimensional arrays 
	- array_splice: can remove array values by index, and index will re-sequence
	
---
### 2021/07/01

+ Commit link [681ccf5](https://github.com/colin861209/DHEMS_web/commit/681ccf5d41eb0b5dddc73a839d262b594990fd54)

- baseParameter: Max demand response number
- index: Participation table
- loadFix: Demand response feedback price

---
### 2021/07/10

+ Commit link [06f8a5d](https://github.com/colin861209/DHEMS_web/commit/06f8a5d285ce56a8d72fc943ef1aba8b57d5d458)
+ Commit link [60530e0](https://github.com/colin861209/DHEMS_web/commit/60530e0e6bd9a75bed0a52e08101248ade585da5)
+ Commit link [28f0799](https://github.com/colin861209/DHEMS_web/commit/28f079954467d36223cd5a32bc98d261f9cc30e0)

- New Page: log.html
	- Show all DHEMS log file
	- pwd is define in log.php and DHEMS show all file include demand response 1&2
- index: Show `Pess info` in chart SOC & `Catch error` with table Participation

---
### 2021/08/09

+ Commit link [2e6d052](https://github.com/colin861209/DHEMS_web/commit/2e6d05257fcb6c0bf9a1f27041d225a378a777ec)

- Feature: Comfort level
- Show in index.html
- For normal visual in comfort level interval, each level in the end time will connect with another level in start time, but in fact the operate time is determine by the second term start time not the end time of the first term.

---
### 2021/10/01

+ Commit link [dc30e4c](https://github.com/colin861209/DHEMS_web/commit/dc30e4c94e363fd703f7797f8c239f2d6165e443)

- Feature: Each household electric cost or feedback
- Show in index.html
- Show detail:
	- origin cost
	- real cost after optimize
	- public load cost
	- feedback from demand response
	- saving efficiency

---
### 2022/01/04

+ Commit link [856a52e](https://github.com/colin861209/DHEMS_web/commit/856a52ec22d80a668cea8febea009818b0540443)
+ `2022/01/03 Comfirm with professor to cancel fast charging and super fast charging`

- Feature: Electric Motor
- New page: emParameter.html
	- Set up base parameter 
	- Set up normal distribution for three type charging user
	- Set up percentage about which motor type can insert to parking lot
- Modify: baseParameter.html
	- EM_flag & EM_generate_random_user_result
- Modify: loadFix.html
	- Show load model with EM

---
### 2022/01/18
+ Commit link [62505be](https://github.com/colin861209/DHEMS_web/commit/62505be741a3d70ee9426d8c22782138b8bfbf90)
+ Commit link [4041322](https://github.com/colin861209/DHEMS_web/commit/404132257e5cdde18a71d26c88e31635f1f95a8f)

- Feature: EM Table & Users' EM status

---
### 2022/01/21~2022/03/25
+ Commit link [f5edd97](https://github.com/colin861209/DHEMS_web/commit/f5edd97f93467d032b0dcd97eccfa3dcdb69da62)

- New Page: EMEV 
- Table display modify
- New loads display
	- UC load
	- Public Load 
- Chart limit 
