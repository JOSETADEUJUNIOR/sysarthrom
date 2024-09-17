<?php
/**
 ** Data : 02/01/2022
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/

require 'lib/PHPMailer-master/src/Exception.php';
require 'lib/PHPMailer-master/src/PHPMailer.php';
require 'lib/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {
    // Configurações do servidor
    private const HOST       = emailHost;
    private const USERNAME   = emailUsuario;
    private const PASSWORD   = emailSenha;
    private const PORT       = 587;
    private const ENCRYPTION = PHPMailer::ENCRYPTION_STARTTLS;
    private const FROM_EMAIL = emailOrigem;
    private const FROM_NAME  = tituloSistema;

    public function sendEmail($toAddresses, $subject, $body, $altBody = '') {
        // Cria uma nova instância do PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configurações do servidor
            $mail->isSMTP();                                   // Define o uso do SMTP
            $mail->Host       = self::HOST;                    // Endereço do servidor SMTP
            $mail->SMTPAuth   = true;                          // Habilita a autenticação SMTP
            $mail->Username   = self::USERNAME;                // Usuário SMTP
            $mail->Password   = self::PASSWORD;                // Senha SMTP
            $mail->SMTPSecure = self::ENCRYPTION;              // Ativa a criptografia TLS, também pode usar PHPMailer::ENCRYPTION_SMTPS
            $mail->Port       = self::PORT;                    // Porta TCP para TLS, geralmente é 587

            // Configurações de remetente
            $mail->setFrom(self::FROM_EMAIL, self::FROM_NAME); // Remetente

            // Adicionando múltiplos destinatários
            foreach ($toAddresses as $address) {
                $mail->addAddress($address); // Adiciona cada endereço de email ao destinatário
            }

            // Adicionando CC e BCC (se necessário)
            // $mail->addCC('cc@dominio.com', 'Nome do CC');
            // $mail->addBCC('bcc@dominio.com', 'Nome do BCC');

            // Conteúdo do email
            $mail->isHTML(true);                                // Define o email como HTML
            $mail->Subject = $subject;                          // Assunto do email
            $mail->Body    = $body;                             // Corpo do email em HTML
            $mail->AltBody = $altBody;                          // Corpo do email em texto puro para clientes que não suportam HTML
            // Configurar a codificação
            $mail->CharSet = 'UTF-8';

            // Enviar o email
            $mail->send();
            print_r('sucesso');
        } catch (Exception $e) {
            print_r($mail->ErrorInfo);
        }
    }
}

