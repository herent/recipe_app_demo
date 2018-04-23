import React, { Component } from 'react';
import axios from 'axios';
import RecipeCard from './RecipeCard';
import './Category.css';

class Category extends Component {
  constructor(props) {
    super(props);
    this.state = {
      error: null,
      isLoaded: false,
      category: [],
      recipes: []
    };
  }
  componentDidMount() {
    const { match: { params } } = this.props;
    var urlCategoryDetails = 'http://recipes.werstnet.local/ajax/recipes/categories/' + params.id;
    var urlCategoryRecipes = 'http://recipes.werstnet.local/ajax/recipes/categories/' + params.id + '/list';
    axios.get(urlCategoryDetails).then(res => {
      const category = res.data.categories[0];
      this.setState({
        category
      });
    });
    axios.get(urlCategoryRecipes).then(res => {
      const recipes = res.data['recipes'];
      console.log(res);
      this.setState({
        isLoaded: true,
        recipes
      });
      console.log(this.state.recipes);
    });
  }
  render() {
    const { error, isLoaded, category, recipes } = this.state;
    if (error) {
      return (
        <div>
          Error: {error.message}
        </div>
      );
    } else if (!isLoaded) {
      return <div>Loading...</div>;
    } else {
      return (
        <article className="category">
          <img src={category.headerImage} alt={category.name} />
          <h2>
            {category.name}
          </h2>
          <div className="recipes">
            {recipes.map(recipe => <RecipeCard key={recipe.id} {...recipe} />)}
          </div>
        </article>
      );
    }
  }
}

export default Category;
