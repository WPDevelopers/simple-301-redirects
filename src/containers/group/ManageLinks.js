import React, { useEffect, useState } from 'react';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import { namespace, API, site_url, is_betterlinks_activated } from './../../utils/helper';
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
			const [key] = Object.keys(data);
			const [value] = Object.values(data);
			return API.post(namespace + 'settings', {
				key: key,
				value: value,
			});
		} else if (type == 'update') {
			setLinks({ ...links });
			const [key] = Object.keys(data);
			const [value, oldKey] = Object.values(data);
			return API.put(namespace + 'settings', {
				key: key,
				value: value,
				oldKey: oldKey,
			});
		} else if (type == 'delete') {
			const [key] = Object.keys(data);
			const tempLinks = { ...links };
			delete tempLinks[key];
			setLinks({ ...tempLinks });
			return API.delete(namespace + 'settings', {
				params: {
					key: key,
				},
			});
		} else {
			setLinks(links);
		}
	};

	return (
		<React.Fragment>
			<div className={`simple301redirects__managelinks ${is_betterlinks_activated ? 'simple301redirects__managelinks--activated-btl' : ''}`}>
				<div className="simple301redirects__managelinks__info">
					<div className="simple301redirects__managelinks__info__inner">
						<div className="simple301redirects__managelinks__info__request">
							<h4>{__('Request', 'simple-301-redirects')}</h4>
							<p>{'example: /about.html'}</p>
						</div>
						<div className="simple301redirects__managelinks__info__destination">
							<h4>{__('Destination', 'simple-301-redirects')}</h4>
							<p>{`example: ${site_url}/about/`}</p>
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
