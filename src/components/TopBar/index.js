import React from 'react';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import { plugin_root_url } from './../../utils/helper';

const propTypes = {};

const defaultProps = {};

export default function TopBar(props) {
	return (
		<React.Fragment>
			<div className="simple301redirects__topbar">
				<div className="simple301redirects__topbar__logo">
					<img width="40" src={plugin_root_url + 'assets/images/logo.svg'} alt="logo" />
				</div>
				<h3 className="simple301redirects__topbar__title">{__('Simple 301 Redirects', 'simple-301-redirects')}</h3>
			</div>
		</React.Fragment>
	);
}

TopBar.propTypes = propTypes;
TopBar.defaultProps = defaultProps;
