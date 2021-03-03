import React from 'react';
import PropTypes from 'prop-types';
import WildCards from './../../components/WildCards';
import Link from './../../components/Link';

const propTypes = {};

const defaultProps = {};

export default function ManageLinks(props) {
	return (
		<React.Fragment>
			<div className="simple301redirects__managelinks">
				<div className="simple301redirects__managelinks__info">
					<div className="simple301redirects__managelinks__info__inner">
						<div className="simple301redirects__managelinks__info__request">
							<h4>Request</h4>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </p>
						</div>
						<div className="simple301redirects__managelinks__info__destination">
							<h4>Destination</h4>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </p>
						</div>
					</div>
				</div>
				<Link />
				<Link />
				<Link />

				<WildCards />
			</div>
		</React.Fragment>
	);
}

ManageLinks.propTypes = propTypes;
ManageLinks.defaultProps = defaultProps;
