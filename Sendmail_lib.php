<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
<<<<<<< HEAD:Sendmail_lib.php
 * Envoie / gestion Mail
 * @author Romain Maingre <contact@romainmaingre.fr>
=======
 * Envoie / gestion Email
 * @author Romain Maingre <support@romainmaingre.fr>
>>>>>>> origin/master:Sendemail_lib.php
 * @category Library
 * @version 1.0.0
 */
class Sendmail_lib
{
    private $_urlReCaptcha = "https://www.google.com/recaptcha/api/siteverify";

    private $_privateKey = PRIVATE_KEY_CAPTCHA;

    private $_sizeMessageMin = 0;

    private $_defaultSubject = '';

    private $_siteOwnersMail;

    private $_mailContent;

    private $_codeError;

    /**
     * Constructeur
     * @author Romain Maingre <support@romainmaingre.fr>
     */
    public function __construct()
    {
        $this->_siteOwnersMail = 'contact@' . WEBSITE_NAME;
        $this->mailReturn = array('statut' => true, 'erreur' => 0, 'message' => array());
    }

    /**
     * Initialise les parametres par defaut
     * @author Romain Maingre <support@romainmaingre.fr>
     * @param string $defaultSubject
     * @param int $sizeMessageMin
     * @param array $ownersMail
     */
    public function initParamMail($defaultSubject, $sizeMessageMin, $ownersMail = array()) {
        $this->_defaultSubject  = $defaultSubject;
        $this->_sizeMessageMin  = $sizeMessageMin;
        if (!empty($ownersMail)) {
            if (!empty($ownersMail['website']) && !empty($ownersMail['contact'])) {
                $this->_siteOwnersMail = $ownersMail['contact'] . '@' . $ownersMail['website'];
            } else if (!empty($ownersMail['contact'])) {
                $this->_siteOwnersMail = $ownersMail['contact'] . '@' . WEBSITE_NAME;
            }
        }
    }

    /**
     * Change le mail de reception
     * @author Romain Maingre <contact@romainmaingre.fr>
     * @param $mail
     */
    public function changeOwnersMail($mail) {
        $this->_siteOwnersMail = $mail;
    }

    /**
     * Set les erreurs survenues
     * @author Romain Maingre <support@romainmaingre.fr>
     * @param string $dataError
     * @param int $error
     */
    private function _setMailError($dataError, $error = 1) {
        $this->mailReturn['statut']    = false;
        $this->mailReturn['erreur']    = $error;
        $this->mailReturn['message']   = $dataError;
    }

    /**
<<<<<<< HEAD:Sendmail_lib.php
     * Vérifie si l'addresse mail est valide
     * @author Romain Maingre <contact@romainmaingre.fr>
=======
     * Vérifie si l'addresse email est valide
     * @author Romain Maingre <support@romainmaingre.fr>
>>>>>>> origin/master:Sendemail_lib.php
     * @param string $mailAddress
     * @return bool
     */
    private function _verifMailAddress($mailAddress) {
        if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is', $mailAddress)) {
            return false;
        }
        return true;
    }

    /**
     * Vérifie la longueur du message de l'expéditeur
     * @author Romain Maingre <support@romainmaingre.fr>
     * @param string $message
     * @return bool
     */
    private function _verifSizeMessage($message) {
        if (strlen($message) < $this->_sizeMessageMin) {
            return false;
        }
        return true;
    }

    /**
<<<<<<< HEAD:Sendmail_lib.php
     * Initialise le mail avant envoie
     * @author Romain Maingre <contact@romainmaingre.fr>
     * @param string $from
     * @param string $mail
     * @param string $message
     * @param string $subject
     * @param string $captcha
     * @param bool $html
     * @return array
     */
    public function setMail($from, $mail, $message, $subject, $captcha = null, $html = false) {
=======
     * Envoie un mail après vérification du Captcha
     * @author Romain Maingre <support@romainmaingre.fr>
     * @param string $from
     * @param string $email
     * @param string $message
     * @param string $subject
     * @param string $captcha
     * @return array
     */
    public function setEmail($from, $email, $message, $subject, $captcha = null) {
>>>>>>> origin/master:Sendemail_lib.php
        try {
            if (!empty($captcha)) {
                $this->_mailContent['captcha'] = $captcha;
                $this->_mailContent['ip'] = IP_ADDRESS;
            }
            if (!$this->_verifMailAddress($mail)) {
                $this->_codeError = 2;
                throw new Exception("Veuillez entrer une adresse mail valide.");
            }
            $this->_mailContent['mailAddress'] = $mail;
            if (!$this->_verifSizeMessage($message)) {
                $this->_codeError = 2;
                throw new Exception("Veuillez entre un message de minimum " . $this->_sizeMessageMin . " caractères.");
            }
            $this->_mailContent['message']  = $message;
            $this->_mailContent['headers']  = "From: " . ucfirst($from) . "\r\n";
            $this->_mailContent['headers'] .= "Reply-To: " . $mail . "\r\n";
            $this->_mailContent['headers'] .= "MIME-Version: 1.0\r\n";
            $this->_mailContent['headers'] .= "Content-Type: text/".($html ? 'html' : 'plain')."; charset=ISO-8859-1\r\n";

            $this->_mailContent['subject']  = (!empty($subject) ? $subject : $this->_defaultSubject);
        } catch (Exception $e) {
            $this->_setMailError($e->getMessage(), $this->_codeError);
        }
        return $this->mailReturn;
    }

    /**
     * Envoie un mail après vérification du Captcha
     * @author Romain Maingre <support@romainmaingre.fr>
     * @return array
     */
    public function sendMailCaptcha() {
        try {
            if (!empty($this->_mailContent['captcha']) && !empty($this->_mailContent['ip'])) {
                $responseReCaptcha = file_get_contents($this->_urlReCaptcha.'?secret='.$this->_privateKey.'&response='.$this->_mailContent['captcha'].'&remoteip='.$this->_mailContent['ip']);
                $responseReCaptcha = json_decode($responseReCaptcha);
                // Check ReCaptcha
                if (isset($responseReCaptcha->success) AND $responseReCaptcha->success == true) {
                    ini_set('sendmail_from', $this->_siteOwnersMail); // for Windows server
                    $mail = mail($this->_siteOwnersMail, $this->_mailContent['subject'], $this->_mailContent['message'], $this->_mailContent['headers']);
                    if (!$mail) {
                        $this->_codeError = 3;
                        throw new Exception("L'envoi du mail a échoué. Veuillez réessayer.");
                    }
                } else {
                    $this->_codeError = 3;
                    throw new Exception("Captcha a échoué. Veuillez réessayer !");
                }
            } else {
                $this->_codeError = 3;
                throw new Exception("Missing Captcha or IP address");
            }
        } catch (Exception $e) {
            $this->_setMailError($e->getMessage(), $this->_codeError);
        }
        return $this->mailReturn;
    }

    /**
     * Envoie un mail
     * @author Romain Maingre <support@romainmaingre.fr>
     * @return array
     */
    public function sendMail() {
        try {
            ini_set('sendmail_from', $this->_siteOwnersMail); // for Windows server
            $mail = mail($this->_siteOwnersMail, $this->_mailContent['subject'], $this->_mailContent['message'], $this->_mailContent['headers']);
            if (!$mail) {
                $this->_codeError = 3;
                throw new Exception("L'envoi du mail a échoué. Veuillez réessayer.");
            }
        } catch (Exception $e) {
            $this->_setMailError($e->getMessage(), $this->_codeError);
        }
        return $this->mailReturn;
    }
}

<<<<<<< HEAD:Sendmail_lib.php
/* End of file Sendmail_lib.php */
/* Location: ./application/libraries/Sendmail_lib.php */
=======
/* End of file Sendemail_lib.php */
/* Location: ./application/libraries/Sendemail_lib.php */
>>>>>>> origin/master:Sendemail_lib.php
