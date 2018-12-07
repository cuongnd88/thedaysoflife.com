<?php
/**
 * Single entry point for API: all api point to this page with an request
 */
require_once("../models/autoload.php");

use jennifer\api\API;
use jennifer\http\Response;
use jennifer\sys\System;
use thedaysoflife\api\ServiceMapper;

/*
 * Sample api request
 * $hash is given to users when they registered for api service
 *
$data = ["userID" => 1000, "permission" => ["user", "day"]];
$token = JWT::encode($data, JWT_KEY_API);
$req = ["token"   => $token,
        "service" => "service_day",
        "action"  => "get_day",
        "para"    => ["id" => "100151", "json" => false,]];
$url = "www.thedaysoflife.com/api/?req=" . json_encode($req);
$url = 'www.thedaysoflife.com/api/?req={"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySUQiOjEwMDAsInBlcm1pc3Npb24iOlsidXNlciIsImRheSJdfQ.f2ieaIQd8OrTK7UrA4BqDwhgg1NpzLV7OdOGIWBbQNU","service":"service_day","action":"get_day","para":{"id":"100151","json":false}}';
*/

try {
    $system = new System([DOC_ROOT . "/config/env.ini"]);
    $system->setApi(new API(new ServiceMapper()))->runAPI();
} catch (Exception $exception) {
    (new Response())->error($exception->getMessage());
}