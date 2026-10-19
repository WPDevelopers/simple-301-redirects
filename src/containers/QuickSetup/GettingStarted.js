import React, { useContext, useState } from 'react';
import { __ } from '@wordpress/i18n';
import { SetupContext } from './context';
import { makeRequest } from './../../utils/helper';

const GettingStarted = () => {
	const { setActiveStep, setClientConsent } = useContext(SetupContext);
	const [show, setShow] = useState(false);
	const handleClientConsent = async (opt_in) => {
		const res = await makeRequest({
			action: 'simple301redirects__client_consent',
			opt_in_value: opt_in,
		});
		setClientConsent(res.data?.success);
	};
	const answer = (opt_in) => {
		handleClientConsent(opt_in);
		setActiveStep(1);
	};
	return (
		<>
			<div className="s3r-qs__head s3r-qs__head--center">
				<h2 className="s3r-qs__head-title">{__('Getting Started', 'simple-301-redirects')}</h2>
				<p className="s3r-qs__head-desc">
					{__('Set up Simple 301 Redirects in a few quick steps and send visitors from old URLs to the right pages.', 'simple-301-redirects')}
				</p>
			</div>

			<div className="s3r-qs__video">
				<iframe
					src="https://www.youtube-nocookie.com/embed/F0Zyo2yDoZY"
					title={__('Simple 301 Redirects video tutorial', 'simple-301-redirects')}
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
					referrerPolicy="strict-origin-when-cross-origin"
					allowFullScreen
				/>
			</div>

			<div className="s3r-qs__consent">
				<p className="s3r-qs__consent-text">
					{__('Help us improve Simple 301 Redirects by sharing basic site and usage data with WPDeveloper. Your redirects and visitor data are never sent.', 'simple-301-redirects')}{' '}
					<button type="button" className="s3r-qs__consent-link" onClick={() => setShow(!show)} aria-expanded={show}>
						{__('What we collect', 'simple-301-redirects')}
					</button>
				</p>
				{show && (
					<div className="s3r-qs__consent-more">
						<ul>
							<li>
								{__(
									'Site: details, WordPress, PHP and server versions, installed plugins, active theme, plugin version, and installation path.',
									'simple-301-redirects'
								)}
							</li>
							<li>
								{__(
									'On deactivation, we also collect any reason provided.',
									'simple-301-redirects'
								)}
							</li>
						</ul>
						<p>
							{__("Data is sent to WPDeveloper's WPInsight service. Your server's IP address is included in the request. Redirect rules and visitor data are never sent.", 'simple-301-redirects')}{' '}
							<a href="https://wpdeveloper.com/privacy-policy/" target="_blank" rel="noopener noreferrer">
								{__('Privacy policy', 'simple-301-redirects')}
							</a>
						</p>
					</div>
				)}
				<div className="s3r-qs__consent-actions">
					<button type="button" className="s3r-qs__btn s3r-qs__btn--primary" onClick={() => answer('yes')}>
						{__('Allow & Continue', 'simple-301-redirects')}
					</button>
					<button type="button" className="s3r-qs__consent-skip" onClick={() => answer('no')}>
						{__('Skip', 'simple-301-redirects')}
					</button>
				</div>
			</div>
		</>
	);
};

export default GettingStarted;
