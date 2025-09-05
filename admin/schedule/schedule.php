<?php
session_start();
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// --- PENGHAPUSAN JADWAL OTOMATIS ---
$conn->query("DELETE FROM jadwal_acara WHERE tanggal_acara < DATE_SUB(CURDATE(), INTERVAL 2 DAY)");

// --- LOGIKA PENGELOMPOKAN JADWAL ---
$sql = "SELECT * FROM jadwal_acara ORDER BY tanggal_acara DESC, jam_acara DESC";
$result = $conn->query($sql);

$grouped_schedules = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $date = $row['tanggal_acara'];
        if (!isset($grouped_schedules[$date])) {
            $grouped_schedules[$date] = [];
        }
        $grouped_schedules[$date][] = $row;
    }
}

// --- PENANDA HARI INI ---
$today_date = date('Y-m-d');

// Fungsi untuk memformat tanggal ke format Indonesia
function format_tanggal_indonesia($date_string) {
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $timestamp = strtotime($date_string);
    return $hari[date('w', $timestamp)] . ', ' . date('d', $timestamp) . ' ' . $bulan[(int)date('n', $timestamp)] . ' ' . date('Y', $timestamp);
}


include __DIR__ . "/../../views/header.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Jadwal Acara</title>
    <style>
        :root { 
            --bs-blue-dark: #0a3d62; 
            --bs-gray: #f5f7fa;
            --bs-success: #28a745;
            --bs-warning-bg: #fff3cd;
            --bs-warning-text: #664d03;
        }
        .main-container { background-color: white; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 2rem; margin-top: -4rem; position: relative; z-index: 2; }
        .page-title { color: var(--bs-blue-dark); font-weight: 700; }
        .controls-section { background-color: var(--bs-gray); border-radius: 0.75rem; padding: 1.5rem; }
        .modal-header { background: linear-gradient(135deg, var(--bs-blue-dark) 0%, #3c6382 100%); color: white; }
        
        .schedule-timeline { max-width: 900px; margin: 0 auto; padding: 20px 10px; }
        
        .date-separator {
            text-align: center; margin: 40px 0 15px;
        }
        .date-separator span {
            display: inline-block; background-color: #e9ecef; color: #6c757d;
            padding: 5px 15px; border-radius: 20px; font-weight: 600; font-size: 0.9em;
        }
        .date-separator.today-reminder span {
            background-color: var(--bs-warning-bg); color: var(--bs-warning-text);
            border: 1px solid rgba(0,0,0,0.1); font-size: 1em; padding: 8px 20px;
        }
        .date-separator.today-reminder i { margin-right: 8px; }

        .bubble-container { display: flex; margin: 15px 0; animation: slideUp 0.5s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .schedule-bubble { max-width: 65%; padding: 12px 18px; border-radius: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .bubble-container.left { justify-content: flex-start; }
        .bubble-container.left .schedule-bubble { background-color: #f8f9fa; border-bottom-left-radius: 5px; }
        .bubble-container.right { justify-content: flex-end; }
        .bubble-container.right .schedule-bubble { background-color: #e2ffc7; border-bottom-right-radius: 5px; }

        .bubble-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; margin-bottom: 10px; }
        .bubble-time { font-weight: 700; color: var(--bs-blue-dark); }
        .bubble-actions .btn { opacity: 0; transition: opacity 0.2s; }
        .schedule-bubble:hover .bubble-actions .btn { opacity: 1; }
        .btn-wa { background-color: #25D366; color: white; }
        .btn-wa:hover { background-color: #1DAE54; }

        .bubble-title { font-weight: 600; font-size: 1.1em; margin-bottom: 5px; }
        .bubble-place { font-size: 0.95em; color: #555; display: flex; align-items: center; gap: 5px; }
        .bubble-place i { color: var(--bs-blue-dark); }
        
        .bubble-agenda { font-size: 0.9em; color: #333; margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(0,0,0,0.08); white-space: pre-wrap; }
        .bubble-keterangan { font-size: 0.85em; color: #6c757d; font-style: italic; margin-top: 8px; padding-top: 8px; border-top: 1px dotted rgba(0,0,0,0.1); white-space: pre-wrap; }
        
        .toast-notification { position: fixed; top: 20px; right: 20px; background-color: var(--bs-success); color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); z-index: 1060; opacity: 0; transform: translateY(-20px); transition: opacity 0.3s ease, transform 0.3s ease; font-weight: 500; }
        .toast-notification.show { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="main-container">
        <div class="text-center mb-4">
            <h1 class="page-title"><i class="bi bi-calendar-heart"></i> Linimasa Jadwal</h1>
        </div>
        <div class="controls-section mb-4">
            <p class="text-center mb-0">Semua jadwal acara ditampilkan secara kronologis. Gunakan tombol di bawah untuk menambahkan jadwal baru.</p>
            <div class="mt-3 text-center">
                 <button class="btn btn-primary" onclick="openTambah()">
                    <i class="bi bi-plus-circle"></i> Tambah Jadwal Baru
                </button>
            </div>
        </div>

        <div class="schedule-timeline">
            <?php if (empty($grouped_schedules)): ?>
                <div class="text-center p-5"><p class="text-muted">Belum ada jadwal yang ditambahkan.</p></div>
            <?php else: ?>
                <?php $date_group_counter = 0; ?>
                <?php foreach ($grouped_schedules as $date => $schedules_on_day): ?>
                    
                    <?php
                        $is_today = ($date == $today_date);
                        $reminder_class = $is_today ? 'today-reminder' : '';
                        $reminder_text = $is_today ? 'HARI INI' : format_tanggal_indonesia($date);
                        $reminder_icon = $is_today ? '<i class="bi bi-bell-fill"></i>' : '';
                    ?>

                    <div class="date-separator <?= $reminder_class ?>">
                        <span data-date-raw="<?= $date ?>"><?= $reminder_icon ?> <?= $reminder_text ?></span>
                    </div>

                    <?php $side = ($date_group_counter % 2 == 0) ? 'left' : 'right'; ?>
                    <div class="day-frame">
                        <?php foreach ($schedules_on_day as $schedule): ?>
                            <div class="bubble-container <?= $side ?>">
                                <div class="schedule-bubble">
                                    <div class="bubble-header">
                                        <span class="bubble-time"><?= date('H:i', strtotime($schedule['jam_acara'])) ?> WIB</span>
                                        <div class="bubble-actions">
                                            <button class="btn btn-sm btn-wa" title="Salin Info Jadwal" onclick="copyScheduleInfo(this)"><i class="bi bi-clipboard-check"></i></button>
                                            <button class="btn btn-sm btn-outline-primary" title="Edit" onclick="openEdit(<?= $schedule['id'] ?>)"><i class="bi bi-pencil-fill"></i></button>
                                            <button class="btn btn-sm btn-outline-danger" title="Hapus" onclick="openDeleteConfirm(<?= $schedule['id'] ?>, '<?= htmlspecialchars($schedule['judul_acara'], ENT_QUOTES) ?>')"><i class="bi bi-trash-fill"></i></button>
                                        </div>
                                    </div>
                                    <div class="bubble-body">
                                        <h5 class="bubble-title"><?= htmlspecialchars($schedule['judul_acara']) ?></h5>
                                        <p class="bubble-place"><i class="bi bi-geo-alt-fill"></i><?= htmlspecialchars($schedule['tempat']) ?></p>
                                        <?php if (!empty($schedule['agenda'])): ?>
                                            <p class="bubble-agenda"><?= nl2br(htmlspecialchars($schedule['agenda'])) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($schedule['keterangan'])): ?>
                                            <p class="bubble-keterangan"><?= nl2br(htmlspecialchars($schedule['keterangan'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php $date_group_counter++; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Form dan Hapus -->
<div class="modal fade" id="formModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="formModalLabel"></h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body" id="modalBodyContent"></div></div></div></div>
<div class="modal fade" id="deleteConfirmModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body">Apakah Anda yakin ingin menghapus jadwal <strong id="scheduleNameToDelete"></strong>?</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="button" id="confirmDeleteBtn" class="btn btn-danger">Ya, Hapus</button></div></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast-notification show';
        toast.textContent = '✅ ' + message;
        document.body.appendChild(toast);
        setTimeout(() => { toast.classList.remove('show'); toast.addEventListener('transitionend', () => toast.remove()); }, 3000);
    }
    
    // ... (Sisa JavaScript: handleFormSubmit, openTambah, fetchScheduleData, openEdit, openDeleteConfirm tetap sama) ...
    function handleFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        fetch(form.action, { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    formModal.hide();
                    showToast(data.message || 'Operasi berhasil!');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Error: ' + (data.error || 'Terjadi kesalahan.'));
                }
            });
    }
    function openTambah() {
        document.getElementById('formModalLabel').innerHTML = '<i class="bi bi-plus-circle"></i> Tambah Jadwal Baru';
        const modalBody = document.getElementById('modalBodyContent');
        modalBody.innerHTML = `
            <form id="scheduleForm" action="schedule_add.php" method="POST">
                <div class="mb-3"><label class="form-label">Judul Acara*</label><input type="text" name="judul_acara" class="form-control" required></div>
                <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Tanggal*</label><input type="date" name="tanggal_acara" class="form-control" required></div><div class="col-md-6 mb-3"><label class="form-label">Jam*</label><input type="time" name="jam_acara" class="form-control" required></div></div>
                <div class="mb-3"><label class="form-label">Tempat*</label><input type="text" name="tempat" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Agenda</label><textarea name="agenda" class="form-control" rows="3"></textarea></div>
                <div class="mb-3"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control" rows="2"></textarea></div>
                <div class="text-end"><button type="submit" class="btn btn-primary">Simpan Jadwal</button></div>
            </form>
        `;
        document.getElementById('scheduleForm').addEventListener('submit', handleFormSubmit);
        formModal.show();
    }
    async function fetchScheduleData(id) {
        try {
            const response = await fetch(`schedule_get.php?id=${id}`);
            const data = await response.json();
            if (!response.ok || data.error) {
                throw new Error(data.error || `HTTP error! Status: ${response.status}`);
            }
            return data;
        } catch (error) {
            console.error("Fetch Error:", error);
            return { fetchError: error.message };
        }
    }
    async function openEdit(id) {
        document.getElementById('formModalLabel').innerHTML = '<i class="bi bi-pencil-square"></i> Edit Jadwal';
        const modalBody = document.getElementById('modalBodyContent');
        modalBody.innerHTML = '<p class="text-center">Memuat data...</p>';
        formModal.show();
        const data = await fetchScheduleData(id);
        if (data && !data.fetchError) {
            modalBody.innerHTML = `
                <form id="scheduleForm" action="schedule_update.php" method="POST">
                    <input type="hidden" name="id" value="${data.id}">
                    <div class="mb-3"><label class="form-label">Judul Acara*</label><input type="text" name="judul_acara" class="form-control" value="${data.judul_acara}" required></div>
                    <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Tanggal*</label><input type="date" name="tanggal_acara" class="form-control" value="${data.tanggal_acara}" required></div><div class="col-md-6 mb-3"><label class="form-label">Jam*</label><input type="time" name="jam_acara" class="form-control" value="${data.jam_acara}" required></div></div>
                    <div class="mb-3"><label class="form-label">Tempat*</label><input type="text" name="tempat" class="form-control" value="${data.tempat}" required></div>
                    <div class="mb-3"><label class="form-label">Agenda</label><textarea name="agenda" class="form-control" rows="3">${data.agenda || ''}</textarea></div>
                    <div class="mb-3"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control" rows="2">${data.keterangan || ''}</textarea></div>
                    <div class="text-end"><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                </form>
            `;
            document.getElementById('scheduleForm').addEventListener('submit', handleFormSubmit);
        } else {
            const errorMessage = data ? data.fetchError : "Gagal memuat data.";
            modalBody.innerHTML = `<p class="text-center text-danger"><strong>Gagal Memuat:</strong><br>${errorMessage}</p>`;
        }
    }
    function openDeleteConfirm(id, scheduleName) {
        document.getElementById('scheduleNameToDelete').textContent = scheduleName;
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        confirmBtn.onclick = () => {
            fetch('schedule_delete.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    deleteConfirmModal.hide();
                    showToast('Jadwal berhasil dihapus!');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Error: ' + (data.error || 'Gagal menghapus jadwal.'));
                }
            });
        };
        deleteConfirmModal.show();
    }

    /**
     * Fungsi yang diperbarui untuk menyalin info jadwal ke clipboard.
     * @param {HTMLElement} buttonElement - Tombol yang diklik.
     */
    function copyScheduleInfo(buttonElement) {
        const bubble = buttonElement.closest('.schedule-bubble');
        const dayFrame = buttonElement.closest('.day-frame');
        const dateSeparator = dayFrame.previousElementSibling;

        const judul = bubble.querySelector('.bubble-title').textContent.trim();
        const waktu = bubble.querySelector('.bubble-time').textContent.trim();
        const tempat = bubble.querySelector('.bubble-place').textContent.trim();
        const tanggalText = dateSeparator.querySelector('span').textContent.replace('HARI INI', '').trim();
        
        const agendaEl = bubble.querySelector('.bubble-agenda');
        const agenda = agendaEl ? agendaEl.textContent.trim() : null;
        
        const keteranganEl = bubble.querySelector('.bubble-keterangan');
        const keterangan = keteranganEl ? keteranganEl.textContent.trim() : null;

        // Susun pesan dengan format yang rapi untuk WA
        let message = `*INFORMASI JADWAL*\n\n`;
        message += `*Judul:*\n${judul}\n\n`;
        message += `*Tanggal:*\n${tanggalText}\n\n`;
        message += `*Waktu:*\n${waktu}\n\n`;
        message += `*Tempat:*\n${tempat}\n\n`;

        if (agenda) {
            message += `*Agenda:*\n${agenda}\n\n`;
        }
        if (keterangan) {
            message += `*Keterangan:*\n${keterangan}\n`;
        }
        
        // Gunakan Clipboard API untuk menyalin pesan
        navigator.clipboard.writeText(message).then(() => {
            // Tampilkan notifikasi toast setelah berhasil menyalin
            showToast('Info jadwal berhasil disalin!');
        }).catch(err => {
            console.error('Gagal menyalin ke clipboard:', err);
            alert('Gagal menyalin info.');
        });
    }
</script>
</body>
</html>

