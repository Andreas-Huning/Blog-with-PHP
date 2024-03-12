<?php
#**************************************************************************************************************************#

				#******************************************#
				#********** GLOBAL CONFIGURATION **********#
				#******************************************#


				#********** DATABASE CONFIGURATION **********#
				define('DB_SYSTEM', 'mysql');
				define('DB_HOST', 	'localhost');
				define('DB_NAME', 	'blog');
				define('DB_USER', 	'root');
				define('DB_PWD', 		'');
								
				
				#********** EXTERNAL STRING VALIDATION CONFIGURATION **********#
				define('INPUT_MIN_LENGTH',		0);
				define('INPUT_MAX_LENGTH',		255);
				define('INPUT_MANDATORY',		true);
				
				#********** IMAGE UPLOAD CONFIGURATION **********#				
				define('IMAGE_MAX_HEIGHT', 			800 );
				define('IMAGE_MAX_WIDTH', 				800 );
				define('IMAGE_MIN_SIZE', 				1024);
				define('IMAGE_MAX_SIZE',				128*1024 );
				define('IMAGE_ALLOWED_MIME_TYPES', 	array('image/jpg' => '.jpg', 'image/jpeg' => '.jpg', 'image/png' => '.png', 'image/gif' => '.gif') );
				define('IMAGE_UPLOAD_PATH', 			'./uploaded_images/' );

				
				
				
				#********** DEBUGGING **********#
				define('DEBUG', 	true);	// Debugging for main document
				define('DEBUG_V', 	true);	// Debugging for values
				define('DEBUG_F', 	true);	// Debugging for form funktions
				define('DEBUG_DB', 	true);	// Debugging for DB funktions


#**************************************************************************************************************************#