<?php
require_once __DIR__.'/../../config/Cors.php';
require_once __DIR__.'/../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../utils/Response.php';

$user = AuthMiddleware::requireAuth(['manager']);
Response::json(['success' => true, 'data' => $user]);
