import React, { useContext } from 'react';
import { __ } from '@wordpress/i18n';
import { SetupContext } from './context';

const Configuration = () => {
	const { settings, setSettings } = useContext(SetupContext);

	const handleOptions = (key) => {
		setSettings((prev) => ({
			...prev,
			[key]: !prev[key],
		}));
	};
	return (
		<>
			<div className="s3r-qs__head">
				<h2 className="s3r-qs__head-title">{__('Configuration', 'simple-301-redirects')}</h2>
				<p className="s3r-qs__head-desc">{__('Choose how your redirects match incoming requests. You can change this any time on the 301 Redirects page.', 'simple-301-redirects')}</p>
			</div>
			<div className="s3r-qs__row">
				<div className="s3r-qs__row-label">
					<h3>{__('Wildcards', 'simple-301-redirects')}</h3>
					<p>{__('Redirect everything inside a folder with one rule.', 'simple-301-redirects')}</p>
				</div>
				<div className="s3r-qs__row-control">
					<label className="s3r-qs__toggle">
						<input type="checkbox" name="wildcards" checked={!!settings.wildcards} onChange={() => handleOptions('wildcards')} />
						<span className="s3r-qs__toggle-track" aria-hidden="true" />
						<span className="s3r-qs__toggle-label">{__('Use Wildcards?', 'simple-301-redirects')}</span>
					</label>
					<p className="s3r-qs__note">
						{__('Put an asterisk (*) after the folder name. Example: /old-folder/* → /new-folder/* sends /old-folder/page/ to /new-folder/page/.', 'simple-301-redirects')}
					</p>
				</div>
			</div>
		</>
	);
};

export default Configuration;
