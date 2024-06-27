<?php
echo json_encode([
    'status' => 'ok',
    'timestamp' => date('U'),
]);

function myFunc(int $param1, string $param2 = 'default'): array
{
    return [$param1, $param2];
}

$r = myFunc(1, 'default');
