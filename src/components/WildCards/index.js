import React from 'react';
import PropTypes from 'prop-types';

const propTypes = {};

const defaultProps = {};

export default function WildCards(props) {
	return (
		<React.Fragment>
			<div className="simple301redirects__wildcards">
				<input type="checkbox" name="wildcard" id="wildcard" />
				<label htmlFor="wildcard"> Use Wildcards?</label>
			</div>
		</React.Fragment>
	);
}

WildCards.propTypes = propTypes;
WildCards.defaultProps = defaultProps;
