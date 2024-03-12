<?php

//admin@admin.de => 1234


				#**************************************#
				#********** BLOG TEST REPORT **********#
				#**************************************#
/*			
				// Logout betrieb
				[x] - alle Beiträge bei Start laden
				[x] - Login Bereich
				[x] - Filterbereich mit Werten befüllt
				[x] - Blogbereich mit allen Daten
				[x] - Vorname, Nachname, Ort, Datum, Uhrzeit, Text und Bild, Link 
				[x] - Filter im Blogartikel
				[x] - Filter im Menu
				[x] - Alle Einträge anzeigen

				// Login betrieb
				[x] - leeres LoginFormular abschicken
				[x] - falsche Email, leeres PW
				[x] - Email, leeres PW
				[x] - richtige Email, leers PW
				[x] - richtige Email, falsches PW
				[x] - richtige Email, richtiges PW
				[x] - Loginbereich ausblenden
				[x] - Links anzeigen
				[x] - Link zum Dashboard
				[x] - Logout 
				[x] - alle Beiträge bei Start laden
				[x] - Filterbereich mit Werten befüllt
				[x] - Blogbereich mit allen Daten
				[x] - Vorname, Nachname, Ort, Datum, Uhrzeit, Text und Bild, Link 
				[x] - Filter im Blogartikel
				[x] - Filter im Menu
				[x] - Alle Einträge anzeigen


*/				

#**********************************************************************************#

				
				#****************************************#
				#********** PAGE CONFIGURATION **********#
				#****************************************#

				require_once('./include/config.inc.php');
				require_once('./include/dateTime.inc.php');
				require_once('./include/form.inc.php');
				require_once('./include/db.inc.php');


#**********************************************************************************#


				#*************************************#
				#********** OUTPUT BUFFERING *********#
				#*************************************#
				
				if( DEBUG OR DEBUG_V OR DEBUG_F OR DEBUG_DB ) {
if(DEBUG) 		echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Output-Buffering ist Aktiv. <i>(" . basename(__FILE__) . ")</i></p>\n"; 

					ob_start();
				}
				
				
#**********************************************************************************#


				#*****************************************#
				#********** INITIALIZE VARIABLES *********#
				#*****************************************#

				$errorLogin = NULL;
				$catID		= NULL;
				
#**********************************************************************************#


				#****************************************#
				#********** PROCESS FORM LOGIN **********#
				#****************************************#
				
				#********** PREVIEW POST ARRAY **********#
/*
if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_POST);					
if(DEBUG_V)	echo "</pre>";
*/
				#****************************************#
				
				// Schritt 1 Form: Prüfen, ob Formular abgeschickt wurde:
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 1 Form: Prüfen, ob Formular abgeschickt wurde <i>(" . basename(__FILE__) . ")</i></p>\n";


				if( isset($_POST['formLogin']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Formular 'formLogin' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
				
					// Schritt 2 Form: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Formularwerte	
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 2 Form: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Formularwerte <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					$userEmail 		= sanitizeString( $_POST['f1'] );
					$userPassword 	= sanitizeString( $_POST['f2'] );
					
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userEmail: 	$userEmail <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userPassword: $userPassword <i>(" . basename(__FILE__) . ")</i></p>\n";
				
					// Schritt 3: Validieren der Formularwerte (Feldprüfungen)
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 3: Validieren der Formularwerte (Feldprüfungen) <i>(" . basename(__FILE__) . ")</i></p>\n";

					$errorUserEmail 		= validateEmail($userEmail);
					$errorUserPassword 	= validateInputString($userPassword);
					
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$errorUserEmail: 	$errorUserEmail <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$errorUserPassword: 	$errorUserPassword <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					#********** FINAL FORM VALIDATION **********#
					if( $errorUserEmail !== NULL OR $errorUserPassword !== NULL ){
						//Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						$errorLogin='Diese Logindaten sind ungültig';
					}else{
						//Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						// Schritt 4: Weiterverarbeitung der Formularwerte
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 4: Weiterverarbeitung der Formularwerte <i>(" . basename(__FILE__) . ")</i></p>\n";						
					
						#********** FETCH USER DATA FROM DATABASE BY EMAIL **********#
						
						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#
						
						// Schritt 1 DB: Verbindung zur Datenbank aufbauen:
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 1 DB: Verbindung zur Datenbank aufbauen <i>(" . basename(__FILE__) . ")</i></p>\n";						
						
						$PDO = dbConnect();
						
						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen:
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen: <i>(" . basename(__FILE__) . ")</i></p>\n";						
						
						$sql = 'SELECT userID, userPassword FROM users 
									WHERE userEmail = :userEmail';
						
						$placeholders = array('userEmail' => $userEmail);

						// Schritt 3 DB: Prepared Statements:
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 3 DB: Prepared Statements: <i>(" . basename(__FILE__) . ")</i></p>\n";						

						try {
							// Prepare: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
							
							// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
							$PDOStatement->execute($placeholders);
							
						} catch(PDOException $error) {
if(DEBUG) 				echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
							$dbError = 'Fehler beim Zugriff auf die Datenbank!';
						} //Schritt 3 DB: Prepared Statements END	

						// Schritt 4 DB: Weiterverarbeitung der Daten aus der DB-Abfrage abhängig von der ausgeführten DB-Operation und schließen der Datenbankverbindung:
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 4 DB: Weiterverarbeitung der Daten aus der DB-Abfrage abhängig von der ausgeführten DB-Operation und schließen der Datenbankverbindung: <i>(" . basename(__FILE__) . ")</i></p>\n";						

						$userData = $PDOStatement -> fetch(PDO::FETCH_ASSOC);
/*						
if(DEBUG_V)			echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$userData <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)			print_r($userData);					
if(DEBUG_V)			echo "</pre>";						
*/					
						#********** CLOSE DB CONNECTION **********#
						// DB-Verbindung schließen 
if(DEBUG_DB)		echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung wird geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
						unset($PDO, $PDOStatement);							
						
						#********** 1. VALIDATE EMAIL **********#
if(DEBUG) 			echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Validiere Email-Adresse... <i>(" . basename(__FILE__) . ")</i></p>\n";						
						
						if( $userData === false ){
							//Fehlerfall
if(DEBUG) 				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Die Email-Adresse '$userEmail' wurde nicht in der DB gefunden! <i>(" . basename(__FILE__) . ")</i></p>\n";							
							
							// Neutrale Fehlermeldung für den User
							$errorLogin = 'Diese Logindaten sind ungültig';
							
						}else{
							//Erfolgsfall
if(DEBUG) 				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Die Email-Adresse '$userEmail' wurde in der DB gefunden. <i>(" . basename(__FILE__) . ")</i></p>\n";						
							
							#********** 2. VALIDATE PASSWORD **********#
if(DEBUG) 				echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Validiere Passwort... <i>(" . basename(__FILE__) . ")</i></p>\n";
													
							if( password_verify( $userPassword, $userData['userPassword'] ) === false ){
								//Fehlerfall
if(DEBUG) 					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Passwort aus dem Formular stimmt NICHT mit dem Passwort aus der DB überein! <i>(" . basename(__FILE__) . ")</i></p>\n";								
								
								// Neutrale Fehlermeldung für den User
								$errorLogin = 'Diese Logindaten sind ungültig';
							}else{
								//Erfolsgfall
if(DEBUG) 					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Passwort aus dem Formular stimmt mit dem Passwort aus der DB überein. <i>(" . basename(__FILE__) . ")</i></p>\n";
								
								#********** 3. PROCESS LOGIN **********#
if(DEBUG) 					echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Login wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>\n";
								
								#********** PREPARE SESSION **********#
								session_name('blog');									
								#********** START SESSION **********#
								if( session_start() === false ) {
									// Fehlerfall
if(DEBUG) 						echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n"; 

									$errorLogin = 'Der Login ist nicht möglich! 
									Bitte aktivieren Sie in Ihrem Brwoser die Annahme von Cookies.';

								} else {
									// Erfolgsfall
if(DEBUG) 						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Session erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n"; 
										
									$_SESSION['ID'] = $userData['userID'];
									$_SESSION['IPAdress'] = $_SERVER['REMOTE_ADDR'];

									#********** REDIRECT TO INTERNAL PAGE **********#
									header('LOCATION: dashboard.php');
									
									}// PREPARE SESSION END															
																	
							}//2. VALIDATE PASSWORD END
						
						}//1. VALIDATE EMAIL END					
					
					}//FINAL FORM VALIDATION END
				
				}//PROCESS FORM LOGIN END
				
				
#**********************************************************************************#


				#**************************************#
				#********** CONTINUE SESSION **********#
				#**************************************#
				
				#********** PREPARE SESSION **********#
				session_name('blog');

				#********** START/CONTINUE SESSION **********#
				session_start();
/*		
if(DEBUG_V) echo "<pre class='debug auth value'><b>Line " . __LINE__ . "</b>: \$_SESSION <i>(" . basename(__FILE__) . ")</i>:<br>\n"; 
if(DEBUG_V) print_r($_SESSION); 
if(DEBUG_V) echo "</pre>";
*/
				#*******************************************#
				#********** CHECK FOR VALID LOGIN **********#
				#*******************************************#

				#********** NO LOGIN **********#
				if( isset($_SESSION['ID']) === false OR $_SESSION['IPAdress'] !== $_SERVER['REMOTE_ADDR'] ){
					// FEHLERFALL
if(DEBUG) 		echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: Login konnte nicht validiert werden! <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					#********** DENY PAGE ACCESS **********#
					// 1. Leere Session Datei löschen				
					session_destroy();
					
					$loggedIn 	= false;
		
				#********** ACTIVE LOGIN **********#
				} else {
					//ERFOLGSFALL
if(DEBUG) 		echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Login erfolgreich validiert. <i>(" . basename(__FILE__) . ")</i></p>\n";					
					
					session_regenerate_id(true);
					
					$loggedIn 	= true;
					
				}//CHECK FOR VALID LOGIN END
				
				
#**********************************************************************************#



				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#

				#********** PREVIEW GET ARRAY **********#
/*
if(DEBUG_V) echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_GET <i>(" . basename(__FILE__) . ")</i>:<br>\n"; 
if(DEBUG_V) print_r($_GET); 
if(DEBUG_V) echo "</pre>";
*/
				#****************************************#
				// Schritt 1 URL: Prüfen, ob URL-Parameter übergeben wurde
				if( isset($_GET['action']) === true ) {
if(DEBUG) 		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: URL-Parameter 'action' wurde übergeben. <i>(" . basename(__FILE__) . ")</i></p>\n"; 

					// Schritt 2 URL: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Parameter-Werte
if(DEBUG) 		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";

					$action = sanitizeString($_GET['action']);
if(DEBUG_V) 	echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$action: $action <i>(" . basename(__FILE__) . ")</i></p>\n";


					// Schritt 3 URL: Je nach Parameterwert verzweigen

					#********** LOGOUT **********#
					if( $action === 'logout' ) {
if(DEBUG) 			echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Logout wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>\n";

						// Schritt 4 URL: Daten weiterverarbeiten
if(DEBUG) 			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Daten werden weiterverarbeitet... <i>(" . basename(__FILE__) . ")</i></p>\n";

						// 1. Session Datei löschen
						session_destroy();

						// 2. Seite neu laden
						header('LOCATION: index.php');

						// 3. Fallback, falls die Umleitung per HTTP-Header ausgehebelt werden sollte
						exit(); 
						
						// LOGOUT END
						
					#********** Filter Category **********#	
					}elseif($action === 'filterCategory'){
if(DEBUG) 			echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Kategorie Einträge werden gefiltert <i>(" . basename(__FILE__) . ")</i></p>\n";
						// Schritt 4 URL: Daten weiterverarbeiten
if(DEBUG) 			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Daten werden weiterverarbeitet... <i>(" . basename(__FILE__) . ")</i></p>\n";

						
						// Schritt 2 URL: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Werte	
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 2 URL: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Werte <i>(" . basename(__FILE__) . ")</i></p>\n";
					
						$catID 		= sanitizeString( $_GET['id'] );											
					
if(DEBUG_V)			echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$catID: 	$catID <i>(" . basename(__FILE__) . ")</i></p>\n";
											
						// Schritt 3 URL ENDE, ab Hier Datenbankabfrage

					} //Filter Category END					

				} // PROCESS URL PARAMETERS END	


#**********************************************************************************#


				#*********************************************#
				#********** FETCH BLOG DATA FROM DB **********#
				#*********************************************#
					

if(DEBUG) 	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Beitragsdaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";

				#***********************************#
				#********** DB OPERATIONS **********#
				#***********************************#

				// Schritt 1 DB: DB-Verbindung herstellen	
if(DEBUG) 	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 1 DB: DB-Verbindung herstellen <i>(" . basename(__FILE__) . ")</i></p>\n";

				$PDO = dbConnect();
					
				// Schritt 2 DB: sql-statement vorbereiten und placeholder-array befüllen
if(DEBUG) 	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 2 DB: sql-statement vorbereiten und placeholder-array befüllen <i>(" . basename(__FILE__) . ")</i></p>\n";
					
				
				if($catID === NULL){
					$sql ='SELECT * from blogs 
						INNER JOIN users USING(userID) 
						INNER JOIN categories USING(catID) 
						ORDER BY blogDate DESC';
					
					$placeholders = array();
				
				}else{
					$sql ='SELECT * from blogs 
							INNER JOIN users USING(userID) 
							INNER JOIN categories USING(catID) 
							WHERE catID = :catID
							ORDER BY blogDate DESC';
					
					$placeholders = array('catID'=>$catID);
				}

				// Schritt 3 DB: Prepared Statements:
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 3 DB: Prepared Statements: <i>(" . basename(__FILE__) . ")</i></p>\n";						

				try {
					// Prepare: SQL-Statement vorbereiten
					$PDOStatement = $PDO->prepare($sql);
						
					// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
					$PDOStatement->execute($placeholders);
						
				} catch(PDOException $error) {
if(DEBUG) 		echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
					$dbError = 'Fehler beim Zugriff auf die Datenbank!';
				} //Schritt 3 DB: Prepared Statements END	

				// Schritt 4 DB: Weiterverarbeitung der Daten aus der DB-Abfrage abhängig von der ausgeführten DB-Operation und schließen der Datenbankverbindung:
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 4 DB: Weiterverarbeitung der Daten aus der DB-Abfrage abhängig von der ausgeführten DB-Operation und schließen der Datenbankverbindung: <i>(" . basename(__FILE__) . ")</i></p>\n";						

				$blogDataArray = $PDOStatement -> fetchALL(PDO::FETCH_ASSOC);
/*						
if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$blogDataArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($blogDataArray);					
if(DEBUG_V)	echo "</pre>";						
*/					
				#********** CLOSE DB CONNECTION **********#
				// DB-Verbindung schließen 
if(DEBUG_DB)echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung wird geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
				unset($PDO, $PDOStatement);	

#**********************************************************************************#


				#********************************************#
				#********** Generate Category Data **********#
				#********************************************#

				#***********************************#
				#********** DB OPERATIONS **********#
				#***********************************#

				// Schritt 1 DB: DB-Verbindung herstellen	
if(DEBUG) 	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 1 DB: DB-Verbindung herstellen <i>(" . basename(__FILE__) . ")</i></p>\n";

				$PDO = dbConnect();
					
				// Schritt 2 DB: sql-statement vorbereiten und placeholder-array befüllen
if(DEBUG) 	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 2 DB: sql-statement vorbereiten und placeholder-array befüllen <i>(" . basename(__FILE__) . ")</i></p>\n";
						

					$sql ='SELECT * from categories 
							ORDER BY catLabel DESC';
					
					$placeholders = array();
				

				// Schritt 3 DB: Prepared Statements:
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 3 DB: Prepared Statements: <i>(" . basename(__FILE__) . ")</i></p>\n";						

				try {
					// Prepare: SQL-Statement vorbereiten
					$PDOStatement = $PDO->prepare($sql);
						
					// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
					$PDOStatement->execute($placeholders);
						
				} catch(PDOException $error) {
if(DEBUG) 		echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
					$dbError = 'Fehler beim Zugriff auf die Datenbank!';
				} //Schritt 3 DB: Prepared Statements END	

				// Schritt 4 DB: Weiterverarbeitung der Daten aus der DB-Abfrage abhängig von der ausgeführten DB-Operation und schließen der Datenbankverbindung:
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 4 DB: Weiterverarbeitung der Daten aus der DB-Abfrage abhängig von der ausgeführten DB-Operation und schließen der Datenbankverbindung: <i>(" . basename(__FILE__) . ")</i></p>\n";						

				$categoryDataArray = $PDOStatement -> fetchALL(PDO::FETCH_ASSOC);
/*						
if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$categoryDataArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($categoryDataArray);					
if(DEBUG_V)	echo "</pre>";						
*/					
				#********** CLOSE DB CONNECTION **********#
				// DB-Verbindung schließen 
if(DEBUG_DB)echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung wird geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
				unset($PDO, $PDOStatement);



/*			
if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$categoryDataArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($categoryDataArray);					
if(DEBUG_V)	echo "</pre>";						
*/

#**********************************************************************************#
?>

<!doctype html>

<html>
	
	<head>	
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">		
		<title>Andy`s Blog</title>
		<style>
			main {
				width: 60%;
				min-width:500px;				
				padding: 20px;
				border: 1px solid gray;
			}
			aside {
				width: 20%;
				padding: 20px;
				border: 1px solid gray;
			}
			.blogPost{
				min-width:400px;
				min-height:20px;<!--DIV vergrößern da die Bilder sonst überschneiden -->
			}
			.blogImg{
				max-height:1000px; <!--Bildgröße Begrenzen da es aus dem DIV wandert -->
			}
		</style>
	</head>
	
	<body>	
			
		<header class="fright loginheader">	
			<!-- -------- LOGIN FORM START -------- -->
			<?php if( $loggedIn === false ): ?>	
					<form action="" method="POST">
						<input type="hidden" name="formLogin">
						<fieldset>
							<legend>Login</legend>
							<span class='error'><?= $errorLogin ?></span><br>					
							<input class="short" type="text" name="f1" placeholder="Email-Adresse...">
							<input class="short" type="password" name="f2" placeholder="Passwort...">
							<input class="short" type="submit" value="Anmelden">
						</fieldset>
					</form>
					<!-- -------- LOGIN FORM END -------- -->	
					
					<!-- -------- LINKS -------- -->				
			<?php else : ?>		
				<p><a href="dashboard.php">Blogbeitrag erstellen >></a></p>
				<p><a href="?action=logout"><< Logout</a></p>
			<?php endif ?>	
					<!-- -------- LINKS END -------- -->				
		</header>

		<h1>Andy`s Blog</h1>	
		
		<p><a href="./" >alle Einträge anzeigen</a></p>
		
					<!-- -------- BLOGBEITRÄGE START -------- -->	
		<main class="fleft">
			<?php foreach($blogDataArray AS $blog):?>
			<div>
				<div class="fright"><a href="?action=filterCategory&id=<?=$blog['catID'] ?>">Kategorie: <?= $blog['catLabel']?></a></div>
				<h2><?= $blog['blogHeadline']?></h2>
				<p><?= $blog['userFirstName']?> <?= $blog['userLastName']?> (<?= $blog['userCity']?>) schrieb am <?= isoToEuDateTime($blog['blogDate'])['date'] ?> um <?= isoToEuDateTime($blog['blogDate'])['time'] ?> Uhr:</p> 
				
				<div class="blogPost">
					<image class="blogImg" style="float: <?= $blog['blogImageAlignment']?>"  src="<?= $blog['blogImagePath']?>" >
					<p><?= $blog['blogContent']?></p>
				</div>
			</div>
			<br>
			<hr>
			<?php endforeach ?>			
		</main>
					<!-- -------- BLOGBEITRÄGE END -------- -->
					
					<!-- -------- KATEGORIEFILTER START -------- -->		
		<aside class="fright">
			<?php foreach($categoryDataArray AS $category):?>
			<p><a href="?action=filterCategory&id=<?=$category['catID'] ?>"><?= $category['catLabel']?></a></p>				
			<?php endforeach ?>
		</aside>
					<!-- -------- KATEGORIEFILTER END -------- -->		
	</body>
	
</html>


