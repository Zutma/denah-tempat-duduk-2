<?php
$graduate = $seat['graduate_name'] ?? null;
$color = $graduate && !empty($seat['faculty_color']) ? $seat['faculty_color'] : '#e2e8f0';
$kode = htmlspecialchars($row['row'] . $seat['number']);
?>
<button type="button" id="seat-<?= $seat['id'] ?>"
    <?php if ($graduate): ?>
    onclick="tampilkanInfo(
        <?= $seat['id'] ?>,
        '<?= $kode ?>',
        '<?= htmlspecialchars(addslashes($seat['graduate_name'])) ?>',
        '<?= htmlspecialchars($seat['nrp']) ?>',
        '<?= htmlspecialchars(addslashes($seat['prodi_name'] . ' - ' . $seat['faculty_name'])) ?>'
    )"
    <?php endif; ?>
    style="width:44px; height:44px; border-radius:6px; border:none; font-size:11px; font-weight:bold;
           background-color: <?= $color ?>; color: <?= $graduate ? 'white' : '#64748b' ?>; cursor:pointer;">
    <?= $kode ?>
</button>