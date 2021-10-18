import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import axios from 'axios';
import PropTypes from 'prop-types';
import { s3r_nonce } from './../../utils/helper';
const propTypes = {};

const defaultProps = {};

export default function WildCards(props) {
	const [savedText, setSavedText] = useState('');
	const [checked, setChecked] = useState(false);
	const [isFetch, setFetch] = useState(false);
	useEffect(() => {
		let form_data = new FormData();
		form_data.append('action', 'simple301redirects/admin/get_wildcard');
		form_data.append('security', s3r_nonce);
		axios.post(ajaxurl, form_data).then(
			(response) => {
				setChecked(response.data.data == 'true' ? true : false);
				setFetch(true);
			},
			(error) => {
				console.log(error);
			}
		);
	}, []);
	const onChangeHandler = (param) => {
		setSavedText('Saving...');
		setChecked(param);
		let form_data = new FormData();
		form_data.append('action', 'simple301redirects/admin/wildcard');
		form_data.append('security', s3r_nonce);
		form_data.append('toggle', param);
		return axios.post(ajaxurl, form_data).then(
			(response) => {
				if (response.data.success) {
					setTimeout(() => {
						setSavedText('Saved!');
						setTimeout(() => {
							setSavedText('');
						}, 3000);
					}, 1000);
				}
			},
			(error) => {
				console.log(error);
			}
		);
	};
	return (
		<React.Fragment>
			{isFetch && (
				<div className="simple301redirects__wildcards">
					<input type="checkbox" name="wildcard" id="wildcard" defaultChecked={checked} onChange={() => onChangeHandler(!checked)} />
					<label htmlFor="wildcard">
						{__('Use Wildcards?', 'simple-301-redirects')} {savedText}
					</label>
				</div>
			)}
		</React.Fragment>
	);
}

WildCards.propTypes = propTypes;
WildCards.defaultProps = defaultProps;
