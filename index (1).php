<?php
session_start(); // Mulai session di awal file

// Simulasi validasi login (Anda bisa menggantinya dengan mekanisme autentikasi dari database)
$validUsername = 'admin';
$validPassword = 'danish';

// Cek jika user ingin logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy(); // Hapus semua session
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect ke halaman login tanpa menyebabkan error
    exit;
}

// Cek jika form login di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']); // Menghapus spasi dari input
    $password = trim($_POST['password']); // Menghapus spasi dari input

    // Pastikan input tidak kosong
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi.";
    } else {
        // Validasi login (ganti sesuai kebutuhan)
        if ($username === $validUsername && $password === $validPassword) {
            $_SESSION['loggedin'] = true; // Set session untuk menandakan login berhasil
            header("Location: " . $_SERVER['PHP_SELF']); // Redirect ke halaman utama setelah login
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    }
}

// Cek apakah user sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Jika belum login, tampilkan form login
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elegant Login</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6e00ff, #b16cff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 380px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .login-container h2 {
            text-align: center;
            color: #6e00ff;
            margin-bottom: 30px;
            font-size: 28px;
            letter-spacing: 1px;
        }

        .login-container label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .login-container input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: none;
            border-radius: 6px;
            background-color: #f7f8fa;
            font-size: 16px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .login-container input:focus {
            outline: none;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .login-container button {
            width: 100%;
            background: linear-gradient(135deg, #6e00ff, #b16cff);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }

        .login-container button:hover {
            background: linear-gradient(135deg, #5c00d4, #a451ff);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .login-container .error-message {
            color: #ff4d4d;
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .login-container p {
            text-align: center;
            color: #999;
            font-size: 14px;
        }

        .login-container p a {
            color: #6e00ff;
            text-decoration: none;
            font-weight: 600;
        }

        .login-container p a:hover {
            text-decoration: underline;
        }

        @media (max-width: 400px) {
            .login-container {
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>

    <?php if (isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>

    <form method="POST" action="">
        <label for="username">Username</label>
        <input type="text" name="username" required placeholder="Enter your username">

        <label for="password">Password</label>
        <input type="password" name="password" required placeholder="Enter your password">

        <button type="submit">Login</button>
    </form>

    <p>Forgot your password? <a href="#">Click here</a></p>
</div>

</body>
</html>

<?php
    exit; // Keluar dari eksekusi PHP jika belum login
}

// Setelah login, tampilkan halaman utama dengan tabel data (pagination dan sorting)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "task27";

// Buat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rowsPerPage = 5;
$offset = ($page - 1) * $rowsPerPage;

// Ambil total jumlah data
$sql = "SELECT COUNT(*) as total FROM orders";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$totalRows = $row['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

// Ambil data untuk halaman saat ini
$sql = "SELECT * FROM orders LIMIT $offset, $rowsPerPage";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sortable Table with Pagination</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #5a009d;
            color: white;
        }

        th, td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            cursor: pointer;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .pagination {
            display: flex;
            justify-content: center;
            padding: 10px 0;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 16px;
            text-decoration: none;
            color: white;
            background-color: #6d28d9;
            border-radius: 4px;
        }

        .pagination a.active {
            background-color: #5a009d;
            cursor: default;
        }

        .pagination a.disabled {
            background-color: #ccc;
            pointer-events: none;
        }
    </style>
</head>
<body>

<h2>Sortable Table with Pagination</h2>
<a href="?action=logout">Logout</a> <!-- Tombol logout -->

<table id="orderTable">
    <thead>
        <tr>
            <th onclick="sortTable(0)">Tanggal</th>
            <th onclick="sortTable(1)">Order ID</th>
            <th onclick="sortTable(2)">Nama Produk</th>
            <th onclick="sortTable(3)">Harga</th>
            <th onclick="sortTable(4)">Kuantitas</th>
            <th onclick="sortTable(5)">Total</th>
        </tr>
    </thead>
    <tbody id="orderData">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['tanggal'] . '</td>';
                echo '<td>' . $row['order_id'] . '</td>';
                echo '<td>' . $row['nama_produk'] . '</td>';
                echo '<td>' . $row['harga'] . '</td>';
                echo '<td>' . $row['kuantitas'] . '</td>';
                echo '<td>' . $row['total'] . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">No data found</td></tr>';
        }
        ?>
    </tbody>
</table>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=1">First</a>
        <a href="?page=<?php echo $page - 1; ?>">Previous</a>
    <?php else: ?>
        <a class="disabled">First</a>
        <a class="disabled">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>">Next</a>
        <a href="?page=<?php echo $totalPages; ?>">Last</a>
    <?php else: ?>
        <a class="disabled">Next</a>
        <a class="disabled">Last</a>
    <?php endif; ?>
</div>

<script>
    // Sorting function
    function sortTable(n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById("orderTable");
        switching = true;
        dir = "asc"; // Set default sort direction to ascending
        while (switching) {
            switching = false;
            rows = table.rows;
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("TD")[n];
                y = rows[i + 1].getElementsByTagName("TD")[n];
                if (dir == "asc") {
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == "desc") {
                    if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchcount++;
            } else {
                if (switchcount == 0 && dir == "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }
    }
</script>

</body>
</html>

<?php
// Tutup koneksi
$conn->close();
?>
