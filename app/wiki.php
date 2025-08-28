<?php include 'auth.php'; ?>
<?php include '_header.php'; ?>

<h2>Wiki Search</h2>
<form method="get">
    <input name="q" placeholder="Search articles..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
    <button type="submit">Search</button>
</form>

<?php
if (isset($_GET['q'])) {
    // Ambil input user dan amankan
    $q = trim($_GET['q']);
    $q = "%".$q."%";

    try {
        // Gunakan prepared statement
        $stmt = $GLOBALS['PDO']->prepare("SELECT title, body FROM articles WHERE title LIKE ?");
        $stmt->execute([$q]);

        // Tampilkan hasil
        if ($stmt->rowCount() > 0) {
            echo "<ul>";
            foreach ($stmt as $row) {
                echo "<li><strong>" . htmlspecialchars($row['title']) . "</strong>: " 
                     . htmlspecialchars($row['body']) . "</li>";
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
?>

<?php include '_footer.php'; ?>
