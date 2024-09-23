<html lang="pl">

<head>
  <title>Notatki.pl</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
  <link  rel="stylesheet" href="public/style.css">
  <script src="https://www.google.com/recaptcha/api.js?render=6LcQ9DwoAAAAABAyIdD8FRvKjlip5fRZMNQU01II"></script>
</head>

<body class="body">
  <div class="wrapper">
    <div class="header">
      <h1><i class="far fa-clipboard"></i>Moje notatki</h1>
    </div>

    <div class="container">
      <div class="menu">
        <ul>
          <li><a href="index.php">Strona główna</a></li>
          <li><a href="index.php?action=createNote">Nowa notatka</a></li>
          <li><a href="index.php?action=searchUser">Użytkownik</a></li>
        </ul>
      </div>

      <div class="page">
        <?php require_once("templates/pages/$page.php"); ?>
      </div>
    </div>

    <div class="footer">
      <p>Notatki - projekt Wojciech Pawlina</p>
    </div>
  </div>
  
  

</body>

</html>