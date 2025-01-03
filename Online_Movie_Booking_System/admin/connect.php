<?php

//session_start();  

$con = mysqli_connect('localhost', 'root', '', 'db_movies');

if (!$con) {

    die('Không thể kết nối cơ sở dữ liệu: ' . mysqli_connect_error());
}

mysqli_set_charset($con, 'utf8');

//session_write_close();
