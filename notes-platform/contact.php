<?php
$pageTitle = "Contact Us - Digital Notes Platform";
include 'header.php';
?>

<style>
  .container {
    max-width: 700px;
    margin: 50px auto 80px;
    padding: 25px 30px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
  }

  h1 {
    font-size: 2.5rem;
    margin-bottom: 25px;
    color: #2c3e50;
    text-align: center;
  }

  p {
    font-size: 1.15rem;
    line-height: 1.7;
    margin-bottom: 30px;
    text-align: center;
  }

  .contact-info {
    font-size: 1.2rem;
    line-height: 2;
    max-width: 450px;
    margin: 0 auto;
  }

  .contact-info p {
    margin-bottom: 20px;
  }

  .contact-info i {
    color: #3498db;
    margin-right: 12px;
    font-size: 1.3rem;
    vertical-align: middle;
    width: 24px;
    text-align: center;
  }

  a {
    color: #2980b9;
    text-decoration: none;
    transition: color 0.3s ease;
  }

  a:hover,
  a:focus {
    color: #1a5276;
    text-decoration: underline;
    outline: none;
  }

  @media (max-width: 480px) {
    .container {
      margin: 30px 15px 60px;
      padding: 20px;
    }

    h1 {
      font-size: 2rem;
    }

    .contact-info {
      font-size: 1.1rem;
      max-width: 100%;
    }
  }
</style>

<main>
  <section class="container" role="main" aria-labelledby="contact-title">
    <h1 id="contact-title">Contact Us</h1>
    <p>If you have any questions, feedback, or suggestions, feel free to reach out to us:</p>

    <div class="contact-info">
      <p><i class="fas fa-phone" aria-hidden="true"></i> Phone: <a href="tel:+250784102659" aria-label="Call +250 784 102 659">+250 784 102 659</a></p>
      <p><i class="fas fa-envelope" aria-hidden="true"></i> Email: <a href="mailto:digitalnotes@gmail.com" aria-label="Email digitalnotes@gmail.com">digitalnotes@gmail.com</a></p>
      <p><i class="fas fa-map-marker-alt" aria-hidden="true"></i> Address: Kigali, Rwanda</p>
    </div>
  </section>
</main>

<?php include 'footer.php'; ?>
