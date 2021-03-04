import React from 'react';
import PropTypes from 'prop-types';
const propTypes = {};
const defaultProps = {};
export default function BetterLinks(props) {
	return (
		<React.Fragment>
			<div className="simple301redirects__betterlinks">
				<div className="simple301redirects__betterlinks__content">
					<h3>Betterlinks</h3>
					<h4>Install BetterLinks to get the best out of 301 Redirects and get access to more advanced features. Check out the features below</h4>
					<ul>
						<li>Easy-to-use & Simple Link Shortener</li>
						<li>Create attractive looking links instantly</li>
						<li>Safe Redirection URLs </li>
						<li>Analyze and Track your marketing campaigns from one place</li>
						<li>Optimized queries to reduce load time & make faster</li>
						<li>Individual Analytics for each links</li>
					</ul>
					<h4>You can migrate from 301 Redirects to BetterLinks within one click.</h4>
				</div>
				<div className="simple301redirects__betterlinks__control">
					<button>Install BetterLinks</button>
					<h3>
						Discount Offer: Grab BetterLinks PRO with 20% Off. <a href="#">Click Here</a> to grab the offer.
					</h3>
				</div>
			</div>
		</React.Fragment>
	);
}
BetterLinks.propTypes = propTypes;
BetterLinks.defaultProps = defaultProps;
