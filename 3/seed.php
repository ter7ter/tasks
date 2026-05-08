<?php
// seed.php
require_once __DIR__ . "/config.php";
require_once __DIR__ . '/Database.php';

use Counter\Database;

$db = Database::getInstance();

$cities = ['Moscow', 'London', 'Paris', 'New York', 'Tokyo', 'Berlin', 'Minsk', 'Kiev', 'Rostov-on-Don', 'Vladivostok', 'Vladimir', 'Samara'];
$countries = ['Russia', 'UK', 'France', 'USA', 'Japan', 'Germany', 'Belarus'];
$user_agents = [
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',
    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',
];


$stmt = $db->getPdo()->prepare("
    INSERT INTO visits (ip, city, country, user_agent, url, visited_at)
    VALUES (INET_ATON(:ip), :city, :country, :user_agent, :url, :visited_at)
");

$numberOfEntries = 500;
echo "Inserting $numberOfEntries entries...
";

for ($i = 0; $i < $numberOfEntries; $i++) {
    $ip = long2ip(rand(0, 4294967295));
    $city = $cities[array_rand($cities)];
    $country = $countries[array_rand($countries)];
    $user_agent = $user_agents[array_rand($user_agents)];
    $url = 'http://example.com/page1';


    $timestamp = time() - rand(0, 30 * 24 * 60 * 60);
    $visited_at = date('Y-m-d H:i:s', $timestamp);

    $stmt->execute([
        ':ip' => $ip,
        ':city' => $city,
        ':country' => $country,
        ':user_agent' => $user_agent,
        ':url' => $url,
        ':visited_at' => $visited_at,
    ]);
    
    if (($i + 1) % 50 == 0) {
        echo "Inserted " . ($i + 1) . " entries.
";
    }
}

echo "Done.
";
