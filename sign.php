<?php  

require 'connect/DB.php';
require 'core/load.php';


/** REGISTRATION FORM CODE */
                                             
if( isset($_POST['first-name']) && !empty($_POST['first-name'])){
        $upFirst = $_POST['first-name'];
        $upLast = $_POST['last-name'];
        $upEmailMobile = $_POST['email-mobile'];
        $upPassword = $_POST['up-password'];
        $birthDay = $_POST['birth-day'];
        $birthMonth = $_POST['birth-month'];
        $birthYear = $_POST['birth-year'];
            if(!empty($_POST['gen'])){
            $upgen = $_POST['gen'];
            }
        $birth = ''.$birthYear.'-'.$birthMonth.'-'.$birthDay.'';
    
        if(empty($upFirst) or empty($upLast) or empty($upEmailMobile) or empty($upgen)){
    
            $error = 'All fields are required';
    
        }else{
            $first_name = $loadFromUser->checkInput($upFirst);
            $last_name = $loadFromUser->checkInput($upLast);
            $email_mobile = $loadFromUser->checkInput($upEmailMobile);
            $password = $loadFromUser->checkInput($upPassword);
            //concatenate
            $screenName = ''.$first_name.'_'.$last_name.'';
            //check if screenname is already in database cap16
                if(DB::query('SELECT screenName from users WHERE screenName = :screenName',
                array(':screenName'=> $screenName ))){
                    $screenRand = rand();
                    //this will make a different id for a new user if one already exists
                    $userLink = ''.$screenName.''.$screenRand.'';
                }else {
                    $userLink = $screenName;
                }
                //Check if the user inputed phone number or email
                //use regexr.com
                
                if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", 
                $email_mobile)){
                        $error = 'Email is not correct. Please try again.';
                }else{
                    if(!filter_var($email_mobile)){
                        $error = "Invalid Email Format";
                    }else if(strlen($first_name)> 20){
                        $error = "Name must be between 2-20 character";
                    }else if(strlen($password)< 5 && strlen($password) >= 60){
                        $error = "The password is either too short or too long";
                    }else{ //check if email is in use
                            if((filter_var($email_mobile, FILTER_VALIDATE_EMAIL)) && $loadFromUser->checkEmail($email_mobile) === true){
                                $error = "Email is already in use";
                            } else{
                                $user_id =  $loadFromUser->create('users', array('first_name'=>$first_name,'last_name'=>$last_name, 
                                'email' => $email_mobile,'password'=>password_hash($password, PASSWORD_BCRYPT), 
                                'screenName'=>$screenName,'userLink'=>$userLink, 'birthday'=>$birth, 'gender'=>$upgen));
        
        $tstrong =  true;
        $token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
                                $loadFromUser->create('token', array('token'=>$token, "user_id"=>$user_id));
        

        //set cookies for users that just signed up
        setcookie('FBID', $token, time()+60*60*24*7, '/', NULL, NULL, true);
        //direct to the index page / homepage
        header('Location: index.php');
                } 
            }
         }   
    }
}



/** LOG IN FORM CODE */
if(isset($_POST['in-email']) && !empty($_POST['in-email'])){
    $emailMobile = $_POST['in-email'];
    $in_pass = $_POST['in-pass'];
    
    //check if the input data is in the right form
     
    if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", 
    $emailMobile)){
            $error = 'Email is not correct. Please try again.';
    }else{
        if(DB::query('SELECT email FROM users WHERE email = :email', array(':email'=>$emailMobile))){
                //if email is found verify if password is correct
                if(password_verify($in_pass, DB::query('SELECT password FROM users WHERE 
                email=:email', array(':email'=>$emailMobile))[0]['password'])){
                    //declare a token again like in registration form
                    //this way helps us store the passowrd in secret form
//First get the user_id from the DB 
$user_id = DB::query('SELECT user_id FROM users WHERE email=:email', array(':email'=>emailMobile))[0]['user_id'];

                    $tstrong =  true;
                    $token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
                                            $loadFromUser->create('token', array('token'=>$token, "user_id"=>$user_id));
                    
            
                    //set cookies for users that just signed up
                    setcookie('FBID', $token, time()+60*60*24*7, '/', NULL, NULL, true);
                    //direct to the index page / homepage
                    header('Location: index.php');
                }else{
                        $error = "Password is not correct";
                }
        }else{
            $error ="User has not been found. ";
        }
}
}

?>



<! DOCTYPE html>
<html lang = "en" dir= "ltr">
    <head>
        <title> RUN-NJIT </title>
        <link rel="stylesheet" href="assets/css/Index.css">
        <meta charset = "utf-8">
    </head>
    
    <body>
        <div class= "header" >
            <div class ="logo">
                <!--<img src = "assets/image/download.png" class = "image2"  alt="" > -->
                 Time to start your virtual college experience 
            </div> 
            <div class="sign-in-form">
            <form action="sign.php" method="post">
                <div class="mobile-input"> 
                <div class="input-text"> Email address</div>
                <input type="text" name="in-email" id="email-mobile" class="input-text-file">
                </div>
                <div class="password-input">
                    <div style=  "font-size: 12px;  padding-bottom: 5px;"> Password</div>
                    <input type="password" name="in-pass" id="in-password" class="input-text-field">
                    <div class="forgotten -account">Forgot Password </div>
                </div>
                <div class="login-button">
                    <input type="submit" value="Log in" class="sign-in Login">
                </div>
            </form>   
            </div>
        </div>


        <div class="main" style="width:100%;">       
            <div class="left-side">    
                <img src = "assets/image/Connecting_Data_People.jpg.jpg" class="image1" alt="">   
            </div>
            <div class="right-side">
                <div class="error">
                    <?php if(!empty($error)){echo $error;} ?>
                </div>

                <div class="box">
                    <h1 style="color:#212121;">Create an account</h1>
                    <form action="sign.php" method="post" name ="user-sign-up"> 
                        
                        <div class="sign-up-form">
                            <div class="sign-up-name">
                                <input type="text" name="first-name" id="first-name" class="text-field" placeholder="First Name">
                                <input type="text" name="last-name"  id="last-name"  class="text-field" placeholder="Last Name" >
                            </div>
        
                            <div class="sign-wrap-mobile">
                                <input type="text" name="email-mobile" id="up-email" class="text-input" placeholder="email address">
                            </div>
        
                            <div class="sign-up-password">
                                <input type="password" name="up-password" id="up-password" class="text-input" placeholder="Password">
                            </div>
        
                            <div class="sign-up-birthday">
                                <div class="bday">Birthday</div>
                                <div class="form-birthday">
                                    <select name="birth-day" id="days" class="select-body"></select>
                                    <select name="birth-month" id="months" class="select-body"></select>
                                    <select name="birth-year" id="years" class="select-body"></select>
                                </div>
                            </div>
        
                            <div class="gender-wrap">
                                <input type="radio" name="gen" id="fem" value="female" class="m0">
                                <label for="fem" class="gender">Female</label>
                                <input type="radio" name="gen" id="male" value="male" class="m0">
                                <label for="male" class="gender">Male</label>
                            </div>
        
                            <div class="term"> By clicking Sign Up, you agree to our terms, Data policy and Cookie policy. You may receive SMS notifications from us and can opt out at any time.   </div>
                                <input type="submit" value="Sign Up" class="sign-up">
                            </div>
                   </form> 
                 </div>
             </div>
        </div> 


        <div class="footer">
            Costumer Service : Phone Number 
        </div> 
        <script type="text/javascript" src="assets/js/jquery-3.5.1.min.js"></script>
        <script>
            for(i = new Date().getFullYear(); i > 1900; i--){
                /*store in years id by appending*/
                $("#years").append($('<option/>').val(i).html(i));
            }

                for(i=1; i < 13; i++){
                    $('#months').append($('<option/>').val(i).html(i));
                }

                updateNumberOfDays();

                function updateNumberOfDays(){
                    $('#days').html('');
                    month = $('#months').val();
                    year = $('#years').val();

                    days = daysInMonth(month, year);
                    for(i = 1; i < days + 1; i++){
                        $('#days').append($('<option/>').val(i).html(i));
                    }
                }
                $('#years, #months').on('change', function(){
                    updateNumberOfDays();  
                })
                
                function daysInMonth(month, year){
                    
                    return new Date(year, month, 0).getDate();
                }


        </script>
    </body>
</html>
