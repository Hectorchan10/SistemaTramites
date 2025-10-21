<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// 🔹 Ruta correcta a vendor/autoload.php según tu estructura
require __DIR__ . '/../vendor/autoload.php';

class Mailer
{
    private $mail;

    public function __construct()
    {
        // Cargar variables de entorno desde la raíz
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host       = $_ENV['MAIL_HOST'];
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $_ENV['MAIL_USERNAME'];
        $this->mail->Password   = $_ENV['MAIL_PASSWORD'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = $_ENV['MAIL_PORT'];
        $this->mail->CharSet    = 'UTF-8';

        $this->mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
        $this->mail->isHTML(true);
    }

    public function enviarCorreo(string $destinatario, string $asunto, string $rutaHtml, array $variables = [], array $adjuntos = [])
    {
        try {
            $this->mail->clearAllRecipients();
            $this->mail->clearAttachments();

            if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
                return "Error al enviar: destinatario inválido";
            }
            $this->mail->addAddress($destinatario);
            $this->mail->Subject = $asunto;

            if (!file_exists($rutaHtml)) {
                throw new Exception("No se encontró la plantilla HTML: $rutaHtml");
            }

            $mensajeHTML = file_get_contents($rutaHtml);

            foreach ($variables as $clave => $valor) {
                $mensajeHTML = str_replace('{{' . $clave . '}}', htmlspecialchars($valor), $mensajeHTML);
            }

            $this->mail->Body    = $mensajeHTML;
            $this->mail->AltBody = strip_tags($mensajeHTML);

            foreach ($adjuntos as $archivo => $nombre) {
                if (is_file($archivo)) {
                    $this->mail->addAttachment($archivo, $nombre);
                }
            }

            $this->mail->send();
            return true;

        } catch (Exception $e) {
            return "Error al enviar: {$this->mail->ErrorInfo}";
        }
    }
}
