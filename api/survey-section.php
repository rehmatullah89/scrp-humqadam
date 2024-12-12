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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );


	$sUser     = IO::strValue("User");
	$iSchool   = IO::intValue("School");
	$sCode     = IO::strValue("Code");
	$iSection  = IO::intValue("Section");
	$sDateTime = IO::strValue("DateTime");

	
	logApiCall($_POST);
	

	$aResponse           = array( );
	$aResponse['Status'] = "ERROR";


	if ($sUser == "" || $iSchool == 0 || $iSection == 0 || $sDateTime == "")
		$aResponse["Message"] = "Invalid Request";

	else
	{
		$sSQL = "SELECT id, name, email, provinces, districts, schools, status FROM tbl_admins WHERE MD5(id)='$sUser'";
		$objDb->query($sSQL);

		if ($objDb->getCount( ) == 0)
			$aResponse["Message"] = "Invalid User";

		else if ($objDb->getField(0, "status") != "A")
			$aResponse["Message"] = "User Account is Disabled";

		else
		{
			$iUser      = $objDb->getField(0, "id");
			$sName      = $objDb->getField(0, "name");
			$sEmail     = $objDb->getField(0, "email");
			$sProvinces = $objDb->getField(0, "provinces");
			$sDistricts = $objDb->getField(0, "districts");
			$sSchools   = $objDb->getField(0, "schools");

			$iProvinces = @explode(",", $sProvinces);
			$iDistricts = @explode(",", $sDistricts);
			$iSchools   = @explode(",", $sSchools);



			$sSQL = "SELECT district_id, province_id FROM tbl_schools WHERE id='$iSchool'";
			$objDb->query($sSQL);

			$iDistrict = $objDb->getField(0, "district_id");
			$iProvince = $objDb->getField(0, "province_id");


			if ($objDb->getCount( ) == 0)
				$aResponse["Message"] = "Invalid Request, no School Found!";

			else if ( ($sSchools != "" && !@in_array($iSchool, $iSchools)) || ($sSchools == "" && (!@in_array($iProvince, $iProvinces) || !@in_array($iDistrict, $iDistricts))) )
				$aResponse["Message"] = "Request denied, You don't have permissions for requested School!";

			else
			{
				$iSurveyId = getDbValue("id", "tbl_surveys", "school_id='$iSchool' AND enumerator='$sName' AND created_by='$iUser'", "id DESC");
				
				if ($iSurveyId > 0)
				{
					if (getDbValue("status", "tbl_survey_details", "survey_id='$iSurveyId' AND section_id='$iSection'") == "C")
					{
						$aResponse["Status"]  = "OK";
						$aResponse["Message"] = "Survey Section # {$iSection} already saved!";
					}
					
					else
					{					
						$sPictures = array( );
						
						
						$bFlag = $objDb->execute("BEGIN", true, $iUser, $sName, $sEmail);

						if (getDbValue("type", "tbl_survey_sections", "id='$iSection'") == "V")
						{
							$sAnswers = stripslashes(IO::strValue("Answers"));
							$sAnswers = @json_decode($sAnswers, true);
							
							
							foreach ($sAnswers as $sAnswerDetails)
							{
								$iQuestion = intval($sAnswerDetails['Question']);
								$sAnswer   = addslashes(trim($sAnswerDetails['Answer']));
								$sOther    = addslashes(trim($sAnswerDetails['Other']));
								

								$sSQL = "INSERT INTO tbl_survey_answers SET survey_id   = '$iSurveyId',
																			question_id = '$iQuestion',
																			answer      = '$sAnswer',
																			other       = '$sOther'";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
								
								if ($bFlag == true)
								{
									$sSurveyPictures = @glob($sRootDir.TEMP_DIR."EMIS{$sCode}-Q{$iQuestion}-*.*");
									
									
									foreach ($sSurveyPictures as $sPicture)
									{
										$sPicture   = @basename($sPicture);
										$sSurveyPic = str_replace("EMIS{$sCode}-", "{$iSurveyId}-", $sPicture);
										
										if (@copy(($sRootDir.TEMP_DIR.$sPicture), ($sRootDir.SURVEYS_DOC_DIR.$sSurveyPic)) == true && getDbValue("COUNT(1)", "tbl_survey_pictures", "survey_id='$iSurveyId' AND section_id='$iSection' AND picture='$sSurveyPic'") == 0)
										{
											$iPicture = getNextId("tbl_survey_pictures");
											
											
											$sSQL  = "INSERT INTO tbl_survey_pictures SET id          = '$iPicture',
																						  survey_id   = '$iSurveyId',
																						  section_id  = '$iSection',
																						  question_id = '$iQuestion',
																						  picture     = '$sSurveyPic'";
											$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);											
											
											if ($bFlag == false)
												break;
											
											
											$sPictures[] = $sPicture;
										}
									}
								}

								if ($bFlag == false)
									break;
							}
						}
						
						else if ($iSection == 3)
						{
							if ($bFlag == true)
							{
								$sSQL = "DELETE FROM tbl_survey_teacher_numbers WHERE survey_id='$iSurveyId'";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
							}
							
							if ($bFlag == true)
							{
								$sTypes   = array("T"  => "Teachers",
												  "SS" => "Staff",
												  "MS" => "Management");
												  
								$sNumbers = array("S"  => "Sanctioned",
												  "F"  => "Filled",
												  "R"  => "Regular");

												  
								foreach ($sTypes as $sType => $sEmployee)
								{
									foreach ($sNumbers as $sNumType => $sNumber)
									{
										$iMale   = IO::intValue("{$sEmployee}{$sNumber}Male");
										$iFemale = IO::intValue("{$sEmployee}{$sNumber}Female");
										$iBoth   = IO::intValue("{$sEmployee}{$sNumber}Both");
										
										
										$iNumber = getNextId("tbl_survey_teacher_numbers");
										
										$sSQL = "INSERT INTO tbl_survey_teacher_numbers SET id              = '$iNumber',
																							survey_id       = '$iSurveyId',
																							staff_type      = '$sType',
																							attendance_type = '$sNumType',
																							male_count      = '$iMale',
																							female_count    = '$iFemale',
																							both_count      = '$iBoth'";
										$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
										
										if ($bFlag == false)
											break;
									}
									
									
									if ($bFlag == false)
										break;
								}
							}

							
							if ($bFlag == true)
							{
								$sSurveyPictures = @glob($sRootDir.TEMP_DIR."EMIS{$sCode}-S{$iSection}-Q*-*.*");
								
								
								foreach ($sSurveyPictures as $sPicture)
								{
									$sPicture   = @basename($sPicture);
									$sSurveyPic = str_replace("EMIS{$sCode}-", "{$iSurveyId}-", $sPicture);
									
									if (@copy(($sRootDir.TEMP_DIR.$sPicture), ($sRootDir.SURVEYS_DOC_DIR.$sSurveyPic)) == true && getDbValue("COUNT(1)", "tbl_survey_pictures", "survey_id='$iSurveyId' AND section_id='$iSection' AND picture='$sSurveyPic'") == 0)
									{
										@list($sSurvey, $sSection, $sQuestion) = @explode("-", $sSurveyPic);
										
										$iQuestion = intval(str_replace("Q", "", $sQuestion));
										$iPicture  = getNextId("tbl_survey_pictures");
										
										
										$sSQL  = "INSERT INTO tbl_survey_pictures SET id          = '$iPicture',
																					  survey_id   = '$iSurveyId',
																					  section_id  = '$iSection',
																					  question_id = '$iQuestion',
																					  picture     = '$sSurveyPic'";
										$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);											
										
										if ($bFlag == false)
											break;

										
										$sPictures[] = $sPicture;
									}
								}
							}							
						}

						else if ($iSection == 4)
						{
							if ($bFlag == true)
							{
								$sSQL = "DELETE FROM tbl_survey_students_enrollment WHERE survey_id='$iSurveyId'";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
							}
							
							if ($bFlag == true)
							{				
								$sEnrolments = array("P"   => "Primary",
													 "M18" => "MiddleG18",
													 "M68" => "MiddleG68",
													 "H"   => "High",
													 "HS"  => "High");
								
								
								foreach ($sEnrolments as $sType => $sEnrolment)
								{
									$iEnrolmentMale   = IO::intValue("{$sEnrolment}Male");
									$iEnrolmentFemale = IO::intValue("{$sEnrolment}Female");
									$iEnrolmentBoth   = IO::intValue("{$sEnrolment}Both");
									
									
									$iEnrolment = getNextId("tbl_survey_students_enrollment");
									
									$sSQL = "INSERT INTO tbl_survey_students_enrollment SET id           = '$iEnrolment',
																							survey_id    = '$iSurveyId',
																							school_type  = '$sType',
																							male_count   = '$iEnrolmentMale',
																							female_count = '$iEnrolmentFemale',
																							both_count   = '$iEnrolmentBoth'";
									$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
									
									if ($bFlag == false)
										break;
								}
							}
							
							
							if ($bFlag == true)
							{
								$sSurveyPictures = @glob($sRootDir.TEMP_DIR."EMIS{$sCode}-S{$iSection}-*.*");
								
								
								foreach ($sSurveyPictures as $sPicture)
								{
									$sPicture   = @basename($sPicture);
									$sSurveyPic = str_replace("EMIS{$sCode}-", "{$iSurveyId}-", $sPicture);
									
									if (@copy(($sRootDir.TEMP_DIR.$sPicture), ($sRootDir.SURVEYS_DOC_DIR.$sSurveyPic)) == true && getDbValue("COUNT(1)", "tbl_survey_pictures", "survey_id='$iSurveyId' AND section_id='$iSection' AND picture='$sSurveyPic'") == 0)
									{							
										$iPicture = getNextId("tbl_survey_pictures");

										
										$sSQL  = "INSERT INTO tbl_survey_pictures SET id          = '$iPicture',
																					  survey_id   = '$iSurveyId',
																					  section_id  = '$iSection',
																					  question_id = '0',
																					  picture     = '$sSurveyPic'";
										$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);											
										
										if ($bFlag == false)
											break;

										
										$sPictures[] = $sPicture;
									}
								}
							}							
						}

						else if ($iSection == 5)
						{
							if ($bFlag == true)
							{
								$sSQL = "DELETE FROM tbl_survey_student_attendance_numbers WHERE survey_id='$iSurveyId'";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
							}
													
							if ($bFlag == true)
							{				
								for ($i = 0; $i <= 12; $i ++)
								{
									$iMorningBoys  = IO::intValue("MorningBoys{$i}");
									$iMorningGirls = IO::intValue("MorningGirls{$i}");
									$iEveningBoys  = IO::intValue("EveningBoys{$i}");
									$iEveningGirls = IO::intValue("EveningGirls{$i}");
									
									
									$iAttendance = getNextId("tbl_survey_student_attendance_numbers");
									
									$sSQL = "INSERT INTO tbl_survey_student_attendance_numbers SET id                  = '$iAttendance',
																								   survey_id           = '$iSurveyId',
																								   class_grade         = '$i',
																								   boys_count_morning  = '$iMorningBoys',
																								   girls_count_morning = '$iMorningGirls',
																								   boys_count_evening  = '$iEveningBoys',
																								   girls_count_evening = '$iEveningGirls'";
									$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
									
									if ($bFlag == false)
										break;
								}
							}
							
							if ($bFlag == true)
							{
								$sSQL = "DELETE FROM tbl_survey_differently_abled_student_numbers WHERE survey_id='$iSurveyId'";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
							}		

							if ($bFlag == true)
							{
								$iBoys            = IO::intValue("Boys");
								$iBoyGrades       = IO::intValue("BoyGrades");
								$iGirls           = IO::intValue("Girls");
								$iGirlGrades      = IO::intValue("GirlGrades");
								$sMorningFromTime = IO::strValue("MorningFromTime");
								$sMorningToTime   = IO::strValue("MorningToTime");
								$sEveningFromTime = IO::strValue("EveningFromTime");
								$sEveningToTime   = IO::strValue("EveningToTime");

								
								$sSQL  = "INSERT INTO tbl_survey_differently_abled_student_numbers (survey_id, boys_count, girls_count, boys_grades, girls_grades, morning_time_in, morning_time_out, evening_time_in, evening_time_out)
																							VALUES ('$iSurveyId', '$iBoys', '$iGirls', '$iBoyGrades', '$iGirlGrades', '$sMorningFromTime', '$sMorningToTime', '$sEveningFromTime', '$sEveningToTime')";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
							}
							
							
							if ($bFlag == true)
							{
								$sSurveyPictures = @glob($sRootDir.TEMP_DIR."EMIS{$sCode}-S{$iSection}-G*-*.*");
								
								
								foreach ($sSurveyPictures as $sPicture)
								{
									$sPicture   = @basename($sPicture);
									$sSurveyPic = str_replace("EMIS{$sCode}-", "{$iSurveyId}-", $sPicture);
									
									if (@copy(($sRootDir.TEMP_DIR.$sPicture), ($sRootDir.SURVEYS_DOC_DIR.$sSurveyPic)) == true && getDbValue("COUNT(1)", "tbl_survey_pictures", "survey_id='$iSurveyId' AND section_id='$iSection' AND picture='$sSurveyPic'") == 0)
									{
										@list($sSurvey, $sSection, $sQuestion) = @explode("-", $sSurveyPic);
										
										$iQuestion = intval(str_replace("G", "", $sQuestion));
										$iPicture  = getNextId("tbl_survey_pictures");
										
										
										$sSQL  = "INSERT INTO tbl_survey_pictures SET id          = '$iPicture',
																					  survey_id   = '$iSurveyId',
																					  section_id  = '$iSection',
																					  question_id = '$iQuestion',
																					  picture     = '$sSurveyPic'";
										$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);											
										
										if ($bFlag == false)
											break;

										
										$sPictures[] = $sPicture;
									}
								}
							}							
						}

						else if ($iSection == 13)
						{
							if ($bFlag == true)
							{
								$sSQL = "DELETE FROM tbl_survey_school_block_details WHERE survey_id='$iSurveyId'";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
							}
							
							if ($bFlag == true)
							{
								$sSQL = "DELETE FROM tbl_survey_school_blocks WHERE survey_id='$iSurveyId'";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
							}
							
							if ($bFlag == true)
							{
								$sToilets = array("1"  => "CRE",
												  "2"  => "CRO",
												  "3"  => "SWA",
												  "4"  => "TFS",
												  "5"  => "TMS",
												  "6"  => "TFT",
												  "7"  => "TMT",
												  "8"  => "UT",
												  "9"  => "US",
												  "10" => "TB");

														  
								for ($i = 1; $i <= 10; $i ++)
								{
									$sBlock = stripslashes(IO::strValue("Block{$i}"));								
									$sBlock = @json_decode($sBlock, true);

									$iAge       = intval($sBlock['Age']);
									$iStoreys   = intval($sBlock['Storeys']);
									$sMaterials = $sBlock['Materials'];
									
									
									$iBlock = getNextId("tbl_survey_school_blocks");
									
									$sSQL = "INSERT INTO tbl_survey_school_blocks SET id        = '$iBlock',
																					  survey_id = '$iSurveyId',
																					  `block`   = 'b{$i}',
																					  age       = '$iAge',
																					  storeys   = '$iStoreys',
																					  cm        = '$sMaterials'";
									$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
									
									if ($bFlag == true)
									{
										foreach ($sToilets as $iToilet => $sType)
										{
											$iToletsT = intval($sBlock["Room{$iToilet}T"]);
											$iToletsG = intval($sBlock["Room{$iToilet}G"]);
											$iToletsR = intval($sBlock["Room{$iToilet}R"]);
											$iToletsD = intval($sBlock["Room{$iToilet}D"]);
											
											
											$iDetails = getNextId("tbl_survey_school_block_details");
											
											$sSQL = "INSERT INTO tbl_survey_school_block_details SET id             = '$iDetails',
																									 survey_id      = '$iSurveyId',
																									 `block`        = 'b{$i}',
																									 room_type_code = '$sType',
																									 total          = '$iToletsT',
																									 good           = '$iToletsG',
																									 rehabilitation = '$iToletsR',
																									 dilapidated    = '$iToletsD'";
											$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
											
											if ($bFlag == false)
												break;
										}
									}
									
									if ($bFlag == false)
										break;
								}
							}
							
							if ($bFlag == true)
							{	
								$iOtherBlock1   = IO::intValue("OtherBlock1");
								$sOtherDetails1 = IO::strValue("OtherDetails1");
								$iOtherBlock2   = IO::intValue("OtherBlock2");
								$sOtherDetails2 = IO::strValue("OtherDetails2");
								$iOtherBlock3   = IO::intValue("OtherBlock3");
								$sOtherDetails3 = IO::strValue("OtherDetails3");
								$sComments      = IO::strValue("Comments");
								
								
								$sSQL = "INSERT INTO tbl_survey_school_other_blocks SET survey_id       = '$iSurveyId',
																						other_block_1   = '$iOtherBlock1',
																						other_details_1 = '$sOtherDetails1',
																						other_block_2   = '$iOtherBlock2',
																						other_details_2 = '$sOtherDetails2',
																						other_block_3   = '$iOtherBlock3',
																						other_details_3 = '$sOtherDetails3',
																						comments        = '$sComments'";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
							}
							
							
							if ($bFlag == true)
							{
								$sSurveyPictures = @glob($sRootDir.TEMP_DIR."EMIS{$sCode}-S{$iSection}-B*-*.*");
								
								
								foreach ($sSurveyPictures as $sPicture)
								{
									$sPicture   = @basename($sPicture);
									$sSurveyPic = str_replace("EMIS{$sCode}-", "{$iSurveyId}-", $sPicture);
									
									if (@copy(($sRootDir.TEMP_DIR.$sPicture), ($sRootDir.SURVEYS_DOC_DIR.$sSurveyPic)) == true && getDbValue("COUNT(1)", "tbl_survey_pictures", "survey_id='$iSurveyId' AND section_id='$iSection' AND picture='$sSurveyPic'") == 0)
									{
										@list($sSurvey, $sSection, $sQuestion) = @explode("-", $sSurveyPic);
										
										$iQuestion = intval(str_replace("B", "", $sQuestion));
										$iPicture  = getNextId("tbl_survey_pictures");
										
										
										$sSQL  = "INSERT INTO tbl_survey_pictures SET id          = '$iPicture',
																					  survey_id   = '$iSurveyId',
																					  section_id  = '$iSection',
																					  question_id = '$iQuestion',
																					  picture     = '$sSurveyPic'";
										$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);											
										
										if ($bFlag == false)
											break;

										
										$sPictures[] = $sPicture;
									}
								}
							}	
						}

						else if ($iSection == 14)
						{
							if ($bFlag == true)
							{
								$sSQL = "DELETE FROM tbl_survey_school_facilities WHERE survey_id='$iSurveyId'";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
							}
							
							if ($bFlag == true)
							{				
								$sFacilities = array("PG" => "Girls",
													 "PB" => "Boys",
													 "BT" => "Both",
													 "BW" => "BoundryWall",
													 "MG" => "MainGate",
													 "RW" => "RetainingWall");
								
								
								foreach ($sFacilities as $sType => $sFacility)
								{
									$fFacilityT        = IO::floatValue("{$sFacility}T");
									$fFacilityG        = IO::floatValue("{$sFacility}G");
									$fFacilityR        = IO::floatValue("{$sFacility}R");
									$fFacilityD        = IO::floatValue("{$sFacility}D");
									$sFacilityMaterial = IO::strValue("{$sFacility}Material");
									$fFacilityHeight   = IO::floatValue("{$sFacility}Height");
									
									
									$iFacility = getNextId("tbl_survey_school_facilities");
									
									$sSQL = "INSERT INTO tbl_survey_school_facilities SET id             = '$iFacility',
																						  survey_id      = '$iSurveyId',
																						  `type`         = '$sType',
																						  total          = '$fFacilityT',
																						  good           = '$fFacilityG',
																						  rehabilitation = '$fFacilityR',
																						  dilapidated    = '$fFacilityD',
																						  material       = '$sFacilityMaterial',
																						  height         = '$fFacilityHeight'";
									$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
									
									if ($bFlag == false)
										break;
								}
							}
							
							
							if ($bFlag == true)
							{
								$sSurveyPictures = @glob($sRootDir.TEMP_DIR."EMIS{$sCode}-S{$iSection}-Q*-*.*");
								
								
								foreach ($sSurveyPictures as $sPicture)
								{
									$sPicture   = @basename($sPicture);
									$sSurveyPic = str_replace("EMIS{$sCode}-", "{$iSurveyId}-", $sPicture);
									
									if (@copy(($sRootDir.TEMP_DIR.$sPicture), ($sRootDir.SURVEYS_DOC_DIR.$sSurveyPic)) == true && getDbValue("COUNT(1)", "tbl_survey_pictures", "survey_id='$iSurveyId' AND section_id='$iSection' AND picture='$sSurveyPic'") == 0)
									{
										@list($sSurvey, $sSection, $sQuestion) = @explode("-", $sSurveyPic);
										
										$iQuestion = intval(str_replace("Q", "", $sQuestion));
										$iPicture  = getNextId("tbl_survey_pictures");
										
										
										$sSQL  = "INSERT INTO tbl_survey_pictures SET id          = '$iPicture',
																					  survey_id   = '$iSurveyId',
																					  section_id  = '$iSection',
																					  question_id = '$iQuestion',
																					  picture     = '$sSurveyPic'";
										$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);											
										
										if ($bFlag == false)
											break;

										
										$sPictures[] = $sPicture;
									}
								}
							}								
						}
						
						else if ($iSection == 15)
						{
							if ($bFlag == true)
							{
								$sSQL = "DELETE FROM tbl_survey_checklist WHERE survey_id='$iSurveyId'";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
							}
							
							if ($bFlag == true)
							{
								$fTotalArea         = IO::floatValue("TotalArea");
								$iStudents          = IO::intValue("Students");
								$sNorthArrow        = IO::strValue("NorthArrow");
								$sClassRooms        = IO::strValue("ClassRooms");
								$sClassRoomSize     = IO::strValue("ClassRoomSize");
								$sToilets           = IO::strValue("Toilets");
								$sPlaygrounds       = IO::strValue("Playgrounds");
								$sWaterTanks        = IO::strValue("WaterTanks");
								$sSepticTanks       = IO::strValue("SepticTanks");
								$sBoundaryWall      = IO::strValue("BoundaryWall");
								$sDrainage          = IO::strValue("Drainage");
								$sNewDevelopment    = IO::strValue("NewDevelopment");
								$sMeasurements      = IO::strValue("Measurements");
								$sSlopes            = IO::strValue("Slopes");
								$sTreesSmaller      = IO::strValue("TreesSmaller");
								$sTreesLarger       = IO::strValue("TreesLarger");
								$sElectricity1Phase = IO::strValue("Electricity1Phase");
								$sElectricity3Phase = IO::strValue("Electricity3Phase");
								
								
								$sPicture  = "EMIS{$sCode}-site-plan.jpg";
								$sSitePlan = "{$iSurveyId}-{$iSection}-sp-site-plan.jpg";
								
								if (@file_exists($sRootDir.TEMP_DIR.$sPicture))
								{
									if (@copy(($sRootDir.TEMP_DIR.$sPicture), ($sRootDir.SURVEYS_DOC_DIR."{$iSurveyId}-{$iSection}-sp-site-plan.jpg")))								
										$sPictures[] = $sPicture;
									
									else
										$sSitePlan = "";
								}
								
								else
									$sSitePlan= "";

								
								
								$sSQL = "INSERT INTO tbl_survey_checklist SET survey_id           = '$iSurveyId',
																			  site_plan           = '$sSitePlan',											 
																			  drawing             = '',
																			  structure           = '',
																			  total_area          = '$fTotalArea',
																			  students            = '$iStudents',
																			  north_arrow         = '$sNorthArrow',
																			  class_rooms         = '$sClassRooms',
																			  class_room_size     = '$sClassRoomSize',
																			  toilets             = '$sToilets',
																			  playgrounds         = '$sPlaygrounds',
																			  water_tanks         = '$sWaterTanks',
																			  spetic_tanks        = '$sSepticTanks',
																			  boundary_wall       = '$sBoundaryWall',
																			  drainage            = '$sDrainage',
																			  new_development     = '$sNewDevelopment',
																			  measurements        = '$sMeasurements',
																			  slopes              = '$sSlopes',
																			  trees_smaller       = '$sTreesSmaller',											 
																			  trees_larger        = '$sTreesLarger',
																			  electricity_1_phase = '$sElectricity1Phase',
																			  electricity_3_phase = '$sElectricity3Phase'";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
							}

							if ($bFlag == true)
							{
								$sSurveyPictures = @glob($sRootDir.TEMP_DIR."EMIS{$sCode}-site-plan-*.*");
								
								
								foreach ($sSurveyPictures as $sPicture)
								{
									$sPicture   = @basename($sPicture);
									$sSurveyPic = str_replace("EMIS{$sCode}-", "{$iSurveyId}-", $sPicture);
									
									if (@copy(($sRootDir.TEMP_DIR.$sPicture), ($sRootDir.SURVEYS_DOC_DIR.$sSurveyPic)) == true && getDbValue("COUNT(1)", "tbl_survey_pictures", "survey_id='$iSurveyId' AND section_id='$iSection' AND picture='$sSurveyPic'") == 0)
									{
										$iPicture = getNextId("tbl_survey_pictures");
										
										
										$sSQL  = "INSERT INTO tbl_survey_pictures SET id          = '$iPicture',
																					  survey_id   = '$iSurveyId',
																					  section_id  = '$iSection',
																					  question_id = '0',
																					  picture     = '$sSurveyPic'";
										$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);											
										
										if ($bFlag == false)
											break;
										
										
										$sPictures[] = $sPicture;
									}
								}
							}
						}					
						
						else if ($iSection == 16)
						{
							if ($bFlag == true)
							{
								$sSQL = "DELETE FROM tbl_survey_declaration WHERE survey_id='$iSurveyId'";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
							}
							
							if ($bFlag == true)
							{
								$sServingDate = IO::strValue("ServingDate");
								$sSignDate    = IO::strValue("SignDate");
								
								if (strlen($sServingDate) == 4)
									$sServingDate = "{$sServingDate}-01-01";
								
								
								$sSQL = "INSERT INTO tbl_survey_declaration SET survey_id    = '$iSurveyId',
																				serving_date = '$sServingDate',
																				sign_date    = '$sSignDate'";
								$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
							}
							
							
							if ($bFlag == true)
							{
								$sSurveyPictures = @glob($sRootDir.TEMP_DIR."EMIS{$sCode}-S{$iSection}-*.*");
								
								
								foreach ($sSurveyPictures as $sPicture)
								{
									$sPicture   = @basename($sPicture);
									$sSurveyPic = str_replace("EMIS{$sCode}-", "{$iSurveyId}-", $sPicture);
									
									if (@copy(($sRootDir.TEMP_DIR.$sPicture), ($sRootDir.SURVEYS_DOC_DIR.$sSurveyPic)) == true && getDbValue("COUNT(1)", "tbl_survey_pictures", "survey_id='$iSurveyId' AND section_id='$iSection' AND picture='$sSurveyPic'") == 0)
									{							
										$iPicture = getNextId("tbl_survey_pictures");

										
										$sSQL  = "INSERT INTO tbl_survey_pictures SET id          = '$iPicture',
																					  survey_id   = '$iSurveyId',
																					  section_id  = '$iSection',
																					  question_id = '0',
																					  picture     = '$sSurveyPic'";
										$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);											
										
										if ($bFlag == false)
											break;

										
										$sPictures[] = $sPicture;
									}
								}
							}							
						}
						

						if ($bFlag == true)
						{
							$sStatus = (($iSection == 15) ? "I" : "C");
							
							
							$sSQL = "UPDATE tbl_survey_details SET status      = '$sStatus',
																   modified_by = '$iUser',
																   modified_at = '$sDateTime'
									 WHERE survey_id='$iSurveyId' AND section_id='$iSection'";
							$bFlag = $objDb->execute($sSQL, true, $iUser, $sName, $sEmail);
						}
						
						
						if ($bFlag == true)
						{
							$objDb->execute("COMMIT", true, $iUser, $sName, $sEmail);
							
							$aResponse["Status"]  = "OK";
							$aResponse["Message"] = "Survey Section # {$iSection} saved successfully!";
							
							
							foreach ($sPictures as $sPicture)							
							{
								@unlink($sRootDir.TEMP_DIR.$sPicture);
							}
						}

						else
						{
							$aResponse["Message"] = "An ERROR occured, please try again.";					

							$objDb->execute("ROLLBACK", true, $iUser, $sName, $sEmail);
						}
					}
				}
				
				else
					$aResponse["Message"] = "No Survey Entry Found";
			}
		}
	}

	print @json_encode($aResponse);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>