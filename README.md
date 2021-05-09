# DHEMS_web

###### tags: `DHEMS`

* [DHEMS kernel (github)](https://github.com/colin861209/DHEMS)
  * [Mathematical Formula Link (hackmd)](https://hackmd.io/pvujnbJeQf6bXQqIibQXXQ)
  
* [web link ](http://140.124.42.70:3332/how/DHEMS_web/loadFix.html)  (should use school network or connect school VPN)
  * file path : SSH =>  `140.124.42.70:3332 /var/www/html/how/DHEMS_web/`
  
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
+ Commit link [/e023c6](https://github.com/colin861209/DHEMS_web/commit/e023c6630a1531bebff71a4e71081a257488481f)

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
