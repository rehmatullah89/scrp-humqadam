<?
	/*********************************************************************************************\
	***********************************************************************************************
	**                                                                                           **
	**  SCRP - School Construction and Rehabilitation Programme                                  **
	**  Version 1.0                                                                              **
	**                                                                                           **
	**  http://www.humdaqam.pk                                                                   **
	**                                                                                           **
	**  Copyright 2015 (C) Triple Tree Solutions                                                 **
	**  http://www.3-tree.com                                                                    **
	**                                                                                           **
	**  ***************************************************************************************  **
	**                                                                                           **
	**  Project Manager:                                                                         **
	**                                                                                           **
	**      Name  :  Muhammad Tahir Shahzad                                                      **
	**      Email :  mtshahzad@sw3solutions.com                                                  **
	**      Phone :  +92 333 456 0482                                                            **
	**      URL   :  http://www.mtshahzad.com                                                    **
	**                                                                                           **
	***********************************************************************************************
	\*********************************************************************************************/

	// Database Configuration
	if (@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE)
	{
		define('DB_SERVER'   , 'localhost');
		define('DB_NAME'     , 'dbImc');
		define('DB_USER'     , 'root');
		define('DB_PASSWORD' , '3tree');

	    define("SITE_URL",    "http://www.3-tree.com/scrp/");
	}

	else
	{
		define("DB_SERVER",   "localhost");
		define("DB_NAME",     "dbimc");
		define("DB_USER",     "root");
		define("DB_PASSWORD", "");

		define("SITE_URL",    "http://localhost/SCRP/");
	}


	// User Queries Logging
	define("LOG_DB_TRANSACTIONS",   ((@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE) ? TRUE : FALSE));
	define("DB_LOGS_DIR",           ($_SERVER['DOCUMENT_ROOT'].((substr($_SERVER['DOCUMENT_ROOT'], -1) == "/") ? "" : "/").((@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE) ? "scrp/" : "SCRP/")."logs/"));
	define("API_CALLS_DIR",         ($_SERVER['DOCUMENT_ROOT'].((substr($_SERVER['DOCUMENT_ROOT'], -1) == "/") ? "" : "/").((@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE) ? "scrp/" : "SCRP/")."backups/api-calls/"));


	// Admin Control Panel Dir
	define("ADMIN_CP_DIR", "mscp");

	// Temp Dir
	define("TEMP_DIR", "temp/");

	// Images Dir
	define("DISTRICTS_IMG_DIR",    "images/districts/");
	define("SCHOOLS_IMG_DIR",      "images/schools/");
	define("CONTRACTORS_IMG_DIR",  "images/contractors/");
	define("INSPECTIONS_IMG_DIR",  "images/inspections/");
	define("BANNERS_IMG_DIR",      "images/banners/");

	define("NEWS_IMG_DIR",         "images/news/");
	define("NEWS_IMG_WIDTH",       80);
	define("NEWS_IMG_HEIGHT",      60);

	define("ADMINS_IMG_DIR",       "images/admins/");
	define("ADMINS_IMG_WIDTH",     160);
	define("ADMINS_IMG_HEIGHT",    160);


	// Files Dir
	define("INSPECTIONS_DOC_DIR", "files/inspections/");
	define("SURVEYS_DOC_DIR",     "files/surveys/");
    define("SORS_DOC_DIR",     "files/sors/");
	define("DOCUMENTS_DIR",       "files/documents/");		


	// Database Backup Config
	define('BACKUPS_DIR',               'backups/');
	define('DATABASE_FILE_NAME_FORMAT', 'db-scrp-%Y-%m-%d-%H-%i-%s.sql');
	define('WEBSITE_FILE_NAME_FORMAT',  'www-scrp-%Y-%m-%d-%H-%i-%s.zip');
?>