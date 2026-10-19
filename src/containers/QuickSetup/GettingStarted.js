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
									'Site: address and name, language and charset, WordPress, PHP and server software versions, whether it is a multisite, text direction, and where Simple 301 Redirects is installed on your server (its file path).',
									'simple-301-redirects'
								)}
							</li>
							<li>{__('Plugins and theme: active and inactive plugins, your active theme and its version, and the Simple 301 Redirects version.', 'simple-301-redirects')}</li>
							<li>{__('On deactivation: the same details again, plus any reason entered in the deactivation form.', 'simple-301-redirects')}</li>
							<li>{__("Like any web request, sending this reveals your server's IP address.", 'simple-301-redirects')}</li>
						</ul>
						<p>
							{__('Sent to WPDeveloper\'s WPInsight service (send.wpinsight.com) when you allow it, then about once a day (only what changed) and when the plugin is deactivated. Nothing is sent if you skip.', 'simple-301-redirects')}{' '}
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
