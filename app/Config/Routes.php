<?php

use App\Controllers\AuthController;
use App\Controllers\DepartmentController;
use App\Controllers\EmployeeController;
use App\Controllers\HomeController;
use App\Controllers\PositionController;
use App\Controllers\RateController;
use App\Controllers\ReportController;
use App\Controllers\WorkEntryController;
use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */


// группа маршрутов, только авторизованный пользователь может присутсвовать
$routes->group('', ['filter' => 'auth'], function ($routes) {
    // главная страницы
    $routes->get('/', [HomeController::class, 'index']);
    
    $routes->get('/employees-list', [EmployeeController::class, 'index']);
    $routes->post('/employees/store', [EmployeeController::class, 'store']);
    $routes->post('/employees/update', [EmployeeController::class, 'update']);
    $routes->get('/employees/delete/(:num)', [EmployeeController::class, 'delete/$1']);
    $routes->get('/employees/export', [EmployeeController::class, 'exportCsv']);
    
    $routes->get('/rates-list', [RateController::class, 'index']);
    $routes->post('/rates/store', [RateController::class, 'store']);
    $routes->post('/rates/update', [RateController::class, 'update']);
    $routes->get('/rates/delete/(:num)', [RateController::class, 'delete/$1']);
    $routes->post('/rates/store-unit', [RateController::class, 'storeUnit']);
    $routes->get('/rates/export-csv', [RateController::class, 'exportCsv']);

    $routes->get('/workentries-list', [WorkEntryController::class, 'index']);
    $routes->get('/workentries/export-csv', [WorkEntryController::class, 'exportCsv']);
    $routes->post('/workentries/store', [WorkEntryController::class, 'store']);
    $routes->post('/workentries/update', [WorkEntryController::class, 'update']);
    $routes->get('/workentries/delete/(:num)', [WorkEntryController::class, 'delete/$1']);

    $routes->get('/department-list', [DepartmentController::class, 'index']);
    // $routes->get('/departments', [DepartmentController::class, 'index']);
    $routes->post('/departments/store', [DepartmentController::class, 'store']);
    $routes->post('/departments/update', [DepartmentController::class, 'update']);
    $routes->get('/departments/delete/(:num)', [DepartmentController::class, 'delete/$1']);
    $routes->get('/departments/view/(:num)', [DepartmentController::class, 'view/$1']);
    $routes->get('/departments/export-csv', [DepartmentController::class, 'exportCsv']);

    $routes->get('/reports-list', [ReportController::class, 'index']);
    // $routes->get('/reports', [ReportController::class, 'index']);
    $routes->get('/reports/generate', [ReportController::class, 'generate']);
    $routes->get('/reports/export-csv', [ReportController::class, 'exportCsv']);

    $routes->get('/positions-list', [PositionController::class, 'index']);
    // $routes->get('/positions', [PositionController::class, 'index']);
    $routes->post('/positions/store', [PositionController::class, 'store']);
    $routes->post('/positions/update', [PositionController::class, 'update']);
    $routes->get('/positions/delete/(:num)', [PositionController::class, 'delete/$1']);

    $routes->post('/profile/update', [AuthController::class, 'profileUpdate']);

    $routes->get('/export-csv', [HomeController::class, 'exportCsv']);
});

$routes->get('/login', [AuthController::class, 'login']);

$routes->post('/login',    [AuthController::class, 'loginPost']);
$routes->get('/register', [AuthController::class, 'register']);
$routes->post('/register', [AuthController::class, 'registerPost']);
$routes->get('/logout',    [AuthController::class, 'logout']);


