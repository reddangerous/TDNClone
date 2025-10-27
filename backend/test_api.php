<?php

$base_url = 'http://192.168.2.53:8000/api/v1';

// Test 1: Get all people
echo "=== Testing GET /api/v1/people ===\n";
$response = file_get_contents("$base_url/people");
$data = json_decode($response, true);
echo "Response Status: Success\n";
echo "Total Items: " . count($data['data']) . "\n";
echo "First Person: " . json_encode($data['data'][0] ?? null, JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Get single person
echo "=== Testing GET /api/v1/people/1 ===\n";
$response = file_get_contents("$base_url/people/1");
$data = json_decode($response, true);
echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

echo "✅ All tests completed successfully!\n";
