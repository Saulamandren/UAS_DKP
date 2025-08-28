<?php 
include 'auth.php'; 
include '_header.php'; 
?>

<h2>Wiki Search</h2>
<form method="get">
    <input name="q" placeholder="Search articles..." 
           value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8') : ''; ?>">
    <button type="submit">Search</button>
</form>

<?php
if (isset($_GET['q'])) {
    // Ambil input user dan amankan
    $q = trim($_GET['q']);

    if ($q === "") {
        echo "<p>Please enter a keyword to search.</p>";
    } else {
        $q = "%".$q."%";

        try {
            // Gunakan prepared statement + limit hasil
            $stmt = $GLOBALS['PDO']->prepare("SELECT title, body FROM articles WHERE title LIKE ? LIMIT 20");
            $stmt->execute([$q]);

            // Tampilkan hasil
            if ($stmt->rowCount() > 0) {
                echo "<ul>";
                foreach ($stmt as $row) {
                    $title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
                    $body  = htmlspecialchars(mb_substr($row['body'], 0, 150), ENT_QUOTES, 'UTF-8'); // potong body
                    echo "<li><strong>{$title}</strong>: {$body}...</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No results found.</p>";
            }

        } catch (PDOException $e) {
            // Jangan tampilkan error detail ke user (hindari info leakage)
            error_log("Database error: " . $e->getMessage());
            echo "<p>Something went wrong. Please try again later.</p>";
        }
    }
}
?>

<?php include '_footer.php'; ?>
