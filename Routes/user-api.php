<?php

use Controllers\UserController;

$router->get('/api/users/profile', [UserController::class, 'getUserProfile'], ['auth']);
$router->put('/api/users/profile', [UserController::class, 'updateUserProfile'], ['auth']);