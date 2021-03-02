import React from 'react';
import PropTypes from 'prop-types';
import { plugin_root_url } from './../../utils/helper';

const propTypes = {};

const defaultProps = {};

export default function Link(props) {
	return (
		<React.Fragment>
			<div className="simple301redirects__managelinks__item">
				<div className="simple301redirects__managelinks__item__request">
					<input type="text" name="request" />
				</div>
				<div className="simple301redirects__managelinks__item__destination">
					<input type="text" name="destination" />
				</div>
				<div className="simple301redirects__managelinks__item__control">
					<button>
						<img src={plugin_root_url + 'assets/images/copy-icon.svg'} alt="copy" />
					</button>
					<button>UPDATE</button>
					<button>DELETE</button>
				</div>
			</div>
		</React.Fragment>
	);
}

Link.propTypes = propTypes;
Link.defaultProps = defaultProps;
