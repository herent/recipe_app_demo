<?php

namespace RecipeAppDemo;

use Controller;
use Core;
use Express;
use Concrete\Core\Express\EntryList;

class Recipe extends Controller
{

    public function listAll($recipeID = false)
    {
        $jsonh = Core::make("helper/json");
        $res   = array();
        if ($recipeID) {
            // we just want one recipe
            $recipe = Express::getEntry($recipeID);
            if ($recipe) {
                $thisRecipe                 = array();
                $thisRecipe["id"]           = $recipe->getID();
                $thisRecipe["name"]         = $recipe->getRecipeName();
                $thisRecipe["prepTime"]     = $recipe->getRecipePrepTime();
                $difficulty                 = (string) $recipe->getRecipeDifficulty();
                $thisRecipe["difficulty"]   = $difficulty;
                $thisRecipe["feeds"]        = $recipe->getRecipeFeeds();
                $thisRecipe["image"]        = $this->getImageURL($recipe->getRecipeImage());
                $thisRecipe["ingredients"]  = $recipe->getRecipeIngredients();
                $thisRecipe["instructions"] = $recipe->getRecipeInstructions();
                $res[]                      = $thisRecipe;
            }
        } else {
            // list them all
            $entity  = Express::getObjectByHandle("recipe");
            $list    = new \Concrete\Core\Express\EntryList($entity);
            $recipes = $list->getResults();
            foreach ($recipes as $recipe) {
                $thisRecipe               = array();
                $thisRecipe["id"]         = $recipe->getID();
                $thisRecipe["name"]       = $recipe->getRecipeName();
                $thisRecipe["prepTime"]   = $recipe->getRecipePrepTime();
                $difficulty               = (string) $recipe->getRecipeDifficulty();
                $thisRecipe["difficulty"] = $difficulty;
                $thisRecipe["feeds"]      = $recipe->getRecipeFeeds();
                $thisRecipe["image"]      = $this->getImageURL($recipe->getRecipeImage());
                $res[]                    = $thisRecipe;
            }
        }
        echo $jsonh->encode($res);
        die();
    }

    private function getImageURL($image = false)
    {
        if ($image) {
            $fv  = $image->getApprovedVersion();
            $url = $fv->getURL();
        }
        return $url;
    }
}