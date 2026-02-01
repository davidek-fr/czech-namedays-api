<?php
/**
 * Czech Namedays & Contacts API (Demo Portfolio)
 * Domain: davidek.fr
 */

// 1. Hlavičky pro Swagger a JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

// 2. Připojení k PostgreSQL 
$db_config = [
    'host' => 'your_host', // např. localhost nebo sql.wedos.com
    'db'   => 'your_database_name',
    'user' => 'your_username',
    'pass' => 'YOUR_SECRET_PASSWORD' 
];

try {
    $dsn = "pgsql:host={$db_config['host']};dbname={$db_config['db']}";
    $pdo = new PDO($dsn, $db_config['user'], $db_config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // 3. Zachycení parametrů z URL
    $action = $_GET['action'] ?? 'today';
    $param  = $_GET['param'] ?? '';

    // Pomocná funkce pro transformaci formátu (24.12. -> 12-24)
    function formatToDb($input) {
        if (!$input) return null;
        $clean = rtrim($input, '.');
        $parts = explode('.', $clean);
        if (count($parts) != 2) return null;
        return sprintf("%02d-%02d", (int)$parts[1], (int)$parts[0]);
    }

    // 4. Budování SQL dotazu s LEFT JOINem a agregací kontaktů do JSON pole
    $sql = "SELECT 
                cal.nameday_name, 
                cal.date_key as date,
                COALESCE(
                    json_agg(
                        json_build_object(
                            'name', con.full_name,
                            'email', con.email,
                            'phone', con.phone_number
                        )
                    ) FILTER (WHERE con.id IS NOT NULL), '[]'
                ) as associated_contacts
            FROM naming_calendar cal
            LEFT JOIN contacts con ON cal.date_key = con.nameday_date ";

    $where = "";
    $args = [];

    // Logika filtrů
    switch ($action) {
        case 'specific':
            $where = "WHERE cal.date_key = :val";
            $args['val'] = formatToDb($param);
            break;
        case 'month_full':
            $where = "WHERE cal.date_key LIKE :val || '-%'";
            $args['val'] = sprintf("%02d", (int)$param);
            break;
        case 'month_remaining':
            $where = "WHERE cal.date_key >= TO_CHAR(CURRENT_DATE, 'MM-DD') 
                      AND cal.date_key LIKE TO_CHAR(CURRENT_DATE, 'MM') || '-%'";
            break;
        case 'year':
            $where = ""; // Žádný filtr
            break;
        default: // today
            $where = "WHERE cal.date_key = TO_CHAR(CURRENT_DATE, 'MM-DD')";
    }

    $stmt = $pdo->prepare($sql . $where . " GROUP BY cal.nameday_name, cal.date_key ORDER BY cal.date_key ASC");
    $stmt->execute($args);
    $results = $stmt->fetchAll();

    // Vyčištění JSON výstupu (PostgreSQL vrací z json_agg string, musíme ho v PHP dekódovat pro finální encode)
    foreach ($results as &$row) {
        $row['associated_contacts'] = json_decode($row['associated_contacts']);
    }

    // 5. Finální odeslání dat
    if (empty($results) && $action === 'specific') {
        http_response_code(404);
        echo json_encode(["message" => "No data found for the given parameter."]);
    } else {
        echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Internal Server Error", "details" => $e->getMessage()]);
}