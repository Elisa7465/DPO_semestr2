<?php

function send_mail($to, $subject, $message) : void {
    $smtp_server = "smtp.mail.ru";
    $smtp_port = 465;
    $smtp_user = "perveeva2025@mail.ru";
    $smtp_pass = getenv("smtp_pass"); 

    $headers = "From: $smtp_user\r\n";
    $headers .= "Reply-To: $smtp_user\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $socket = fsockopen("ssl://$smtp_server", $smtp_port, $errno, $errstr, 30);
    if (!$socket) {
        echo "Ошибка подключения к SMTP: $errstr ($errno)";
        return;
    }

    fputs($socket, "EHLO $smtp_server\r\n"); fgets($socket, 512);
    fputs($socket, "AUTH LOGIN\r\n"); fgets($socket, 512);
    fputs($socket, base64_encode($smtp_user) . "\r\n"); fgets($socket, 512);
    fputs($socket, base64_encode($smtp_pass) . "\r\n"); fgets($socket, 512);
    fputs($socket, "MAIL FROM: <$smtp_user>\r\n"); fgets($socket, 512);
    fputs($socket, "RCPT TO: <$to>\r\n"); fgets($socket, 512);
    fputs($socket, "DATA\r\n"); fgets($socket, 512);
    fputs($socket, "Subject: $subject\r\n");
    fputs($socket, "$headers\r\n");
    fputs($socket, "$message\r\n");
    fputs($socket, ".\r\n"); fgets($socket, 512);
    fputs($socket, "QUIT\r\n"); fclose($socket);
}

session_start();
require_once 'db.php'; // подключение к базе

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

  $valid = true;

    // Проверка пустых
    if (empty($name) || empty($email) || empty($phone)) {
        $valid = false;
    }

    // Валидация
    if (!preg_match('/^[А-Яа-яЁё\s\-]+$/u', $name)) $valid = false;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $valid = false;
    $digits = preg_replace('/\D/', '', $phone);
    if (strlen($digits) !== 11 || $digits[0] !== '7') $valid = false;

    if (!$valid) {
        echo "Ошибка: данные не прошли проверку.";
        exit;
    }



 $stmt = $pdo->prepare("SELECT created_at FROM feedback_requests WHERE email = :email ORDER BY created_at DESC LIMIT 1");
$stmt->execute(['email' => $email]);
$lastRequest = $stmt->fetchColumn();

if ($lastRequest) {
    $lastTime = new DateTime($lastRequest);
    $now = new DateTime('now', new DateTimeZone('Europe/Moscow'));

    $interval = $now->getTimestamp() - $lastTime->getTimestamp();

    if ($interval < 3600) {
        $remaining = 3600 - $interval;
        $minutes = floor($remaining / 60)-180;
        $seconds = $remaining % 60;

        echo <<<HTML
<p style="color:red;"><strong>Вы уже отправляли заявку.</strong></p>
<p>Повторно можно оставить заявку через: <strong>{$minutes} мин {$seconds} сек</strong></p>
HTML;
        exit;
    }
}


    // Разделим ФИО на части (если нужно)
    $nameParts = explode(" ", $name);
    $firstName = $nameParts[0] ?? '';
    $lastName = $nameParts[1] ?? '';
    $middleName = $nameParts[2] ?? '';

    // Расчёт времени через 1.5 часа
    $next = new DateTime();
    $next->modify('+4 hour 30 minutes');
    $contactTime = $next->format('H:i:s d.m.Y');

    // Расчёт текущего времени
    $now = new DateTime();
    $now->modify('+3 hour');
    $NowTime = $now->format('Y-m-d H:i:s');


    // Сохраняем в БД
    $stmt = $pdo->prepare("INSERT INTO feedback_requests (first_name, last_name, middle_name, email, phone, comment, created_at)
                           VALUES (:first_name, :last_name, :middle_name, :email, :phone, :comment, :created_at)");
    try {
        $stmt->execute([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $middleName,
            'email' => $email,
            'phone' => $phone,
            'comment' => $comment,
            'created_at' => $NowTime
        ]);
    } catch (PDOException $e) {
        echo "Ошибка при сохранении в базу данных: " . $e->getMessage();
        exit;
    }

    // Подготовка HTML-письма
    $htmlMessage = "
        <h3>Оставлена новая заявка:</h3>
        <p><strong>Имя:</strong> $firstName</p>
        <p><strong>Фамилия:</strong> $lastName</p>
        <p><strong>Отчество:</strong> $middleName</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Телефон:</strong> $phone</p>
        <p><strong>Комментарий:</strong> $comment</p>
        <p><strong>Текущее время:</strong> $NowTime</p>
        <p>Свяжитесь с пользователем после: <strong>$contactTime</strong></p>
    ";

    // Отправка письма
    send_mail("ed573808@gmail.com", "Новая заявка с формы", $htmlMessage);


    // Вывод
    echo <<<HTML
<p>Оставлено сообщение из формы обратной связи.</p>
<p>Имя: {$firstName}</p>
<p>Фамилия: {$lastName}</p>
<p>Отчество: {$middleName}</p>
<p>E-mail: {$email}</p>
<p>Телефон: {$phone}</p>
<p>С Вами свяжутся после <strong>{$contactTime}</strong></p>
HTML;
}
?>
