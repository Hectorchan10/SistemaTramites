<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../config/email/Exception.php';
require __DIR__ . '/../config/email/PHPMailer.php';
require __DIR__ . '/../config/email/SMTP.php';



class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // Configuración del servidor SMTP (Mailtrap)
        $this->mail->isSMTP();
        $this->mail->Host       = 'sandbox.smtp.mailtrap.io';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = '22610c67e30330';
        $this->mail->Password   = 'b1775a48229a42';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 2525;
        $this->mail->CharSet    = 'UTF-8';

        // Configuración general
        $this->mail->setFrom('no-reply@tramites.gt', 'Sistema de Trámites');
        $this->mail->isHTML(true);
    }

    /**
     * Envía un correo electrónico.
     * @param string $destinatario Correo destino.
     * @param string $asunto Asunto del mensaje.
     * @param string $rutaHtml Ruta del archivo HTML con el cuerpo.
     * @param array $variables Variables opcionales para reemplazar en el HTML.
     * @param array $adjuntos Lista de archivos adjuntos opcionales.
     * @return bool|string true si se envía correctamente, o mensaje de error.
     */
    public function enviarCorreo(string $destinatario, string $asunto, string $rutaHtml, array $variables = [], array $adjuntos = [])
    {
        try {
            $this->mail->clearAllRecipients();
            $this->mail->addAddress($destinatario);
            $this->mail->Subject = $asunto;

            // Leer el archivo HTML
            if (!file_exists($rutaHtml)) {
                throw new Exception("No se encontró la plantilla HTML: $rutaHtml");
            }

            $mensajeHTML = file_get_contents($rutaHtml);

            // Reemplazar variables {{variable}} dentro del HTML
            foreach ($variables as $clave => $valor) {
                $mensajeHTML = str_replace('{{' . $clave . '}}', htmlspecialchars($valor), $mensajeHTML);
            }

            $this->mail->Body    = $mensajeHTML;
            $this->mail->AltBody = strip_tags($mensajeHTML);

            // Adjuntar archivos (si hay)
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
