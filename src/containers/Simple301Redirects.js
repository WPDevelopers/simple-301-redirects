import React from 'react';
import TopBar from './../components/TopBar';
import ManageLinks from './group/ManageLinks';
import BetterLinks from './../components/BetterLinks';
import Documentation from './../components/Documentation';
import Tools from './../components/Tools';

export default function Simple301Redirects(props) {
	return (
		<React.Fragment>
			<TopBar />
			<div className="Simple301Redirects__content__wrapper">
				<div className="Simple301Redirects__main__content">
					<ManageLinks />
					<BetterLinks />
				</div>
				<div className="Simple301Redirects__archive__sidebar">
					<Tools />
					<Documentation />
				</div>
			</div>
		</React.Fragment>
	);
}