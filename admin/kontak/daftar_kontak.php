<?php
session_start();
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM kontak_mitra ORDER BY id DESC");

// panggil header
include __DIR__ . "/../../views/header.php";
?>

<style>
  body { font-family:"Segoe UI",sans-serif; background:#f5f7fa; color:#333; }
  h2 { text-align:center; margin:15px 0; color:#222; font-size:20px; font-weight:600; }

  .top-bar { display:flex; justify-content:flex-start; align-items:center; gap:10px; margin:0 20px 20px; }
  .btn { padding:7px 14px; border-radius:8px; text-decoration:none; font-size:13px; transition:.2s; }
  .btn.add { background:#4CAF50; color:#fff; } .btn.add:hover{background:#45a049;}
  .btn.edit{ background:#2196F3; color:#fff;} .btn.edit:hover{background:#1976d2;}
  .btn.delete{ background:#f44336; color:#fff;} .btn.delete:hover{background:#d32f2f;}

  .search-box{position:relative; display:inline-block;}
  .search-box input{padding:7px 28px 7px 28px; border:1px solid #ccc; border-radius:8px; font-size:13px; width:180px;}
  .search-box i{position:absolute; top:50%; left:8px; transform:translateY(-50%); color:#888; font-size:14px;}

  .card-container{display:grid; grid-template-columns:repeat(auto-fill, minmax(280px,1fr)); gap:16px; padding:0 20px 40px;}
  .contact-card{background:#fff; border:1px solid #e0e0e0; border-radius:16px; padding:20px; position:relative;
                box-shadow:0 6px 16px rgba(0,0,0,0.1); transition:.2s;}
  .contact-card:hover{transform:translateY(-5px); box-shadow:0 10px 20px rgba(0,0,0,0.15);}
  .contact-card h3{margin:0 0 8px; font-size:18px; color:#111;}
  .contact-card p{margin:4px 0; font-size:14px; color:#555;}
  .card-actions{margin-top:12px;}

  /* Tombol WA di pojok kanan atas */
  .btn-wa {
    position:absolute;
    top:10px; right:10px;
    background:#25D366; color:#fff;
    padding:6px 10px; border-radius:50%;
    font-size:15px; cursor:pointer;
    box-shadow:0 2px 5px rgba(0,0,0,0.2);
    transition:0.2s;
  }
  .btn-wa:hover { background:#1ebe5c; }

  /* Modal */
  .modal {
    display: flex;
    position: fixed;
    z-index: 9999;
    left: 0; top: 0;
    width: 100%; height: 100%;
    justify-content: center;
    align-items: center;
    opacity: 0;
    background: rgba(0,0,0,0);
    pointer-events: none;
    transition: opacity 0.3s ease, background 0.3s ease;
  }
  .modal.show { opacity: 1; background: rgba(0,0,0,0.5); pointer-events: all; }
  .modal-content {
    background:#fff;
    padding:20px;
    border-radius:12px;
    width:350px;
    max-width:90%;
    box-shadow:0 4px 10px rgba(0,0,0,.2);
    transform: translateY(-20px);
    transition: transform 0.3s ease;
  }
  .modal.show .modal-content { transform: translateY(0); }
  .close{float:right; font-size:20px; cursor:pointer; color:#333;}
</style>

<h2>Kontak Mitra</h2>

<div class="top-bar">
  <a href="javascript:void(0)" class="btn add" onclick="openTambah()">+ Tambah Kontak</a>
  <div class="search-box">
    <i class="fas fa-search"></i>
    <input type="text" id="searchInput" placeholder="Cari...">
  </div>
</div>

<div class="card-container" id="kontakContainer">
  <?php while($row = $result->fetch_assoc()): ?>
    <div class="contact-card">
      <!-- Tombol WA -->
      <span class="btn-wa" onclick="shareWA(this)"><i class="fab fa-whatsapp"></i></span>

      <h3><?= htmlspecialchars($row['nama_perusahaan']) ?></h3>
      <p><strong>PIC:</strong> <?= htmlspecialchars($row['nama_pic']) ?></p>
      <p><strong>No. Telp:</strong> <?= htmlspecialchars($row['nomor_telp']) ?></p>
      <?php if (!empty($row['alamat_email'])): ?>
        <p><strong>Email:</strong> <?= htmlspecialchars($row['alamat_email']) ?></p>
      <?php endif; ?>

      <div class="card-actions">
        <a href="javascript:void(0)" class="btn edit" onclick="openEdit(<?= $row['id'] ?>)">Edit</a>
        <a href="delete_kontak.php?id=<?= $row['id'] ?>" class="btn delete"
           onclick="return confirm('Yakin hapus kontak ini?')">Delete</a>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<!-- Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <div id="modalBody">Loading...</div>
  </div>
</div>

<script>
  // search
  const searchInput=document.getElementById("searchInput");
  const cards=document.querySelectorAll(".contact-card");
  searchInput.addEventListener("keyup", function(){
    let filter=searchInput.value.toLowerCase();
    cards.forEach(card=>{
      let text=card.innerText.toLowerCase();
      card.style.display=text.includes(filter)?"block":"none";
    });
  });

  // buka form edit
  function openEdit(id){
    const modal=document.getElementById("editModal");
    const body=document.getElementById("modalBody");
    modal.classList.add("show");
    body.innerHTML="Loading...";

    fetch("edit_kontak.php?id="+id+"&ajax=1")
      .then(res=>res.text())
      .then(html=>{
        body.innerHTML=html;
        const form = document.getElementById("editForm");
        form.addEventListener("submit", function(e){
          e.preventDefault();
          const formData = new FormData(form);
          fetch("edit_kontak.php?id="+id+"&ajax=1", {
            method: "POST",
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if(data.success){
              alert("Data berhasil diperbarui!");
              closeModal();
              location.reload();
            } else {
              alert("Gagal update: " + data.error);
            }
          });
        });
      });
  }

  // buka form tambah
  function openTambah(){
    const modal=document.getElementById("editModal");
    const body=document.getElementById("modalBody");
    modal.classList.add("show");
    body.innerHTML="Loading...";

    fetch("tambah_kontak.php?ajax=1")
      .then(res=>res.text())
      .then(html=>{
        body.innerHTML=html;
        const form = document.getElementById("tambahForm");
        form.addEventListener("submit", function(e){
          e.preventDefault();
          const formData = new FormData(form);
          fetch("tambah_kontak.php?ajax=1", {
            method: "POST",
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if(data.success){
              alert("Kontak berhasil ditambahkan!");
              closeModal();
              location.reload();
            } else {
              alert("Gagal tambah: " + data.error);
            }
          });
        });
      });
  }

  // tutup modal
  function closeModal(){
    const modal=document.getElementById("editModal");
    modal.classList.remove("show");
    setTimeout(()=>{ document.getElementById("modalBody").innerHTML=""; }, 300);
  }

  // tombol WA copy (nama + pic + link wa + email bila ada)
  function shareWA(el){
    let card = el.closest(".contact-card");
    let nama = card.querySelector("h3").innerText;
    let pic = card.querySelector("p:nth-of-type(1)").innerText.replace("PIC: ","").trim();
    let telp = card.querySelector("p:nth-of-type(2)").innerText.replace("No. Telp: ","").trim();
    let emailEl = card.querySelector("p:nth-of-type(3)");
    let email = emailEl ? emailEl.innerText.replace("Email: ","").trim() : "";

    let nomorWA = telp.replace(/^0/, "62");
    let linkWA = "https://wa.me/" + nomorWA;

    let text = `*${nama}* - ${pic}\n${linkWA}`;
    if(email) text += `\n${email}`;

    navigator.clipboard.writeText(text).then(()=>{
      alert("✅ Data WA berhasil disalin:\n" + text);
    }).catch(err=>{
      alert("❌ Gagal menyalin: " + err);
    });
  }
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php echo "</main></body></html>"; ?>
