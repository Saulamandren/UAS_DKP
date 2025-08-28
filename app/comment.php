<?php 
include 'auth.php'; 
include '_header.php'; 

// 🔹 Pastikan koneksi PDO tersedia
if (!isset($GLOBALS['PDO'])) {
    try {
        $GLOBALS['PDO'] = new PDO("sqlite:".__DIR__."/database.sqlite"); 
        $GLOBALS['PDO']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Koneksi gagal: " . $e->getMessage());
    }
}
?>

<h2>Post comments</h2>
<form method="post">
  <input name="author" placeholder="Name..." required>
  <textarea name="content" placeholder="Comments..." required></textarea>
  <button type="submit">Post</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🔹 Filter input untuk mencegah XSS
    $author  = htmlspecialchars(trim($_POST['author']), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(trim($_POST['content']), ENT_QUOTES, 'UTF-8');

    if (!empty($author) && !empty($content)) {
        $stmt = $GLOBALS['PDO']->prepare(
            "INSERT INTO comments(author, content, created_at) VALUES(:author, :content, datetime('now'))"
        );
        $stmt->execute([
            ':author'  => $author,
            ':content' => $content
        ]);
    }

    // 🔹 Redirect agar form tidak auto-resubmit saat refresh
    header("Location: comment.php");
    exit;
}
?>

<h3>Comment lists :</h3>
<?php
$stmt = $GLOBALS['PDO']->query("SELECT * FROM comments ORDER BY id DESC");
foreach ($stmt as $row) {
    // 🔹 Escape output agar XSS tidak dieksekusi
    $safeAuthor  = htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8');
    $safeContent = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
    echo "<p><b>{$safeAuthor}</b>: {$safeContent}</p>";
}
?>

<?php include '_footer.php'; ?>
