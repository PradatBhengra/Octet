</main>

<footer class="nic-footer">
  <div class="footer-container">
    <div class="footer-logo-section">
      <img src="https://www.presentations.gov.in/wp-content/uploads/2020/06/nic-logo-nic-logo-1-bilingual-white-1-01.jpg" alt="NIC Logo" class="footer-nic-logo">
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQF_77pox1wMORrigecBHkLE9yrfQSWNo-JBA&s" alt="Jharkhand Emblem" class="footer-jh-logo">
    </div>

    <div class="footer-text">
      <p>&copy; <?= date('Y') ?> <strong>NIC Jharkhand</strong>. All Rights Reserved.</p>
      <p>Designed and Developed by <strong>National Informatics Centre, Jharkhand</strong></p>
    </div>
  </div>
</footer>

</div> <!-- End of .wrapper from header.php -->

<style>
.nic-footer {
  background-color: #003366;
  color: #ffffff;
  padding: 25px 0;
  border-top: 6px solid #ff9933;
  font-family: 'Segoe UI', sans-serif;
  font-size: 15px;
  flex-shrink: 0; /* Prevent footer from shrinking in flex layout */
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  text-align: center;
}

.footer-logo-section {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 30px;
  margin-bottom: 15px;
}

.footer-nic-logo,
.footer-jh-logo {
  height: 50px;
}

.footer-text p {
  margin: 5px 0;
  font-weight: 500;
}

@media (max-width: 768px) {
  .footer-logo-section {
    flex-direction: column;
    gap: 15px;
  }
}
</style>

</body>
</html>
