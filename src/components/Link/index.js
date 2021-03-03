import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { plugin_root_url } from './../../utils/helper';

const propTypes = {
	request: PropTypes.string,
	destination: PropTypes.string,
	isNewLink: PropTypes.bool,
	clickHandler: PropTypes.func,
};

const defaultProps = {
	request: '',
	destination: '',
	isNewLink: false,
};

export default function Link({ request, destination, isNewLink, clickHandler }) {
	const [localRequest, setLocalRequest] = useState(request);
	const [localDestination, setDestination] = useState(destination);
	const [showError, setShowError] = useState(false);
	const localClickHandler = (type) => {
		if (localRequest && localDestination) {
			clickHandler(type, {
				[localRequest]: localDestination,
			});
		} else {
			setShowError(true);
		}
	};
	return (
		<React.Fragment>
			<div className="simple301redirects__managelinks__item">
				<div className="simple301redirects__managelinks__item__inner">
					<div className="simple301redirects__managelinks__item__request">
						<input
							className={showError && localRequest == '' ? 'error' : ''}
							type="text"
							name="request"
							value={localRequest}
							onChange={(e) => setLocalRequest(e.target.value)}
							required
						/>
					</div>
					<div className="simple301redirects__managelinks__item__destination">
						<input
							className={showError && localDestination == '' ? 'error' : ''}
							type="text"
							name="destination"
							value={localDestination}
							onChange={(e) => setDestination(e.target.value)}
							required
						/>
					</div>
				</div>
				<div className="simple301redirects__managelinks__item__control">
					{isNewLink ? (
						<button className="simple301redirects__button" onClick={() => localClickHandler('new')}>
							Add New
						</button>
					) : (
						<>
							<button className="simple301redirects__icon__button">
								<img src={plugin_root_url + 'assets/images/copy-icon.svg'} alt="copy" />
							</button>
							<button className="simple301redirects__button" onClick={() => localClickHandler('update')}>
								UPDATE
							</button>
							<button className="simple301redirects__button" onClick={() => localClickHandler('delete')}>
								DELETE
							</button>
						</>
					)}
				</div>
			</div>
		</React.Fragment>
	);
}

Link.propTypes = propTypes;
Link.defaultProps = defaultProps;
