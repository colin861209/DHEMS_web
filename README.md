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

### index.html

* Show `each household's load deployment` and `total load consumption`

### loadFix.html

* Show enerage management result
    
    1. Total Price Cost
    2. Price & Loads optimize chart

    ![](https://i.imgur.com/IHWCP2O.png)
    
    3. SOC & Loads optimize chart
    4. Total Loads chart

    ![](https://i.imgur.com/HBL4TeG.jpg)
