<?php
// memberitahu PHP untuk melaporkan semua jenis error, termasuk warning, notice, dan fatal error.
error_reporting(E_ALL);
// memberitahu PHP untuk menampilkan error di browser (output layar).
ini_set('display_errors', 1);

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_or_email = $_POST['username'];
    $password = $_POST['password'];
    
    $host = 'localhost';
    $dbuser = 'root'; // Sesuaikan dengan username database
    $dbpass = '';     // Sesuaikan dengan password database
    $dbname = 'niflix';

    try {
        // Koneksi database
        $conn = new mysqli($host, $dbuser, $dbpass, $dbname);
        
        if ($conn->connect_error) {
            throw new Exception("Koneksi gagal: " . $conn->connect_error);
        }

        // Cari user berdasarkan username atau email
        $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM user WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // mengecek apakah hasil query dari variable result mengembalikan tepat satu baris.
        // Artinya: hanya ada satu pengguna yang cocok dengan username atau email yang diinput.
        if ($result->num_rows === 1) {
            // Jika ada 1 hasil (artinya user ditemukan), maka ambil datanya dan simpan ke variabel $user.
            $user = $result->fetch_assoc();
            
            // mencocokkan password yang ada di database dengan hasil 1 baris dari variable user
            // Verifikasi password
            // jikalau password benar
            if (password_verify($password, $user['password'])) {
                // maka session dari user akan di set. (data: id, username, is_admin)
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'is_admin' => $user['is_admin'] // AMBIL DATA is_admin DARI DATABASE
                ];
                // kalau sudah di set maka akan ke dashboard
                header('Location: dashboard.php');
                exit();
            }
        }
        
        // Pesan error generik untuk keamanan
        $_SESSION['error'] = 'Login gagal! Username/Email atau password salah.';
        header('Location: login.php');
        exit();

    } catch (Exception $e) {
        $_SESSION['error'] = 'Terjadi kesalahan sistem';
        header('Location: login.php');
        exit();
    } finally {
        // Tutup koneksi
        if (isset($conn)) {
            $conn->close();
        }
    }
}
?>
