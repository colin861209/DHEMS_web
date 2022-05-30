# chart_style.js

#### function `set_series_function`
**Parm intro**
1. `multi`: Let **column** type data in chart can combine and calulate total
2. `series_type`: Such as ```column, line, spline, areaspline```
3. `DATA`: Things that you want to show in the chart, the data should to ***multi-dimensional array*** if parm `multi` set `'1'`
4. `stack_class`: The `name` you want to show in chart label which corresponding to the `DATA`
5. `yAxis_locate`: If set `'1'` means that `DATA` should see the y-axis by right side
6. `chart_series_type`: Push `series_type` to array `chart_series_type`
7. `chart_series_name`: Push `multi_name or stack_class` to array `chart_series_name` depending on `multi` is `'0' or '1'`
8. `chart_series_data`: Push `DATA` to array `chart_series_data`
9. `chart_series_stack`: Push `stack_class` to array `chart_series_stack`
10. `chart_series_yAxis`: Push `stack_class` to array `chart_series_yAxis`
11. `multi_name`: An array which saving the `name` you want to show in chart label when `multi` set `'1'` 

#### function `show_chart_with_redDashLine`
+ The chart which follow the timeblock and showing with redDashLine

#### function `show_chart_with_pinkAreaOrComforLevel`
+ The chart which show the `comfort level color interval` or `load's operation start time to end time`

#### function `show_chart_with_EM_users`
+ The chart which show the maximize EMEV users in x-axis, and we can see all users SOC status

#### function `show_chart_with_household_load_select`
+ The chart only show the amount of three type loads in each households 

#### function `set_each_load_function`
+ Old function to set the each load before `show_chart_with_pinkAreaOrComforLevel`

#### function `insertText_after_breadcrumb`
+ Show information like `DB name, weather, init SOC, DR info` on the breadcrumb

var `energyType`
+ A dictionary which save label name

var `path`
var `compare_timeblock`
+ `path compare_timeblock` are the data which to determine the correspond page reload or not