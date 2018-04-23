import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import './CategoryCard.css';

class CategoryCard extends Component {
  render(props) {
    return (
      <article className="category-card">
        <img src={this.props.headerImage} alt={this.props.name} />
        <h3>
          <Link to={`/category/${this.props.id}`}>
            {this.props.name}
          </Link>
        </h3>
        <h4>
          Recipe Count: {this.props.recipeCount}
        </h4>
      </article>
    );
  }
}

export default CategoryCard;
