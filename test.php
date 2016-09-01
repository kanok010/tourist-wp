<?php

$dblink = mysql_connect("TouristAppDB.ppm.in.th", "touristct_rw", "Ltq4542tiY");
mysql_select_db("touristapp_content",$dblink);

mysql_query("SET character_set_results=utf8");//ตั้งค่าการดึงข้อมูลออกมาให้เป็น utf8
mysql_query("SET character_set_client=utf8");//ตั้งค่าการส่งข้อมุลลงฐานข้อมูลออกมาให้เป็น utf8
mysql_query("SET character_set_connection=utf8");

$sql_statement = "UPDATE wp_posts
SET guid = REPLACE(guid, 'http://localhost/touristApp/', 'http://touristapplication.truecorp.co.th/content/')
WHERE guid LIKE '%http://localhost/touristApp/%'";
mysql_query($sql_statement,$dblink);

// $sql_statement = "UPDATE `touristapp_content`.`wp_options` SET `option_value` = 'http://touristapplication.truecorp.co.th/content/' WHERE `wp_options`.`option_id` = 2";
// mysql_query($sql_statement,$dblink);

// $sql_statement = "UPDATE `touristapp_content`.`wp_options` SET `option_value` = 'http://touristapplication.truecorp.co.th/content/wp-content/uploads' WHERE `wp_options`.`option_id` = 56";
// mysql_query($sql_statement,$dblink);

print_r( "aasdasdds" );exit;

?>