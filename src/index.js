import React from 'react';
import ReactDOM from 'react-dom';
import Simple301Redirects from './containers/Simple301Redirects';
import QuickSetup from './containers/QuickSetup';

document.addEventListener('DOMContentLoaded', function () {
	const quickSetupRoot = document.getElementById('simple301redirectsquicksetup');
	if (quickSetupRoot) {
		ReactDOM.render(<QuickSetup />, quickSetupRoot);
		return;
	}
	ReactDOM.render(<Simple301Redirects />, document.getElementById('simple301redirectsbody'));
});
