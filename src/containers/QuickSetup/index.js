import React, { useState } from 'react';
import SetupCanvas from './SetupCanvas';
import { SetupContext } from './context';
import { simple301redirects_quick_setup_step, wildcard } from './../../utils/helper';

export const getInitialRedirect = () => ({
	request: '',
	destination: '',
});

const QuickSetup = () => {
	const [activeStep, setActiveStep] = useState(simple301redirects_quick_setup_step ? 1 : 0);
	const [clientConsent, setClientConsent] = useState(+simple301redirects_quick_setup_step);
	const [settings, setSettings] = useState({
		wildcards: 'true' === wildcard,
	});
	const [redirectOptions, setRedirectOptions] = useState(getInitialRedirect());
	const [createdRedirect, setCreatedRedirect] = useState(null);
	const [errors, setErrors] = useState({});

	// all settings
	const value = {
		activeStep,
		setActiveStep,
		clientConsent,
		setClientConsent,
		settings,
		setSettings,
		redirectOptions,
		setRedirectOptions,
		createdRedirect,
		setCreatedRedirect,
		errors,
		setErrors,
	};
	return (
		<SetupContext.Provider value={value}>
			<SetupCanvas />
		</SetupContext.Provider>
	);
};

export default QuickSetup;
