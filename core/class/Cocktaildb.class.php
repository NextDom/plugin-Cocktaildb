<?php

require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
require_once __DIR__ . '/../../3rdparty/Cocktaildb_api.class.php';

class Cocktaildb extends eqLogic
{

    /*************** Attributs ***************/

    /************* Static methods ************/

    /**
     * Tâche exécutée toutes les 30 minutes.
     *
     * @param null $_eqLogic_id Identifiant des objets
     */
    public static function cron30($_eqLogic_id = null)
    {
        // Récupère la liste des équipements
        if ($_eqLogic_id == null) {
            $eqLogics = self::byType('Cocktaildb', true);
        } else {
            $eqLogics = array(self::byId($_eqLogic_id));
        }
        // Met à jour l'ensemble des équipements
        foreach ($eqLogics as $CocktaildbObj) {
            // On récupère la commande 'data' appartenant à l'équipement
            $getDataCmd = $this->getCmd(null, 'refresh');
            $dataCmd = $CocktaildbObj->getCmd('action', 'refresh');
            $dataCmd->execute();
        }
    }

    /**************** Methods ****************/

    public function postUpdate()
    {
        $getDataCmd = $this->getCmd(null, 'strDrink');
        if (!is_object($getDataCmd)) {
            // Création de la commande
	    $this->createcmd('Cocktail','strDrink','info','string');
	    $this->createcmd('Type de Verre','strGlass','info','string');
	    $this->createcmd('Ingredients','ingredients','info','string');
	    $this->createcmd('Recette','strInstructions','info','string');
	    $this->createcmd('URL Image','strDrinkThumb','info','string');
	    $this->createcmd('Rafraichir','refresh','action','other');
        }
    }
    /********** Getters and setters **********/
    public function createcmd($Name,$Id,$Type,$SubType)
    {
            $cmd = new CocktaildbCmd();
            $cmd->setName($Name);
            $cmd->setLogicalId($Id);
            $cmd->setEqLogic_id($this->getId());
            $cmd->setType($Type);
            $cmd->setSubType($SubType);
            $cmd->setIsVisible(1);
            $cmd->save();
    }

}

class CocktaildbCmd extends cmd
{

    /*************** Attributs ***************/

    /************* Static methods ************/

    /**************** Methods ****************/

    public function execute($_options = array()) {
        // Test pour ne répondre qu'à la commande rafraichir
	    if ($this->getLogicalId() == 'refresh') {
		$cocktaildbobj = Cocktaildb::byId($this->getEqlogic_id());
	        $dataCmd = $cocktaildbobj->getCmd('info', 'strDrink');
			      
		$session_cocktaildb = new Cocktaildb_api();
		$DataCocktaildb = $session_cocktaildb->CocktailRandom($this->getConfiguration('api'));
		$Cocktails = json_decode($DataCocktaildb, true);

		foreach ($Cocktails["drinks"] as $cocktail )
	        {
	                $cocktaildbobj->checkAndUpdateCmd('strDrink',$cocktail["strDrink"]."<br>");
	                $cocktaildbobj->checkAndUpdateCmd('strGlass',$cocktail["strGlass"]."<br>");
	                $cocktaildbobj->checkAndUpdateCmd('strInstructions',$cocktail["strInstructions"]."<br>");
	                $cocktaildbobj->checkAndUpdateCmd('strDrinkThumb',$cocktail["strDrinkThumb"]);
			$val = $cocktail["strMeasure1"]." ".$cocktail["strIngredient1"];
			for ($x = 2; $x <= 15; $x++) {
				$sep = ($x%2)?"<br>":"";
				$val = $val . (empty($cocktail["strIngredient".$x])?"":",".$sep).$cocktail["strMeasure".$x]." ".$cocktail["strIngredient".$x];
		        }
	                $cocktaildbobj->checkAndUpdateCmd('ingredients',$val."<br>");
		}
		$dataCmd->save();
        }
    }

}
