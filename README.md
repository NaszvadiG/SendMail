# SendMail
Library Send Email for Codeigniter 3

## Requirements

- PHP 5.3.x (Composer requirement)
- CodeIgniter 3.0.x

## Installation
### Step 1 Installation by Composer
#### Run composer
```shell
composer require rorocloud/sendmail
```

### Step 2 Configuration
Edit file './index.php'
```php
define('IP_ADDRESS', isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');
```

Edit file './application/config/constants.php'
```php
defined('WEBSITE_NAME') OR define('WEBSITE_NAME', 'romainmaingre.fr');
```

### Step 3 Examples
For a contact formular
```php
  public function mySendMail() {
        $this->load->library('Sendemail_lib');

        $name       = trim($this->input->post('contactName'));
        $email      = trim($this->input->post('contactEmail'));
        $msg        = trim($this->input->post('contactMessage'));
        $subject    = trim($this->input->post('contactSubject'));
        $captcha    = trim($this->input->post('g-recaptcha-response'));

        $this->sendemail_lib->initParamMail('Contact by '.ucfirst($name), 15);
        $result = $this->sendemail_lib->setEmail($name, $email, $msg, $subject, $captcha);
        if ($result['statut']) {
            $result = $this->sendemail_lib->sendEmailCaptcha();
        }
    }
```

#### Help
```php
/**
 * Initializes the default parameters
 */
function initParamMail($defaultSubject, $sizeMessageMin, $ownersEmail = array());

/**
 * Initializes the contents of the mail
 */
 function setEmail($from, $email, $message, $subject, $captcha = null);
 
 /**
  * Send Mail with captcha
  */
  function sendMailCaptcha();

 /**
  * Send Mail without captcha
  */
  function sendMail();

```
