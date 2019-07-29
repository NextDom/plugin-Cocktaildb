<?php
class Cocktaildb_api {

    function CocktailRandom($api)
    {
	$url = 'https://www.thecocktaildb.com/api/json/v1/1/random.php';
	$request_http = new com_http($url);
	$result = $request_http->exec(30);
	return $result;
    }

}
?>
