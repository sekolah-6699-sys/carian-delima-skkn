<?php
// Nama fail CSV anda
$fail_csv = 'DELIMA-PELAJAR-SEKOLAH_KEBANGSAAN_KAMPUNG_NANGKA-07092026.csv';

$carian_ic = isset($_GET['ic']) ? preg_replace('/[^0-9]/', '', $_GET['ic']) : '';
$hasil = null;
$ralat = '';

if (isset($_GET['ic'])) {
    if (empty($carian_ic) || strlen($carian_ic) < 12) {
        $ralat = 'Sila masukkan 12 digit Nombor Kad Pengenalan / MyKid yang sah.';
    } elseif (!file_exists($fail_csv)) {
        $ralat = 'Fail data murid tidak dijumpai pada server.';
    } else {
        if (($handle = fopen($fail_csv, "r")) !== FALSE) {
            $header = fgetcsv($handle, 1000, ","); // Baca header
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Lajur CSV: [0] ID DELIMa, [1] NAMA, [2] NO KAD PENGENALAN, [3] KELAS
                $ic_rekod = preg_replace('/[^0-9]/', '', $data[2]);
                
                if ($ic_rekod === $carian_ic) {
                    $hasil = [
                        'nama' => htmlspecialchars($data[1]),
                        'kelas' => htmlspecialchars($data[3]),
                        'id_delima' => htmlspecialchars($data[0])
                    ];
                    break;
                }
            }
            fclose($handle);
            
            if (!$hasil) {
                $ralat = 'Rekod tidak dijumpai. Sila pastikan Nombor Kad Pengenalan tepat.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semakan ID DELIMa - SK Kampung Nangka</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f0f2f5; display: flex; justify-content: center; padding: 40px 15px; }
        .card { background: white; width: 100%; max-width: 500px; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { color: #1a73e8; margin: 0 0 5px 0; font-size: 22px; }
        .header p { color: #5f6368; font-size: 14px; margin: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 14px; color: #333; }
        input[type="text"] { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 15px; }
        input[type="text"]:focus { border-color: #1a73e8; outline: none; }
        button { width: 100%; padding: 12px; background-color: #1a73e8; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; }
        button:hover { background-color: #1557b0; }
        .alert-error { background-color: #fce8e6; color: #c5221f; padding: 12px; border-radius: 6px; font-size: 14px; margin-top: 15px; border: 1px solid #fad2cf; }
        .result-box { margin-top: 20px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
        .result-header { background: #e8f0fe; color: #1967d2; font-weight: bold; padding: 10px 15px; font-size: 15px; }
        .result-body { padding: 15px; font-size: 14px; line-height: 1.6; }
        .result-item { margin-bottom: 8px; }
        .result-item strong { color: #5f6368; display: inline-block; width: 110px; }
        .delima-id { color: #1a73e8; font-weight: bold; word-break: break-all; }
        .footer-note { margin-top: 15px; font-size: 12px; color: #70757a; background: #f8f9fa; padding: 10px; border-radius: 6px; border-left: 3px solid #1a73e8; }
    </style>
</head>
<body>

<div class="card">
    <div class="header">
        <h2>Semakan ID DELIMa Murid</h2>
        <p>SK Kampung Nangka</p>
    </div>

    <form method="GET" action="">
        <div class="form-group">
            <label for="ic">Nombor Kad Pengenalan / MyKid:</label>
            <input type="text" id="ic" name="ic" placeholder="Contoh: 190819110399" value="<?php echo htmlspecialchars($carian_ic); ?>" maxlength="12" required>
        </div>
        <button type="submit">Cari ID DELIMa</button>
    </form>

    <?php if (!empty($ralat)): ?>
        <div class="alert-error"><?php echo $ralat; ?></div>
    <?php endif; ?>

    <?php if ($hasil): ?>
        <div class="result-box">
            <div class="result-header">Maklumat Akaun Murid</div>
            <div class="result-body">
                <div class="result-item"><strong>Nama Murid:</strong> <?php echo $hasil['nama']; ?></div>
                <div class="result-item"><strong>Kelas:</strong> <?php echo $hasil['kelas']; ?></div>
                <div class="result-item"><strong>ID DELIMa:</strong> <span class="delima-id"><?php echo $hasil['id_delima']; ?></span></div>
            </div>
        </div>

        <div class="footer-note">
            <strong>Panduan Kata Laluan Laluan (Default):</strong><br>
            Kata laluan laluan murid secara standard ialah kombinasi Kod Sekolah dan 4 digit akhir Kad Pengenalan/MyKid.
        </div>
    <?php endif; ?>
</div>

</body>
</html>