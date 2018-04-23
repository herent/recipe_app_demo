import React, { Component } from 'react';
import CategoryList from './CategoryList';
import Category from './Category';
import Recipe from './Recipe';
import { BrowserRouter as Router, Route } from 'react-router-dom';

class App extends Component {
  render() {
    return (
      <Router>
        <div>
          <Route exact path="/" component={CategoryList} />
          <Route path="/category/:id" component={Category} />
          <Route path="/recipes/:id" component={Recipe} />
        </div>
      </Router>
    );
  }
}

export default App;
