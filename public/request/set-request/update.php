<?php

require_once __DIR__ . '/../../../lib/bootstrap.php';

use RA\Permissions;

$gameID = seekGET('i');
settype($gameID, 'integer');

if (RA_ReadCookieCredentials($user, $points, $truePoints, $unreadMessageCount, $permissions, Permissions::Registered)) {
    $setRequestList = getUserRequestList($user);
    $totalRequests = getUserRequestsInformation($user, $setRequestList, $gameID);
    $totalRequests['gameRequests'] = getSetRequestCount($gameID);

    $success = toggleSetRequest($user, $gameID, $totalRequests['remaining']);
} else {
    $success = false;
}

echo json_encode([
    'Success' => $success,
]);
