import React, { Component } from 'react';
import axios from 'axios';
import CategoryCard from './CategoryCard';

class CategoryList extends Component {
  constructor(props) {
    super(props);
    this.state = {
      error: null,
      isLoaded: false,
      categories: []
    };
  }
  componentDidMount() {
    axios.get('http://recipes.werstnet.local/ajax/recipes/categories').then(res => {
      const categories = res.data.categories;
      this.setState({
        isLoaded: true,
        categories
      });
    });
  }
  render() {
    const { error, isLoaded, categories } = this.state;
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
        <div className="categories">
          {categories.map(category => <CategoryCard key={category.id} {...category} />)}
        </div>
      );
    }
  }
}

export default CategoryList;
