<?php

	error_reporting(0);

	header('Content-Type: application/json');

	$request = @$_GET['request'];
	$data    = @$_POST['data'];
	$paket   = @$_POST['paket'];
	$output  = array();
	//$paket   = strtolower($data['firstDayPostpaidReturnModel']['firstDayInfo']['type']);

	function clearPostData($string){
		$string = stripslashes($string);
		$string = str_replace('/', '', $string);

		return $string;
	}


	if($paket == 'postpaid'){
		$transform = array(
			'name'              => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['name']),
			'tarif_name'        => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['tariff']['name']),
			'tarif_content'     => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['tariff']['content']),
			'tarif_description' => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['tariff']['description']),
			'tarif_fee'         => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['tariff']['fee']),
			'addons'            => ($data['firstDayPostpaidReturnModel']['firstDayInfo']['addons']),

			'expectedFee'       => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['invoice']['expectedFee']),
			'monthlyTotal'      => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['invoice']['monthlyTotal']),
			'monthlyTariffFee' => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['invoice']['monthlyTariffFee']),
			'monthlyAddonsFee' => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['invoice']['monthlyAddonsFee']),
			'monthlyDeviceFee' => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['invoice']['monthlyDeviceFee']),
			'monthlyTaxesFee' => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['invoice']['monthlyTaxesFee']),
			'cutOffDate' => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['invoice']['cutOffDate']),
			'cutOffDateShort' => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['invoice']['cutOffDateShort']),
			'proratedContent' => clearPostData($data['firstDayPostpaidReturnModel']['firstDayInfo']['invoice']['proratedContent'])
		);
	}else if($paket == 'prepaid'){
		$transform = array(
			'name' => clearPostData($data['firstDayPrepaidReturnModel']['firstDayInfo']['name']),
			'tarif_name' => clearPostData($data['firstDayPrepaidReturnModel']['firstDayInfo']['tariff']['name']),
			'tarif_content' => clearPostData($data['firstDayPrepaidReturnModel']['firstDayInfo']['tariff']['content']),
			'tarif_description' => clearPostData($data['firstDayPrepaidReturnModel']['firstDayInfo']['tariff']['description']),
			'tarif_fee' => clearPostData($data['firstDayPrepaidReturnModel']['firstDayInfo']['tariff']['fee']),
			'addons' => ($data['firstDayPrepaidReturnModel']['firstDayInfo']['addons']),

			'monthlyTotal' => clearPostData($data['firstDayPrepaidReturnModel']['firstDayInfo']['prepaid']['monthlyTotal']),
			'monthlyTariffFee' => clearPostData($data['firstDayPrepaidReturnModel']['firstDayInfo']['prepaid']['monthlyTariffFee']),
			'monthlyAddonsFee' => clearPostData($data['firstDayPrepaidReturnModel']['firstDayInfo']['prepaid']['monthlyAddonsFee']),
			'monthlyDeviceFee' => clearPostData($data['firstDayPrepaidReturnModel']['firstDayInfo']['prepaid']['monthlyDeviceFee']),
			'monthlyTaxesFee' => clearPostData($data['firstDayPrepaidReturnModel']['firstDayInfo']['prepaid']['monthlyTaxesFee']),
			'renewalInfo' => clearPostData($data['firstDayPrepaidReturnModel']['firstDayInfo']['prepaid']['renewalInfo']),
			'startingPackageInfo' => clearPostData($data['firstDayPrepaidReturnModel']['firstDayInfo']['startingPackageInfo']),
			'vfyanimdaInfo' => clearPostData($data['firstDayPrepaidReturnModel']['firstDayInfo']['vfyanimdaInfo'])
		);
	}
	
	function clearText($string){
		$string = trim(str_replace(array("\n", "\r"), "", $string));
		return $string;
	}



	if($request == 'nodes'){
		function createNode($text, $elementName, $delay, $soundDelay, $finish, $options = array()){
			global $paket;

			$output = array();
			$file   = file_get_contents('nodes/'.$paket.'/'.$elementName);

			preg_match_all('/([0-9]*\.[0-9]+|[0-9]+)./', $file, $matches);
			$matches = $matches[0];

			for($i=0;$i<count($matches);$i+=3){
				$index = (int) $matches[$i];
				$number001 = (float) $matches[$i+1];
				$number002 = (float) $matches[$i+2];

				if(@$output[$index]){
					$output[$index][] = array($number001, $number002);
				}else{
					$output[$index] = array(array($number001, $number002));
				}
			}

			return array(
				'name' => $elementName, 
				'text' => $text, 
				'points' => $output,
				'delay' => $delay,
				'finish' => $finish,
				'soundDelay' => $soundDelay,
				'options' => $options
			);
		}

		function splitLines($text, $fontSize = 26){
			$words = explode(' ', $text);
			$lines = array();

			$temp  = array();
			$nextTemp  = array();

			foreach ($words as $key => $word) {
				$nextTemp = $temp;
				$nextTemp[] = $words[$key+1];

				if(strlen(implode(' ', $nextTemp)) < 26){
					$temp[] = $word;
				}else{
					$lines[] = trim(implode(' ', $temp));
					$temp = array();
					$temp[] = $word;
				}
			}

			return explode("::::", wordwrap($text, $fontSize, "::::"));
		}

		//Postpaid

		if($paket == 'postpaid'){

			$output[] = createNode($transform['name'], 'kapi', 367, 390, 493, 
				array(
					'rectangle' => array(
						'width' => 300, 
						'height' => 50,
						'pivot' => array(-150, -25),
						'anchor' => array(0.5, 0.5)
					),
					'fontAlign' => 'center',
					'fontSize' => 30,
					'color' => '#ffffff',
					'strokeColor' => '#666666',
					'strokeWidth' => 0,
					'fontAlpha' => 0.66
				)
			);

			//Tarife içerikleri
			$masaOffset = -145;

				/*
			//Paket
			$output[] = createNode($transform['tarif_name'], 'masa', 0, 1030, 1090, 
				array(
					'rectangle' => array(
						'width' => 600, 
						'height' => 600,
						'pivot' => array($masaOffset, -120),
						'anchor' => array(0, 0.5)
					),
					'fontAlign' => 'center',
					'fontSize' => 34,
					'color' => '#000000',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.76
				)
			);
			*/
			
			$text = $transform['tarif_name'];
			$text = clearText($text);

			$stringLength = strlen($text);

			$fontSize = 34;
			$containerLength = 20;
			$marginTop = 0;
			$lineHeight = 36;


			//$lines = explode(', ', $text);
			$lines = splitLines($text, $containerLength);
			$lastLength = 0;

			foreach ($lines as $key => $line) {
				if(trim($line)){
					$output[] = createNode(trim($line), 'masa', 0, 1030 + $key * 5, 1090, 
						array(
							'rectangle' => array(
								'width' => 600, 
								'height' => 600,
								'pivot' => array($masaOffset + 5, -140 - 50 * $key ),
								'anchor' => array(0, 1)
							),
							'fontAlign' => 'center',
							'fontSize' => 34,
							'color' => '#000000',
							'strokeColor' => '#000000',
							'strokeWidth' => 1,
							'fontAlpha' => 0.76
						)
					);
					
					$lastLength = $masaOffset2 - 10 - 30 * $key;
				}
			}

			

			/*
			$output[] = createNode('/Ay', 'masa', 0, 1040, 1090, 
				array(
					'rectangle' => array(
						'width' => 600, 
						'height' => 600,
						'pivot' => array($masaOffset - 135, -380),
						'anchor' => array(0, 1)
					),
					'fontAlign' => 'center',
					'fontSize' => 25,
					'color' => '#000000',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.66
				)
			);
			*/

			$masaOffset2 = -130;
			//Tarife içeriği
			/*
			$output[] = createNode('20 GB İnternet', 'masa', 0, 1110, 1210, 
				array(
					'rectangle' => array(
						'width' => 600, 
						'height' => 600,
						'pivot' => array($masaOffset, $masaOffset2),
						'anchor' => array(0, 1)
					),
					'fontAlign' => 'center',
					'fontSize' => 28,
					'color' => '#000000',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.76
				)
			);
			*/
			
			$text = $transform['tarif_content'];
			$text = clearText($text);

			$stringLength = strlen($text);

			$fontSize = 25;
			$containerLength = 26;
			$marginTop = 0;
			$lineHeight = 32;


			$lines = explode(', ', $text);
			//$lines = splitLines($text, $containerLength);
			$lastLength = 0;

			foreach ($lines as $key => $line) {
				if(trim($line)){
					$output[] = createNode('◈ '.trim($line), 'masa', 0, 1110 + $key * 5, 1210, 
						array(
							'rectangle' => array(
								'width' => 600, 
								'height' => 600,
								'pivot' => array($masaOffset + 5, $masaOffset2 + $marginTop - 30 * $key ),
								'anchor' => array(0, 1)
							),
							'fontAlign' => 'center',
							'fontSize' => 28,
							'color' => '#000000',
							'strokeColor' => '#000000',
							'strokeWidth' => 1,
							'fontAlpha' => 0.76
						)
					);
					
					$lastLength = $masaOffset2 - 10 - 30 * $key;
				}
			}
			
			$output[] = createNode($transform['tarif_fee'], 'masa', 0, 1040, 1090, 
				array(
					'rectangle' => array(
						'width' => 600, 
						'height' => 600,
						'pivot' => array($masaOffset - 25, -360),
						'anchor' => array(0, 0.5)
					),
					'fontAlign' => 'center',
					'fontSize' => 39,
					'color' => '#000000',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.76
				)
			);

			$text = $transform['tarif_description'];
			$text = clearText($text);

			$lines = splitLines($text);

			foreach ($lines as $key => $line) {
				$linetext = clearText($line);

				if($linetext){
					$output[] = createNode($linetext, 'masa', 0, 1215 + $key * 5, 1350, 
						array(
							'rectangle' => array(
								'width' => 600, 
								'height' => 600,
								'pivot' => array($masaOffset + 5, $masaOffset2 - 10 - 30 * $key),
								'anchor' => array(0, 1)
							),
							'fontAlign' => 'center',
							'fontSize' => 24,
							'color' => '#000000',
							'strokeColor' => '#000000',
							'strokeWidth' => 1,
							'fontAlpha' => 0.76
						)
					);
				}
			}

			$lineTop = 0;

			$features = $transform['addons'];
			foreach ($features as $key => $line) {

				$text  = '◈ '.$line['commercialName'];
				$lines = splitLines($text);
				

				foreach ($lines as $index => $line) {
					$output[] = createNode(clearText($line), 'masa', 0, 1365 + $key * 5, 1490, 
						array(
							'rectangle' => array(
								'width' => 600, 
								'height' => 600,
								'pivot' => array($masaOffset + 5, $masaOffset2 - 10 - 30 * $lineTop),
								'anchor' => array(0, 1)
							),
							'fontAlign' => 'center',
							'fontSize' => 24,
							'color' => '#000000',
							'strokeColor' => '#000000',
							'strokeWidth' => 1,
							'fontAlpha' => 0.76
						)
					);

					$lineTop++;
				}

				$lineTop+=0.5;
			}

			//duvar1
			$output[] = createNode('', 'duvar1', 0, 1548, 1597, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-70, -180),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'bold',
					'fontAlign' => 'center',
					'fontSize' => 48,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 0,
					'fontAlpha' => 0.5
				)
			);

			//duvar2
			$output[] = createNode($transform['monthlyTotal'], 'duvar2', 0, 1598, 1778, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-70, -180),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'bold',
					'fontAlign' => 'center',
					'fontSize' => 48,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.76,
					'animate' => false
				)
			);

			$output[] = createNode($transform['monthlyTariffFee'], 'duvar2', 0, 1598, 1778, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-100, -323),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'bold',
					'fontAlign' => 'center',
					'fontSize' => 24,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.96
				)
			);

			$output[] = createNode($transform['monthlyAddonsFee'], 'duvar2', 0, 1598, 1778, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-140, -356),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'bold',
					'fontAlign' => 'center',
					'fontSize' => 24,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.96
				)
			);

			$output[] = createNode($transform['monthlyDeviceFee'], 'duvar2', 0, 1598, 1778, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-190, -392),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'bold',
					'fontAlign' => 'center',
					'fontSize' => 24,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.96
				)
			);

			$output[] = createNode($transform['monthlyTaxesFee'], 'duvar2', 0, 1598, 1778, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-282, -430),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'bold',
					'fontAlign' => 'center',
					'fontSize' => 24,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.96
				)
			);

			//kısa metin gelecek, her ayın 21'inde tarzı
			$output[] = createNode($transform['cutOffDateShort'], 'duvar2', 0, 1798, 1895, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-50, -225),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'normal',
					'fontAlign' => 'center',
					'fontSize' => 32,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.96
				)
			);

			$text = $transform['cutOffDate'];

			$lines = splitLines($text);

			foreach ($lines as $key => $line) {
				$output[] = createNode($line, 'duvar2', 0, 1798 + $key * 5, 1895, 
					array(
						'rectangle' => array(
							'width' => 400, 
							'height' => 600,
							'pivot' => array($masaOffset + 60, -305 - 30 * $key),
							'anchor' => array(0, 0)
						),
						'fontAlign' => 'left',
						'fontSize' => 24,
						'color' => '#ffffff',
						'strokeColor' => '#000000',
						'strokeWidth' => 1,
						'fontAlpha' => 0.96
					)
				);
			}

			//duvar3
			$text = $transform['proratedContent'];
			$text = clearText($text);

			$stringLength = strlen($text);

			if($stringLength < 25){
				$fontSize = 28;
				$lineHeight = 30;
				$containerLength = 26;
				$marginTop = -30;
			}else if($stringLength < 55){
				$fontSize = 25;
				$containerLength = 26;
				$marginTop = 0;
				$lineHeight = 32;
			}else{
				$fontSize = 20;
				$lineHeight = 25;
				$containerLength = 35;
				$marginTop = 5;
			}

			//$lines = splitLines($text, $containerLength);
			$lines = explode(', ', $text);

			foreach ($lines as $key => $line) {
				$linetext = trim($line);

				if($linetext){
					$output[] = createNode('◈ '.$linetext, 'duvar3', 0, 2184 + $key * 5, 2320,
						array(
							'rectangle' => array(
								'width' => 400, 
								'height' => 600,
								'pivot' => array($masaOffset + 100, $masaOffset2 + $marginTop - ($lineHeight - 3) * $key - 75),
								'anchor' => array(0, 0)
							),
							'fontAlign' => 'left',
							'fontSize' => $fontSize - 3,
							'color' => '#ffffff',
							'strokeColor' => '#000000',
							'strokeWidth' => 1,
							'fontAlpha' => 0.76
						)
					);
				}
			}

			/*
			$output[] = createNode($transform['proratedContent'], 'duvar3', 0, 2184, 2320, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-50, -205),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'normal',
					'fontAlign' => 'center',
					'fontSize' => 25,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.96
				)
			);
			*/ 

			$output[] = createNode($transform['expectedFee'], 'duvar3', 0, 2230, 2320, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-50, -400),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'bold',
					'fontAlign' => 'center',
					'fontSize' => 34,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.96
				)
			);
		}

		if($paket == 'prepaid'){
			$output[] = createNode($transform['name'], 'kapi', 0, 350, 493, 
				array(
					'rectangle' => array(
						'width' => 300, 
						'height' => 50,
						'pivot' => array(-150, -25),
						'anchor' => array(0.5, 0.5)
					),
					'fontAlign' => 'center',
					'fontSize' => 30,
					'color' => '#ffffff',
					'strokeColor' => '#666666',
					'strokeWidth' => 0,
					'fontAlpha' => 0.66
				)
			);

			//Tarife içerikleri
			$masaOffset = -140;

			$text = $transform['startingPackageInfo'];

			$lines = splitLines($text, 30);

			foreach ($lines as $key => $line) {
				$output[] = createNode($line, 'masa', 0, 902 + $key * 30, 1050, 
					array(
						'rectangle' => array(
							'width' => 600, 
							'height' => 600,
							'pivot' => array($masaOffset, -100 - 35 * $key),
							'anchor' => array(0, 0)
						),
						'fontAlign' => 'left',
						'fontSize' => 28,
						'color' => '#000000',
						'strokeColor' => '#000000',
						'strokeWidth' => 1,
						'fontAlpha' => 0.96
					)
				);
			}

			/*
			$output[] = createNode($transform['tarif_name'], 'masa', 0, 1090, 1140, 
				array(
					'rectangle' => array(
						'width' => 600, 
						'height' => 600,
						'pivot' => array($masaOffset, -120),
						'anchor' => array(0, 0.5)
					),
					'fontAlign' => 'center',
					'fontSize' => 34,
					'color' => '#000000',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.76
				)
			);
			*/

			$text = $transform['tarif_name'];
			$text = clearText($text);

			$stringLength = strlen($text);

			if($stringLength < 25){
				$fontSize = 28;
				$lineHeight = 30;
				$containerLength = 26;
				$marginTop = -30;
			}else if($stringLength < 55){
				$fontSize = 25;
				$containerLength = 26;
				$marginTop = 0;
				$lineHeight = 32;
			}else{
				$fontSize = 20;
				$lineHeight = 25;
				$containerLength = 35;
				$marginTop = 5;
			}



			$lines = splitLines($text, $containerLength);
			$lastLength = 0;

			foreach ($lines as $key => $line) {
				$output[] = createNode($line, 'masa', 0, 1090 + $key * 5, 1140, 
					array(
						'rectangle' => array(
							'width' => 600, 
							'height' => 600,
							'pivot' => array($masaOffset + 5, $masaOffset2 + $marginTop - $lineHeight * $key - 120),
							'anchor' => array(0, 1)
						),
						'fontAlign' => 'center',
						'fontSize' => $fontSize,
						'color' => '#000000',
						'strokeColor' => '#000000',
						'strokeWidth' => 1,
						'fontAlpha' => 0.76
					)
				);
				
				$lastLength = $masaOffset2 - 10 - 30 * $key;
			}



			$text = $transform['tarif_fee'];
			$text = clearText($text);

			$stringLength = strlen($text);

			if($stringLength < 25){
				$fontSize = 28;
				$lineHeight = 30;
				$containerLength = 26;
				$marginTop = -30;
			}else if($stringLength < 55){
				$fontSize = 25;
				$containerLength = 26;
				$marginTop = 0;
				$lineHeight = 32;
			}else{
				$fontSize = 20;
				$lineHeight = 25;
				$containerLength = 35;
				$marginTop = -20;
			}



			$lines = splitLines($text, $containerLength);
			$lastLength = 0;

			foreach ($lines as $key => $line) {
				$output[] = createNode($line, 'masa', 0, 1110 + $key * 5, 1140, 
					array(
						'rectangle' => array(
							'width' => 600, 
							'height' => 600,
							'pivot' => array($masaOffset - 35, $masaOffset2 + $marginTop - $lineHeight * $key - 345),
							'anchor' => array(0, 1)
						),
						'fontAlign' => 'center',
						'fontSize' => $fontSize,
						'color' => '#000000',
						'strokeColor' => '#000000',
						'strokeWidth' => 1,
						'fontAlpha' => 0.76
					)
				);
				
				$lastLength = $masaOffset2 - 10 - 30 * $key;
			}

			/*
			$output[] = createNode('/Ay', 'masa', 0, 1110, 1140, 
				array(
					'rectangle' => array(
						'width' => 600, 
						'height' => 600,
						'pivot' => array($masaOffset - 135, -380),
						'anchor' => array(0, 1)
					),
					'fontAlign' => 'center',
					'fontSize' => 25,
					'color' => '#000000',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.66
				)
			);
			*/

			//TARİFE içeriği
			$masaOffset2 = -120;

			/*
			$text = $transform['tarif_content'];
			$text = clearText($text);

			$stringLength = strlen($text);

			$fontSize = 25;
			$containerLength = 26;
			$marginTop = 0;
			$lineHeight = 32;

			$lines = explode(', ', $text);

			foreach ($lines as $key => $line) {
				if(strlen($line) > 1){
					$output[] = createNode('◈ '.trim($line), 'masa', 0, 1140 + $key * 5, 1270, 
						array(
							'rectangle' => array(
								'width' => 600, 
								'height' => 600,
								'pivot' => array($masaOffset + 5, $masaOffset2 + $marginTop - $lineHeight * $key),
								'anchor' => array(0, 1)
							),
							'fontAlign' => 'center',
							'fontSize' => $fontSize,
							'color' => '#000000',
							'strokeColor' => '#000000',
							'strokeWidth' => 1,
							'fontAlpha' => 0.76
						)
					);
				}
			}
			*/

			/*
			$text = $transform['tarif_content'];
			$text = clearText($text);

			$stringLength = strlen($text);

			$fontSize = 25;
			$containerLength = 26;
			$marginTop = 0;
			$lineHeight = 32;

			$lines = explode(', ', $text);

			foreach ($lines as $key => $line) {
				if(strlen($line) > 1){
					$lines = splitLines($line);

					foreach ($lines as $key => $line) {
						$output[] = createNode('◈ '.trim($line), 'masa', 0, 1140 + $key * 5, 1270, 
							array(
								'rectangle' => array(
									'width' => 600, 
									'height' => 600,
									'pivot' => array($masaOffset + 5, $masaOffset2 + $marginTop - $lineHeight * $key),
									'anchor' => array(0, 1)
								),
								'fontAlign' => 'center',
								'fontSize' => $fontSize,
								'color' => '#000000',
								'strokeColor' => '#000000',
								'strokeWidth' => 1,
								'fontAlpha' => 0.76
							)
						);
					}
				}
			}
			*/

			$lineTop = 0;

			$text = $transform['tarif_content'];
			$text = clearText($text);

			$stringLength = strlen($text);

			$fontSize = 25;
			
			$marginTop = 0;
			

			$features = explode(', ', $text);

			$isList = count($features) > 1 ? true : false;

			if($isList){
				$containerLength = 26;
				$lineHeight = 19;
			}else{
				$isList = false;
				$lastLength = 0;

				$containerLength = 25;
				$lineHeight = 25;
			}

			if($isList){
				foreach ($features as $key => $text) {
					$lines = splitLines($text, $containerLength);
					
					foreach ($lines as $index => $line) {
						$output[] = createNode(($isList ? '◈ ' : '').trim($line), 'masa', 0, 1140 + $key * 5, 1270,
							array(
								'rectangle' => array(
									'width' => 600, 
									'height' => 600,
									'pivot' => array($masaOffset + 5, $masaOffset2 - 5 - $lineHeight * $lineTop),
									'anchor' => array(0, 1)
								),
								'fontAlign' => 'center',
								'fontSize' => $fontSize,
								'color' => '#000000',
								'strokeColor' => '#000000',
								'strokeWidth' => 1,
								'fontAlpha' => 0.76
							)
						);

						$lineTop++;
					}

					$lineTop+=0.5;
				}
			}else{
				$lines = splitLines($text, $containerLength);
					
				foreach ($lines as $index => $line) {
					$output[] = createNode(trim($line), 'masa', 0, 1140 + $key * 5, 1270,
						array(
							'rectangle' => array(
								'width' => 600, 
								'height' => 600,
								'pivot' => array($masaOffset + 5, $masaOffset2 - 5 - $lineHeight * $lineTop),
								'anchor' => array(0, 1)
							),
							'fontAlign' => 'center',
							'fontSize' => $fontSize,
							'color' => '#000000',
							'strokeColor' => '#000000',
							'strokeWidth' => 1,
							'fontAlpha' => 0.76
						)
					);

					$lineTop++;
				}
			}

			$text = $transform['tarif_description'];
			$text = clearText($text);

			$lines = splitLines($text);

			foreach ($lines as $key => $line) {
				if(trim($line)){
					$output[] = createNode($line, 'masa', 0, 1300 + $key * 5, 1390, 
						array(
							'rectangle' => array(
								'width' => 600, 
								'height' => 600,
								'pivot' => array($masaOffset + 5, $masaOffset2 - 10 - 30 * $key),
								'anchor' => array(0, 1)
							),
							'fontAlign' => 'center',
							'fontSize' => 24,
							'color' => '#000000',
							'strokeColor' => '#000000',
							'strokeWidth' => 1,
							'fontAlpha' => 0.76
						)
					);
				}
			}

			$lineTop = 0;

			$features = $transform['addons'];
			foreach ($features as $key => $line) {

				$text  = '◈ '.$line['commercialName'];
				$lines = splitLines($text);
				

				foreach ($lines as $index => $line) {
					$output[] = createNode(clearText($line), 'masa', 0, 1435 + $key * 5, 1560,
						array(
							'rectangle' => array(
								'width' => 600, 
								'height' => 600,
								'pivot' => array($masaOffset + 5, $masaOffset2 - 10 - 30 * $lineTop),
								'anchor' => array(0, 1)
							),
							'fontAlign' => 'center',
							'fontSize' => 24,
							'color' => '#000000',
							'strokeColor' => '#000000',
							'strokeWidth' => 1,
							'fontAlpha' => 0.76
						)
					);

					$lineTop++;
				}

				$lineTop+=0.5;
			}

			//duvar1
			$output[] = createNode('', 'duvar1', 0, 1600, 1672, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-60, -160),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'bold',
					'fontAlign' => 'center',
					'fontSize' => 48,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.66
				)
			);

			//duvar2
			$output[] = createNode($transform['monthlyTotal'], 'duvar2', 0, 1673, 1878, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-70, -140),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'bold',
					'fontAlign' => 'center',
					'fontSize' => 48,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.76,
					'animate' => false
				)
			);

			$output[] = createNode($transform['monthlyTariffFee'], 'duvar2', 0, 1673, 1878, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-100, -266),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'bold',
					'fontAlign' => 'center',
					'fontSize' => 24,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.96
				)
			);

			$output[] = createNode($transform['monthlyAddonsFee'], 'duvar2', 0, 1673, 1878, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-140, -298),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'bold',
					'fontAlign' => 'center',
					'fontSize' => 24,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.96
				)
			);

			$output[] = createNode($transform['monthlyTaxesFee'], 'duvar2', 0, 1673, 1878, 
				array(
					'rectangle' => array(
						'width' => 400, 
						'height' => 600,
						'pivot' => array(-285, -329),
						'anchor' => array(0, 0)
					),
					'fontWeight' => 'bold',
					'fontAlign' => 'center',
					'fontSize' => 24,
					'color' => '#ffffff',
					'strokeColor' => '#000000',
					'strokeWidth' => 1,
					'fontAlpha' => 0.96
				)
			);

			//Toplam bakiyeniz eklenecek

			$text = 'Toplam TL bakiyeniz ödenecek vergiler dahil olmak üzere yeterli ise, tarife ve ek paketleriniz düzenli olarak yenilenecektir.';
			$text = clearText($text);

			$stringLength = strlen($text);

			if($stringLength < 25){
				$fontSize = 28;
				$lineHeight = 30;
				$containerLength = 26;
				$marginTop = -30;
			}else if($stringLength < 55){
				$fontSize = 25;
				$containerLength = 26;
				$marginTop = 0;
				$lineHeight = 32;
			}else if($stringLength < 85){
				$fontSize = 20;
				$lineHeight = 25;
				$containerLength = 35;
				$marginTop = 5;
			}else{
				$fontSize = 20;
				$lineHeight = 21;
				$containerLength = 35;
				$marginTop = 5;
			}

			$lines = splitLines($text, $containerLength);

			foreach ($lines as $key => $line) {
				$output[] = createNode($line, 'duvar2', 0, 1683, 1878, 
					array(
						'rectangle' => array(
							'width' => 400, 
							'height' => 600,
							'pivot' => array(-75, $masaOffset2 + $marginTop - ($lineHeight - 3) * $key - 308),
							'anchor' => array(0, 0)
						),
						'fontAlign' => 'left',
						'fontSize' => $fontSize - 1,
						'color' => '#ffffff',
						'strokeColor' => '#000000',
						'strokeWidth' => 1,
						'fontAlpha' => 0.96
					)
				);
			}

			$text = $transform['renewalInfo'];
			$text = clearText($text);

			$stringLength = strlen($text);

			if($stringLength < 25){
				$fontSize = 28;
				$lineHeight = 30;
				$containerLength = 26;
				$marginTop = -30;
			}else if($stringLength < 55){
				$fontSize = 25;
				$containerLength = 26;
				$marginTop = 0;
				$lineHeight = 32;
			}else if($stringLength < 85){
				$fontSize = 20;
				$lineHeight = 25;
				$containerLength = 35;
				$marginTop = 5;
			}else{
				$fontSize = 15;
				$lineHeight = 17;
				$containerLength = 55;
				$marginTop = 5;
			}

			$lines = splitLines($text, $containerLength);

			foreach ($lines as $key => $line) {
				$output[] = createNode($line, 'duvar2', 0, 1895 + $key * 5, 2178, 
					array(
						'rectangle' => array(
							'width' => 400, 
							'height' => 600,
							'pivot' => array(-50, $masaOffset2 + $marginTop - ($lineHeight - 3) * $key - 105),
							'anchor' => array(0, 0)
						),
						'fontAlign' => 'left',
						'fontSize' => $fontSize - 3,
						'color' => '#ffffff',
						'strokeColor' => '#000000',
						'strokeWidth' => 1,
						'fontAlpha' => 0.96
					)
				);
			}


		}
	}

	echo json_encode($output);
	?>