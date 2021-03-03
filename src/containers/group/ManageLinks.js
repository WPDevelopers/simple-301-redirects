import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { namespace, API } from './../../utils/helper';
import WildCards from './../../components/WildCards';
import Link from './../../components/Link';

const propTypes = {};

const defaultProps = {};

export default function ManageLinks(props) {
	const [links, setLinks] = useState({});
	useEffect(() => {
		API.get(namespace + 'settings').then((res) => {
			setLinks(res.data);
		});
	}, []);

	const clickHandler = (type, data = {}) => {
		if (type == 'new') {
			setLinks({ ...links, ...data });
		} else if (type == 'update') {
			setLinks({ ...links });
		} else if (type == 'delete') {
			const [deleteItemKey] = Object.keys(data);
			const tempLinks = { ...links };
			delete tempLinks[deleteItemKey];
			setLinks({ ...tempLinks });
		} else {
			setLinks(links);
		}
	};

	return (
		<React.Fragment>
			{console.log(links)}
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
				{Object.entries(links).map(([request, destination], index) => (
					<Link request={request} destination={destination} clickHandler={clickHandler} key={index} />
				))}
				<Link isNewLink={true} clickHandler={clickHandler} />
				<WildCards />
			</div>
		</React.Fragment>
	);
}

ManageLinks.propTypes = propTypes;
ManageLinks.defaultProps = defaultProps;
