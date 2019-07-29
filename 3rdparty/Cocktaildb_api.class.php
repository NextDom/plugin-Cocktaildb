<?php
class Cocktaildb_api {

    function CocktailRandom($api)
    {
	$url = 'https://www.thecocktaildb.com/api/json/v1/1/random.php';
	$request_http = new com_http($url);
	$result = $request_http->exec(30);
	return $result;
    }
    function translate($val)
    {
 $CLIENT_ID = "FREE_TRIAL_ACCOUNT";
 $CLIENT_SECRET = "PUBLIC_SECRET";
 // Specify your translation requirements here:
 $postData = array(
   'fromLang' => "en",
     'toLang' => "fr",
       'text' => $val 
       );
       $headers = array(
         'Content-Type: application/json',
           'X-WM-CLIENT-ID: '.$CLIENT_ID,
             'X-WM-CLIENT-SECRET: '.$CLIENT_SECRET
             );
             $url = 'http://api.whatsmate.net/v1/translation/translate';
             $ch = curl_init($url);
             curl_setopt($ch, CURLOPT_POST, 1);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
             $response = curl_exec($ch);
             echo "Response: ".$response;
             curl_close($ch);
             return $response;

    }


}
?>
