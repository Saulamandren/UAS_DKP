<?php
include 'auth.php';

class Profile {
    public $username;
    public $isAdmin = false;

    function __construct($u, $isAdmin = false) {
        $this->username = $u;
        $this->isAdmin = $isAdmin;
    }

    function __toString() {
        return "User: {$this->username}, Role: " . ($this->isAdmin ? "Admin" : "User");
    }
}

if ($_POST) {
    // Mengambil input dengan sanitasi
    $u = htmlspecialchars($_POST['username']);  // Sanitasi input untuk menghindari XSS
    $p = $_POST['password'];

    // Prepared Statement untuk menghindari SQL Injection
    $stmt = $GLOBALS['PDO']->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->bindParam(':username', $u);
    $stmt->bindParam(':password', $p);
    $stmt->execute();

    if ($row = $stmt->fetch()) {
        // Perbaikan: Menyimpan data di session, bukan cookie (lebih aman)
        $_SESSION['user'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        // Menggunakan password hashing
        if (password_verify($p, $row['password'])) {
            $pObj = new Profile($row['username'], $row['role'] === 'admin');
            // Hanya menyimpan ID pengguna dalam cookie, bukan objek yang diserialisasi
            setcookie('profile', $row['username'], time() + 3600, "/", "", true, true); // Dengan flag Secure dan HttpOnly

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Login failed.";
        }
    } else {
        $error = "Login failed.";
    }
}
?>
<?php include '_header.php'; ?>

<h2>Login</h2>
<?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>

<!-- Menambahkan CSRF token -->
<form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <label>Username <input name="username"></label>
    <label>Password <input type="password" name="password"></label>
    <button type="submit">Login</button>
</form>

<?php include '_footer.php'; ?>

<?php
// Generating CSRF token for form
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Membuat token CSRF yang aman
}
?>
