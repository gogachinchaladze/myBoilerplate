<?php

//TODO CREATE IPLIGENCE SERVICE

require_once 'cfg.php';

function isUserFromGeorgia($ip){
  $ip = "'$ip'";
  $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME_IP);
  if (!$conn) {
    die("Connection to country detection database failed: " . mysqli_connect_error() . "<br/>");
  }
  mysqli_set_charset($conn,"utf8");
  $sql = "SELECT country_code
            FROM ipligence_geo
            WHERE ip_from <= INET_ATON($ip) and ip_to >= INET_ATON($ip)
            LIMIT 1;";

  $result = $conn->query($sql);
  mysqli_close($conn);
  if($result && $result->num_rows > 0){
    return true;
  }
  else{
    return false;
  }
}

function getCountryCodeByIP($ip){
  $ip = "'$ip'";
  $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME_IP);
  if (!$conn) {
    die("Connection to country detection database failed: " . mysqli_connect_error() . "<br/>");
  }
  mysqli_set_charset($conn,"utf8");

  $sql = "SELECT country_code
            FROM ipligence
            WHERE ip_from <= INET_ATON($ip) and ip_to >= INET_ATON($ip)
            LIMIT 1;";
  $result = $conn->query($sql);
  mysqli_close($conn);
  if($result && $result->num_rows > 0){
    $country = mysqli_fetch_assoc($result);
    return $country['country_code'];
  }
  else{
    return "-1";
  }
}

?>