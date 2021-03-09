import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import axios from 'axios';
import { nonce } from './../../utils/helper';

const propTypes = {};

const defaultProps = {};

export default function Tools(props) {
	const [isOpen, setOpen] = useState(false);
	const [importResponse, setImportResponse] = useState(false);
	useEffect(() => {
		const urlParams = new URLSearchParams(window.location.search);
		if (urlParams.get('import') == 'true') {
			axios.post(`${ajaxurl}?action=simple301redirects/admin/get_import_info&security=${nonce}`).then(
				(response) => {
					setImportResponse(response.data.data);
				},
				(error) => {
					console.log(error);
				}
			);
		}
	}, []);
	return (
		<React.Fragment>
			<div className="simple301redirects__documentation">
				<div className="simple301redirects__documentation__panel-header">
					<h4>Tools</h4>
					<button onClick={() => setOpen(!isOpen)}>
						<span className={`dashicons dashicons-arrow-${isOpen ? 'up' : 'down'}-alt2`}></span>
					</button>
				</div>
				<div className="simple301redirects__documentation__panel-body">
					<form action={'admin.php?page=301options&export=true'} method="POST">
						<button type="submit" className="btl-export-download-button">
							{__('Export File', 'simple-301-redirects')}
						</button>
					</form>

					<form action={'admin.php?page=301options&import=true'} method="POST" encType="multipart/form-data">
						<div role="group" className="btl-radio-group" aria-labelledby="my-radio-group">
							<p className="btl-file-chooser">
								<label htmlFor="upload">{__('Choose the File You Want to Import', 'simple-301-redirects')}</label>
								<input type="file" id="upload_file" name="upload_file" size="25" />
							</p>
							{importResponse && <p>{importResponse}</p>}
							<p className="submit">
								<input type="submit" name="submit" id="submit" className="button button-primary" value={__('Import File', 'simple-301-redirects')} disabled="" />
							</p>
						</div>
					</form>
				</div>
			</div>
		</React.Fragment>
	);
}

Tools.propTypes = propTypes;
Tools.defaultProps = defaultProps;
