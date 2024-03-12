<?php
#**************************************************************************************************************************#
					
					
				#*************************************#
				#********** SANITIZE STRING **********#
				#*************************************#

				function sanitizeString( $value ){
if(DEBUG_F)		echo "<p class='debug sanitizeString'>🌀 <b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$value') <i>(" . basename(__FILE__) . ")</i></p>\n";

					if($value !== Null){

						$value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, double_encode:false);

						$value = trim($value);	

						if( $value ==='' ){
							$value = NULL;
						}
					}
					return $value;
				}		
				
				
#**************************************************************************************************************************#


				#*******************************************#
				#********** VALIDATE INPUT STRING **********#
				#*******************************************#

				function validateInputString( $value, $mandatory=INPUT_MANDATORY, $minLength=INPUT_MIN_LENGTH, $maxLength=INPUT_MAX_LENGTH ){
if(DEBUG_F)		echo "<p class='debug validateInputString'>🌀 <b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$value [$minLength|$maxLength],mandatory:$mandatory') <i>(" . basename(__FILE__) . ")</i></p>\n";

						#********** MANDATORY CHECK **********#
						if( $mandatory === true AND $value === NULL ){
							//Fehlerfall
							return 'Dies ist ein Pflichtfeld!';
							
							
						#********** MAXIMUM LENGTH CHECK **********#	

						}elseif( $value !== NULL AND mb_strlen($value) > $maxLength ){
							// Fehlerfall
							return "Darf maximal $maxLength Zeichen lang sein!";
						#********** MINIMUM LENGTH CHECK **********#	

						}elseif( $value !== NULL AND mb_strlen($value) < $minLength ){
							// Fehlerfall
							return "Muss mindestens $minLength Zeichen lang sein!";									
						}
				}


#***************************************************************************#


				#*******************************************#
				#********** VALIDATE EMAIL FORMAT **********#
				#*******************************************#

				function validateEmail($value, $mandatory=true) {
				#********** LOCAL SCOPE START **********#
if(DEBUG_F) echo "<p class='debug validateEmail'>🌀 <b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('value:$value, mandatory:$mandatory') <i>(" . basename(__FILE__) . ")</i></p>\n";

					#********** MANDATORY CHECK **********#
					if( $mandatory === true AND $value === NULL ){
						//Fehlerfall
						return 'Dies ist ein Pflichtfeld!';
						
					#********** VALIDATE EMAIL FORMAT **********#	
					}elseif( filter_var($value,FILTER_VALIDATE_EMAIL)=== false ){
						// Fehlerfall
						return 'Dies ist keine gültige Email-Adresse!';
					
					#********** ERFOLGSFALL **********#	
					}else{
						return NULL;
					}

				}

#***************************************************************************#


				#*******************************************#
				#********** VALIDATE IMAGE UPLOAD **********#
				#*******************************************#

				function validateImageUpload( $fileTemp,
														$imageMaxHeight 			= IMAGE_MAX_HEIGHT,
														$imageMaxWidth 			= IMAGE_MAX_WIDTH,
														$imageMinSize 				= IMAGE_MIN_SIZE,
														$imageMaxSize 				= IMAGE_MAX_SIZE,
														$imageAllowedMimeTypes 	= IMAGE_ALLOWED_MIME_TYPES,
														$imageUploadPath		 	= IMAGE_UPLOAD_PATH ) 
				{
					#********** LOCAL SCOPE START **********#
if(DEBUG_F) 	echo "<p class='debug validateImageUpload'>🌀 <b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$fileTemp') <i>(" . basename(__FILE__) . ")</i></p>\n";


					#**************************************************************************#
					#********** 1. GATHER INFORMATION FOR IMAGE FILE VIA FILE HEADER **********#
					#**************************************************************************#

					
					$imageDataArray = getimagesize($fileTemp);
/*					
if(DEBUG_F)		echo "<pre class='debug validateImageUpload value'><b>Line " . __LINE__ . "</b>: \$imageDataArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_F)		print_r($imageDataArray);					
if(DEBUG_F)		echo "</pre>";				
*/					
					
					#********** CHECK FOR VALID MIME TYPE **********#
					if( $imageDataArray === false ){
						//FEHLERFALL (MIME TYPE IS NO VALID IMAGE TYPE)
						return array('imagePath' => NULL ,'imageError'=> 'Dies ist keine Bilddatei');
					
					}else{
						//ERFOLGSFALL (MIME TYPE IS A VALID IMAGE TYPE)
						
						$imageWidth 		= sanitizeString( $imageDataArray[0] );
						$imageHeight 		= sanitizeString( $imageDataArray[1] );
						//$imageBits 		= sanitizeString( $imageDataArray['bits'] );
						//$imageChannels	= sanitizeString( $imageDataArray['channels'] );
						$imageMimeType		= sanitizeString( $imageDataArray['mime'] );
						$fileSize 			= fileSize($fileTemp);
						
if(DEBUG_F)			echo "<p class='debug validateImageUpload value'><b>Line " . __LINE__ . "</b>: \$imageWidth: 	$imageWidth px<i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)			echo "<p class='debug validateImageUpload value'><b>Line " . __LINE__ . "</b>: \$imageHeight: 	$imageHeight px<i>(" . basename(__FILE__) . ")</i></p>\n";
//if(DEBUG_F)		echo "<p class='debug validateImageUpload value'><b>Line " . __LINE__ . "</b>: \$imageBits: 		$imageBits <i>(" . basename(__FILE__) . ")</i></p>\n";
//if(DEBUG_F)		echo "<p class='debug validateImageUpload value'><b>Line " . __LINE__ . "</b>: \$imageChannels: $imageChannels <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)			echo "<p class='debug validateImageUpload value'><b>Line " . __LINE__ . "</b>: \$imageMimeType: $imageMimeType <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)			echo "<p class='debug validateImageUpload value'><b>Line " . __LINE__ . "</b>: \$fileSize: 		". round($fileSize/1024, 1) ." kB<i>(" . basename(__FILE__) . ")</i></p>\n";
						
						
					}//1. GATHER INFORMATION FOR IMAGE FILE VIA FILE HEADER END
				
					#*****************************************#
					#********** 2. IMAGE VALIDATION **********#
					#*****************************************#
					
					#********** VALIDATE PLAUSIBILITY OF FILE HEADER **********#

						// CHECK IMAGE HAEDER AND FILE SIZE
					if( !$imageWidth OR !$imageHeight OR !$imageMimeType OR $fileSize < $imageMinSize ){
						// 1. Fehlerfall: Verdächtiger Dateiheader
						return array('imagePath' => NULL ,'imageError'=> 'Verdächtiger Dateiheader');
					}	// CHECK IMAGE HAEDER AND FILE SIZE END
					
					
					#********** VALIDATE IMAGE MIME TYPE **********#
					if( array_key_exists($imageMimeType,$imageAllowedMimeTypes) === false){
						// 2. Fehlerfall: Unerlaubter Bildtyp
						return array('imagePath' => NULL ,'imageError'=> 'Dies ist kein erlaubter Bildtyp');
					}

					#***************** VALIDATE IMAGE ********************************************#				
					
					#********** VALIDATE IMAGE WIDTH **********#
					if( $imageWidth > $imageMaxWidth ){
						//3. Fehlerfall Bildbreite zu groß
						return array('imagePath' => NULL ,'imageError'=> "Die Bildbreite darf maximal ".$imageMaxWidth." px betragen");
					} //VALIDATE IMAGE WIDTH END
					
					
					#********** VALIDATE IMAGE HEIGHT **********#
					if( $imageHeight > $imageMaxHeight ){
						//4. Fehlerfall Bildhöhe zu groß
						return array('imagePath' => NULL ,'imageError'=> "Die Bildhöhe darf maximal ".$imageMaxHeight." px betragen");
					}	//VALIDATE IMAGE HEIGHT END			
					
					#********** VALIDATE IMAGE FILE SIZE **********#				
					if( $fileSize > $imageMaxSize ){
						//5. Fehlerfall Dateigröße zu groß
						return array('imagePath' => NULL ,'imageError'=> "Die Dateigröße darf maximal ".$imageMaxSize." kB betragen");
					}	//VALIDATE IMAGE FILE SIZE END
					
					#***************** VALIDATE IMAGE END ********************************************#
					
					
					#*************************************************************#
					#********** 3. PREPARE IMAGE FOR PERSISTANT STORAGE **********#
					#*************************************************************#
					
					#********** GENERATE UNIQUE FILE NAME **********#

					$fileName = mt_rand() . str_shuffle('abcdefghijklmnopqrstuvwxyz__--0123456789'). str_replace( array('.', ' '), '',microtime() );

					#********** GENERATE FILE EXTENSION **********#

					$fileExtension = $imageAllowedMimeTypes[$imageMimeType];
					
					#********** GENERATE FILE TARGET **********#					
					$fileTarget = $fileName. $fileExtension;

					$fileTarget = $imageUploadPath . $fileName . $fileExtension;

if(DEBUG_V)		echo "<p class='debug validateImageUpload value'><b>Line " . __LINE__ . "</b>: \$fileTarget: Länge: ".strlen($fileTarget)." <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug validateImageUpload value'><b>Line " . __LINE__ . "</b>: \$fileTarget: '$fileTarget' <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					#********** PREPARE IMAGE FOR PERSISTANT STORAGE END **********#	


					#********************************************************#
					#********** 4. MOVE IMAGE TO FINAL DESTINATION **********#
					#********************************************************#

					if( move_uploaded_file( $fileTemp, $fileTarget ) === false ){
						//6. Fehlerfall: Bild kann nicht verschoben werden
if(DEBUG_F) 		echo "<p class='debug validateImageUpload err'><b>Line " . __LINE__ . "</b>: FEHLER beim Verschieben des Bildes nach <i>'$fileTarget'</i>! <i>(" . basename(__FILE__) . ")</i></p>\n";						
						//TODO: errorLog, Email an Admin
						return array('imagePath' => NULL ,'imageError'=> 'Es ist ein Fehler aufgetreten! Bitte versuchen Sie es später noch einmal.');
					}else{
						// Erfolgsfall
if(DEBUG_F) 		echo "<p class='debug validateImageUpload ok'><b>Line " . __LINE__ . "</b>: Bild erfolgreich nach <i>'$fileTarget'</i> verschoben <i>(" . basename(__FILE__) . ")</i></p>\n";					
						return array('imagePath' => $fileTarget ,'imageError'=> NULL);
						
					}// 4. MOVE IMAGE TO FINAL DESTINATION END

				}//VALIDATE IMAGE UPLOAD END
			
				
#***************************************************************************#




















