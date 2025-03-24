import React from 'react';
import { createRoot } from 'react-dom/client';

import App from './app/App.js';
import './styles/app.scss';

(() => {
  'use-strict';
  const app = document.querySelector('div#app');
  const root = createRoot(app);

  root.render(<App />);
})();
