<?php
// koneksi database
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// include header
include __DIR__ . "/../../views/header.php";
?>

<div class="container">
  <h2>📅 Jadwal Kegiatan</h2>
  <div id="calendar"></div>
</div>

<!-- FullCalendar & SweetAlert -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  body { font-family: Arial, sans-serif; }
  .container { max-width: 1000px; margin: 20px auto; }
  #calendar { max-width: 900px; margin: auto; background: #fff; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);}
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      selectable: true,
      editable: true,
      events: 'schedule_load.php', // ambil event dari server

      // tambah event baru
      select: function(info) {
        Swal.fire({
          title: 'Tambah Jadwal',
          input: 'text',
          inputLabel: 'Judul Kegiatan',
          showCancelButton: true,
        }).then((result) => {
          if (result.value) {
            fetch("schedule_add.php", {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: "title=" + encodeURIComponent(result.value) +
                    "&start=" + info.startStr +
                    "&end=" + info.endStr
            }).then(() => {
              calendar.refetchEvents();
            });
          }
        });
      },

      // drag & drop / resize
      eventDrop: function(info) {
        updateEvent(info.event);
      },
      eventResize: function(info) {
        updateEvent(info.event);
      },

      // klik event untuk hapus
      eventClick: function(info) {
        Swal.fire({
          title: 'Hapus Jadwal?',
          text: info.event.title,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Hapus'
        }).then((result) => {
          if (result.isConfirmed) {
            fetch("schedule_delete.php?id=" + info.event.id)
            .then(() => {
              info.event.remove();
            });
          }
        });
      }
    });
    calendar.render();

    function updateEvent(event) {
      fetch("schedule_update.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + event.id +
              "&start=" + event.start.toISOString() +
              "&end=" + (event.end ? event.end.toISOString() : event.start.toISOString())
      });
    }
  });
</script>

<?php include __DIR__ . "/../../views/footer.php"; ?>
