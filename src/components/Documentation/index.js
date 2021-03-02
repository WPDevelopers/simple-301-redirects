import React from 'react';
import PropTypes from 'prop-types';

const propTypes = {};

const defaultProps = {};

export default function Documentation(props) {
	return (
		<React.Fragment>
			<div className="simple301redirects__documentation">
				<div className="simple301redirects__documentation__panel-header">
					<h4>
						<span className="dashicons dashicons-media-document"></span> Documentation
					</h4>
					<button>
						<span className="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>
				<div className="simple301redirects__documentation__panel-body"></div>
			</div>
		</React.Fragment>
	);
}

Documentation.propTypes = propTypes;
Documentation.defaultProps = defaultProps;
