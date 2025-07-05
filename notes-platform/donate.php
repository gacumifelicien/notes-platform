<?php
$pageTitle = "Donate - Digital Notes Platform";
include 'header.php';
?>

<style>
  .container {
    max-width: 700px;
    margin: 50px auto 80px;
    padding: 20px 30px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    text-align: center;
  }

  h1 {
    font-size: 2.5rem;
    margin-bottom: 20px;
    color: #2c3e50;
  }

  p {
    font-size: 1.1rem;
    line-height: 1.7;
    margin-bottom: 30px;
  }

  .donate-button {
    display: inline-block;
    background-color: #0070ba; /* PayPal blue */
    color: white;
    font-weight: 600;
    padding: 15px 35px;
    border-radius: 50px;
    font-size: 1.2rem;
    text-decoration: none;
    box-shadow: 0 5px 15px rgba(0,112,186,0.4);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }

  .donate-button i {
    margin-right: 10px;
    font-size: 1.5rem;
    vertical-align: middle;
  }

  .donate-button:hover,
  .donate-button:focus {
    background-color: #004c99;
    box-shadow: 0 8px 25px rgba(0,76,153,0.6);
    outline: none;
  }

  .thank-you-note {
    margin-top: 40px;
    font-size: 1.2rem;
    font-style: italic;
    color: #27ae60;
  }

  @media (max-width: 480px) {
    .container {
      margin: 30px 15px 60px;
      padding: 20px;
    }

    h1 {
      font-size: 2rem;
    }

    .donate-button {
      padding: 12px 25px;
      font-size: 1rem;
    }
  }
</style>

<main>
  <section class="container" role="main" aria-labelledby="donate-title">
    <h1 id="donate-title">Support Our Platform ❤️</h1>
    <p>
      Our mission is to provide free educational resources to students everywhere.<br>
      Your donation helps us maintain the platform, improve features, and reach more learners.<br><br>
      If you appreciate this service, please consider making a small donation.
    </p>

    <a href="https://www.paypal.com/donate" target="_blank" rel="noopener" class="donate-button" aria-label="Donate with PayPal">
      <i class="fab fa-paypal" aria-hidden="true"></i> Donate with PayPal
    </a>

    <p class="thank-you-note" aria-live="polite">Thank you for your support! 🙏</p>
  </section>
</main>

<?php include 'footer.php'; ?>
