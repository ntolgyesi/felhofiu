<?php

/* =====================================================
 * change this to the email you want the form to send to
 * ===================================================== */
$email_to = "norbi@felhofiu.hu";
$email_subject = "Üzenet a Felhőfiú és más mesék c. könyvvel kapcsolatban";

if(isset($_POST['email']))
{
		
    function return_error($error)
    {
		echo $error;
        die();
    }
		
    // check for empty required fields
    if (!isset($_POST['name']) ||
        !isset($_POST['email']) ||
        !isset($_POST['message']))
    {
        return_error('Kérlek töltsd ki az összes kötelező mezőt!');
    }

    // form field values
    $name = $_POST['name']; // required
    $email = $_POST['email']; // required
    $contact_number = $_POST['contact_number']; // not required
    $message = $_POST['message']; // required

    // form validation
    $error_message = "";

	if (strlen($name)== 0) 
    {
        $this_error = 'Add meg a neved!';
        $error_message .= ($error_message == "") ? $this_error : "<br/>".$this_error;
    }
	
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
    if (!preg_match($email_exp,$email))
    {
        $this_error = 'Érvénytelen email cím!';
        $error_message .= ($error_message == "") ? $this_error : "<br/>".$this_error;
    } 
    
	require_once('recaptchalib.php');
	$privatekey = "6Lf5UvgSAAAAADIfnUEU7k8pZuINFvEhP6jJZq9e";
	$resp = recaptcha_check_answer ($privatekey,
								$_SERVER["REMOTE_ADDR"],
								$_POST["recaptcha_challenge_field"],
								$_POST["recaptcha_response_field"]);

	if (!$resp->is_valid) {	
		// What happens when the CAPTCHA was entered incorrectly		
		
		$this_error = "Helytelen captcha!<br/>"."Próbáld újra!";		 
		
		$error_message .= ($error_message == "") ? $this_error : "<br/>".$this_error;
	}
	
	// if there are validation errors
    if(strlen($error_message) > 0)
    {
        return_error($error_message);
    }

	
    // prepare email message
    $email_message = "Form details below.\n\n";

    function clean_string($string)
    {
        $bad = array("content-type", "bcc:", "to:", "cc:", "href");
        return str_replace($bad, "", $string);
    }

    $email_message .= "Name: ".clean_string($name)."\n";
    $email_message .= "Email: ".clean_string($email)."\n";
    $email_message .= "Contact number: ".clean_string($contact_number)."\n";
    $email_message .= "Message: ".clean_string($message)."\n";

    // create email headers
    $headers = 'From: '.$email."\r\n".
    'Reply-To: '.$email."\r\n" .
    'X-Mailer: PHP/' . phpversion();
    if (@mail($email_to, $email_subject, $email_message, $headers))
    {
        echo 'Sikeres küldés!<br/>Köszönöm!';
    }
    else 
    {
        echo 'Hiba történt!';
        die();        
    }
}
else
{
    echo 'Kérlek töltsd ki az<br/>összes szükséges mezőt!';
    die();
}
?>