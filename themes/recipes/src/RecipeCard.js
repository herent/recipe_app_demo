import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import './RecipeCard.css';

class RecipeCard extends Component {
  render(props) {
    return (
      <article className="recipe-card">
        <img src={this.props.image} alt={this.props.name} />
        <h3>
          <Link to={`/recipes/${this.props.id}`}>
            {this.props.name}
          </Link>
        </h3>
        <h4>
          Difficulty: {this.props.difficulty}
        </h4>
        <h4>
          Prep Time: {this.props.prepTime}
        </h4>
      </article>
    );
  }
}

export default RecipeCard;
