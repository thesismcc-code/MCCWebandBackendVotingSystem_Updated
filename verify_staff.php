<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$db  = app(\Kreait\Firebase\Contract\Database::class);
$pw  = \Illuminate\Support\Facades\Hash::make('password123');
$now = now()->toIso8601String();

$staff = [
    'ADMaB3kL9mNpQr' => [
        'id' => 'ADMaB3kL9mNpQr', 'first_name' => 'Admin', 'middle_name' => '',
        'last_name' => 'MCC', 'email' => 'admin@thesismcc.com',
        'role' => 'admin', 'admin_id' => 'ADMaB3kL9mNpQr',
        'student_id' => null, 'teacher_id' => null,
    ],
    'SAOxK7wP2dYcHj' => [
        'id' => 'SAOxK7wP2dYcHj', 'first_name' => 'SAO', 'middle_name' => '',
        'last_name' => 'MCC', 'email' => 'sao@thesismcc.com',
        'role' => 'sao', 'admin_id' => null,
        'student_id' => null, 'teacher_id' => 'SAOxK7wP2dYcHj',
    ],
    'THRmN4vZ8qEtWs' => [
        'id' => 'THRmN4vZ8qEtWs', 'first_name' => 'Comelec', 'middle_name' => '',
        'last_name' => 'MCC', 'email' => 'comelec@thesismcc.com',
        'role' => 'comelec', 'admin_id' => null,
        'student_id' => null, 'teacher_id' => 'THRmN4vZ8qEtWs',
    ],
];

foreach ($staff as $id => $data) {
    $db->getReference("users/{$id}")->set(array_merge($data, [
        'password'          => $pw,
        'is_deleted'        => false,
        'email_verified_at' => $now,
        'created_at'        => $now,
        'updated_at'        => $now,
    ]));
    echo "✓ Set: {$data['role']} | {$data['email']} | password123\n";
}

echo "\nDone.\n";
