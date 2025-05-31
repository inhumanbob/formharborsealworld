<?php
session_start();

if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
}

$errors = [];
$data = [
    'nama' => '',
    'jk' => '',
    'tempat' => '',
    'tgllahir' => '',
    'email' => '',
    'password' => '',
    'alamat' => '',
    'nohp' => '',
    'captcha' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $key => $val) {
        if ($key !== 'password' && $key !== 'captcha') {
            $data[$key] = isset($_POST[$key]) ? trim($_POST[$key]) : '';
        } else {
            $data[$key] = isset($_POST[$key]) ? $_POST[$key] : '';
        }
    }

    // Validasi Nama
    if ($data['nama'] === '') $errors['nama'] = "Nama lengkap tidak boleh kosong.";

    // Validasi Jenis Kelamin
    if ($data['jk'] === '') $errors['jk'] = "Jenis kelamin tidak boleh kosong.";

    // Validasi TTL
    if ($data['tempat'] === '' || $data['tgllahir'] === '') {
        $errors['ttl'] = "Tempat dan tanggal lahir tidak boleh kosong.";
    } elseif ($data['tgllahir'] > date('Y-m-d')) {
        $errors['ttl'] = "Tanggal lahir tidak boleh lebih dari hari ini.";
    }

    // Validasi Email
    if ($data['email'] === '') {
        $errors['email'] = "Email tidak boleh kosong.";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email tidak valid.";
    }

    // Validasi Password
    if ($data['password'] === '') $errors['password'] = "Password tidak boleh kosong.";

    // Validasi Alamat
    if ($data['alamat'] === '') $errors['alamat'] = "Alamat tidak boleh kosong.";

    // Validasi No HP
    if ($data['nohp'] === '') {
        $errors['nohp'] = "No HP tidak boleh kosong.";
    } elseif (!preg_match("/^08[0-9]{8,11}$/", $data['nohp'])) {
        $errors['nohp'] = "No HP tidak valid. Format: 08xxxxxxxxxx";
    }

    // Validasi Captcha
    if ($data['captcha'] === '') {
        $errors['captcha'] = "Captcha tidak boleh kosong.";
    } elseif (strtoupper($data['captcha']) !== $_SESSION['captcha']) {
        $errors['captcha'] = "Captcha salah.";
    }

    // Jika tidak ada error, tampilkan hasil
    if (empty($errors)) {
        $ucapan = $data['jk'] === "L" ? "Congratulations for your ticket!" : "Congratulations for your ticket!";
        $tgllahir = date('d-m-Y', strtotime($data['tgllahir']));
        echo "<link rel='stylesheet' href='style.css'>";
        echo "<div class='hasil'>";
        echo "<h2>$ucapan " . htmlspecialchars($data['nama']) . "</h2>";
        echo "<p>Tempat/Tgl Lahir: " . htmlspecialchars($data['tempat']) . ", $tgllahir</p>";
        echo "<p>Email: " . htmlspecialchars($data['email']) . "</p>";
        echo "<p>Alamat: " . htmlspecialchars($data['alamat']) . "</p>";
        echo "<p>No HP: " . htmlspecialchars($data['nohp']) . "</p>";
        echo "</div>";
        // Reset captcha agar tidak bisa diulang
        $_SESSION['captcha'] = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Harbor Seal World</title>
    <link rel="icon" type="image/png" href="ison-removebg-preview.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Harbor Seal World Ticket Form</h1>
    <div class="form-box">
        <div class="logo">
            <img src="logobg.png" alt="Logo" width="150"><br>
        </div>
        <div class="form-isi">
            <form method="post" autocomplete="off">
                <label>Nama Lengkap
                    <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>">
                    <?php if (isset($errors['nama'])) echo "<div class='error'>{$errors['nama']}</div>"; ?>
                </label>
                <label>Jenis Kelamin
                    <div class="left-align" style="margin-bottom: 5px;">
                        <input type="radio" name="jk" value="L" <?= $data['jk'] === 'L' ? 'checked' : '' ?>> Laki-laki
                        <input type="radio" name="jk" value="P" <?= $data['jk'] === 'P' ? 'checked' : '' ?>> Perempuan
                    </div>
                    <?php if (isset($errors['jk'])) echo "<div class='error'>{$errors['jk']}</div>"; ?>
                </label>
                <label>Tempat, Tanggal Lahir
                    <div class="ttl-group">
                        <input type="text" name="tempat" class="ttl-input" value="<?= htmlspecialchars($data['tempat']) ?>" placeholder="Tempat">
                        <span>,</span>
                        <input type="date" name="tgllahir" class="ttl-date" value="<?= htmlspecialchars($data['tgllahir']) ?>">
                    </div>
                    <?php if (isset($errors['ttl'])) echo "<div class='error'>{$errors['ttl']}</div>"; ?>
                </label>
                <label>Email
                    <input type="text" name="email" value="<?= htmlspecialchars($data['email']) ?>">
                    <?php if (isset($errors['email'])) echo "<div class='error'>{$errors['email']}</div>"; ?>
                </label>
                <label>Password
                    <input type="password" name="password" value="">
                    <?php if (isset($errors['password'])) echo "<div class='error'>{$errors['password']}</div>"; ?>
                </label>
                <label>Alamat
                    <textarea name="alamat"><?= htmlspecialchars($data['alamat']) ?></textarea>
                    <?php if (isset($errors['alamat'])) echo "<div class='error'>{$errors['alamat']}</div>"; ?>
                </label>
                <label>No.HP
                    <input type="text" name="nohp" value="<?= htmlspecialchars($data['nohp']) ?>">
                    <?php if (isset($errors['nohp'])) echo "<div class='error'>{$errors['nohp']}</div>"; ?>
                </label>
                <label>
                    Inputkan captcha<br>
                    <span class="captcha"><?= $_SESSION['captcha']; ?></span>
                    <input type="text" name="captcha" value="">
                    <?php if (isset($errors['captcha'])) echo "<div class='error'>{$errors['captcha']}</div>"; ?>
                </label>
                <button type="submit" class="submit-btn">SUBMIT</button>
            </form>
        </div>
    </div>
</body>
</html>