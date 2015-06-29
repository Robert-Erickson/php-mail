<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Try it</title>
</head>

<body>
    <h1>Testing Email</h1>
    <?php
// generally i define all contants in an include file
// I defined here for ease of demo
define("ADMIN_EMAIL", get_current_user() . "@uvm.edu");


// define variables:
$to = get_current_user() . "@uvm.edu"; // normally coming from your form
$from = ADMIN_EMAIL;
$cc = "";
$bcc = "";
$subject = "Sample Mail";
$message = '<p>Mihi videtur, me et te
Sunt post aliquid, yeah.
Tibus scire quid hic?
Iniquum tibi videtur, et mihi
Obliti estis aliquid, Yeah,
Quod amor tam facile in oblivionem.</p>

<p>Fluunt et refluunt convertat rotam vita omnia
Est vere iustus a circuli.</p>';

// create object
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
    print '<p class="erorr">Your email has the following mistake';
    
    if(count($status)>1) print 's';
    print '</p>';
    
    print "<ol>";
    foreach($status as $error){
        print "<li>" . $error . "</li>";
    }
    print "</ol>";
}

?>
</body>