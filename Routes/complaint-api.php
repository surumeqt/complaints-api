<?php

use Controllers\ComplaintController;

// normies

$router->post('/api/complaints', [ComplaintController::class, 'createComplaint'], ['auth']);
$router->get('/api/complaints/{complaint_id}', [ComplaintController::class, 'getComplaintsById'], ['auth']);
$router->get('/api/complaints/user/{user_id}', [ComplaintController::class, 'getComplaintByUser'], ['auth']);

// admin things

$router->put('/api/complaints/{complaint_id}', [ComplaintController::class, 'updateComplaintStatusByAdmin'], ['auth', 'admin']);
$router->post('/api/complaints/{complaint_id}', [ComplaintController::class, 'createAdminResponseToComplaint'], ['auth', 'admin']);

// gets

$router->get('/api/complaints', [ComplaintController::class, 'getComplaintsByAdmin'], ['auth', 'admin']);
$router->get('/api/reports/complaint-category', [ComplaintController::class, 'getComplaintsCategoryByAdmin'], ['auth', 'admin']);
$router->get('/api/reports/resolution-status', [ComplaintController::class, 'getResolutionStatusByAdmin'], ['auth', 'admin']);