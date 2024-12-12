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
?>
              <?= $sPageContents ?>
              <br />
<?
	$sSQL = "SELECT * FROM tbl_faqs WHERE category_id='0' AND status='A' ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	if ($iCount > 0)
	{
		for ($i = 0; $i < $iCount; $i ++)
		{
			$sQuestion = $objDb->getField($i, "question");
			$sAnswer   = $objDb->getField($i, "answer");
?>
              <h4><?= $sQuestion ?></h4>
              <?= $sAnswer ?>
              <div class="br10"></div>
<?
		}
	}



	$bFaqs = false;


	$sSQL = "SELECT id, name, description FROM tbl_faq_categories WHERE status='A' ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iCategory    = $objDb->getField($i, "id");
		$sCategory    = $objDb->getField($i, "name");
		$sDescription = $objDb->getField($i, "description");


		$sSQL = "SELECT * FROM tbl_faqs WHERE category_id='$iCategory' AND status='A' ORDER BY position";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		if ($iCount2 > 0)
		{
			$bFaqs = true;
?>
              <br />
              <br />
              <h2><?= $sCategory ?></h2>
<?
			if (trim($sDescription) != "")
			{
?>
              <?= nl2br($sDescription) ?><br />
<?
			}
?>
              <br />
<?
			for ($j = 0; $j < $iCount2; $j ++)
			{
				$sQuestion = $objDb2->getField($j, "question");
				$sAnswer   = $objDb2->getField($j, "answer");
?>
              <h4><?= $sQuestion ?></h4>
              <?= $sAnswer ?>
              <div class="br10"></div>
<?
			}
		}
	}


	if ($bFaqs == false)
	{
?>
			  <div class="info noHide">No News Available at the moment!</div>
<?
	}
?>