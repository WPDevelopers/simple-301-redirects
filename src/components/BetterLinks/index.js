import React, { useState } from 'react';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import axios from 'axios';
import InstallPlugin from './../../components/InstallPlugin';
const propTypes = {};
const defaultProps = {};
import { plugin_root_url, s3r_nonce, is_betterlinks_activated, hide_btl_notice } from './../../utils/helper';
export default function BetterLinks(props) {
	const [isHideNotice, setHideNotice] = useState(hide_btl_notice);
	const noticCloseHandler = () => {
		let form_data = new FormData();
		form_data.append('action', 'simple301redirects/admin/hide_notice');
		form_data.append('security', s3r_nonce);
		form_data.append('hide', true);
		return axios.post(ajaxurl, form_data).then(
			(response) => {
				setHideNotice(true);
			},
			(error) => {
				console.log(error);
			}
		);
	};
	return (
		<React.Fragment>
			{!is_betterlinks_activated && !isHideNotice && (
				<div className="simple301redirects__betterlinks">
					<button onClick={noticCloseHandler} className="simple301redirects__betterlinks__close__button">
						<img width="20" src={plugin_root_url + 'assets/images/close.svg'} alt="logo" />
					</button>
					<div className="simple301redirects__betterlinks__content">
						<h3>{__('BetterLinks – Shorten, Track and Manage any URL', 'simple-301-redirects')}</h3>
						<h4>
							{__('Install BetterLinks to get the best out of Simple 301 Redirects and get access to more advanced features. Check out the features below', 'simple-301-redirects')}
							<img width="18" style={{ transform: 'translateY(6px) scale(1.5)', marginLeft: 8 }} src={plugin_root_url + 'assets/images/pointing-down.svg'} alt="logo" />
						</h4>
						<ul>
							<li>{__('Easy-to-use & Simple Link Shortener', 'simple-301-redirects')}</li>
							<li>{__('Create attractive looking links instantly', 'simple-301-redirects')}</li>
							<li>{__('Safe Redirection URLs', 'simple-301-redirects')}</li>
							<li>{__('Analyze and Track your marketing campaigns from one place', 'simple-301-redirects')}</li>
							<li>{__('Optimized queries to reduce load time & make faster', 'simple-301-redirects')}</li>
							<li>{__('Individual Analytics for each links', 'simple-301-redirects')}</li>
						</ul>
						<h4>{__('You can migrate from Simple 301 Redirects to BetterLinks within one click.', 'simple-301-redirects')}</h4>
					</div>
					<div className="simple301redirects__betterlinks__control">
						<InstallPlugin label={__('Install BetterLinks', 'simple-301-redirects')} />
					</div>
				</div>
			)}
		</React.Fragment>
	);
}
BetterLinks.propTypes = propTypes;
BetterLinks.defaultProps = defaultProps;
