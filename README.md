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
defined('WEBSITE_NAME')         OR define('WEBSITE_NAME', 'mywebsite.com');
defined('PRIVATE_KEY_CAPTCHA')  OR define('PRIVATE_KEY_CAPTCHA', '6WecUuhw5MWX4SAtvOLfawyY663RSzGTAAIuqz');
```

### Step 3 Examples
For a contact formular
```php

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct() {
        parent::__construct();

        $this->load->add_package_path(APPPATH.'third_party/sendmail');
        $this->load->library('Sendemail_lib');

        $name       = 'John Doe';
        $email      = 'john.doe@example.com';
        $msg        = 'Hello World';
        $subject    = 'Just a message';
        
        $result = $this->sendemail_lib->setEmail($name, $email, $msg, $subject, $captcha);
        if ($result['statut']) {
            $result = $this->sendemail_lib->sendEmail();
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
  function sendEmailCaptcha();

 /**
  * Send Mail without captcha
  */
  function sendEmail();

```
