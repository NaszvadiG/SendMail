<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Envoie / gestion Email
 * @author Romain Maingre <contact@romainmaingre.fr>
 * @category Library
 * @version 1.0.0
 */
class Sendemail_lib
{
    private $_urlReCaptcha = "https://www.google.com/recaptcha/api/siteverify";

    private $_privateKey = "6LfawyYTAAAAAIuqw5MWX4SWecUuh663RSzGtvOz";

    private $_sizeMessageMin = 0;

    private $_defaultSubject = '';

    private $_siteOwnersEmail;

    private $_emailContent;

    private $_codeError;

    /**
     * Constructeur
     * @author Romain Maingre <contact@romainmaingre.fr>
     */
    public function __construct()
    {
        $this->_siteOwnersEmail = 'contact@' . WEBSITE_NAME;
        $this->emailReturn = array('statut' => true, 'erreur' => 0, 'message' => array());
    }

    /**
     * Initialise les parametres par defaut
     * @author Romain Maingre <contact@romainmaingre.fr>
     * @param string $defaultSubject
     * @param int $sizeMessageMin
     * @param array $ownersEmail
     */
    public function initParamMail($defaultSubject, $sizeMessageMin, $ownersEmail = array()) {
        $this->_defaultSubject  = $defaultSubject;
        $this->_sizeMessageMin  = $sizeMessageMin;
        if (!empty($ownersEmail)) {
            if (!empty($ownersEmail['website']) && !empty($ownersEmail['contact'])) {
                $this->_siteOwnersEmail = $ownersEmail['contact'] . '@' . $ownersEmail['website'];
            } else if (!empty($ownersEmail['contact'])) {
                $this->_siteOwnersEmail = $ownersEmail['contact'] . '@' . WEBSITE_NAME;
            }
        }
    }

    /**
     * Set les erreurs survenues
     * @author Romain Maingre <contact@romainmaingre.fr>
     * @param string $dataError
     * @param int $error
     */
    private function _setEmailError($dataError, $error = 1) {
        $this->emailReturn['statut']    = false;
        $this->emailReturn['erreur']    = $error;
        $this->emailReturn['message']   = $dataError;
    }

    /**
     * Vérifie si l'addresse email est valide
     * @author Romain Maingre <contact@romainmaingre.fr>
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
     * @author Romain Maingre <contact@romainmaingre.fr>
     * @param string $message
     * @return bool
     */
    private function _verifSizeMessage($message) {
        if (strlen($message) < $this->_sizeMessageMin) {
            return false;
        }
        return true;
    }

    public function setEmail($from, $email, $message, $subject, $captcha = null) {
        try {
            if (!empty($captcha)) {
                $this->_emailContent['captcha'] = $captcha;
                $this->_emailContent['ip'] = IP_ADDRESS;
            }

            if (!$this->_verifMailAddress($email)) {
                $this->_codeError = 2;
                throw new Exception("Veuillez entrer une adresse email valide.");
            }
            $this->_emailContent['mailAddress'] = $email;
            if (!$this->_verifSizeMessage($message)) {
                $this->_codeError = 2;
                throw new Exception("Veuillez entre un message de minimum " . $this->_sizeMessageMin . " caractères.");
            }
            $this->_emailContent['message']  = $message;
            $this->_emailContent['headers']  = "From: " . ucfirst($from) . "\r\n";
            $this->_emailContent['headers'] .= "Reply-To: " . $email . "\r\n";
            $this->_emailContent['headers'] .= "MIME-Version: 1.0\r\n";
            $this->_emailContent['headers'] .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            $this->_emailContent['subject']  = (!empty($subject) ? $subject : $this->_defaultSubject);
        } catch (Exception $e) {
            $this->_setEmailError($e->getMessage(), $this->_codeError);
        }
        return $this->emailReturn;
    }

    /**
     * Envoie un mail après vérification du Captcha
     * @author Romain Maingre <contact@romainmaingre.fr>
     * @return array
     */
    public function sendEmailCaptcha() {
        try {
            if (!empty($this->_emailContent['captcha']) && !empty($this->_emailContent['ip'])) {
                $responseReCaptcha = file_get_contents($this->_urlReCaptcha.'?secret='.$this->_privateKey.'&response='.$this->_emailContent['captcha'].'&remoteip='.$this->_emailContent['ip']);
                $responseReCaptcha = json_decode($responseReCaptcha);
                // Check ReCaptcha
                if (isset($responseReCaptcha->success) AND $responseReCaptcha->success == true) {
                    ini_set('sendmail_from', $this->_siteOwnersEmail); // for Windows server
                    $mail = mail($this->_siteOwnersEmail, $this->_emailContent['subject'], $this->_emailContent['message'], $this->_emailContent['headers']);
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
            $this->_setEmailError($e->getMessage(), $this->_codeError);
        }
        return $this->emailReturn;
    }

    /**
     * Envoie un mail après vérification du Captcha
     * @author Romain Maingre <contact@romainmaingre.fr>
     * @return array
     */
    public function sendEmail() {
        try {
            ini_set('sendmail_from', $this->_siteOwnersEmail); // for Windows server
            $mail = mail($this->_siteOwnersEmail, $this->_emailContent['subject'], $this->_emailContent['message'], $this->_emailContent['headers']);
            if (!$mail) {
                $this->_codeError = 3;
                throw new Exception("L'envoi du mail a échoué. Veuillez réessayer.");
            }
        } catch (Exception $e) {
            $this->_setEmailError($e->getMessage(), $this->_codeError);
        }
        return $this->emailReturn;
    }
}

/* End of file Sendemail_lib.php */
/* Location: ./application/libraries/Sendemail_lib.php */