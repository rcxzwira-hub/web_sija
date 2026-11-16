<?php
// 1. Koneksi langsung ke Database XAMPP
$host = "localhost";
$user = "root";
$pass = "";
$db   = "banyu database";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// 2. Proses simpan data ke tabel 'siswa'
if (isset($_POST['simpan'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $kelas    = mysqli_real_escape_string($conn, $_POST['kelas']);
    $keahlian = mysqli_real_escape_string($conn, $_POST['keahlian']);
    $project  = mysqli_real_escape_string($conn, $_POST['project']);

    $query = "INSERT INTO siswa (Nama, Kelas, Keahlian, Project) 
              VALUES ('$nama', '$kelas', '$keahlian', '$project')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: index.php?status=sukses");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// 3. Proses hapus data dari tabel 'siswa'
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM siswa WHERE id = $id");
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Data Praktikum SIJA</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #0f172a; color: #e2e8f0; margin: 0; padding: 30px 15px; }
        .container { max-width: 950px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #38bdf8; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #38bdf8; font-size: 28px; }
        .header p { margin: 5px 0 0; color: #94a3b8; }
        .card { background-color: #1e293b; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 25px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; color: #cbd5e1; }
        input, select { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #334155; background-color: #0f172a; color: #fff; font-size: 14px; }
        input:focus, select:focus { outline: none; border-color: #38bdf8; }
        .btn-submit { background-color: #0284c7; color: white; border: none; padding: 12px 20px; font-size: 15px; font-weight: bold; border-radius: 6px; cursor: pointer; width: 100%; transition: 0.2s; }
        .btn-submit:hover { background-color: #0369a1; }
        .alert { background-color: #065f46; color: #a7f3d0; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #334155; font-size: 14px; }
        th { background-color: #0f172a; color: #38bdf8; }
        tr:hover { background-color: #263349; }
        .badge { background-color: #334155; color: #38bdf8; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .waktu { color: #94a3b8; font-size: 12px; }
        .btn-hapus { color: #f87171; text-decoration: none; font-weight: bold; font-size: 13px; }
        .btn-hapus:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>💻 Portal Data Siswa & Praktikum SIJA</h1>
        <p>Database: <strong>banyu database</strong> | Tabel: <strong>siswa</strong></p>
    </div>

    <div class="card">
        <h3 style="margin-top:0; color:#38bdf8;">+ Input Data Anak SIJA</h3>
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
            <div class="alert">✅ Data berhasil disimpan ke tabel siswa di phpMyAdmin!</div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Masukkan nama lengkap..." required>
            </div>

            <div class="form-group">
                <label>Kelas</label>
                <input type="text" name="kelas" placeholder="Contoh: X SIJA 2" required>
            </div>

            <div class="form-group">
                <label>Fokus Keahlian</label>
                <select name="keahlian" required>
                    <option value="">-- Pilih Fokus Keahlian --</option>
                    <option value="Network Engineer">Network Engineer (Router & Switching)</option>
                    <option value="Linux System Administrator">Linux System Administrator (Server)</option>
                    <option value="Fullstack Web Developer">Fullstack Web Developer (PHP/JS)</option>
                    <option value="Cloud & IoT Engineer">Cloud Computing & IoT</option>
                </select>
            </div>

            <div class="form-group">
                <label>Project / Praktikum</label>
                <input type="text" name="project" placeholder="Contoh: Deploy Web Server Ubuntu / Subnetting" required>
            </div>

            <button type="submit" name="simpan" class="btn-submit">Simpan ke Database</button>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-top:0; color:#38bdf8;">📋 Daftar Data Tersimpan</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Keahlian</th>
                    <th>Project</th>
                    <th>Waktu Input</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $ambil_data = mysqli_query($conn, "SELECT * FROM siswa ORDER BY id DESC");
                
                if ($ambil_data && mysqli_num_rows($ambil_data) > 0) {
                    while ($row = mysqli_fetch_assoc($ambil_data)) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td><strong>" . htmlspecialchars($row['Nama']) . "</strong></td>";
                        echo "<td>" . htmlspecialchars($row['Kelas']) . "</td>";
                        echo "<td><span class='badge'>" . htmlspecialchars($row['Keahlian']) . "</span></td>";
                        echo "<td>" . htmlspecialchars($row['Project']) . "</td>";
                        echo "<td class='waktu'>" . htmlspecialchars($row['Waktu_input']) . "</td>";
                        echo "<td><a class='btn-hapus' href='index.php?hapus=" . $row['id'] . "' onclick='return confirm(\"Hapus data ini?\")'>Hapus</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center; color:#64748b;'>Belum ada data. Silakan isi form di atas!</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>