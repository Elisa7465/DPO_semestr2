<!DOCTYPE html>
<html lang="en">
<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="./index.css">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
      <title>Document</title>
</head>
<body>       
      <div class="blank">
            <h1>Обратная связь</h1>
            <form action="form.php" method="POST" novalidate id="feedback-form">

                  <div class="section">
                        <label>ФИО</label>
                        <input type="text" name="name" >
                        <span class="error-message"></span>
                  </div>

                  <div class="section">
                        <label>Email</label>
                        <input type="text" name="email" >
                        <span class="error-message"></span>
                  </div>

                  <div class="section">
                        <label>Телефон</label>
                        <input type="text" name="phone" >
                        <span class="error-message"></span>
                  </div>

                  <div class="section">
                        <label>Комменатрий</label>
                        <input type="text" name="comment">
                  </div>

                  <input type="submit" value="Отправить" id="submit">
            </form>
            <div id="result" style="display:none; margin-top: 20px;"></div>
    </div>
<script src="index.js"></script>
</body>
</html>