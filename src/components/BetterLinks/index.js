import React from 'react';
import PropTypes from 'prop-types';

const propTypes = {};

const defaultProps = {};

export default function BetterLinks(props) {
	return (
		<React.Fragment>
			<div className="simple301redirects__betterlinks">
				<div className="simple301redirects__betterlinks__icon">
					<img src="" alt="" />
				</div>
				<div className="simple301redirects__betterlinks__content">
					<h3>Betterlinks</h3>
					<p>
						To get the best out of 301 Redirects and get analytics data, you can migrate to the ultimate Link shortener plugin- BetterLinks. To install BetterLinks,{' '}
						<a href="#">Click here</a>
					</p>
				</div>
				<div className="simple301redirects__betterlinks__control">
					<button>Install BetterLinks</button>
				</div>
			</div>
		</React.Fragment>
	);
}

BetterLinks.propTypes = propTypes;
BetterLinks.defaultProps = defaultProps;
