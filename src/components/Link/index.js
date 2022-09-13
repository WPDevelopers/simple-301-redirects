import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { __ } from '@wordpress/i18n';
import InstallPlugin from './../InstallPlugin';
import CopyLink from './../CopyLink';
import UpdateLink from './../updateLink';
import { plugin_root_url, is_betterlinks_activated } from './../../utils/helper';
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
	const [localRequest, setLocalRequest] = useState('');
	const [localDestination, setDestination] = useState('');
	useEffect(() => {
		setLocalRequest(request);
		setDestination(destination);
	}, [request, destination]);

	const [showError, setShowError] = useState(false);
	const localClickHandler = (type) => {
		if (type == 'update') {
			buttonHandler(localRequest, localDestination, type);
		} else if (type == 'delete') {
			let isDelete = confirm('Delete This Redirect?');
			if (isDelete === true) {
				buttonHandler(localRequest, localDestination, type);
			}
		} else {
			buttonHandler(localRequest, localDestination, type);
			setLocalRequest('');
			setDestination('');
		}
	};
	const buttonHandler = (localRequest, localDestination, type) => {
		if (localRequest && localDestination) {
			let param = {
				[localRequest]: localDestination,
			};
			if (request) {
				param.oldKey = request;
			}
			clickHandler(type, param);
		} else {
			setShowError(true);
		}
	};
	const keyPressEventHandler = (event) => {
		if (event.key === 'Enter') {
			if (request == '' || destination == '') {
				localClickHandler('new');
			} else {
				localClickHandler('update');
			}
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
							onKeyPress={keyPressEventHandler}
							required
						/>
					</div>
					<div className="simple301redirects__managelinks__item__icon">
						<img width="25" src={plugin_root_url + 'assets/images/icon-arrow.svg'} alt="doc" />
					</div>
					<div className="simple301redirects__managelinks__item__destination">
						<textarea
							className={showError && localDestination == '' ? 'error' : ''}
							value={localDestination}
							onChange={(e) => setDestination(e.target.value)}
							onKeyPress={keyPressEventHandler}
							rows="1"
							required
						/>
					</div>
				</div>
				<div className="simple301redirects__managelinks__item__control">
					{isNewLink ? (
						<button className="simple301redirects__button primary__button" onClick={() => localClickHandler('new')}>
							{__('Add New', 'simple-301-redirects')}
						</button>
					) : (
						<>
							<CopyLink request={localRequest} />
							<UpdateLink localClickHandler={localClickHandler} />
							{!is_betterlinks_activated && (
								<div className="simple301redirects__button lock__button s3r-tooltip">
									<img width="15" src={plugin_root_url + 'assets/images/icon-lock.svg'} alt="local" />
									<span>{__('3/1 CLICKS', 'simple-301-redirects')}</span>
									<div className="s3r-tooltiptext-wrapper">
										<div className="s3r-tooltiptext">
											{__('To see Analytics data', 'simple-301-redirects')} <InstallPlugin label={__('Install BetterLinks', 'simple-301-redirects')} />
										</div>
									</div>
								</div>
							)}
							<button className="simple301redirects__icon__button delete__button" onClick={() => localClickHandler('delete')}>
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
