<?php
/* Get Data for summary stats - Summary */

$sqlsummary = "SELECT count(*) as cases_tot, count(distinct(Field7)) as courts_tot, SUM(Field8) AS enslaved_tot, SUM(Field9) AS liberated_tot, SUM(Field10) as registered_tot from person ";

$sqlstatsdepts = "SELECT Field6, CV_Govt_Departments.Name as govt_dept, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot from person left join CV_Govt_Departments on (CV_Govt_Departments.ID = person.Field6) ";

$sqlstatscourts = "SELECT MIN(Field6) as Field6, Field7, CV_Court_Names.Name as court_name, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot from person left join CV_Court_Names on (CV_Court_Names.ID = person.Field7) ";

$sqlstatsregion = "SELECT Field26, CV_Places.Name as Region, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot from person left join CV_Places on (CV_Places.ID = person.Field26) ";

$sqlstatsplaces = "SELECT MIN(Field26) as Field26, Field27, CV_Places.Name as Place, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot from person left join CV_Places on (CV_Places.ID = person.Field27) ";

$sqltimeline = "SELECT Field2 AS Decade, SUM(Field9) AS amt from person ";

?>