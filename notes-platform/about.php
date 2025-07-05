<?php
$pageTitle = "About Us - Digital Notes Platform";
include 'header.php';
?>

<style>
  .container {
    max-width: 800px;
    margin: 60px auto 80px;
    padding: 30px 40px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #2c3e50;
    line-height: 1.7;
  }

  h1 {
    font-size: 3rem;
    margin-bottom: 30px;
    text-align: center;
    color: #2980b9;
  }

  p {
    font-size: 1.15rem;
    margin-bottom: 22px;
  }

  strong {
    color: #34495e;
  }

  .highlight {
    color: #3498db;
    font-weight: 600;
  }

  .mission, .community, .vision {
    margin-bottom: 35px;
  }

  /* Responsive */
  @media (max-width: 600px) {
    .container {
      margin: 40px 15px 60px;
      padding: 25px 20px;
    }

    h1 {
      font-size: 2.2rem;
    }
  }
</style>

<main>
  <article class="container" role="main" aria-labelledby="about-title">
    <h1 id="about-title">About Us</h1>

    <section class="welcome">
      <p>Welcome to <strong class="highlight">Digital Notes Platform</strong> — your open and free educational resource hub. Here, students and learners can easily access, read, and download high-quality learning materials across a wide range of subjects.</p>
    </section>

    <section class="mission" aria-label="Our mission">
      <h2>Our Mission</h2>
      <p>Our mission is to democratize education by making knowledge freely accessible to everyone. We empower learners by providing an easy-to-use platform for uploading, sharing, and downloading notes and educational content in multiple formats including PDF, DOC, and PPT.</p>
    </section>

    <section class="community" aria-label="Our community">
      <h2>Our Community</h2>
      <p>The strength of our platform lies in our diverse and engaged community of educators, students, and contributors who continuously enrich the content and help expand our reach.</p>
      <p>We invite you to join us, share your resources, and help support the project so we can grow and assist even more learners worldwide.</p>
    </section>

    <section class="vision" aria-label="Our vision">
      <h2>Our Vision</h2>
      <p>Together, we believe in empowering education and learning for all, breaking barriers, and inspiring lifelong curiosity and success. Let’s build a brighter future through shared knowledge and collaboration. 🚀</p>
    </section>
  </article>
</main>

<?php include 'footer.php'; ?>
