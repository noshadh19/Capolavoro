<?php
error_reporting(E_ALL);
ini_set("display_errors", false);
ini_set("log_errors", true);
ini_set("error_log", "errors.log");

function ExceptionHandler(Exception $exception){
	http_response_code(500);
	error_log($exception->getMessage());
	require "error.php";
}

set_exception_handler("ExceptionHandler");