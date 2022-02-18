import React, {Component} from 'react'
import {
  Routes,
  Route
} from "react-router-dom";
import ProductsList from './ProductsList';
import ProductAdd from './ProductAdd';


/**
 * Main app component responsible for routing routes to approprite page component
 */
class App extends Component {
  render() {
    return (
      <div className='App'>
        <Routes>
          <Route path='/' element={<ProductsList/>}/>
          <Route path='/add-product' element={<ProductAdd />} />
        </Routes>
      </div>
    )
  }
}

export default App;
