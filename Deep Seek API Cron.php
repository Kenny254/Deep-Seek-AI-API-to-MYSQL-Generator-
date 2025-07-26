<?php
// ✅ Increase execution time & memory limit for cron job
ini_set('max_execution_time', 150); // 150 seconds
ini_set('memory_limit', '512M');    // Increase memory for large API responses

// ======================
// ✅ Database Connection
// ======================
$db_host = "localhost";
$db_user = "DB_USERNAME";
$db_pass = "DB_PASSWORD";
$db_name = "DB_NAME";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ======================
// ✅ DeepSeek API Config
// ======================
$api_key = "YOUR_DEEPSEEK_API_KEY";
$api_url = "https://api.deepseek.com/v1/chat/completions";

// ✅ Categories to Fetch
$categories = ["Technology", "Politics", "Medicine", "Business", "Education", "Entertainment"];

// ✅ Function: Fetch News from DeepSeek
function getNewsFromDeepSeek($api_url, $api_key, $category) {
    $postData = [
        "model" => "deepseek-chat",
        "messages" => [
            ["role" => "system", "content" => "You are a professional $category news writer."],
            ["role" => "user", "content" =>
                "Generate 3 unique and fresh $category news articles. 
                 Each should have a catchy title (first line) followed by 3-5 paragraphs of content. 
                 Focus on current $category trends in Kenya. 
                 Make sure they are different from previous ones. Current time: " . date("Y-m-d H:i:s")]
        ],
        "max_tokens" => 800,
        "temperature" => 0.9,
        "top_p" => 0.95
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $api_key",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        return false;
    }

    $data = json_decode($response, true);
    return $data["choices"][0]["message"]["content"] ?? false;
}

// ======================
// ✅ Fetch & Insert News
// ======================
$summary = [];
foreach ($categories as $category) {
    $deepseek_response = getNewsFromDeepSeek($api_url, $api_key, $category);

    if ($deepseek_response) {
        $articles = preg_split("/\n\s*\n/", trim($deepseek_response));
        $inserted = 0;
        $skipped = 0;

        foreach ($articles as $article) {
            $lines = explode("\n", trim($article));
            $title = trim($lines[0]);

            // ✅ Optional: Remove prefix/suffix if needed
            $title = trim($title);

            $content = trim(implode("\n", array_slice($lines, 1)));

            if (empty($title) || empty($content)) continue;

            // ✅ Check for duplicate
            $check_stmt = $conn->prepare("SELECT id FROM gonews WHERE title = ?");
            $check_stmt->bind_param("s", $title);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows == 0) {
                $slug = strtolower(str_replace(" ", "-", preg_replace("/[^a-zA-Z0-9 ]/", "", $title))) . "-" . time();
                $author_id = 1;
                $featured_image = "assets/card.jpeg";
                $is_top = rand(0, 1);
                $status = "published";

                $stmt = $conn->prepare("INSERT INTO gonews (category, title, slug, content, author_id, featured_image, is_top, status) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssisis", $category, $title, $slug, $content, $author_id, $featured_image, $is_top, $status);

                if ($stmt->execute()) {
                    $inserted++;
                }
                $stmt->close();
            } else {
                $skipped++;
            }
            $check_stmt->close();
        }

        $summary[] = "✅ [$category] Inserted: $inserted | Skipped: $skipped";
    } else {
        $summary[] = "❌ [$category] Failed to fetch news.";
    }
}

// ======================
// ✅ Log Summary
// ======================
$log_message = "[" . date("Y-m-d H:i:s") . "] " . implode(" | ", $summary) . "\n";
file_put_contents(__DIR__ . "/cron_news.log", $log_message, FILE_APPEND);

echo $log_message;
?>
