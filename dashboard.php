<?php
				
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

				#****************************************#
				#********** SECURE PAGE ACCESS **********#
				#****************************************#
				
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

				#********** INVALID LOGIN **********#
				if( isset($_SESSION['ID']) === false OR $_SESSION['IPAdress'] !== $_SERVER['REMOTE_ADDR'] ){
					// FEHLERFALL
if(DEBUG) 		echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: Login konnte nicht validiert werden! <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					#********** DENY PAGE ACCESS **********#
					// 1. Leere Session Datei löschen				
					session_destroy();
					
					// 2. User auf öffentliche Seite umleiten					
					header('LOCATION: index.php');
					
					// 3. Fallback, falls die Umleitung per HTTP-Header ausgehebelt werden sollte
					exit();
				
					#********** VALID LOGIN **********#
				} else {
					//ERFOLGSFALL
if(DEBUG) 		echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Login erfolgreich validiert. <i>(" . basename(__FILE__) . ")</i></p>\n";					
					
					// generate new Session ID
					session_regenerate_id(true);
					
					// UserID aus der Session auslesen
					$userID = $_SESSION['ID'];
					
				}//CHECK FOR VALID LOGIN
				
				
#**********************************************************************************#


				#*****************************************#
				#********** INITIALIZE VARIABLES *********#
				#*****************************************#
				
				// Array SELECT MENU
				$blogImageAlignArray	= array('left'=>'align left','right'=>'align right');
				
				// USERMESSAGES
				$errorBlogHeadline		= NULL;
				$errorBlogContent		= NULL;
				$errorImageUpload		= NULL;
				$errorNewCategory		= NULL;
				
				// Variables
				$catID 					= NULL;
				$blogHeadline 			= NULL;
				$blogImageAlignment 	= NULL;
				$blogContent 			= NULL;
				$blogImagePath			= NULL;
				$newCategory			= NULL;


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

						// 2. User auf öffentliche Seite umleiten
						header('LOCATION: index.php');

						// 3. Fallback, falls die Umleitung per HTTP-Header ausgehebelt werden sollte
						exit(); 

					} // BRANCHING END

				} // PROCESS URL PARAMETERS END


#**********************************************************************************#


				#**************************************************#
				#********** PROCESS FORM CREAT BLOG POST **********#
				#**************************************************#
				
				#********** PREVIEW POST ARRAY **********#
/*
if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_POST);					
if(DEBUG_V)	echo "</pre>";
*/
				#****************************************#
				
				// Schritt 1 Form: Prüfen, ob Formular abgeschickt wurde:
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 1 Form: Prüfen, ob Formular abgeschickt wurde <i>(" . basename(__FILE__) . ")</i></p>\n";


				if( isset($_POST['formCreateBlog']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Formular 'formCreateBlog' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
				
					
					
					
					// Schritt 2 Form: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Formularwerte	
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 2 Form: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Formularwerte <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					$catID 					= sanitizeString( $_POST['category'] );
					$blogHeadline 			= sanitizeString( $_POST['blogHeadline'] );
					$blogImageAlignment 	= sanitizeString( $_POST['blogImageAlign'] );
					$blogContent 			= sanitizeString( $_POST['blogtext'] );
					
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$catID: 				$catID <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$blogHeadline: 			$blogHeadline <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$blogImageAlignment: 	$blogImageAlignment <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$blogContent: 			$blogContent <i>(" . basename(__FILE__) . ")</i></p>\n";

					// Schritt 3: Validieren der Formularwerte (Feldprüfungen)
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 3: Validieren der Formularwerte (Feldprüfungen) <i>(" . basename(__FILE__) . ")</i></p>\n";

					$errorBlogHeadline 	= validateInputString($blogHeadline);
					$errorBlogContent 	= validateInputString($blogContent, maxLength:10_000);
					
					#********** FINAL FORM VALIDATION I (FIELDS VALIDATION)**********#
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION I (FIELDS VALIDATION) <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					if( $errorBlogHeadline !== NULL OR $errorBlogContent !== NULL ){
						//Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Formular enthält noch Fehler <i>(" . basename(__FILE__) . ")</i></p>\n";				

					}else{
						//Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Formular ist Formal Fehlerfrei <i>(" . basename(__FILE__) . ")</i></p>\n";				

						
						#****************************************#
						#********** IMAGE UPLOAD START **********#
						#****************************************#

							
						#********** PREVIEW FILES ARRAY **********#
/*							
if(DEBUG_V) 		echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_FILES <i>(" . basename(__FILE__) . ")</i>:<br>\n"; 
if(DEBUG_V) 		print_r($_FILES); 
if(DEBUG_V) 		echo "</pre>";
*/							
						#********** CHECK IF IMAGE UPLOAD IS ACTIVE **********#
						if( $_FILES['blogImage']['tmp_name'] === '' ) {
								// IMAGE UPLOAD IS INACTIVE
if(DEBUG) 					echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Image upload inactive. <i>(" . basename(__FILE__) . ")</i></p>\n"; 

							} else {
								// IMAGE UPLOAD IS ACTIVE
if(DEBUG) 					echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Image upload active. <i>(" . basename(__FILE__) . ")</i></p>\n"; 

								$validateImageUploadReturnArray = validateImageUpload($_FILES['blogImage']['tmp_name']);
/*
if(DEBUG_V) 				echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$validateImageUploadReturnArray <i>(" . basename(__FILE__) . ")</i>:<br>\n"; 
if(DEBUG_V) 				print_r($validateImageUploadReturnArray); 
if(DEBUG_V) 				echo "</pre>";
*/
								#********** VALIDATE IMAGE UPLOAD **********#
								if( $validateImageUploadReturnArray['imageError'] !== NULL ){
									//FEHLERFALL
if(DEBUG)						echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Bildupload: '<i>$validateImageUploadReturnArray[imageError]</i>' <i>(" . basename(__FILE__) . ")</i></p>\n";				
									$errorImageUpload = $validateImageUploadReturnArray['imageError'];
									
									// Fehlermeldung für User generieren
									$dbError = 'Es wurden keine Daten geändert.';
									
								}else{
									//ERFOLGSFALL
if(DEBUG)						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Bild erfolgreich unter '<i>$validateImageUploadReturnArray[imagePath]</i>' auf den Server geladen. <i>(" . basename(__FILE__) . ")</i></p>\n";				
															
									// Save new Image Path into Variable
									$blogImagePath = $validateImageUploadReturnArray['imagePath'];							
								
								}//IMAGE UPLOAD END
						
						}//CHECK IF IMAGE UPLOAD IS ACTIVE END	
						
						#********** FINAL FORM VALIDATION II (IMAGE ULPOAD VALIDATION)**********#	
					
						if( $errorImageUpload !== NULL ){
							//Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION II: Das Formular IMAGE ULPOAD VALIDATION enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						}else{
							//ERFOLGSFALL
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION II: Das Formular IMAGE ULPOAD VALIDATION ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";				
								

							#********** Create Blog Post in DB **********#						
if(DEBUG) 				echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Speichere Blog Post Daten in DB... <i>(" . basename(__FILE__) . ")</i></p>\n";						
							 
							 
							 // Kleiner Check ob alle Variablen gefüllt sind
/*							 
if(DEBUG_V)				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$blogHeadline: 			$blogHeadline <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$blogImagePath: 			$blogImagePath <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$blogImageAlignment: 	$blogImageAlignment <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$blogContent: 				$blogContent <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$catID: 						$catID <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userID: 					$userID <i>(" . basename(__FILE__) . ")</i></p>\n";
*/
							#***********************************#
							#********** DB OPERATIONS **********#
							#***********************************#
							
							// Schritt 1 DB: DB-Verbindung herstellen
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 1 DB: Verbindung zur Datenbank aufbauen <i>(" . basename(__FILE__) . ")</i></p>\n";						

							$PDO = dbConnect();
			
							// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen: <i>(" . basename(__FILE__) . ")</i></p>\n";						

							$sql				=	'INSERT INTO blogs
													(blogHeadline, blogImagePath, blogImageAlignment, blogContent, catID, userID)
													VALUES
													(:blogHeadline, :blogImagePath, :blogImageAlignment, :blogContent, :catID, :userID)';
													
						
							$placeholders 	= array(	'blogHeadline' 			=> $blogHeadline,
														'blogImagePath'			=> $blogImagePath,
														'blogImageAlignment'	=> $blogImageAlignment,
														'blogContent'			=> $blogContent,
														'catID'					=> $catID,
														'userID'				=> $userID );
							
							// Schritt 3 DB: Prepared Statements:
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 3 DB: Prepared Statements <i>(" . basename(__FILE__) . ")</i></p>\n";						

							try {
								// Prepare: SQL-Statement vorbereiten
								$PDOStatement = $PDO->prepare($sql);
								
								// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
								$PDOStatement->execute($placeholders);
								
							} catch(PDOException $error) {
if(DEBUG) 					echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
								$dbError = 'Fehler beim Zugriff auf die Datenbank!';
							}								
							
							// Schritt 4 DB: Weiterverarbeitung der Daten und schließen der Datenbankverbindung:
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 4 DB: Weiterverarbeitung der Daten und schließen der Datenbankverbindung: <i>(" . basename(__FILE__) . ")</i></p>\n";						
							
							$rowCount = $PDOStatement->rowCount();
if(DEBUG_V) 			echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>\n";
				
							// Prüfen ob Beitrag gespeichert wurde
							if( $rowCount !==1 ){
								//Fehlerfall
if(DEBUG) 					echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Fehler beim Speichern des Blog Beitrages <i>(" . basename(__FILE__) . ")</i></p>\n";					
					
								// Fehlermeldung für User generieren
								$dbError = 'Es wurden keine Daten geändert.';
									
								// TODO: Eintrag in ErrorLog
									
							}else{										
								//Erfolgsfall
								$newBlogID = $PDO->lastInsertID();
if(DEBUG) 					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Blogdatensatz erfolgreich unter $newBlogID gespeichert. <i>(" . basename(__FILE__) . ")</i></p>\n";					
									
								$dbSuccess = 'Ihr Beitrag wurde erfolgreich gespeichert';
								
								// Variablen leeren um die Feldvorbelegung zu entfernen
								$catID 					= NULL;
								$blogHeadline 			= NULL;
								$blogImageAlignment 	= NULL;
								$blogContent 			= NULL;

						
							}//Prüfen ob Beitrag gespeichert wurde END							
						
							#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB) 			echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
							unset($PDO, $PDOStatement);							
							
						}//FINAL FORM VALIDATION II (IMAGE ULPOAD VALIDATION) END	

					}//FINAL FORM VALIDATION I (FIELDS VALIDATION) END				
				
				}// PROCESS FORM CREAT BLOG POST END


#**********************************************************************************#


				#*************************************************#
				#********** PROCESS FORM CREATE CATEGORY **********#
				#*************************************************#

				#********** PREVIEW POST ARRAY **********#
/*
if(DEBUG_V) echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n"; 
if(DEBUG_V) print_r($_POST); 
if(DEBUG_V) echo "</pre>";
*/
				#****************************************#
				//Schritt1  FORM: prüfen ob Formular abgeschickt wurde
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 1 Form: Prüfen, ob Formular abgeschickt wurde <i>(" . basename(__FILE__) . ")</i></p>\n";

				if( isset($_POST['formCreateCategory']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Formular 'formCreateCategory' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
				
					// Schritt 2 Form: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Formularwerte	
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 2 Form: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Formularwerte <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					$newCategory = sanitizeString( $_POST['newCategory'] );
					
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$newCategory: 	$newCategory <i>(" . basename(__FILE__) . ")</i></p>\n";
				
					// Schritt 3: Validieren der Formularwerte (Feldprüfungen)
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 3: Validieren der Formularwerte (Feldprüfungen) <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					$errorNewCategory = validateInputString($newCategory);
					
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$errorNewCategory: 	$errorNewCategory <i>(" . basename(__FILE__) . ")</i></p>\n";

					#********** FINAL FORM VALIDATION **********#
					if( $errorNewCategory !== NULL ){
						//Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";				

					}else{
						//Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
						// Schritt 4: Werte verarbeiten / mit Daten aus Datenbank prüfen
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 4: Werte verarbeiten / mit Daten aus Datenbank prüfen <i>(" . basename(__FILE__) . ")</i></p>\n";						
						
						#********** FETCH CATEGORY DATA FROM DATABASE **********#
						
						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#	
						
						// Count Category from DB 				
if(DEBUG) 			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Kategoriedaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";
				
						// Schritt 1 DB: DB-Verbindung herstellen
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 1 DB: Verbindung zur Datenbank aufbauen <i>(" . basename(__FILE__) . ")</i></p>\n";						

						$PDO = dbConnect();
			
						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen: <i>(" . basename(__FILE__) . ")</i></p>\n";						
				
						$sql 				= 'SELECT COUNT(catLabel) FROM categories
												WHERE catLabel = :catLabel';
				
						$placeholders 	= array('catLabel' => $newCategory);
				
						// Schritt 3 DB: Prepared Statements						
						try {
							// Prepare: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
							
							// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
							$PDOStatement->execute($placeholders);
							
						} catch(PDOException $error) {
if(DEBUG) 				echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
							$dbError = 'Fehler beim Zugriff auf die Datenbank!';
						}// Schritt 3 DB: Prepared Statements END							

						// Schritt 4 DB: Weiterverarbeitung der Daten aus der DB-Abfrage abhängig von der ausgeführten DB-Operation und schließen der Datenbankverbindung:
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 4 DB: Weiterverarbeitung der Daten aus der DB-Abfrage abhängig von der ausgeführten DB-Operation und schließen der Datenbankverbindung: <i>(" . basename(__FILE__) . ")</i></p>\n";						

						$countCategoryData = $PDOStatement->fetchColumn();
						
if(DEBUG_V)			echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$countCategoryData: $countCategoryData <i>(" . basename(__FILE__) . ")</i></p>\n";
									
						//1. Check if Category already exist
						if( $countCategoryData !== 0 ){
							//Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: 1. Check if Category already exist: Die Kategorie $newCategory existiert bereits <i>(" . basename(__FILE__) . ")</i></p>\n";				
							// Fehlermeldung für den User
							$dbError = "Die Kategorie '$newCategory' ist bereits vorhanden";
							
						}elseif( $countCategoryData === 0 ){
							//Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: 1. Check if Category already exist: Die Kategorie $newCategory existiert noch nicht <i>(" . basename(__FILE__) . ")</i></p>\n";				
							
							//2. Create new Category into DB
							
							// Schritt1 DB: Verbindung herstellen - Verbindung ist noch geöffnet
							
							// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 2 DB Create new Category into DB : SQL-Statement und Placeholder-Array erstellen: <i>(" . basename(__FILE__) . ")</i></p>\n";						
				
						$sql 				= 'INSERT INTO categories (catLabel) 
												VALUES (:catLabel)';
				
						$placeholders 	= array( 'catLabel' => $newCategory );
				
						// Schritt 3 DB: Prepared Statements		
						try {
							// Prepare: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
							
							// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
							$PDOStatement->execute($placeholders);
							
						} catch(PDOException $error) {
if(DEBUG) 				echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
							$dbError = 'Fehler beim Zugriff auf die Datenbank!';
						}	// Schritt 3 DB: Prepared Statements END	

							// Schritt 4 DB: Daten weiterverarbeiten
							$rowCount = $PDOStatement->rowCount();
if(DEBUG_V) 			echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>\n";
				
							// Prüfen ob Kategorie gespeichert wurde
							if( $rowCount !==1 ){
								//Fehlerfall
if(DEBUG) 					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Speichern der neuen Kategorie! <i>(" . basename(__FILE__) . ")</i></p>\n";					
					
								// Fehlermeldung für User generieren
								$dbError = 'Es ist ein Fehler aufgetreten! Bitte versuchen Sie es später noch einmal.';
								
								// TODO: Eintrag in ErrorLog
	
								}else{										
								//Erfolgsfall
								$newCatID = $PDO->lastInsertID();
if(DEBUG) 					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Neue Kategorie erfolgreich unter ID $newCatID gespeichert. <i>(" . basename(__FILE__) . ")</i></p>\n";					
								// Meldung für den User
								$dbSuccess = "Neue Kategorie: $newCategory erfolgreich angelegt ";
if(DEBUG_V) 				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$dbSuccess: $dbSuccess <i>(" . basename(__FILE__) . ")</i></p>\n";
								//Variable wieder leeren
								$newCategory = NULL;
							}// Prüfen ob Kategorie gespeichert wurde END
							
							
						}//1. Check if Category already exist END
						
						#********** CLOSE DB CONNECTION **********#
						// DB-Verbindung schließen
if(DEBUG_DB)		echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung wird geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
						unset($PDO, $PDOStatement);
						
					}//FINAL FORM VALIDATION END					
		
				}//PROCESS FORM CREATE CATEGORY END

																																			
#**********************************************************************************#

				#**********************************************#
				#********** FETCH DATA FROM DATABASE **********#
				#**********************************************#				
				
				// Read Data from DB				
if(DEBUG) 	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Daten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";
				
				// Schritt 1 DB: DB-Verbindung herstellen
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 1 DB: Verbindung zur Datenbank aufbauen <i>(" . basename(__FILE__) . ")</i></p>\n";						

				$PDO = dbConnect();
			
				#********** 1. UserDaten aus der Datenbank holen **********#
				
				// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen: <i>(" . basename(__FILE__) . ")</i></p>\n";						
				
				$sql 				= 'SELECT userFirstName, userLastName FROM users 
										WHERE userID = :userID';
				
				$placeholders 	= array('userID' => $userID);
				
				// Schritt 3 DB: Prepared Statements
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 3 DB: Prepared Statements: <i>(" . basename(__FILE__) . ")</i></p>\n";						
				
				try {
					// Prepare: SQL-Statement vorbereiten
					$PDOStatement = $PDO->prepare($sql);
					
					// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
					$PDOStatement->execute($placeholders);
					
				} catch(PDOException $error) {
if(DEBUG) 		echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
					$dbError = 'Fehler beim Zugriff auf die Datenbank!';
				}//Schritt 3 DB: Prepared Statements END
				
				// Schritt 4 DB: Weiterverarbeitung der Daten aus der DB-Abfrage abhängig von der ausgeführten DB-Operation und schließen der Datenbankverbindung:
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 4 DB: Weiterverarbeitung der Daten aus der DB-Abfrage abhängig von der ausgeführten DB-Operation und schließen der Datenbankverbindung: <i>(" . basename(__FILE__) . ")</i></p>\n";						

				$userData = $PDOStatement -> fetch(PDO::FETCH_ASSOC);					
				
				#********** PREVIEW $userData ARRAY **********#
/*
if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$userData <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($userData);					
if(DEBUG_V)	echo "</pre>";					
*/				
				#*********************************************#					
				#********** 1. UserDaten aus der Datenbank holen END**********#
				
				#********** 2. Kategorie Daten aus der Datenbank holen **********#
				
// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen: <i>(" . basename(__FILE__) . ")</i></p>\n";						
				
				$sql 				= 'SELECT * FROM categories';
				
				$placeholders 	= array();
				
				// Schritt 3 DB: Prepared Statements
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 3 DB: Prepared Statements: <i>(" . basename(__FILE__) . ")</i></p>\n";						
				
				try {
					// Prepare: SQL-Statement vorbereiten
					$PDOStatement = $PDO->prepare($sql);
					
					// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
					$PDOStatement->execute($placeholders);
					
				} catch(PDOException $error) {
if(DEBUG) 		echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
					$dbError = 'Fehler beim Zugriff auf die Datenbank!';
				}//Schritt 3 DB: Prepared Statements END
				
				// Schritt 4 DB: Weiterverarbeitung der Daten aus der DB-Abfrage abhängig von der ausgeführten DB-Operation und schließen der Datenbankverbindung:
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Schritt 4 DB: Weiterverarbeitung der Daten aus der DB-Abfrage abhängig von der ausgeführten DB-Operation und schließen der Datenbankverbindung: <i>(" . basename(__FILE__) . ")</i></p>\n";						

				$categoryData = $PDOStatement -> fetchALL(PDO::FETCH_ASSOC);					
				#********** PREVIEW $categoryData ARRAY **********#
/*
if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$categoryData <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($categoryData);					
if(DEBUG_V)	echo "</pre>";					
*/				
				#*********************************************#

				
				#********** 2. Kategorie Daten aus der Datenbank holen END**********#				
							
				
				#********** CLOSE DB CONNECTION **********#
				// DB-Verbindung schließen 
if(DEBUG_DB)echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung wird geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
				unset($PDO, $PDOStatement);
					

#**********************************************************************************#
?>

<!doctype html>

<html>
	
	<head>	
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">		
		<title>Andy`s Blog Dashboard</title>
		<style>
			main {
				width: 50%;
				padding: 20px;
				border: 1px solid gray;
			}
			aside {
				width: 30%;
				padding: 20px;
				border: 1px solid gray;
			}
			.categorie, .fullWidth{
				width: 100%;	

			}
			.mtop{
				margin-top:20px;
			}
			textarea{
				  width: 100%;
				  min-height: 150px;
				  padding: 12px 20px;
				  box-sizing: border-box;
				  border: 2px solid #ccc;
				  border-radius: 4px;
				  background-color: #f8f8f8;
				  font-size: 0.9em;
				  resize: none;
				  overflow:auto;
			}

		</style>
	
	</head>
	
	<body>	
			<!-- -------- PAGE HEADER -------- -->
		<header class="fright loginheader">

			<p><a href="./"><< zur Startseite</a></p>
			<p><a href="?action=logout"><< Logout</a></p>
		</header>
		<!-- -------- PAGE HEADER END -------- -->
		
		<h1>Andy`s Blog - Dashboard</h1>
		<p>Aktiver Benutzer: <?= $userData['userFirstName'] ?> <?= $userData['userLastName'] ?></p>
		
		<!-- -------- USER MESSAGES START -------- -->
		<?php if( isset($dbError) === true ): ?>
		<h3 class="error"><?= $dbError ?></h3>
		<?php elseif( isset($dbInfo) === true ): ?>
		<h3 class="info"><?= $dbInfo ?></h3>
		<?php elseif( isset($dbSuccess) === true ): ?>
		<h3 class="success"><?= $dbSuccess ?></h3>
		<?php endif ?>
		<!-- -------- USER MESSAGES END -------- -->	
		
		<!-- -------- CREATE BLOG START -------- -->		
		<main class="fleft">
			<h2>Neuen Blog-Eintrag verfassen</h2>			

			<form action="" method="POST" enctype="multipart/form-data">
				
				<input type="hidden" name="formCreateBlog">				
				<label>Kategorie:</label>
				<br>
				<br>
				
				<select name="category" class="categorie">
					<?php foreach($categoryData AS $cat) :?>
						<option name="<?=$cat['catLabel']?>" value="<?=$cat['catID']?>" <?php if( $catID == $cat['catID'] ) echo 'selected' ?>><?= $cat['catLabel']?></option>
					<?php endforeach?>		
				</select>
				
				<br>
				<br>
				<label>Überschrift:</label>
				<span class="error"><?php echo $errorBlogHeadline ?></span><br>
				<input type="text" class="fullWidth mtop" name="blogHeadline" placeholder="" value="<?= $blogHeadline?>">	
				
				<br>
				<br>
				
				<label>Bild hochladen:</label>
				<span class="error"><?= $errorImageUpload ?></span><br>
				
				<!-- -------- INFOTEXT FOR IMAGE UPLOAD START -------- -->
				<p class="small">
					Erlaubt sind Bilder vom Typ 
					<?php $imageAlloedMimeTypes = implode( ', ', array_keys(IMAGE_ALLOWED_MIME_TYPES) ) ?>
					<?= strtoupper( str_replace( array('image/jpeg,','image/' ),'',$imageAlloedMimeTypes )) ?> 
					<br>
					Die Bildhöhe darf <?= IMAGE_MAX_HEIGHT ?> pixel nicht überschreiten.<br>
					Die Bildhöhe darf <?= IMAGE_MAX_WIDTH ?> pixel nicht überschreiten.<br>
					Die Bildhöhe darf <?= IMAGE_MAX_SIZE/1024 ?> kB nicht überschreiten.<br>
				</p>
				<!-- -------- INFOTEXT FOR IMAGE UPLOAD END -------- -->


				<input type="file" name="blogImage">
				<select name="blogImageAlign">
					<?php foreach($blogImageAlignArray AS $index => $key) :?>
						<option name="<?=$index?>" value="<?=$index?>"  <?php if( $blogImageAlignment == $index ) echo 'selected' ?>  ><?=$key?></option>
					<?php endforeach?>				
				</select>
				
				<br>
				
				<span class="error"><?php echo $errorBlogContent ?></span><br>
				<textarea name="blogtext"><?=$blogContent?></textarea>
				
				<input type="submit" class="mtop" value="Beitrag anlegen">				
			</form>
		</main>
		<!-- -------- CREATE BLOG END -------- -->	
		
		<!-- -------- CREATE CATEGORY START -------- -->				

		<aside class="fright">
			<h2>Neue Kategorie anlegen</h2>
			<span class="error"><?= $errorNewCategory ?></span>
			
			<form action="" method="POST">
				<input type="hidden" name="formCreateCategory">		
				<input type="text" class="fullWidth" name="newCategory" value=<?=$newCategory?>>		
				<input type="submit" class="mtop" value="Kategorie anlegen">				
			</form>			
		</aside>
		<!-- -------- CREATE CATEGORY END -------- -->

	</body>
	
</html>