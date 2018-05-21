<div class="mobile-nav">
  <nav class="navbar navbar-default" style="padding-left: 10px; padding-right: 10px; font-size: 18px;">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="index.php">
          <img src="images/logo.svg" width="80" height="80" class="d-inline-block align-top" alt="">
        </a>
        <a class="navbar-brand" href="index.php">Ίδρυμα Κοινωνικών Ασφαλίσεων</a>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>

      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav navbar-right">
          <?php if(!isset($_SESSION['user'])): ?>
          <li><a href="signup.php"><i class ="fas fa-user"></i> Εγγραφή</a></li>
          <?php else: ?>
          <li><a href="profile.php"><i class ="fas fa-user"></i> <?php echo $_SESSION['user']?></a></li>
          <?php endif; ?>
          <?php if(!isset($_SESSION['user'])): ?>
            <li><a href="signin.php"><span class="glyphicon glyphicon-log-in"></span> Σύνδεση</a></li>
          <?php else: ?>
            <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Αποσύνδεση</a></li>
          <?php endif; ?>
        </ul>
        <ul class="nav navbar-nav">
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Ασφαλισμένοι<span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="#">Συνταξιοδότηση</a></li>
              <li><a href="insured_application.php">Δήλωση Έμμεσα Ασφαλισμένου</a></li>
              <li><a href="#">Ασφαλιστικές Εισφορές</a></li>
            </ul>
          </li>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Συνταξιούχοι<span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="#">Ενημέρωση Μηνιαίας Σύνταξης</a></li>
              <li><a href="confirmation.php">Βεβαίωση Σύνταξης για Φορολογική Χρήση</a></li>
              <li><a href="#">Πρόγραμμα Κατ'οίκον Φροντίδας</a></li>
            </ul>
          </li>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Εργοδότες <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="#">Αναλυτική Περιοδική Δήλωση</a></li>
              <li><a href="insured_application.php">Βεβαίωση Ασφαλιστικής Ενημερότητας</a></li>
            </ul>
          </li>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Α.Μ.Ε.Α. <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="#">Αναπηρική Σύνταξη</a></li>
              <li><a href="insured_application.php">Πιστοποίηση Ποσοστού Αναπηρίας</a></li>
            </ul>
          </li>
        </ul>

      </div>
    </div>
  </nav>
</div>
