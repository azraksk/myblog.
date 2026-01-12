<?php
session_start();
require_once __DIR__ . "/../backend/db.php";

/* CSRF TOKEN */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* AUTH CHECK */
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

/* FETCH CATEGORIES */
$categories = $pdo
    ->query("SELECT id, name FROM categories ORDER BY name ASC")
    ->fetchAll(PDO::FETCH_ASSOC);

/* DEFAULTS */
$error = "";
$success = "";

$title = "";
$content = "";
$category_id = "";

/* FORM SUBMIT */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $title = trim($_POST["title"] ?? "");
    $content = trim($_POST["content"] ?? "");
    $content = str_replace("&nbsp;", " ", $content);
    $category_id = $_POST["category_id"] ?? "";
    $imageName = null;

    if ($title === "" || $content === "" || $category_id === "") {
        $error = "Please fill in all required fields.";
    }
    
    
    /* IMAGE UPLOAD */
    if (!$error && !empty($_FILES["image"]["name"])) {
      $allowedExt  = ['jpg', 'jpeg', 'png', 'webp'];
      $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];

      $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime  = finfo_file($finfo, $_FILES["image"]["tmp_name"]);
      finfo_close($finfo);

      if (!in_array($ext, $allowedExt)) {
          $error = "Invalid image extension.";
      } elseif (!in_array($mime, $allowedMime)) {
          $error = "Invalid image MIME type.";
      } else {
        
        $imageName = uniqid("post_", true) . "." . $ext;
        $uploadDir = __DIR__ . "/../uploads/";
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $uploadDir . $imageName)) {
            $error = "Image upload failed.";
        }
    }
}


    /* INSERT */
    if (!$error) {
        $stmt = $pdo->prepare("
            INSERT INTO posts (user_id, title, content, category_id, image)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_SESSION["user_id"],
            $title,
            $content,
            $category_id,
            $imageName
        ]);

        $success = "Post published successfully ✨";
        $title = $content = $category_id = "";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>New Post | MyBlog</title>
  <link rel="stylesheet" href="../assets/css/admin.css?v=5">
</head>
<body>

<header class="admin-navbar">
  <div class="admin-nav-wrapper">
    <div class="admin-logo">MyBlog<span class="dot">.</span></div>
    <nav class="admin-links">
      <a href="dashboard.php">Dashboard</a>
      <a href="../index.php" target="_blank">View Site</a>
      <a href="../auth/logout.php" class="logout">Logout</a>
    </nav>
  </div>
</header>

<main class="editor-page">

<?php if ($error): ?>
  <div class="alert error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
  <div class="alert success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

  <!-- TITLE -->
  <input
    type="text"
    name="title"
    class="editor-title"
    placeholder="Write your title…"
    value="<?= htmlspecialchars($title) ?>"
    required
  >


  <!-- CONTENT -->
<div class="editor-wrapper" style="position: relative;">
  <div
    class="editor-content"
    contenteditable="true"
    data-placeholder="Write your story…"
  ><?= $content ?></div>


  <!-- Sağ altta word count -->
  <div id="wordCount" style="
       position: absolute;
       bottom: 5px;
       right: 10px;
       font-size: 12px;
       color: #666;
  ">0 words</div>
</div>

<div class="floating-toolbar" id="floatingToolbar">
  <button type="button" data-cmd="bold"><b>B</b></button>
  <button type="button" data-cmd="italic"><i>I</i></button>
  <button type="button" data-cmd="insertUnorderedList">•</button>
  <button data-cmd="blockquote">❝</button>
  <button data-cmd="link">🔗</button>
  <button type="button" data-cmd="h2">T</button>
  <button type="button" data-cmd="p">t</button>
</div>


  <input type="hidden" name="content" id="contentInput">

  <!-- CATEGORY -->
  <div class="post-settings">
    <label>Category</label>
    <div class="category-options">
      <?php foreach ($categories as $cat): ?>
        <div
          class="category-chip <?= ($cat['id'] == $category_id) ? 'selected' : '' ?>"
          data-id="<?= $cat['id'] ?>"
        >
          <?= htmlspecialchars($cat['name']) ?>
        </div>
      <?php endforeach; ?>
    </div>
    <input type="hidden" name="category_id" id="categoryInput" value="<?= htmlspecialchars($category_id) ?>">
  </div>

  <!-- COVER IMAGE -->
  <div class="field cover-upload">
    <label>Cover image</label>

    <input
      type="file"
      name="image"
      id="coverInput"
      accept="image/*"
      hidden
    >

    <div class="cover-drop" id="coverDrop">
      <span>+ Add a cover image</span>
      <img id="coverPreview" alt="">
    </div>

    <button type="button" class="remove-cover">Remove cover</button>
  </div>
  <!-- PUBLISH -->
  <div class="publish-bar">
    <span class="hint">Ready to publish ✨</span>
    <button type="submit">Publish</button>
  </div>

</form>
</main>



<script>
document.addEventListener('DOMContentLoaded', () => {
  const editor = document.querySelector('.editor-content');
  const toolbar = document.getElementById('floatingToolbar');
  const coverDrop = document.getElementById('coverDrop');
  const coverInput = document.getElementById('coverInput');
  const coverPreview = document.getElementById('coverPreview');
  const wordCount = document.getElementById('wordCount');


  coverDrop.addEventListener('click', () => {
    coverInput.click();
  });

  coverInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(evt) {
      coverPreview.src = evt.target.result;
      coverPreview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  });

  document.querySelector('.remove-cover').addEventListener('click', () => {
    coverPreview.src = '';
    coverPreview.style.display = 'none';
    coverInput.value = '';
  });



  // TEXT SELECTION
  document.addEventListener('selectionchange', () => {
    const selection = window.getSelection();
    if (!selection.rangeCount) {
      toolbar.style.display = 'none';
      return;
    }
    const range = selection.getRangeAt(0);
    const selectedText = selection.toString();

    if (selectedText.length > 0 && editor.contains(range.commonAncestorContainer)) {
      const rect = range.getBoundingClientRect();
      toolbar.style.display = 'flex';
      toolbar.style.top = `${rect.top + window.scrollY - 45}px`;
      toolbar.style.left = `${rect.left + rect.width / 2}px`;
      toolbar.style.transform = 'translateX(-50%)';
    } else {
      toolbar.style.display = 'none';
    }
  });

  // TOOLBAR BUTTONS
  toolbar.querySelectorAll('button').forEach(btn => {
    btn.addEventListener('click', () => {
      const cmd = btn.dataset.cmd;

    if (cmd === 'h2' || cmd === 'p' || cmd === 'blockquote') {
        document.execCommand('formatBlock', false, cmd);
      } else if (cmd === 'link') {
        addLink();
      } else {
        document.execCommand(cmd, false, null);
      }
    });
  });

  // FORM SUBMIT
  document.querySelector('form').addEventListener('submit', () => {
    document.getElementById('contentInput').value = editor.innerHTML;
  });

  document.querySelectorAll('.category-chip').forEach(chip => {
  chip.addEventListener('click', () => {
    document.getElementById('categoryInput').value = chip.dataset.id;
    document.querySelectorAll('.category-chip').forEach(c => c.classList.remove('selected'));
    chip.classList.add('selected');
  });
});


  // LINK TIKLAMALARINI ENGELLE
  editor.addEventListener('click', e => {
    if (e.target.tagName === 'A') e.preventDefault();
  });

  

  // BLOCK FONKSİYONU
function setBlock(tag, editor) {
    const selection = window.getSelection();
    if (!selection.rangeCount) return;

    let node = selection.getRangeAt(0).commonAncestorContainer;
    if (node.nodeType === 3) node = node.parentNode;

    const block = node.closest('p, h1, h2, h3, h4, h5, h6, div');
    if (!block || !editor.contains(block)) return;
    if (block.tagName.toLowerCase() === tag) return;

        // Eğer zaten aynı tag ise, toggle olarak <p> ile geri dön
    if (block.tagName.toLowerCase() === tag) {
        const newBlock = document.createElement('p');
        newBlock.innerHTML = block.innerHTML;
        block.replaceWith(newBlock);

        const range = document.createRange();
        range.selectNodeContents(newBlock);
        range.collapse(false);
        selection.removeAllRanges();
        selection.addRange(range);
        return;
    }

    const newBlock = document.createElement(tag);
    newBlock.innerHTML = block.innerHTML;
    block.replaceWith(newBlock);

    const range = document.createRange();
    range.selectNodeContents(newBlock);
    range.collapse(false);

    selection.removeAllRanges();
    selection.addRange(range);
}


  // LINK EKLEME
  function addLink() {
    const selection = window.getSelection();
    if (!selection.rangeCount || selection.isCollapsed) return;

    let url = prompt("Link URL:");
    if (!url) return;




    if (!/^https?:\/\//i.test(url)) url = 'https://' + url;

    document.execCommand('createLink', false, url);
  }

});


</script>


</body>
</html>
