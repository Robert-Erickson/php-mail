<?php
// This class is for preparing and sending an email message with php.
// Written by: Robert M. Erickson
//             June 29, 2015
// 
// standard setters and getters
// valdiates informmation for correctness
//
//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
// CONSTRAINTS:
//      $to must not be empty and must be an email format
//      $cc must be an email format if its not empty
//      $bcc must be an email format if its not empty
//      $from must not be empty and must be an email format
//      $subject must not be empty
//      $message must not be empty
//      $message must have a minimum number of characters
//      $message must be a minimum length (just count the characters and spaces)
//      $message should be cleaned of invalid html before being sent here as you//            may want to allow html characters
//
// USAGE:
/*
// generally i define all contants in an include file
// I defined here for ease of demo
define("ADMIN_EMAIL", get_current_user() . "@uvm.edu");


// define variables:
// see sample

// create object, normally i put Email.php in the lib folder
include ("Email.php");
$thisEmail = new Email($to, $cc, $bcc, $from, $subject, $message);

// send mail
$status = $thisEmail->sendMail($to, $cc, $bcc, $from, $subject, $message);

if($status === true){
    print "<p>Mail has been sent to: ";
    print $to;
    print ". Print a copy for your records:</p>";
    print $message;
}else{
  // $status holds an array of errors
  // you may not need to do this 
    print '<p class="erorr">Your email has the following mistake';
    
    if(count($status)>1) print 's';
    print '</p>';
    
    print "<ol>";
    foreach($status as $error){
        print "<li>" . $error . "</li>";
    }
    print "</ol>";
}
 * 
 * 
 */
class Email {

    private $to; // mail to this address
    private $cc; // carbon copy mail to this address
    private $bcc; // blind carbon copy mail to this address
    private $from; // mail coming frmo this person
    private $subject; // subject of the mail message, always important to have a good subject
    private $message; // actual message, html allowed
    private $signature; // how you want to sign the end of the message
    private $headers; //headers for the email

    const MIN_MESSAGE_LENGTH = 20; // just trying prevent a short (spam type) email
    const HEADER = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
    const FROM_NAME = "Web Master";
    public function __construct($to, $cc, $bcc, $from, $subject, $message) {
        $this->setTo($to);
        $this->setCC($cc);
        $this->setBCC($bcc);
        $this->setFrom($from);
        $this->setSubject($subject);
        $this->setMessage($message);
        $this->setHeaders();
    }

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
//        Getters
//
    public function getTo() {
        return $this->to;
    }

    public function getCC() {
        return $this->cc;
    }

    public function getBCC() {
        return $this->bcc;
    }

    public function getFrom() {
        return $this->from;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getSignature() {
        return $this->signature;
    }

    private function getHeaders() {
        return $this->headers;
    }

//#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#$#
//
//        Setters (Sanitize Data)
//
    private function setTo($to) {
        $this->to = filter_var($to, FILTER_SANITIZE_EMAIL);
    }

    private function setCC($cc) {
        $cc = filter_var($cc, FILTER_SANITIZE_EMAIL);

        if ($cc) {
            $this->cc = $cc;
        } else {
            $this->cc = "";
        }
    }

    private function setBCC($bcc) {
        $bcc = filter_var($bcc, FILTER_SANITIZE_EMAIL);

        if ($bcc) {
            $this->bcc = $bcc;
        } else {
            $this->bcc = "";
        }
    }

    private function setFrom($from) {      
        $this->from = filter_var($from, FILTER_SANITIZE_EMAIL);       
    }

    private function setSubject($subject) {
        $this->subject = filter_var($subject, FILTER_SANITIZE_STRING);
    }

    private function setMessage($message, $top = true) {
        if ($top) {
            $messageTop = '<html><head><title>' . $this->getSubject() . '</title></head><body>';
        }
        $this->message = filter_var($message, FILTER_SANITIZE_STRING);
    }

    private function setSignature($signature) {
        $this->signature = filter_var($signature, FILTER_SANITIZE_STRING);
    }

    private function setHeaders() {
        $header = "";
        $from = $this->getFrom();
        $cc = $this->getCC();
        $bcc = $this->getBCC();
        if (!empty($from)) {

            $header = self::HEADER;
            
            $header .= "From: " . self::FROM_NAME . "<" . $from . ">\r\n";
                  
            if (!empty($cc)) {
                $header .= "CC: " . $cc . "\r\n";
            }
          
            if (!empty($bcc)) {
                $header .= "BCC: " . $bcc . "\r\n";
            }
        
            $this->headers = $header;
        }
    }

    
//!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!
//
//       Validate Data
//
    public static function validateTo($to) {
        $error = "";
        $to = filter_var($to, FILTER_SANITIZE_EMAIL);
        if (empty($to)) {
            $error = "You must send the mail to someone.";
        } else if (!$to) {
            $error = "The email address you are sending the message to appears to be incorrect.";
        }

        return $error;
    }

    public static function validateCC($cc) {
        $error = "";
        $cc = filter_var($cc, FILTER_SANITIZE_EMAIL);
        if (!empty($cc)) {
            if (!$cc) {
                $error = "The email address you are CCing the message to appears to be incorrect.";
            }
        }

        return $error;
    }

    public static function validateBCC($bcc) {
        $error = "";
        $bcc = filter_var($bcc, FILTER_SANITIZE_EMAIL);
        if (!empty($bcc)) {
            if (!$bcc) {
                $error = "The email address you are BCCing the message to appears to be incorrect.";
            }
        }

        return $error;
    }

    public static function validateFrom($from) {
        $error = "";
        $from = filter_var($from, FILTER_SANITIZE_EMAIL);

        if (empty($from)) {
            $error = "You must have the mail sent from someone.";
        } else if (!$from) {
            $error = "The email address you are sending the message from to appears to be incorrect.";
        }

        return $error;
    }

    public static function validateSubject($subject) {
        $error = "";
        $subject = filter_var($subject, FILTER_SANITIZE_STRING);
        if (empty($subject)) {
            $error = "You must have a subject for the mail.";
        } else if (!$subject) {
            $error = "The subject appears to have invalild characters.";
        }

        return $error;
    }

    public static function validateMessage($message) {
        $error = "";
        $message = filter_var($message, FILTER_SANITIZE_STRING);
        if (empty($message)) {
            $error = "You must have a message for the mail.";
        } else if (strlen($message) < self::MIN_MESSAGE_LENGTH) {
            $error = "The message is to short, it should be at least " . self::MIN_MESSAGE_LENGTH . " charcters long";
        } else if (!$message) {
            $error = "The message appears to have invalild characters.";
        }
        return $error;
    }

    
//!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!@!
//
//       Sends the mail message after checking data
//       returns true on success or a list of errors on failure
//
    public function sendMail($to, $cc, $bcc, $from, $subject, $message) {
        // verify everything is correct before sending message
        $errorMsg = array();

        $error = $this->validateTo($to);
        if (!empty($error)) {
            $errorMsg[] = $error;
        }

        $error = $this->validateCC($cc);
        if (!empty($error)) {
            $errorMsg[] = $error;
        }

        $error = $this->validateBCC($bcc);
        if (!empty($error)) {
            $errorMsg[] = $error;
        }

        $error = $this->validateFrom($from);
        if (!empty($error)) {
            $errorMsg[] = $error;
        }

        $error = $this->validateSubject($subject);
        if (!empty($error)) {
            $errorMsg[] = $error;
        }

        $error = $this->validateMessage($message);
        if (!empty($error)) {
            $errorMsg[] = $error;
        }


        if (empty($errorMsg)) {
            // preprare variables and send mail

            $this->setTo($to);
            $this->setCC($cc);
            $this->setBCC($bcc);
            $this->setFrom($from);
            $this->setSubject($subject);
            $this->setMessage($message);
            $this->setHeaders();

            return mail($this->to, $this->subject, $this->message, $this->headers);
        } else {
            return $errorMsg;
        }
    }

}
?>

