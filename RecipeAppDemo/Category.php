<?php

namespace RecipeAppDemo;

defined('C5_EXECUTE') or die(_('Access Denied.'));

use Controller;
use Core;
use Express;
use Concrete\Core\Express\EntryList;

class Category extends Controller
{

    public function listAll($categoryID = false)
    {
        $jsonh      = Core::make("helper/json");
        $res           = array();
        if ($categoryID) {
            // we just want one category
            $category = Express::getEntry($categoryID);
            if ($category) {
                $thisCategory = array();
                $thisCategory["id"]          = $category->getID();
                $thisCategory["name"]        = $category->getRecipeCategoryName();
                $thisCategory["headerImage"] = $this->getImageURL($category->getRecipeCategoryImage());
                $thisCategory["recipeCount"] = count($category->getRecipeCategories());
                $res[] = $thisCategory;
            }
        } else {
            // list them all
            $entity     = Express::getObjectByHandle("recipe_category");
            $list       = new \Concrete\Core\Express\EntryList($entity);
            $categories = $list->getResults();
            foreach ($categories as $category) {
                $thisCategory = array();
                $thisCategory["id"]          = $category->getID();
                $thisCategory["name"]        = $category->getRecipeCategoryName();
                $thisCategory["headerImage"] = $this->getImageURL($category->getRecipeCategoryImage());
                $thisCategory["recipeCount"] = count($category->getRecipeCategories());
                $res[] = $thisCategory;
            }
        }
        echo $jsonh->encode($res);
        die();
    }

    public function getCategoryRecipes($categoryID = false)
    {
        $jsonh         = Core::make("helper/json");
        $res           = array();
        if ($categoryID) {
            $category = Express::getEntry($categoryID);
            if ($category) {
                $recipes       = $category->getRecipeCategories();
                foreach ($recipes as $recipe) {
                    $thisRecipe               = array();
                    $thisRecipe["id"]         = $recipe->getID();
                    $thisRecipe["name"]       = $recipe->getRecipeName();
                    $thisRecipe["prepTime"]   = $recipe->getRecipePrepTime();
                    $difficulty               = (string) $recipe->getRecipeDifficulty();
                    $thisRecipe["difficulty"] = $difficulty;
                    $thisRecipe["image"]      = $this->getImageURL($recipe->getRecipeImage());
                    $res[]          = $thisRecipe;
                }
            }
        }
        echo $jsonh->encode($res);
        die();
    }

    public function getFeatured($categoryID = false)
    {
        $jsonh = Core::make("helper/json");
        $res   = array();
        if ($categoryID) {
            // just the featured for this one
            $category = Express::getEntry($categoryID);
            if ($category) {
                $recipe                   = $category->getFeaturedRecipe();
                $thisRecipe               = array();
                $thisRecipe["id"]         = $recipe->getID();
                $thisRecipe["name"]       = $recipe->getRecipeName();
                $thisRecipe["prepTime"]   = $recipe->getRecipePrepTime();
                $difficulty               = (string) $recipe->getRecipeDifficulty();
                $thisRecipe["difficulty"] = $difficulty;
                $thisRecipe["image"]      = $this->getImageURL($recipe->getRecipeImage());
                $res[]                    = $thisRecipe;
            }
        } else {
            // get all the recipes, find the featured for each one
            $entity     = Express::getObjectByHandle("recipe_category");
            $list       = new \Concrete\Core\Express\EntryList($entity);
            $categories = $list->getResults();

            foreach ($categories as $category) {
                $recipe                   = $category->getFeaturedRecipe();
                $thisRecipe               = array();
                $thisRecipe["id"]         = $recipe->getID();
                $thisRecipe["name"]       = $recipe->getRecipeName();
                $thisRecipe["prepTime"]   = $recipe->getRecipePrepTime();
                $difficulty               = (string) $recipe->getRecipeDifficulty();
                $thisRecipe["difficulty"] = $difficulty;
                $thisRecipe["image"]      = $this->getImageURL($recipe->getRecipeImage());
                $res[]                    = $thisRecipe;
            }
        }
        echo $jsonh->encode($res);
        die();
    }

    private function getImageURL($image = false){
        if ($image){
            $fv = $image->getApprovedVersion();
            $url = $fv->getURL();
        }
        return $url;
    }
}