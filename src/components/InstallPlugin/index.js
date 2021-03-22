import React, { useState } from 'react';
import PropTypes from 'prop-types';
import { installPlugin, activePlugin } from './../../utils/helper';

const propTypes = {
	label: PropTypes.string,
};

const defaultProps = {
	label: '',
};

export default function InstallPlugin({ label }) {
	const [installButtonMessage, setInstallButtonMessage] = useState(label);
	const installHandler = async () => {
		setInstallButtonMessage('Installing...');
		installPlugin('betterlinks')
			.then((res) => {
				setInstallButtonMessage(res.data);
			})
			.then(() => {
				setInstallButtonMessage(installButtonMessage + ' Activating...');
				activePlugin('betterlinks/betterlinks.php').then((res) => {
					setInstallButtonMessage(res.data);
					setTimeout(() => {
						window.location.reload();
					}, 1000);
				});
			});
	};
	return (
		<React.Fragment>
			<button onClick={installHandler}>{installButtonMessage}</button>
		</React.Fragment>
	);
}

InstallPlugin.propTypes = propTypes;
InstallPlugin.defaultProps = defaultProps;
