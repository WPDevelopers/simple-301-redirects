import React, { useState } from 'react';
import PropTypes from 'prop-types';
import axios from 'axios';
import InstallPlugin from './../../components/InstallPlugin';
const propTypes = {};
const defaultProps = {};
import { plugin_root_url, nonce, is_betterlinks_activated, hide_btl_notice } from './../../utils/helper';
export default function BetterLinks(props) {
	const [isHideNotice, setHideNotice] = useState(hide_btl_notice);
	const noticCloseHandler = () => {
		let form_data = new FormData();
		form_data.append('action', 'simple301redirects/admin/hide_notice');
		form_data.append('security', nonce);
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
						<InstallPlugin label="Install BetterLinks" />
						<h3>
							Discount Offer: Grab BetterLinks PRO with 20% Off. <a href="#">Click Here</a> to grab the offer.
						</h3>
					</div>
				</div>
			)}
		</React.Fragment>
	);
}
BetterLinks.propTypes = propTypes;
BetterLinks.defaultProps = defaultProps;
