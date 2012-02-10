<?php
//allow sessions to be passed so we can see if the user is logged in
session_start();
//connect to the database so we can check, edit, or insert data to our users table
$con = mysql_connect('localhost', 'nastyz', 'q40014123') or die(mysql_error());
$db = mysql_select_db('loginTut', $con) or die(mysql_error());
//include out functions file giving us access to the protect() function
include "./functions.php";
?>
<html>
	<head>
		<title>Login with Users Online Tutorial</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	<body>
		<?php
		//Check to see if the form has been submitted
		if(isset($_POST['submit'])){
			//protect and then add the posted data to variables
			$username = protect($_POST['username']);
			$password = protect($_POST['password']);
			$passconf = protect($_POST['passconf']);
			$email = protect($_POST['email']);
			//check to see if any of the boxes were not filled in
			if(!$username || !$password || !$passconf || !$email){
				//if any weren't display the error message
				echo "<center>You need to fill in all of the required fields!</center>";
			}else{
				//if all were filled in continue checking
				//Check if the wanted username is more than 32 or less than 3 charcters long
				if(strlen($username) > 32 || strlen($username) < 3){
					//if it is display error message
					echo "<center>Your <b>Username</b> must be between 3 and 32 characters long!</center>";
				}else{
					//if not continue checking
					//select all the rows from out users table where the posted username matches the username stored
					$res = mysql_query("SELECT * FROM `users` WHERE `username` = '".$username."'");
					$num = mysql_num_rows($res);
					//check if theres a match
					if($num == 1){
						//if yes the username is taken so display error message
						echo  "<center>The <b>Username</b> you have chosen is already taken!</center>";
					}else{
						//otherwise continue checking
						//check if the password is less than 5 or more than 32 characters long
						if(strlen($password) < 5 || strlen($password) > 32){
							//if it is display error message
							echo "<center>Your <b>Password</b> must be between 5 and 32 characters long!</center>";
						}else{
							//else continue checking
							//check if the password and confirm password match
							if($password != $passconf){
								//if not display error message
								echo "<center>The <b>Password</b> you supplied did not math the confirmation password!</center>";
							}else{
								//otherwise continue checking
								//Set the format we want to check out email address against
								$checkemail = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
								//check if the formats match
								if(!preg_match($checkemail, $email)){
									//if not display error message
									echo "<center>The <b>E-mail</b> is not valid, must be name@server.tld!</center>";
								}else{
									//if they do, continue checking
									//select all rows from our users table where the emails match
									$res1 = mysql_query("SELECT * FROM `users` WHERE `email` = '".$email."'");
									$num1 = mysql_num_rows($res1);
									//if the number of matchs is 1
									if($num1 == 1){
										//the email address supplied is taken so display error message
										echo "<center>The <b>E-mail</b> address you supplied is already taken</center>";
									}else{
										//finally, otherwise register there account
										//time of register (unix)
										$registerTime = date('U');
										//make a code for our activation key
										$code = md5($username).$registerTime;
										//insert the row into the database
										$res2 = mysql_query("INSERT INTO `users` (`username`, `password`, `email`, `rtime`) VALUES('".$username."','".$password."','".$email."','".$registerTime."')");
										//send the email with an email containing the activation link to the supplied email address
										mail($email, $INFO['chatName'].' registration confirmation', "Thank you for registering to us ".$username.",\n\nHere is your activation link. If the link doesn't work copy and paste it into your browser address bar.\n\nhttp://www.yourwebsitehere.co.uk/activate.php?code=".$code, 'From: noreply@youwebsitehere.co.uk');
										//display the success message
										echo "<center>You have successfully registered, please visit you inbox to activate your account!</center>";
									}
								}
							}
						}
					}
				}
			}
		}
		?>
		<div id="border">
			<form action="register.php" method="post">
				<table cellpadding="2" cellspacing="0" border="0">
					<tr>
						<td>Username: </td>
						<td><input type="text" name="username" /></td>
					</tr>
					<tr>
						<td>Password: </td>
						<td><input type="password" name="password" /></td>
					</tr>
					<tr>
						<td>Confirm Password: </td>
						<td><input type="password" name="passconf" /></td>
					</tr>
					<tr>
						<td>Email: </td>
						<td><input type="text" name="email" size="25"/></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" name="submit" value="Register" /></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><a href="login.php">Login</a> | <a href="forgot.php">Forgot Pass</a></a></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>


