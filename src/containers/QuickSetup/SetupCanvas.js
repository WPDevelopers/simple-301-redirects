import React, { Fragment, useContext, useEffect, useState } from 'react';
import { __ } from '@wordpress/i18n';
import GettingStarted from './GettingStarted';
import Configuration from './Configuration';
import CreateRedirect from './CreateRedirect';
import Finish from './Finish';
import { SetupContext } from './context';
import { makeRequest, settings_page_url } from './../../utils/helper';

const CheckIcon = () => (
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
		<path d="m4 12.5 5 5L20 6.5" />
	</svg>
);

const ArrowIcon = ({ dir = 'right' }) => (
	<svg
		xmlns="http://www.w3.org/2000/svg"
		viewBox="0 0 24 24"
		width="16"
		height="16"
		fill="none"
		stroke="currentColor"
		strokeWidth="2"
		strokeLinecap="round"
		strokeLinejoin="round"
		aria-hidden="true"
		style={'left' === dir ? { transform: 'rotate(180deg)' } : undefined}
	>
		<path d="M4 12h15" />
		<path d="m13 6 6 6-6 6" />
	</svg>
);

// Same steps as BetterLinks' Quick Setup, without Migration: there is nothing to migrate into Simple 301 Redirects.
const getSteps = () => [__('Getting Started', 'simple-301-redirects'), __('Configuration', 'simple-301-redirects'), __('Create Redirect', 'simple-301-redirects'), __('Finish', 'simple-301-redirects')];

const getSetupStepComponents = (component) => {
	const components = {
		0: <GettingStarted />,
		1: <Configuration />,
		2: <CreateRedirect />,
		3: <Finish />,
	};
	return components[component];
};

const SetupCanvas = () => {
	const steps = getSteps();
	const { activeStep, setActiveStep, clientConsent, settings, redirectOptions, setRedirectOptions, setCreatedRedirect, setErrors } = useContext(SetupContext);
	const [isNextDisabled, setNextDisabled] = useState(false);
	const [isBusy, setBusy] = useState(false);

	const handleStepChange = () => {
		switch (activeStep) {
			case 1:
				saveSettings();
				break;
			case 2:
				submitRedirectHandler(redirectOptions);
				break;
			case 3:
				completeSetup();
				break;
			default:
				break;
		}
	};

	// Configuration: wildcards is the plugin's only setting, saved through the same endpoint as the settings page.
	const saveSettings = async () => {
		setBusy(true);
		try {
			await makeRequest({
				action: 'simple301redirects/admin/wildcard',
				toggle: settings.wildcards ? 'true' : 'false',
			});
			setActiveStep(2);
		} catch (error) {
			console.log('---error (saveSettings)--', { error });
		}
		setBusy(false);
	};

	const submitRedirectHandler = async (values) => {
		const request = values.request.trim();
		const destination = values.destination.trim();
		if (!request || !destination) {
			setErrors({ redirect: __('Please enter both a request and a destination.', 'simple-301-redirects') });
			return;
		}
		setBusy(true);
		try {
			const res = await makeRequest({
				action: 'simple301redirects/admin/create_new_link',
				key: request,
				value: destination,
			});
			const links = res.data?.data || {};
			// The endpoint keeps an existing redirect for the same request instead of overwriting it.
			if (links[request] !== undefined && links[request] !== destination) {
				setErrors({ redirect: __('A redirect for this request already exists. Use a different request.', 'simple-301-redirects') });
			} else if (links[request] === destination) {
				setErrors({});
				setCreatedRedirect({ request, destination });
				setRedirectOptions({ request, destination });
				setActiveStep(3);
			} else {
				setErrors({ redirect: __('The redirect could not be saved. Please try again.', 'simple-301-redirects') });
			}
		} catch (error) {
			console.log('---error (submitRedirectHandler)--', { error });
			setErrors({ redirect: __('The redirect could not be saved. Please try again.', 'simple-301-redirects') });
		}
		setBusy(false);
	};

	const completeSetup = async () => {
		setBusy(true);
		const res = await makeRequest({
			action: 'simple301redirects__complete_setup',
		});
		const data = res.data?.data;
		if (data?.result === 'complete') {
			window.location.href = settings_page_url;
			return;
		}
		setBusy(false);
	};

	useEffect(() => {
		if (2 == activeStep) {
			setNextDisabled('' == redirectOptions.request.trim() || '' == redirectOptions.destination.trim());
		} else {
			setNextDisabled(false);
		}
	}, [activeStep, redirectOptions.request, redirectOptions.destination]);

	const isLastStep = activeStep === steps.length - 1;

	return (
		<div className="s3r-qs">
			{/* Progress stepper */}
			<div className="s3r-qs__steps" role="list" aria-label={__('Setup progress', 'simple-301-redirects')}>
				{steps.map((label, index) => (
					<Fragment key={label}>
						{index > 0 && <span className={`s3r-qs__step-line${index <= activeStep ? ' is-done' : ''}`} />}
						<div className={`s3r-qs__step${index === activeStep ? ' is-active' : ''}${index < activeStep ? ' is-done' : ''}`} role="listitem">
							<span className="s3r-qs__step-dot">{index < activeStep ? <CheckIcon /> : index + 1}</span>
							<span className="s3r-qs__step-label">{label}</span>
						</div>
					</Fragment>
				))}
			</div>

			<div className="s3r-qs__panel">{getSetupStepComponents(activeStep)}</div>

			<div className="s3r-qs__footer">
				{2 === activeStep && (
					<button
						type="button"
						className="s3r-qs__skip"
						onClick={() => {
							setErrors({});
							setActiveStep(activeStep + 1);
						}}
					>
						{__('Skip this step', 'simple-301-redirects')}
					</button>
				)}
				{activeStep > 0 && (
					<div className="s3r-qs__actions">
						{(activeStep !== 1 || !clientConsent) && (
							<button type="button" className="s3r-qs__btn s3r-qs__btn--ghost" onClick={() => setActiveStep(activeStep - 1)} disabled={isBusy}>
								<ArrowIcon dir="left" />
								{__('Back', 'simple-301-redirects')}
							</button>
						)}
						<button type="button" className="s3r-qs__btn s3r-qs__btn--primary" onClick={handleStepChange} disabled={isNextDisabled || isBusy}>
							{isLastStep ? (
								__('Finish', 'simple-301-redirects')
							) : (
								<>
									{__('Next', 'simple-301-redirects')}
									<ArrowIcon dir="right" />
								</>
							)}
						</button>
					</div>
				)}
			</div>
		</div>
	);
};

export default SetupCanvas;
