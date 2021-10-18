import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import { site_url, s3r_nonce, is_betterlinks_activated } from './../../utils/helper';
import WildCards from './../../components/WildCards';
import Link from './../../components/Link';

const propTypes = {};

const defaultProps = {};

export default function ManageLinks(props) {
	const [links, setLinks] = useState({});
	useEffect(() => {
		axios
			.get(ajaxurl, {
				params: {
					action: 'simple301redirects/admin/fetch_all_links',
					security: s3r_nonce,
				},
			})
			.then(
				(response) => {
					if (response.data) {
						setLinks(response.data.data);
					}
				},
				(error) => {
					console.log(error);
				}
			);
	}, []);

	const clickHandler = (type, data = {}) => {
		if (type == 'new') {
			createLink(data);
		} else if (type == 'update') {
			updateLink(data);
		} else if (type == 'delete') {
			deleteLink(data);
		}
	};

	const createLink = (data) => {
		let form_data = new FormData();
		form_data.append('action', 'simple301redirects/admin/create_new_link');
		form_data.append('security', s3r_nonce);
		form_data.append('key', Object.keys(data));
		form_data.append('value', Object.values(data));
		return axios.post(ajaxurl, form_data).then(
			(response) => {
				if (response.data) {
					setLinks({ ...links, ...data });
				}
			},
			(error) => {
				console.log(error);
			}
		);
	};

	const updateLink = (data) => {
		const [key] = Object.keys(data);
		const [value, oldKey] = Object.values(data);
		let form_data = new FormData();
		form_data.append('action', 'simple301redirects/admin/update_link');
		form_data.append('security', s3r_nonce);
		form_data.append('key', key);
		form_data.append('value', value);
		form_data.append('oldKey', oldKey);
		return axios.post(ajaxurl, form_data).then(
			(response) => {
				if (response.data) {
					setTimeout(() => {
						setLinks(response.data.data);
					}, 2000);
				}
			},
			(error) => {
				console.log(error);
			}
		);
	};

	const deleteLink = (data) => {
		const [key] = Object.keys(data);
		let form_data = new FormData();
		form_data.append('action', 'simple301redirects/admin/delete_link');
		form_data.append('security', s3r_nonce);
		form_data.append('key', key);
		return axios.post(ajaxurl, form_data).then(
			(response) => {
				if (response.data) {
					delete links[key];
					setLinks({ ...links });
				}
			},
			(error) => {
				console.log(error);
			}
		);
	};

	return (
		<React.Fragment>
			<div className={`simple301redirects__managelinks ${is_betterlinks_activated ? 'simple301redirects__managelinks--activated-btl' : ''}`}>
				<div className="simple301redirects__managelinks__info">
					<div className="simple301redirects__managelinks__info__inner">
						<div className="simple301redirects__managelinks__info__request">
							<h4>{__('Request', 'simple-301-redirects')}</h4>
							<p>{'example: /old-page/'}</p>
						</div>
						<div className="simple301redirects__managelinks__info__destination">
							<h4>{__('Destination', 'simple-301-redirects')}</h4>
							<p>{`example: ${site_url}new-page/`}</p>
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
