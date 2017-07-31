<?php

namespace RecipeAppDemo;

use Controller;
use Core;
use Express;
use Concrete\Core\Express\EntryList;
use Symfony\Component\HttpFoundation\JsonResponse;

class Category extends Controller
{

    public function listAll($categoryID = false)
    {
        $jsonh = Core::make("helper/json");
        $res   = array();
        if ($categoryID) {
            // we just want one category
            $category = Express::getEntry($categoryID);
            if ($category) {
                $thisCategory                = array();
                $thisCategory["id"]          = $category->getID();
                $thisCategory["name"]        = $category->getRecipeCategoryName();
                $thisCategory["headerImage"] = $this->getImageURL($category->getRecipeCategoryImage());
                $thisCategory["recipeCount"] = count($category->getRecipeCategories());
                $res[]                       = $thisCategory;
            }
        } else {
            // list them all
            $entity     = Express::getObjectByHandle("recipe_category");
            $list       = new \Concrete\Core\Express\EntryList($entity);
            $categories = $list->getResults();
            foreach ($categories as $category) {
                $thisCategory                = array();
                $thisCategory["id"]          = $category->getID();
                $thisCategory["name"]        = $category->getRecipeCategoryName();
                $thisCategory["headerImage"] = $this->getImageURL($category->getRecipeCategoryImage());
                $thisCategory["recipeCount"] = count($category->getRecipeCategories());
                $res[]                       = $thisCategory;
            }
        }
        return new JsonResponse($res);
    }

    public function getCategoryRecipes($categoryID = false)
    {
        $res   = array();
        if ($categoryID) {
            $category = Express::getEntry($categoryID);
            if ($category) {
                $recipes = $category->getCategoryRecipes();
                foreach ($recipes as $recipe) {
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
        }
        return new JsonResponse($res);
    }

    public function getFeatured($categoryID = false)
    {
        $res   = array();
        if ($categoryID) {
            // just the featured for this one
            $category = Express::getEntry($categoryID);
            if ($category) {
                $recipe = $category->getFeaturedRecipe();
                if ($recipe) {
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
        } else {
            // get all the recipes, find the featured for each one
            $entity     = Express::getObjectByHandle("recipe_category");
            $list       = new \Concrete\Core\Express\EntryList($entity);
            $categories = $list->getResults();

            foreach ($categories as $category) {
                $recipe = $category->getFeaturedRecipe();
                if ($recipe) {
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
        }
        return new JsonResponse($res);
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