import React, { useContext } from 'react';
import { __ } from '@wordpress/i18n';
import { SetupContext } from './context';
import { site_url } from './../../utils/helper';

const CreateRedirect = () => {
	const { redirectOptions, setRedirectOptions, errors, setErrors } = useContext(SetupContext);

	const onChange = (key, value) => {
		setRedirectOptions((prev) => ({
			...prev,
			[key]: value,
		}));
		if (errors.redirect) {
			setErrors({});
		}
	};
	return (
		<div className="create-redirect">
			<div className="s3r-qs__head">
				<h2 className="s3r-qs__head-title">{__('Create Redirect', 'simple-301-redirects')}</h2>
				<p className="s3r-qs__head-desc">{__('Add your first 301 redirect. You can add more, edit or import them later on the 301 Redirects page.', 'simple-301-redirects')}</p>
			</div>
			<div className="s3r-qs__form-group">
				<label className="s3r-qs__form-label s3r-qs__required" htmlFor="s3r-qs-request">
					{__('Request', 'simple-301-redirects')}
				</label>
				<input
					id="s3r-qs-request"
					className="s3r-qs__form-control"
					type="text"
					value={redirectOptions.request}
					placeholder="/old-page/"
					onChange={(e) => onChange('request', e.target.value)}
				/>
				<p className="s3r-qs__help">{__('The old address, relative to your site root.', 'simple-301-redirects')}</p>
			</div>
			<div className="s3r-qs__form-group">
				<label className="s3r-qs__form-label s3r-qs__required" htmlFor="s3r-qs-destination">
					{__('Destination', 'simple-301-redirects')}
				</label>
				<input
					id="s3r-qs-destination"
					className="s3r-qs__form-control"
					type="text"
					value={redirectOptions.destination}
					placeholder={`${site_url}new-page/`}
					onChange={(e) => onChange('destination', e.target.value)}
				/>
				<p className="s3r-qs__help">{__('Where visitors should go: a full URL, or a path on this site such as /new-page/.', 'simple-301-redirects')}</p>
			</div>
			{errors.redirect && <div className="s3r-qs__error">{errors.redirect}</div>}
		</div>
	);
};

export default CreateRedirect;
