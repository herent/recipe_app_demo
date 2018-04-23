import React, { Component } from 'react';
import axios from 'axios';

class Recipe extends Component {
  constructor(props) {
    super(props);
    this.state = {
      error: null,
      isLoaded: false,
      recipe: null
    };
  }
  componentDidMount() {
    const { match: { params } } = this.props;
    var urlRecipeDetails = '//recipes.werstnet.local/ajax/recipes/detail/' + params.id;
    axios.get(urlRecipeDetails).then(res => {
      const recipe = res.data[0];
      console.log(recipe);
      this.setState({
        isLoaded: true,
        recipe
      });
    });
  }
  render() {
    const { error, isLoaded, recipe } = this.state;
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
        <article className="recipe">
          <img src={recipe.image} alt={recipe.name} />
          <h2>
            {recipe.name}
          </h2>
          <section className="details">
            <div className="difficulty">
              Difficulty: {recipe.difficulty}
            </div>
            <div className="feeds">
              Feeds: {recipe.feeds}
            </div>
            <div className="prep-time">
              Prep Time: {recipe.prepTime}
            </div>
          </section>
          <section
            className="ingredients"
            dangerouslySetInnerHTML={{ __html: '<h4>Ingredients</h4>' + recipe.ingredients }}
          />
          <section
            className="instructions"
            dangerouslySetInnerHTML={{ __html: '<h4>Instructions</h4>' + recipe.instructions }}
          />
        </article>
      );
    }
  }
}

export default Recipe;
