<?php
if (!file_exists('uploads')) mkdir('uploads', 0777, true);

// Helper to format file sizes
function formatSize($bytes) {
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2).' GB';
    elseif ($bytes >= 1048576) return number_format($bytes / 1048576, 2).' MB';
    elseif ($bytes >= 1024) return number_format($bytes / 1024, 2).' KB';
    elseif ($bytes > 1) return $bytes.' bytes';
    elseif ($bytes == 1) return '1 byte';
    else return '0 bytes';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>My File Cloud</title>
<style>
/* Base styles (mobile preserved) */
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #007bff, #00d4ff);
  margin: 0;
  padding: 0;
}
.header {
  text-align: center;
  padding: 15px;
  color: #fff;
  font-size: 20px;
  font-weight: 600;
  letter-spacing: 0.5px;
}
/* Container sizes adjust for tablet/laptop - mobile unchanged */
.container {
  width: 90%;
  max-width: 1000px;
  margin: 12px auto 40px;
  background: #fff;
  padding: 22px;
  border-radius: 14px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.10);
}
.drop-zone {
  border: 3px dashed #007bff;
  border-radius: 12px;
  text-align: center;
  padding: 42px;
  color: #007bff;
  cursor: pointer;
  transition: 0.18s;
  font-size: 15px;
  background: #f8fbff;
}
.drop-zone.dragover {
  background: #e6f2ff;
  border-color: #0056b3;
}
/* progress box style (option 2: full progress box with percent) */
.upload-status {
  margin-top: 12px;
  display: none;
  align-items: center;
  gap: 12px;
}
.progress-box {
  background: #f1f5f9;
  border-radius: 8px;
  padding: 10px 12px;
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 180px;
  justify-content: space-between;
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.6);
}
.progress-bar {
  height: 10px;
  width: 220px;
  background: #e6eefc;
  border-radius: 6px;
  overflow: hidden;
  margin-right: 12px;
}
.progress-fill {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, #4ade80, #16a34a);
  transition: width 0.12s linear;
}
.progress-text {
  min-width: 60px;
  font-weight: 600;
  color: #0f172a;
  text-align: right;
}
.time-text {
  font-size: 13px;
  color: #374151;
  margin-left: 8px;
}
/* file grid */
.file-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 14px;
  margin-top: 20px;
}
.file {
  background: #f8f9fa;
  border-radius: 10px;
  padding: 12px;
  text-align: center;
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
  transition: transform 0.16s;
}
.file:hover { transform: translateY(-3px); box-shadow: 0 6px 12px rgba(0,0,0,0.10); }
.file-name { font-weight: 600; font-size: 14px; word-break: break-all; color: #111827; }
.file-size { font-size: 12px; color: #6b7280; margin: 6px 0; }
.actions { display:flex; justify-content:center; gap:8px; flex-wrap:wrap; }
.actions a, .actions button {
  text-decoration:none;
  padding:7px 12px;
  border-radius:6px;
  font-size:13px;
  color:#fff;
  border:none;
  cursor:pointer;
}
.download { background:#16a34a; }
.delete { background:#dc2626; }

/* Mobile optimization - keep mobile look same as before */
@media (max-width:600px){
  .header { font-size:20px; padding:12px; }
  .container { padding:14px; margin:10px; }
  .drop-zone { padding:28px; font-size:14px; }
  .file-list { grid-template-columns: 1fr 1fr; gap:10px; }
  .file { padding:10px; }
  .file-name { font-size:13px; }
}

@media (max-width: 400px) {
  .file-list { grid-template-columns: 1fr; }
.container {
    width: 87.4%;
}

/* Tablet */
@media (min-width:601px) and (max-width:992px) {
  .container { max-width:860px; padding:22px; }
  .drop-zone { padding:38px; font-size:15px; }
  .file-list { grid-template-columns: repeat(3, 1fr); }
}

/* Laptop/desktop */
@media (min-width:993px) {
  .container { max-width:1000px; padding:30px; }
  .drop-zone { padding:46px; font-size:16px; }
  .file-list { grid-template-columns: repeat(4, 1fr); }
}
</style>
</head>
<body>
<div class="header">☁ My File Cloud</div>
<div class="container">
  <div id="dropZone" class="drop-zone" role="button" tabindex="0">
    📂 <strong>Drag & Drop</strong> files here or click to upload
    <input type="file" id="fileInput" multiple style="display:none;" />
  </div>

  <div id="uploadStatus" class="upload-status" aria-live="polite">
    <div class="progress-box">
      <div style="display:flex; align-items:center;">
        <div class="progress-bar" aria-hidden="true"><div id="progressFill" class="progress-fill"></div></div>
        <div id="progressText" class="progress-text">0%</div>
      </div>
      <div>
        <div id="timeText" class="time-text">—</div>
      </div>
    </div>
  </div>

  <h3 style="margin-top:20px;">Available Files</h3>
  <div class="file-list" id="fileList">
    <?php
      $files = array_diff(scandir("uploads"), ['.', '..']);
      if (!$files) echo "<p style='text-align:center;'>No files uploaded yet.</p>";
      foreach ($files as $file) {
        $path = "uploads/$file";
        $size = formatSize(filesize($path));
        $safeFile = rawurlencode($file);
        echo "<div class='file'>
                <div class='file-name'>".htmlspecialchars($file)."</div>
                <div class='file-size'>📦 $size</div>
                <div class='actions'>
                  <a class='download' href='download.php?file={$safeFile}'>Download</a>
                  <button class='delete' data-file='".htmlspecialchars($file)."'>Delete</button>
                </div>
              </div>";
      }
    ?>
  </div>
</div>

<script>
// Drag & drop + click + AJAX upload with progress and elapsed time
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const uploadStatus = document.getElementById('uploadStatus');
const progressFill = document.getElementById('progressFill');
const progressText = document.getElementById('progressText');
const timeText = document.getElementById('timeText');

dropZone.addEventListener('click', () => fileInput.click());
dropZone.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') fileInput.click(); });

dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', e => { dropZone.classList.remove('dragover'); });
dropZone.addEventListener('drop', e => { e.preventDefault(); dropZone.classList.remove('dragover'); handleFiles(e.dataTransfer.files); });

fileInput.addEventListener('change', e => handleFiles(e.target.files));

function handleFiles(files) {
  // upload one by one sequentially
  const fileArray = Array.from(files);
  uploadNext(fileArray, 0);
}

function uploadNext(files, index) {
  if (index >= files.length) {
    // all done; small delay then reload to show files
    setTimeout(() => { location.reload(); }, 700);
    return;
  }
  const file = files[index];
  uploadFile(file, () => uploadNext(files, index+1));
}

function uploadFile(file, callback) {
  const xhr = new XMLHttpRequest();
  const form = new FormData();
  form.append('file', file);

  xhr.upload.addEventListener('loadstart', () => {
    uploadStatus.style.display = 'flex';
    progressFill.style.width = '0%';
    progressText.textContent = '0%';
    timeText.textContent = 'Uploading...';
    // start timer
    window._uploadStartTime = performance.now();
  });

  xhr.upload.addEventListener('progress', (e) => {
    if (e.lengthComputable) {
      const percent = Math.round((e.loaded / e.total) * 100);
      progressFill.style.width = percent + '%';
      progressText.textContent = percent + '%';
      const elapsed = (performance.now() - window._uploadStartTime) / 1000;
      timeText.textContent = 'Elapsed: ' + elapsed.toFixed(2) + 's';
    }
  });

  xhr.upload.addEventListener('load', () => {
    // done uploading to server, but waiting server response
  });

  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4) {
      try {
        const res = JSON.parse(xhr.responseText || '{}');
        const elapsedTotal = (performance.now() - window._uploadStartTime) / 1000;
        if (res.success) {
          progressFill.style.width = '100%';
          progressText.textContent = '100%';
          timeText.textContent = 'Uploaded in ' + elapsedTotal.toFixed(2) + 's';
        } else {
          progressText.textContent = 'Error';
          timeText.textContent = res.error || 'Upload failed';
          progressFill.style.background = '#ef4444';
        }
      } catch (err) {
        progressText.textContent = 'Error';
        timeText.textContent = 'Invalid server response';
        progressFill.style.background = '#ef4444';
      }
      // small delay before next upload
      setTimeout(() => { callback(); }, 700);
    }
  };

  xhr.open('POST', 'upload.php', true);
  xhr.send(form);
}

// Wire up delete buttons (AJAX)
document.querySelectorAll('button.delete').forEach(btn => {
  btn.addEventListener('click', () => {
    const fname = btn.getAttribute('data-file');
    if (!confirm('Delete ' + fname + '?')) return;
    fetch('delete.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ file: fname })
    }).then(r => r.json()).then(js => {
      if (js.success) location.reload();
      else alert(js.error || 'Delete failed');
    }).catch(() => alert('Delete failed'));
  });
});
</script>
</body>
</html>
