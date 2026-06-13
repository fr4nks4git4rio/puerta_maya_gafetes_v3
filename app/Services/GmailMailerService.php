<?php

namespace App\Services;

use App\GmailToken;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Token\AccessToken;
use Swift_Attachment;
use Throwable;

class GmailMailerService
{
    /**
     * Summary of send
     * @param string $to
     * @param string $from_email
     * @param string $from_name
     * @param string $subject
     * @param string $body
     * @param mixed $others
     * @param array $attachments
     * @throws \Exception
     * @return void
     */
    public function send(
        string $to,
        string $subject,
        string $body,
        string $from_email = '',
        string $from_name = '',
        $others = null,
        array $attachments = []
    ): void {
        $username = $from_email ?: config('app.GMAIL_USER');
        $token = $this->getValidAccessToken($username); // Debe contener un objeto que tenga ->getToken()
        if (!$token) {
            throw new \Exception('No hay token de Gmail guardado');
        }

        ini_set('default_socket_timeout', 10);

        $accessToken = $token;

        // Crea el transportador SMTP
        $transport = new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls');
        // Establecer XOAUTH2 como autenticador
        $transport->setAuthMode('XOAUTH2');
        $transport->setUsername($username);
        $transport->setPassword($accessToken);

        // Enviar correo
        $mailer = new Swift_Mailer($transport);

        $from_name = mb_convert_encoding($from_name, "UTF-8", "auto");
        $body = mb_convert_encoding($body, "UTF-8", "auto");

        $email = (new Swift_Message($subject))
            ->setFrom([$username => $from_name])
            ->setTo($to)
            ->setBody($body)
            ->setContentType('text/html');
        
        if (count($attachments) > 0) {
            foreach ($attachments as $attachment)
                if (file_exists($attachment['src']))
                    $email->attach(
                        Swift_Attachment::fromPath($attachment['src'])
                            ->setFilename($attachment['name'])
                            ->setContentType($attachment['mime'])
                    );
        }
        if ($others) {
            $email->setCc($others);
        }

        try {
            logger()->info("✅ Enviando correo a: $to");
            $mailer->send($email);
            logger()->info("✅ Correo enviado a: $to");
        } catch (Throwable $e) {
            // Loguea el error sin romper el flujo
            logger()->error('Fallo al enviar correo: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getValidAccessToken($username): string
    {
        $record = GmailToken::where('email', $username)->first();

        if (!$record) {
            throw new \Exception('No hay token de Gmail guardado en base de datos.');
        }

        $token = new AccessToken([
            'access_token'  => $record->access_token,
            'refresh_token' => $record->refresh_token,
            'expires'       => $record->expires,
        ]);

        $provider = new Google([
            'clientId'     => config('app.GOOGLE_CLIENT_ID'),
            'clientSecret' => config('app.GOOGLE_CLIENT_SECRET'),
            'redirectUri'  => config('app.GOOGLE_REDIRECT_URI'),
        ]);

        if ($token->hasExpired()) {
            $newToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $token->getRefreshToken(),
            ]);

            $record->update([
                'access_token'  => $newToken->getToken(),
                'refresh_token' => $newToken->getRefreshToken() ?? $token->getRefreshToken(),
                'expires'       => $newToken->getExpires(),
            ]);

            return $newToken->getToken();
        }

        return $token->getToken();
    }
}
