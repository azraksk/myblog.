<?php
session_start();

$BASE_URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
          . '://' . $_SERVER['HTTP_HOST']
          . '/blog-project';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php'; // composer autoload

$contact_success = "";
$contact_error = "";

if (isset($_POST['contact_submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if ($name === "" || $email === "" || $message === "") {
        $contact_error = "All fields are required.";
    } else {
        $mail = new PHPMailer(true);

        try {
            // SMTP ayarları
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'yusraazrademirel@gmail.com'; // Senin Gmail adresin
            $mail->Password = 'gfnl tkot yzak zmfp';       // Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Mail ayarları
            $mail->addAddress('yusraazrademirel@gmail.com', 'MyBlog'); // Alıcı (senin mailin)
            $mail->addReplyTo($email, $name); // Kullanıcının maili

            $mail->isHTML(true);
            $mail->Subject = "Contact Form Message from $name";
            $mail->Body = nl2br(htmlspecialchars($message));

            $mail->send();
            $contact_success = "Your message has been sent successfully! 🎉";
        } catch (Exception $e) {
            $contact_error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About | MyBlog</title>

  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/about.css?">
  <link rel="stylesheet" href="assets/css/header.css">
</head>
<body>

<?php include_once __DIR__ . "/includes/header.php"; ?>

<main class="about-page">

  <!-- HERO -->
  <section class="about-hero">
    <h1>About MyBlog</h1>
    <p>A space for thoughts, creativity and quiet reflection.</p>
  </section>

  <!-- CONTENT -->
  <section class="about-content">

    <div class="about-text">
      <h2>Why MyBlog?</h2>
      <p>
        MyBlog was created as a personal digital space where ideas,
        experiences and creativity come together.
      </p>

      <p>
        In a fast world full of noise, writing allows us to slow down.
        This blog is a reminder that words still matter.
      </p>
    </div>

    <div class="about-quote">
      <p>“Writing is the painting of the voice.”</p>
      <span>— Voltaire</span>
    </div>

  </section>

  <!-- VALUES -->
  <section class="about-values">

    <div class="value-card">
      <h3>Authenticity</h3>
      <p>Real thoughts, honest writing, no filters.</p>
    </div>

    <div class="value-card">
      <h3>Creativity</h3>
      <p>Ideas grow when we allow ourselves to explore.</p>
    </div>

    <div class="value-card">
      <h3>Consistency</h3>
      <p>Writing regularly builds clarity and confidence.</p>
    </div>

  </section>

  <!-- WHO ARE WE -->
  <section class="who-are-we">
    <h2>Who Are We?</h2>
    <p>
      We are <strong>Zeynep İnce</strong> and <strong>Yüsra Azra Demirel</strong>,
      senior Computer Engineering students at Fenerbahçe University.
    </p>

    <p>
      MyBlog is a space where we share thoughts on technology, design,
      and creativity.
    </p>
  </section>

  <!-- CONTACT -->
<section class="about-contact">
  <div class="contact-wrapper">
    <!-- Sol taraf: Bilgiler -->
    <div class="contact-info">
      <h2>Contact Us</h2>
      <p>Have a question, feedback or just want to say hello? Feel free to reach out.</p>
      <span>Email: myblog@email.com</span>
      <span>Instagram: @myblog</span>
      <span>Location: Istanbul, Turkiye</span>
    </div>

    <!-- Sağ taraf: Form -->
    <form class="contact-form" method="POST">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="email" name="email" placeholder="Your Email" required>
      <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
      <button type="submit" name="contact_submit">Send</button>
    </form>
  </div>
</section>






    </div>
  </section>

</main>
<!-- FOOTER -->
<footer class="footer">
  <div class="footer-content">
    <p><strong>MyBlog.</strong></p>
    <p>Email: myblog@email.com</p>
    <p>Instagram: @myblog</p>
    <p>© 2025 MyBlog</p>
  </div>
</footer>


</body>
</html>
