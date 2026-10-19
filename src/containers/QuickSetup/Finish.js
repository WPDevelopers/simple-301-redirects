import React, { useContext, useEffect, useState } from 'react';
import { __ } from '@wordpress/i18n';
import { SetupContext } from './context';
import { copyToClipboard, site_url } from './../../utils/helper';

const Finish = () => {
	const { createdRedirect } = useContext(SetupContext);
	const [copy, setCopy] = useState(false);
	useEffect(() => {
		window.scrollTo(0, 0);
	}, []);

	// The request is stored relative to the site root; build the full URL people will visit.
	const requestUrl = createdRedirect?.request ? site_url + createdRedirect.request.replace(/^\/+/, '') : '';

	return (
		<div className="s3r-qs__finish">
			<span className="s3r-qs__finish-badge">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
					<path d="m4 12.5 5 5L20 6.5" />
				</svg>
			</span>
			<h2 className="s3r-qs__finish-title">{__('Great job! You’re all set', 'simple-301-redirects')}</h2>
			<p className="s3r-qs__finish-desc">
				{__('Your setup is complete. Manage, import and export your redirects any time from Settings → 301 Redirects.', 'simple-301-redirects')}
			</p>

			{requestUrl && (
				<>
					<div className="s3r-qs__shorturl">
						<code>{requestUrl}</code>
						<button
							type="button"
							className={`s3r-qs__shorturl-btn${copy ? ' is-copied' : ''}`}
							aria-label={__('Copy redirect URL', 'simple-301-redirects')}
							onClick={() => {
								setCopy(true);
								copyToClipboard(requestUrl);
								setTimeout(() => setCopy(false), 1000);
							}}
						>
							{copy ? (
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
									<path d="m4 12.5 5 5L20 6.5" />
								</svg>
							) : (
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
									<rect x="9" y="9" width="11" height="11" rx="2" />
									<path d="M15 5.5A1.5 1.5 0 0 0 13.5 4H6a2 2 0 0 0-2 2v7.5A1.5 1.5 0 0 0 5.5 15" />
								</svg>
							)}
						</button>
						<a href={requestUrl} target="_blank" rel="noreferrer" className="s3r-qs__shorturl-btn" aria-label={__('Test redirect', 'simple-301-redirects')}>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
								<path d="M14 4h6v6" />
								<path d="M20 4 11 13" />
								<path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5" />
							</svg>
						</a>
					</div>
					<p className="s3r-qs__finish-redirect">
						{__('Redirects to', 'simple-301-redirects')} <strong>{createdRedirect.destination}</strong>
					</p>
				</>
			)}
		</div>
	);
};

export default Finish;
