<?php
require 'config/database.php';
require 'models/User.php';
require 'models/Planning.php';
require 'controllers/AuthController.php';
require 'controllers/PlanningController.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'login';

switch ($action) {
    case 'login':
        $controller = new AuthController($database);
        $controller->login();
        break;

    case 'logout':
        $controller = new AuthController($database);
        $controller->logout();
        break;

    case 'planning':
        $controller = new PlanningController($database);
        $controller->index();
        break;

    case 'update_planning':
        $controller = new PlanningController($database);
        $controller->update();
        break;

    case 'add_person':
        $controller = new PlanningController($database);
        $controller->addPerson();
        break;

    case 'delete_user':
        $controller = new PlanningController($database);
        $controller->deleteUser();
        break;

    default:
        header('Location: index.php?action=login');
        break;
}
