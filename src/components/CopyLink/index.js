import React, { useState } from 'react';
import { __ } from '@wordpress/i18n';
import { site_url, plugin_root_url, copyToClipboard } from './../../utils/helper';
const CopyLink = (props) => {
	const [isCopyUrl, setCopyUrl] = useState(false);
	const copyShortUrl = (url) => {
		copyToClipboard(url);
		setCopyUrl(true);
		window.setTimeout(function () {
			setCopyUrl(false);
		}, 3000);
	};
	return (
		<React.Fragment>
			<button className="simple301redirects__icon__button" onClick={() => copyShortUrl(site_url + props.request)}>
				{isCopyUrl ? <span className="dashicons dashicons-yes"></span> : <img src={plugin_root_url + 'assets/images/icon-copy.svg'} alt="copy" />}
			</button>
		</React.Fragment>
	);
};
export default CopyLink;
