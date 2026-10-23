<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';
use App\Core\{View,HttpException,Security};
try {
    $routes=require ROOT.'/config/routes.php';
    $route=is_string($_GET['r'] ?? '') ? trim($_GET['r'] ?? '', '/') : '__invalid';
    $method=$_SERVER['REQUEST_METHOD'];
    if (!isset($routes[$route])) throw new HttpException(404,'Không tìm thấy trang bạn yêu cầu.');
    if (!isset($routes[$route][$method])) throw new HttpException(405,'Phương thức truy cập không được hỗ trợ.');
    if ($method==='POST') Security::checkCsrf();
    [$controller,$action]=$routes[$route][$method];
    (new $controller())->$action();
} catch (HttpException $e) {
    View::render('error',['title'=>'Không thể thực hiện','message'=>$e->getMessage(),'code'=>$e->status],$e->status);
} catch (Throwable $e) {
    error_log((string)$e);
    View::render('error',['title'=>'Hệ thống đang bận','message'=>'Không thể xử lý yêu cầu. Vui lòng thử lại hoặc liên hệ quản trị viên.','code'=>500],500);
}
