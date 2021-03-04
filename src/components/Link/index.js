import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import CopyLink from './../CopyLink';
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
		if (type == 'new') {
			setLocalRequest('');
			setDestination('');
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
					<div className="simple301redirects__managelinks__item__icon">
						<img width="25" src={plugin_root_url + 'assets/images/icon-arrow.svg'} alt="doc" />
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
						<button className="simple301redirects__button primary__button" onClick={() => localClickHandler('new')}>
							Add New
						</button>
					) : (
						<>
							<CopyLink request={localRequest} />
							<button className="simple301redirects__button success__button" onClick={() => localClickHandler('update')}>
								UPDATE
							</button>
							<button className="simple301redirects__icon__button" onClick={() => localClickHandler('delete')}>
								<img src={plugin_root_url + 'assets/images/icon-delete.svg'} alt="delete" />
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
