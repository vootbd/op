import React from 'react'
import ReactDOM from 'react-dom'
import { Test } from './components/test'

if(module.hot) {
  module.hot.accept();
}

ReactDOM.render(<Test />, document.getElementById("mixsan"))